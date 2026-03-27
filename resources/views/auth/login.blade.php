@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<h1 class="auth-title">Welcome back</h1>
<p class="auth-subtitle">Sign in to your Marketing CRM account</p>

<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="you@company.com" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="••••••••" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted small" for="remember">Remember me</label>
        </div>
        <a href="#" class="small text-decoration-none" style="color: #6366f1;">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
    </button>
</form>

<div class="divider">or continue with</div>

<a href="{{ route('auth.google') }}" class="btn btn-google d-flex align-items-center justify-content-center gap-2">
    <svg width="18" height="18" viewBox="0 0 18 18">
        <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 01-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
        <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z"/>
        <path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/>
        <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/>
    </svg>
    Continue with Google
</a>

<p class="text-center mt-4 mb-0 text-muted small">
    Don't have an account?
    <a href="{{ route('register') }}" class="text-decoration-none" style="color: #6366f1;">Create one</a>
</p>
@endsection
