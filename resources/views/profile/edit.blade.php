@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Edit Profil</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Profile Info --}}
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">
                        <i class="bi bi-person me-2"></i>Informasi Profil
                    </h5>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Photo Upload --}}
                        @include('partials.photo-upload', ['user' => auth()->user(), 'size' => 130])

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            @if (auth()->user()->hasRole('mahasiswa') || auth()->user()->hasRole('dosen'))
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                <div class="form-text">Nama tidak dapat diubah. Hubungi admin untuk perubahan.</div>
                            @else
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nim_nip_display" class="form-label">NIM / NIP</label>
                                <input type="text" class="form-control" id="nim_nip_display"
                                    value="{{ auth()->user()->nim_nip ?? '-' }}" disabled>
                                <div class="form-text">Hubungi admin untuk perubahan NIM/NIP.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon/WA</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                                rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">
                        <i class="bi bi-shield-lock me-2"></i>Ubah Password
                    </h5>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span
                                    class="text-danger">*</span></label>
                            <input type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password Baru <span
                                        class="text-danger">*</span></label>
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-key me-1"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
