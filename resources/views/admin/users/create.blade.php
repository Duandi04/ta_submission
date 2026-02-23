@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Pengguna Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.users.index', request()->query()) }}" class="btn btn-outline-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <label for="nim_nip" class="form-label">NIM/NIP</label>
                            <input type="text" class="form-control @error('nim_nip') is-invalid @enderror" id="nim_nip"
                                name="nim_nip" value="{{ old('nim_nip') }}">
                            @error('nim_nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="program_studi_id" class="form-label">Program Studi <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('program_studi_id') is-invalid @enderror"
                                id="program_studi_id" name="program_studi_id">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}"
                                        {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                        [{{ $prodi->code }}] {{ $prodi->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1">Wajib diisi untuk mahasiswa, dosen, dan kaprodi.</div>
                            @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Akun Aktif</label>
                            </div>
                            <div class="form-text">Jika tidak aktif, user tidak akan bisa login.</div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
