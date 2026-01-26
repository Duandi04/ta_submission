<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Attempt to authenticate the user.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attempt(array $credentials, bool $remember = false): bool
    {
        $credentials['is_active'] = true;
        return Auth::attempt($credentials, $remember);
    }

    /**
     * Log in an authenticated user and regenerate the session.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function loginUser($request): void
    {
        $request->session()->regenerate();

        activity()
            ->causedBy(Auth::user())
            ->log('User logged in');
    }

    /**
     * Log out the current user.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function logout($request): void
    {
        activity()
            ->causedBy(Auth::user())
            ->log('User logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return User|null
     */
    public function user(): ?User
    {
        return Auth::user();
    }
}
