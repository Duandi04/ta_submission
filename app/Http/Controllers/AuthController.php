<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->authService->attempt($credentials, $request->boolean('remember'))) {
            $this->authService->loginUser($request);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun Anda belum aktif.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
