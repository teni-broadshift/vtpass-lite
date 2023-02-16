<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.login');
});

// GUEST ROUTES
// Route::middleware('guest')->group(function () {

    // SHOW REGISTER FORM
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    
    // Show login form
    Route::get('/login', [AuthController::class, 'login'])->name('login');

// });

Route::post('/user/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

Route::post('/users', [AuthController::class, 'create_user'])->name('create.user');

Route::middleware('auth')->group(function() {
    $PRODUCT_PAGE_REGEX = "(^data$|^tv$|^airtime$|^electricity$)";
    $SERVICE_REGEX = "(^mtn$|^airtel$|^etisalat$|^glo$|^mtn-data$|^airtel-data$|^etisalat-data$|^glo-data$|^ikeja-electric$|^eko-electric$))))";

    Route::get('/success', [DashboardController::class, 'show_success_page']);

    Route::get('/{product}', [DashboardController::class, 'show_product_page'])->where(['product', $PRODUCT_PAGE_REGEX]);
    Route::get('/buy/{service_id}', [DashboardController::class, 'show_purchase_page'])->where(['service_id', $SERVICE_REGEX]);

    Route::post('/confirm-transaction/{service_id}', [DashboardController::class, 'confirm_purchase'])->where(['service_id', $SERVICE_REGEX]);

    Route::get('/confirm/{transaction_id}', [DashboardController::class, 'show_confirmation_page']);

    Route::post('/pay', [DashboardController::class, 'buy']);

    Route::post('/pay-with-paystack', [DashboardController::class, 'initiate_paystack_payment']);

    Route::get('payment/callback', [DashboardController::class, 'verify_paystack_payment']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

