<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthService;
use App\Http\Services\UserService;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    protected $userService;
    protected $authService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authService = new AuthService();
    }

    public function login()
    {
        return view('pages.login');
    }

    public function logout() {
        return $this->authService->logout();
    }

    public function register()
    {
        return view('pages.register');
    }

    public function create_user() {

        // validate input fields
        $fields = request()->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'password' => ['required']
        ]);
            
        // verify that user with email or phone doesn't already exist
        $existing_user = $this->userService->findUserByEmail($fields['email']);

        if ($existing_user) {
            return redirect('/register')->with('message', 'User with this email already exist');
        }

        // generate password hash
        $fields['password'] = bcrypt($fields['password']);

        // create user
        $user = $this->userService->createUser($fields);

        // authenticate user
        auth()->login($user);

        return redirect('/airtime');
    }

    public function authenticate()
    {
        // validate input fields
        $fields = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(auth()->attempt($fields)) {
            request()->session()->regenerate();
            return redirect('/airtime')->with('message', 'You are now logged in');
        }

        return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }
}
