<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'The password confirmation does not match.'
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = bcrypt($request->new_password);
        $user->already_reset_password = true;
        $user->save();

        return back()->with('success', 'Password reset successfully!');
    }
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

        if (Auth::user()->status === 'inactive') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
