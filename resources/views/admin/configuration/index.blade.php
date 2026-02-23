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
                <div class="card-body p-4 text-center">
                    <i class="bi bi-info-circle fs-1 text-muted mb-3 d-block"></i>
                    <p>Pengaturan identitas aplikasi saat ini dikunci oleh sistem.</p>
                    <p class="small text-muted mb-0">Hubungi pengembang untuk perubahan logo atau nama institusi.</p>
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