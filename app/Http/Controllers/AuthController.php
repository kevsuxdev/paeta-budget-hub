<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'admin' => redirect()->intended('admin/dashboard'),
            'finance' => redirect()->intended('finance/dashboard'),
            'dept_head' => redirect()->intended('dept_head/dashboard'),
            'staff' => redirect()->intended('staff/dashboard'),
            default => redirect()->intended('/'),
        };
    }
}
