<?php

use App\Models\Transaction;

function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function generate_request_id() {
    return date_create('now', new DateTimeZone('Africa/Lagos'))->format('YmdHi') . generate_random_string(10);
}

function build_payment_payload ($purchase_data) {
    $request_id = generate_request_id();

    // get necessary payload from $purchase_data object - service_id, amount, phone
    $payload = [
        'request_id' => $request_id,
        'serviceID' => $purchase_data->service_id,
        'amount' => $purchase_data->amount,
        'email' => $purchase_data->email,
        'phone' => $purchase_data->phone,
    ];

    if (str_contains($purchase_data->service_id, '-data')) {
        $payload = array_merge($payload, [
            'billersCode' => $purchase_data->phone,
            'variation_code' => $purchase_data->variation_code
        ]);
    } elseif (str_contains($purchase_data->service_id, 'tv')) {
        $payload = array_merge($payload, [
            'billersCode' => $purchase_data->smartcard_no,
            'variation_code' => $purchase_data->variation_code,
            'subscription_type' => $purchase_data->subscription_type,
            'quantity' => $purchase_data->quantity
        ]);
    } elseif (str_contains($purchase_data->service_id, 'electric')) {
        $payload = array_merge($payload, [
            'billersCode' => $purchase_data->meter_no,
            'variation_code' => $purchase_data->variation_code,
        ]);
    }

    return $payload;
}


/**
 * Search an associative array by a field and return the match
 * @param array $arr
 * @param string $field
 *
 * @return
 */
function search_assoc_arr($arr, $field, $val)
{
    foreach ($arr as $data) {
        if ($data->$field == $val)
            return $data;
    }
}