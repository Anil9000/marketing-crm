<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => new UserResource($result['user']),
            'token'   => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 201);
    }

    /**
     * Login and return JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json([
            'message'    => 'Login successful.',
            'user'       => new UserResource($result['user']),
            'token'      => $result['token'],
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    /**
     * Refresh JWT token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = $this->authService->refreshToken();

            return response()->json([
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token cannot be refreshed.'], 401);
        }
    }

    /**
     * Logout and invalidate token.
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Successfully logged out.']);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Failed to logout.'], 500);
        }
    }

    /**
     * Return the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    /**
     * Redirect to Google OAuth.
     */
    public function googleRedirect(): JsonResponse
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

        return response()->json(['redirect_url' => $url]);
    }

    /**
     * Handle Google OAuth callback.
     */
    public function googleCallback(): JsonResponse
    {
        try {
            $socialiteUser = Socialite::driver('google')->stateless()->user();
            $result        = $this->authService->findOrCreateGoogleUser($socialiteUser);

            return response()->json([
                'message'    => 'Google authentication successful.',
                'user'       => new UserResource($result['user']),
                'token'      => $result['token'],
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Google authentication failed.'], 400);
        }
    }
}
