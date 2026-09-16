@extends('layouts.guest')
@section('content')

    <!-- Session Status -->
    <x-auth-session-status class="mb-3 alert alert-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold text-secondary">Email Administrator</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3 text-start">
            <label for="password" class="form-label fw-semibold text-secondary">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label class="form-check-label text-muted small" for="remember_me">Ingat Saya</label>
            </div>

            @if (Route::has('password.request'))
                <a class="small text-decoration-none text-primary fw-semibold" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i> MASUK SISTEM
            </button>
        </div>
    </form>

@endsection