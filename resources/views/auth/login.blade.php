@extends('layouts.guest')

@section('title', 'Login - Sistem Pengajuan TA')

@section('content')
<div class="card">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <i class="bi bi-book text-primary" style="font-size: 3rem;"></i>
            <h1 class="h3 mt-2">Sistem Pengajuan TA</h1>
            <p class="text-muted">Masuk ke akun Anda</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </div>
        </form>

        <hr class="my-4">

        <div class="text-center text-muted small">
            <p>Akun Demo:</p>
            <p class="mb-1"><strong>Admin:</strong> admin@ta.test</p>
            <p class="mb-1"><strong>Mahasiswa:</strong> mahasiswa1@ta.test</p>
            <p class="mb-1"><strong>Password:</strong> password</p>
        </div>
    </div>
</div>
@endsection