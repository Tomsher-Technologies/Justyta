<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $redirectTo = match ($user->user_type) {
                'admin' => route('admin.dashboard'),
                'staff' => route('admin.dashboard'),
                default => '/user/dashboard',
            };
                     
            return redirect()->intended($redirectTo);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

     // Logout the user
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

}
