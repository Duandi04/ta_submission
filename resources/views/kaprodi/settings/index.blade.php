@extends('layouts.app')

@section('title', 'Pengaturan Sistem TA')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Pengaturan Sistem</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-gear-fill me-2 text-primary"></i>Konfigurasi Mahasiswa</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('kaprodi.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="max_thesis_drafts" class="form-label">Maksimal Draft TA Terkirim</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_thesis_drafts" name="max_thesis_drafts"
                                    value="{{ $settings['max_thesis_drafts'] ?? 3 }}" min="1">
                                <span class="input-group-text">Draft</span>
                            </div>
                            <div class="form-text">Jumlah maksimal proposal/draft yang dapat diunggah oleh setiap mahasiswa.
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-info-circle-fill me-2 text-info"></i>Informasi Sistem</span>
                </div>
                <div class="card-body">
                    <p>Halaman ini digunakan oleh Kaprodi untuk melakukan konfigurasi parameter sistem pengajuan Tugas
                        Akhir.</p>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Status Sistem
                            <span class="badge bg-success">Aktif</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Versi Aplikasi
                            <span class="text-muted">v2.0.0-blue</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection