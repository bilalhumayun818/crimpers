<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('pos.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('auth_expires_at', now()->addDays(29)->timestamp);

        $user->last_login_at = now();
        $user->save();

        return redirect()->route('pos.index');
    }



    public function sessionExpired()
    {
        return view('auth.session-expired');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}