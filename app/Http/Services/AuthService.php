<?php

namespace App\Http\Services;

class AuthService {

    public function loginUser($payload) 
    {
        
    }

    public function generateAuthToken($payload)
    {
        
    }

    public function logout() {
        auth()->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}