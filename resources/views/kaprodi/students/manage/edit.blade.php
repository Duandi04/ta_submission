@extends('layouts.app')

@section('title', 'Edit Mahasiswa')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Edit Mahasiswa</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'kaprodi.students.manage.edit'])
            <div class="ms-3">
                <a href="{{ route('kaprodi.students.manage.index', request()->query()) }}"
                    class="btn btn-outline-secondary shadow-none">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('kaprodi.students.manage.update', $student) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="mahasiswa">

                        {{-- Photo Upload --}}
                        @include('partials.photo-upload', ['user' => $student])

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $student->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nim_nip" class="form-label">NIM <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nim_nip') is-invalid @enderror"
                                    id="nim_nip" name="nim_nip" value="{{ old('nim_nip', $student->nim_nip) }}" required>
                                @error('nim_nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="angkatan" class="form-label">Tahun Angkatan</label>
                                <input type="number" class="form-control @error('angkatan') is-invalid @enderror"
                                    id="angkatan" name="angkatan" value="{{ old('angkatan', $student->angkatan) }}" placeholder="Contoh: 2021">
                                @error('angkatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div hidden class="col-md-4">
                                <label for="program_studi_id" class="form-label">Program Studi <span
                                        class="text-danger">*</span></label>
                                <input type="hidden" name="program_studi_id" value="{{ $student->program_studi_id }}">
                                <input type="text" class="form-control bg-light" id="program_studi_id_display"
                                    value="{{ $student->programStudi->name }}" readonly disabled>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $student->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $student->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch p-0">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="is_active"
                                        name="is_active" value="1"
                                        {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">Akun Aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch p-0">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="can_exceed_submission_limit"
                                        name="can_exceed_submission_limit" value="1"
                                        {{ old('can_exceed_submission_limit', $student->can_exceed_submission_limit) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-primary" for="can_exceed_submission_limit">
                                        <i class="bi bi-star-fill me-1"></i> Pengecualian Batas Pengajuan
                                    </label>
                                </div>
                                <div class="form-text ms-4">Izinkan mahasiswa ini mengunggah lebih dari batas maksimal draft TA yang ditentukan prodi.</div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('kaprodi.students.manage.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Perbarui Mahasiswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
