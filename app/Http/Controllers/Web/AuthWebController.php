<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthWebController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);

        $result = $this->authService->register($request->only('name', 'email', 'password'));

        Auth::login($result['user']);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function googleRedirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request): RedirectResponse
    {
        try {
            $socialiteUser = Socialite::driver('google')->user();
            $result        = $this->authService->findOrCreateGoogleUser($socialiteUser);

            Auth::login($result['user']);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }
    }
}
