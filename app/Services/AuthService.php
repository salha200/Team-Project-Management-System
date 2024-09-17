<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Handle login logic
     * 
     * @param array $credentials
     * @return array|null
     */
    public function login(array $credentials): ?array
    {
        $token = Auth::attempt($credentials);
        if (!$token) {
            return null;
        }

        $user = Auth::user();
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Handle registration logic
     * 
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = Auth::login($user);
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout the user
     * 
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Refresh user token
     * 
     * @return array
     */
    public function refresh(): array
    {
        return [
            'user' => Auth::user(),
            'token' => Auth::refresh(),
        ];
    }
}
