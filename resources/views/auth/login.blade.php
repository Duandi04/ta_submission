@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
    <div class="login-card">
        <div class="login-header">
            <div class="icon-wrapper">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1>{{ \App\Models\Setting::getValue('campus_name', 'Sistem Pengajuan TA') }}</h1>
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

            <div class="divider">
                <span>Akun Demo</span>
            </div>

            <div class="demo-credentials">
                <h6><i class="bi bi-info-circle"></i> Kredensial Demo</h6>
                <p><strong>Admin:</strong> admin@ta.test</p>
                <p><strong>Mahasiswa:</strong> mahasiswa1@ta.test</p>
                <p><strong>Password:</strong> password</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>     document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword'); const password = document.querySelector('#password'); const icon = document.querySelector('#toggleIcon');
            if (togglePassword && password && icon) { togglePassword.addEventListener('click', function () {                 // Toggle the type attribute                 const type = password.getAttribute('type') === 'password' ? 'text' : 'password';                 password.setAttribute('type', type);
        // Toggle the icon                 icon.classList.toggle('bi-eye');                 icon.classList.toggle('bi-eye-slash');             });         }     });
    </script>
@endpush