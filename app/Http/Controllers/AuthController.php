<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if (Auth::guard('web')->attempt($credentials)) {
                $request->session()->regenerate();
                
                \App\Services\EmployeeActivityLogger::log('login', 'Employee logged in to the platform');

                return redirect()->intended('/dashboard');
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'username' => __('messages.account_inactive'),
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
            \App\Services\EmployeeActivityLogger::log('login', 'Logged in to the system');
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => __('messages.invalid_credentials'),
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
