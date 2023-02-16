<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $product_categories = ['Airtime Recharge', 'Data Services', 'TV Subscription', 'Electricity Bill'];
        $products = [
            (object)[
                'category' => 'Airtime Recharge',
                'name' => 'Airtel Airtime',
                'service_id' => 'airtel'
            ],
            (object)[
                'category' => 'Airtime Recharge',
                'name' => 'MTN Airtime',
                'service_id' => 'mtn'
            ],
            (object)[
                'category' => 'Airtime Recharge',
                'name' => 'GLO Airtime',
                'service_id' => 'glo'
            ],
            (object)[
                'category' => 'Airtime Recharge',
                'name' => '9Mobile Airtime',
                'service_id' => 'etisalat'
            ],
            (object)[
                'category' => 'Data Services',
                'name' => 'Airtel Data',
                'service_id' => 'airtel-data'
            ],
            (object)[
                'category' => 'Data Services',
                'name' => 'MTN Data',
                'service_id' => 'mtn-data'
            ],
            (object)[
                'category' => 'Data Services',
                'name' => 'GLO Data',
                'service_id' => 'glo-data'
            ],
            (object)[
                'category' => 'Data Services',
                'name' => '9Mobile Data',
                'service_id' => 'etisalat-data'
            ],
            (object)[
                'category' => 'Data Services',
                'name' => 'Smile Payment',
                'service_id' => 'smile-direct'
            ],
            (object)[
                'category' => 'TV Subscription',
                'name' => 'DSTV Subscription',
                'service_id' => 'dstv'
            ],
            (object)[
                'category' => 'TV Subscription',
                'name' => 'GOTV Payment',
                'service_id' => 'gotv'
            ],
            (object)[
                'category' => 'TV Subscription',
                'name' => 'Startimes Subscription',
                'service_id' => 'startimes'
            ],
            (object)[
                'category' => 'TV Subscription',
                'name' => 'ShowMax',
                'service_id' => 'showmax'
            ],
            (object)[
                'category' => 'Electricity Bill',
                'name' => 'Ikeja Electric Payment - IKEDC',
                'service_id' => 'ikeja-electric',
            ],
            (object)[
                'category' => 'Electricity Bill',
                'name' => 'Eko Electric Payment - EKEDC',
                'service_id' => 'eko-electric'
            ],
            (object)[
                'category' => 'Electricity Bill',
                'name' => 'Abuja Electric Distribution Company - AEDC',
                'service_id' => 'abuja-electric'
            ],
            (object)[
                'category' => 'Electricity Bill',
                'name' => 'KEDCO - Kano Electric',
                'service_id' => 'kano-electric'
            ],
        ];


        foreach ($product_categories as $category_name) {
            $category = \App\Models\ProductCategory::factory()->create([
                'name' => $category_name
            ]);

            foreach($products as $product) {
                if ($product->category == $category->name) {
                    $product = \App\Models\Product::factory()->create([
                        'name' => $product->name,
                        'category_id' => $category->id,
                        'service_id' => $product->service_id
                    ]);
                }
            }
        }
    }
}
