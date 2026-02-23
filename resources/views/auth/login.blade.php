@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
    <style>
        .password-toggle-btn {
            z-index: 5;
            color: #64748b;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.2s;
        }

        .password-toggle-btn:hover {
            color: var(--primary-500);
        }

        .password-toggle-btn:focus {
            box-shadow: none;
        }
    </style>
    <div class="login-card">
        <div class="login-header">
            <img src="{{ url(\App\Models\Setting::getValue('app_logo', 'images/logo_uvers.webp')) }}" alt="Logo"
                class="mb-4 dynamic-logo" style="height: 120px; object-fit: contain;">
            <!-- <h1>{{ \App\Models\Setting::getValue('campus_name', 'Sistem Pengajuan TA') }}</h1> -->
            <h1>Sistem Pengajuan Draft Proposal TA</h1>
            <p>Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <div class="login-body">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="form-floating">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        value="{{ old('email') }}" placeholder="email@example.com" required autofocus>
                    <label for="email"><i class="bi bi-envelope me-2"></i>Alamat Email</label>
                </div>

                <div class="form-floating position-relative password-input-wrapper">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    <button type="button"
                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3 password-toggle-btn"
                        id="togglePassword">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>


        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush