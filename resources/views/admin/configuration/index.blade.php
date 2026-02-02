@extends('layouts.app')

@section('title', 'Konfigurasi Sistem')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Konfigurasi Sistem</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Umum</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.configuration.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="campus_name" class="form-label">Nama Kampus / Institusi</label>
                            <input type="text" class="form-control @error('campus_name') is-invalid @enderror"
                                id="campus_name" name="campus_name"
                                value="{{ old('campus_name', $settings['campus_name'] ?? 'Sistem TA') }}" required>
                            <div class="form-text">Nama ini akan muncul di Navbar, Halaman Login, dan Judul Halaman.</div>
                            @error('campus_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="app_logo" class="form-label">Upload Logo Aplikasi</label>
                            @if(isset($settings['app_logo']))
                                <div class="mb-2">
                                    <img src="{{ url($settings['app_logo']) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('app_logo') is-invalid @enderror"
                                id="app_logo" name="app_logo" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 2MB. Logo saat ini: {{ $settings['app_logo'] ?? 'Default' }}</div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary shadow-none">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-info-circle-fill me-2 text-info"></i>Informasi</span>
                </div>
                <div class="card-body">
                    <p>Gunakan halaman ini untuk menyesuaikan identitas aplikasi dengan instansi Anda.</p>
                    <div class="alert alert-info border-0 shadow-none small">
                        <i class="bi bi-lightbulb me-2"></i> Perubahan nama kampus akan langsung berdampak pada seluruh
                        halaman aplikasi.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection