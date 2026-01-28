@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Profil Saya</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person"></i> Informasi Profil
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            @php
                                $isRestricted =
                                    auth()->user()->hasRole('mahasiswa') || auth()->user()->hasRole('dosen');
                            @endphp
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', auth()->user()->name) }}"
                                {{ $isRestricted ? 'readonly' : 'required' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($isRestricted)
                                <small class="text-muted">Nama lengkap tidak dapat diubah secara mandiri. Silakan hubungi
                                    admin jika terdapat kesalahan.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nim_nip" class="form-label">NIM/NIP</label>
                            <input type="text" class="form-control" id="nim_nip" value="{{ auth()->user()->nim_nip }}"
                                readonly>
                            <small class="text-muted">NIM/NIP tidak dapat diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-key"></i> Ubah Password
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Informasi Akun
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Role:</strong><br>
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->getRoleNames()->first())) }}
                        </span>
                    </p>
                    <p class="mb-2">
                        <strong>Status:</strong><br>
                        @if (auth()->user()->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Terdaftar sejak:</strong><br>
                        {{ auth()->user()->created_at->format('d/m/Y') }}
                    </p>
                    @if (auth()->user()->programStudi)
                        <hr class="opacity-50">
                        <p class="mb-2">
                            <strong>Program Studi:</strong><br>
                            <span class="text-dark fw-medium small">{{ auth()->user()->programStudi->name }}</span>
                        </p>
                        @if (auth()->user()->programStudi->faculty)
                            <p class="mb-0">
                                <strong>Fakultas:</strong><br>
                                <span
                                    class="text-dark fw-medium small">{{ auth()->user()->programStudi->faculty->name }}</span>
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
