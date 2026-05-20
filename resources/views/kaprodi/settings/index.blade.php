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
                            <label for="max_batches" class="form-label">Maksimal Batch Pengajuan (SIklus)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_batches" name="max_batches"
                                    value="{{ $settings['max_batches'] ?? 2 }}" min="1">
                                <span class="input-group-text">Batch</span>
                            </div>
                            <div class="form-text text-muted small">Berapa kali siklus pengajuan yang diizinkan (Total Max = Batch * Pengajuan per Batch).</div>
                        </div>

                        <div class="mb-3">
                            <label for="attempts_per_batch" class="form-label">Maksimal Pengajuan per Batch (Slot Aktif)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="attempts_per_batch" name="attempts_per_batch"
                                    value="{{ $settings['attempts_per_batch'] ?? 3 }}" min="1">
                                <span class="input-group-text">Pengajuan</span>
                            </div>
                            <div class="form-text text-muted small">Berapa banyak judul yang dapat diajukan secara aktif dalam satu batch.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="submission_start" class="form-label">Waktu Mulai Pengajuan</label>
                                <input type="datetime-local" class="form-control" id="submission_start" name="submission_start"
                                    value="{{ isset($settings['submission_start']) && $settings['submission_start'] ? \Carbon\Carbon::parse($settings['submission_start'])->format('Y-m-d\TH:i') : '' }}">
                                <div class="form-text text-muted small">Tanggal & waktu pembukaan masa pengajuan.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="submission_end" class="form-label">Waktu Deadline Pengajuan</label>
                                <input type="datetime-local" class="form-control" id="submission_end" name="submission_end"
                                    value="{{ isset($settings['submission_end']) && $settings['submission_end'] ? \Carbon\Carbon::parse($settings['submission_end'])->format('Y-m-d\TH:i') : '' }}">
                                <div class="form-text text-muted small">Tanggal & waktu batas akhir pengajuan (deadline).</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Konfigurasi</button>
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