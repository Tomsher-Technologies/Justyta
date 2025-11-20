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
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            if ($user->user_type === 'admin' || $user->user_type === 'staff') {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return redirect()->back()->withErrors(['password' => 'Unauthorized User.']);
            }
        }

        return back()->withErrors(['password' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

}
