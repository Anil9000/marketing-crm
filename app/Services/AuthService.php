<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'viewer',
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): ?array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        return [
            'user'  => auth()->user(),
            'token' => $token,
        ];
    }

    public function refreshToken(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function findOrCreateGoogleUser(SocialiteUser $socialiteUser): array
    {
        $user = User::where('google_id', $socialiteUser->getId())
            ->orWhere('email', $socialiteUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id if not set
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $socialiteUser->getId(),
                    'avatar'    => $socialiteUser->getAvatar(),
                ]);
            }
        } else {
            $user = User::create([
                'name'              => $socialiteUser->getName(),
                'email'             => $socialiteUser->getEmail(),
                'google_id'         => $socialiteUser->getId(),
                'avatar'            => $socialiteUser->getAvatar(),
                'email_verified_at' => now(),
                'role'              => 'viewer',
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}
