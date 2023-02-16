<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Http\Services\API;
use App\Http\Services\DashboardService;
use App\Models\PaystackTransaction;
use App\Models\Transaction;
use App\Models\Wallet;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $api;
    protected $paystack;
    protected $dashboardService;

    public function __construct()
    {
        $this->api = new API();
        $this->paystack = new API('https://api.paystack.co', 'paystack');
        $this->dashboardService = new DashboardService();
    }
    public function show_product_page()
    {      
        $product_name = request('product');

        // fetch airtime data from db
        $product = ProductCategory::where('name', 'LIKE', "%$product_name%")->first();

        $product_providers= Product::where('category_id', $product->id)->get()->toArray();

        $page_title = ucfirst($product->name);

        // send data to FE
        return view('pages.product', compact('product_providers', 'page_title'));
    }

    public function show_purchase_page()
    {

        $service_id = request('service_id');

        $product = $this->dashboardService->get_product_from_vtpass($service_id);

        $variations = [];

        $category_name = strtolower(explode(' ', $product->category)[0]);

        if (in_array($category_name, ["data", "tv", "electricity"])) {
            // get data bundles and pass to view
            $response = $this->api->get("/api/service-variations", ["serviceID" => $service_id]);

            if (!$response->isSuccessful()) {
                $variations = [];
            }

            $variations = $response->getResponse()->content->varations;
        }

        return view("pages.buy-$category_name", compact('product', 'variations'));
    }

    public function confirm_purchase() {
        $user = auth()->user();

        $service_id = request('service_id');

        $vtpass_product = $this->dashboardService->get_product_from_vtpass($service_id);

        $amount_rule = !empty($vtpass_product->maximum_amount) ?
        [
            'required', 'numeric', 
            "gt:".$vtpass_product->minimium_amount ?? 0, 
            "lt:".$vtpass_product->maximum_amount
        ] :
        [
            'required', 'numeric', 
            "gt:0", 
        ];

        $rule = [
            'bundle' => ['nullable'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'numeric'],
            'amount' => $amount_rule,
            'service_id' => ['required', 'string'],
            'variation_code' => ['nullable', 'string'],
            'product_name' => ['required', 'string'],
            'smartcard_no' => ['nullable', 'string'],
            'meter_number' => ['nullable', 'string'],
            'sub_type' => ['nullable', 'string'],
            'sub_plan' => ['nullable', 'string'],
            'quantity' => ['nullable', 'numeric'],
        ];

        $purchase_data = request()->validate($rule);

        $product= Product::where('service_id', $service_id)->first();
        
        $transaction_payload = [
            'wallet_id' => $user->wallet()->id,
            'user_id' => $user->id,
            'status' => 'initiated',
            'amount' => $purchase_data['amount'],
            'phone' => $purchase_data['phone'],
            'email' => $purchase_data['email'],
            'service_id' => $purchase_data['service_id'],
            'product_id' => $product->id,
            'ref_no' => generate_random_string(12)
        ];

        if (in_array('bundle', array_keys($purchase_data))) {
            $purchase_data['bundle'] = json_decode($purchase_data['bundle']);

            $transaction_payload = array_merge($transaction_payload, [
                    'variation_code' => $purchase_data['bundle']->variation_code
            ]);
        } elseif (in_array('smartcard_no', array_keys($purchase_data))) {
            $purchase_data['sub_plan'] = json_decode($purchase_data['sub_plan']);

            $transaction_payload = array_merge($transaction_payload, [
                    'smartcard_no' => $purchase_data['smartcard_no'],
                    'subscription_type' => $purchase_data['sub_type'],
                    'quantity' => $purchase_data['quantity'],
                    'variation_code' => $purchase_data['sub_plan']->variation_code
            ]);
        } elseif (in_array('meter_number', array_keys($purchase_data))) {
            $transaction_payload = array_merge($transaction_payload, [
                    'meter_no' => $purchase_data['meter_number'],
                    'variation_code' => $purchase_data['variation_code'],
            ]);
        }

        // dd($transaction_payload);

        $transaction = Transaction::create($transaction_payload);

        return redirect("/confirm/$transaction->ref_no");
    }

    public function show_confirmation_page()
    {
        $transaction_key = request('transaction_id');

        $purchase_data = Transaction::where('ref_no', $transaction_key)->first();

        return view('pages.confirm', compact('purchase_data'));
    }

    public function buy() 
    {
        $user_id = auth()->user()->id;
        $user_wallet = Wallet::where('user_id', $user_id)->first();

        // get purchase_data
        $purchase_data = json_decode(request('purchase_data'));

        // build payload
        $payload = build_payment_payload($purchase_data);

        $transaction = Transaction::where('ref_no', $purchase_data->ref_no)->first();

        if ($transaction->status == "failed" || $transaction->status == "successful") {
            return back()->with('message', 'This transaction has been completed. Go back to the products page for purchase.');
        }
        
        // Send request to API
        $response = $this->api->post('/api/pay', $payload);


        if ($user_wallet->balance < $payload['amount']) {
            return back()->with('message', 'Insufficient funds. Fund your account to continue this transaction');
        }

        // debit user wallet
        $user_wallet->balance -= $payload['amount'];

        $user_wallet->save();

        if ($response->getResponse()->code != "000") {
            $data = $response->getResponse();

            Log::error($response->getMessage() . ' ' . $response->getStatusCode());

            $transaction->status = 'failed';
            $transaction->request_id = $data->requestId;
            
            $transaction->save();

            // debit user wallet
            $user_wallet->balance += $payload['amount'];

            $user_wallet->save();

            return back()->with('message', 'Something went wrong while initiating payment. Try again');
        }

        $data = $response->getResponse();

        if (str_contains($purchase_data->service_id, 'electric')) {
            $transaction->utility_token = $data->purchased_code;
        }

        $transaction->status = 'successful';
        $transaction->request_id = $data->requestId;

        $transaction->save();

        $product = $data->content->transactions->product_name;

        $message = "$data->amount purchase of $product successful. \n" . ($data->purchased_code ?? '');

        return redirect('/success')->with('message', $message);
    }

    public function initiate_paystack_payment() {

        $user_id = auth()->user()->id;
        $user_wallet = Wallet::where('user_id', $user_id)->first();

        // get purchase_data
        $purchase_data = json_decode(request('purchase_data'));

        // build payload
        $payload = build_payment_payload($purchase_data);

        $transaction = Transaction::where('ref_no', $purchase_data->ref_no)->first();

        if ($transaction->status == "failed" || $transaction->status == "successful") {
            return back()->with('message', 'This transaction has been completed. Go back to the products page for purchase.');
        }

        // Ping Paystack Charge API.
        $paystack_req = $this->paystack->post('/transaction/initialize', [ 
            'email' => $payload['email'], 
            'amount' => ((int) $payload['amount']) * 100 
        ]);

        if (!$paystack_req->isSuccessful()) {
            dd($paystack_req->getMessage());

            return null;
        }

        $cookie = Cookie::forever('transaction_ref', $transaction->ref_no);

        $response = $paystack_req->getResponse();

        // Redirect FE to authorization_url

        return redirect($response->data->authorization_url)->withCookie($cookie);
    }

    public function verify_paystack_payment() {
        // get transaction details
        $tx_ref = request('trxref');

        // verify payment from paystack
        $paystack_req = $this->paystack->get("/transaction/verify/$tx_ref");

        if (!$paystack_req->isSuccessful()) {
            dd($paystack_req->getMessage());

            return null;
        }

        $transaction_ref = Cookie::get('transaction_ref');

        Cookie::forget('transaction_ref');

        $response = $paystack_req->getResponse();

        if ($response->status == true) {
            // create paystack transaction
            $paystack_tx = PaystackTransaction::create([
                'paystack_ref' => $response->data->reference,
                'transaction_id' => $transaction_ref
            ]);

            $transaction = Transaction::where('ref_no', $transaction_ref)->first();

            $vtpass_payment_payload = build_payment_payload($transaction);
            
            // make payment to vtpass
            $response = $this->api->post('/api/pay', $vtpass_payment_payload);

            if ($response->getResponse()->code != "000") {
                $data = $response->getResponse();

                Log::error($response->getMessage() . ' ' . $response->getStatusCode() . ' ' . $data->code);

                $transaction->status = 'failed';
                $transaction->request_id = $data->requestId;

                $transaction->save();

                return back()->with('message', 'Something went wrong while initiating payment. Try again');
            }

            $data = $response->getResponse();
            
            $transaction->status = "successful";
            $transaction->request_id = $data->requestId;

            $transaction->save();

            $product = $data->content->transactions->product_name;

            $message = "$data->amount purchase of $product successful. \n" . ($data->purchased_code ?? '');
            
            return redirect("/success")->with('message', $message);
        }
    }

    public function show_success_page () {
        return view('pages.success');
    }
}
