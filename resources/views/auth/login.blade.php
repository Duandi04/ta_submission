@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
    <div class="login-card split-layout">
        <div class="login-image-side">
            <div class="login-image-bg" style="background-image: url('{{ asset('images/login_side_bg.webp') }}');"></div>
            <div class="image-overlay"></div>
            <div class="brand-content">
                <h2 class="brand-title">Sistem Pengajuan TA Teknik Perangkat Lunak</h2>
                <p class="brand-subtitle">Platform Akademik Penunjang Penyelesaian Tugas Akhir Mahasiswa Teknik Perangkat
                    Lunak.</p>
            </div>
        </div>

        <div class="login-form-side">
            <div class="login-form-content">
                <div class="mb-4 text-center">
                    <img src="{{ asset('images/uvers_logo_blue.webp') }}" alt="Logo" class="brand-logo-img">
                </div>

                <div class="form-title-group">
                    <h1 class="form-title">Selamat Datang</h1>
                    <p class="form-subtitle">Silakan masuk ke akun Anda untuk melanjutkan akses sistem.</p>
                </div>

                <div class="login-body p-0 mt-4">
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

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <div class="input-premium-group mb-4">
                            <i class="bi bi-envelope icon-prefix"></i>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="Alamat Email" required autofocus
                                autocomplete="email" tabindex="1">
                        </div>

                        <div class="input-premium-group password-input-wrapper mb-4">
                            <i class="bi bi-lock icon-prefix"></i>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Password" required
                                autocomplete="current-password" tabindex="2">
                            <button type="button" class="password-toggle-btn" id="togglePassword" tabindex="-1"
                                aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>

                        <!-- CAPTCHA Field -->
                        @if(!empty(config('services.recaptcha.site_key')) && !empty(config('services.recaptcha.secret_key')))
                            <div class="mb-4 d-flex justify-content-center">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            </div>
                            @error('captcha')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        @else
                            <div class="mb-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="position-relative border rounded overflow-hidden select-none bg-light d-flex align-items-center justify-content-center" style="height: 48px; min-width: 130px;">
                                        <img src="{{ route('captcha.image') }}" alt="CAPTCHA" id="captcha-img" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" id="reload-captcha" style="height: 48px; width: 48px;" title="Reload CAPTCHA" tabindex="-1">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                    <div class="input-premium-group mb-0 flex-grow-1">
                                        <i class="bi bi-shield-check icon-prefix"></i>
                                        <input type="text" class="form-control @error('captcha') is-invalid @enderror" id="captcha"
                                            name="captcha" placeholder="Teks Keamanan" required autocomplete="off" tabindex="3" style="padding-top: 0.75rem; padding-bottom: 0.75rem;">
                                    </div>
                                </div>
                                @error('captcha')
                                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <button type="submit" class="btn btn-login py-3" id="loginBtn" tabindex="4">
                            <span class="btn-login-text">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Masuk ke Sistem
                            </span>
                            <span class="btn-login-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Memproses...
                            </span>
                        </button>
                    </form>
                </div>

                <div class="login-footer mt-5 text-center">
                    <p>&copy; {{ date('Y') }} Universitas Universal <br/> Sistem Pengajuan TA Teknik Perangkat Lunak.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(!empty(config('services.recaptcha.site_key')) && !empty(config('services.recaptcha.secret_key')))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush
