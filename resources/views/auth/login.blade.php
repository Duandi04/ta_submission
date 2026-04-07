@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
    <div class="login-card split-layout">
        <div class="login-image-side" style="background-image: url('{{ asset('images/login_side_bg.webp') }}');">
            <div class="image-overlay"></div>
            <div class="brand-content">
                <div class="mb-4">
                    <img src="{{ url(\App\Models\Setting::getValue('app_logo', 'images/logo_uvers.webp')) }}" alt="Logo"
                        class="brand-logo glass-logo">
                </div>
                <h2 class="brand-title">Sistem Pengajuan TA Teknik Perangkat Lunak</h2>
                <p class="brand-subtitle">Platform Akademik Penunjang Penyelesaian Tugas Akhir Mahasiswa Teknik Perangkat Lunak.</p>
                {{-- <div class="brand-features mt-4">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Pengajuan Proposal Digital</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Monitoring Progress Real-time</span>
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="login-form-side">
            <div class="login-form-content">
                <div class="login-header-mobile d-lg-none mb-4">
                    <img src="{{ url(\App\Models\Setting::getValue('app_logo', 'images/logo_uvers.webp')) }}" alt="Logo"
                        style="height: 60px; object-fit: contain;">
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
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                name="password" placeholder="Password" required autocomplete="current-password" tabindex="2">
                            <button type="button" class="password-toggle-btn" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>

                        <button type="submit" class="btn btn-login py-3" id="loginBtn" tabindex="3">
                            <span class="btn-login-text">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Masuk ke Sistem
                            </span>
                            <span class="btn-login-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Memproses...
                            </span>
                        </button>
                    </form>
                </div>

                <div class="login-footer mt-5 text-center">
                    <p>&copy; {{ date('Y') }} Universitas Universal &mdash; Sistem Pengajuan TA Teknik Perangkat Lunak.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush
