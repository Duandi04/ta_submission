@extends('layouts.app')

@section('title', 'Tambah Dosen')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Dosen Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.lecturers.manage.index', request()->query()) }}"
                class="btn btn-outline-secondary shadow-none text-nowrap">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('kaprodi.lecturers.manage.store') }}" method="POST" class="needs-validation"
                        novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Contoh: Dr. Ir. Gunawan, M.T."
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nim_nip" class="form-label">NIP (Nomor Induk Pegawai) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nim_nip') is-invalid @enderror"
                                    id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}"
                                    placeholder="Contoh: 198001012010121001" required>
                                @error('nim_nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="email@dosen.ac.id" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div hidden class="col-md-6">
                                <label for="program_studi_id" class="form-label">Program Studi <span
                                        class="text-danger">*</span></label>
                                <input type="hidden" name="program_studi_id" value="{{ $programStudis->first()->id }}">
                                <input type="text" class="form-control bg-light" id="program_studi_id_display"
                                    value="{{ $programStudis->first()->name }}" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            
                            <div class="col-md-6">
                                <!-- Hidden role selection, default to 'dosen' for Kaprodi management -->
                                <input type="hidden" name="role" value="dosen">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
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

                        <div class="mb-4">
                            <div class="form-check form-switch p-0">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="is_active"
                                        name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">Aktifkan akun
                                        segera</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('kaprodi.lecturers.manage.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Dosen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
