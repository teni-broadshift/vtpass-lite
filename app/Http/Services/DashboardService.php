<?php

namespace App\Http\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;

class DashboardService {

    protected $api;

    public function __construct()
    {
        $this->api = new API();
    }

    public function get_product_from_vtpass(string $service_id)
    {
        $product_query = Product::where('service_id', $service_id);

        $product_category = $product_query->first()->category()->first()->toArray();
        
        if (in_array($product_category['name'], ["TV Subscription", "Electricity Bill"])) {
            $service_category = implode('-', explode(" ", strtolower($product_category['name'])));
        } else {
            $service_category = explode(" ", strtolower($product_category['name']))[0];
        }

        $response = $this->api->get("/api/services", ["identifier" => $service_category])->getResponse()->content;

        $product = search_assoc_arr($response, 'serviceID', $service_id);
        
        $product->category = $product_category['name'];

        return $product;
    }

}