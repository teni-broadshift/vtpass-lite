<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['inflow', 'outflow'])->default('outflow');
            $table->foreignId('wallet_id')->index()->constrained('wallets');
            $table->foreignId('user_id')->index()->constrained('users');
            $table->enum('status', ['initiated', 'pending', 'successful', 'failed'])->index();
            $table->string('request_id')->nullable()->index();
            $table->string('ref_no')->default(generate_random_string(12))->index();
            $table->foreignId('product_id')->index()->constrained('products');
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('service_id')->index();
            $table->string('meter_no')->nullable()->index();
            $table->string('smartcard_no')->nullable()->index();
            $table->string('subscription_type')->nullable()->index();
            $table->string('variation_code')->nullable();
            $table->string('utility_token')->nullable();
            $table->float('amount')->index();
            $table->integer('quantity')->nullable()->index();
            $table->string('currency_code')->default('NGN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
