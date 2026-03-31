@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
    <div class="login-card">
        <div class="login-header">
            <div class="mb-3">
                <img src="{{ url(\App\Models\Setting::getValue('app_logo', 'images/logo_uvers.webp')) }}" alt="Logo"
                    style="height: 8vh; object-fit: contain;">
            </div>
            <h1>Sistem Pengajuan Draft Proposal TA</h1>
            <p>Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <div class="login-body">
            @if (session('error'))
                <div class="alert alert-danger login-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger login-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf

                <div class="input-premium-group">
                    <i class="bi bi-envelope icon-prefix"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="Alamat Email" required autofocus
                        autocomplete="email">
                </div>

                <div class="input-premium-group password-input-wrapper">
                    <i class="bi bi-lock icon-prefix"></i>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="Password" required autocomplete="current-password">
                    <button type="button"
                        class="password-toggle-btn"
                        id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <div class="login-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login" id="loginBtn">
                    <span class="btn-login-text">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk
                    </span>
                    <span class="btn-login-loading d-none">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memproses...
                    </span>
                </button>
            </form>
        </div>

        <div class="login-footer">
            <p>&copy; {{ date('Y') }} Universitas &mdash; Sistem Pengajuan TA. All rights reserved.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush
