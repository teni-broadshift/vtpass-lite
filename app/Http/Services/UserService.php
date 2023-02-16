<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Wallet;

class UserService {

    public function __construct()
    {
        
    }
    
    public function findUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function findUserByPhone(string $phone)
    {
        return User::where('phone', $phone)->first();
        
    }

    public function createUser($payload) 
    {
        $user = User::create($payload);

        // create user wallet
        $user_wallet = Wallet::create([
            'balance' => env('VTPAY_DEFAULT_BALANCE'),
            'currency' => 'NGN',
            'user_id' => $user->id
        ]);

        return $user;
    }
}