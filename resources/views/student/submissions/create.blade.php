@extends('layouts.app')

@section('title', 'Buat Pengajuan Baru')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Buat Pengajuan Tugas Akhir Baru</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('student.submissions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-text"></i> Form Pengajuan
                </div>
                <div class="card-body">
                    <form action="{{ route('student.submissions.store') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf

                        @error('limit')
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ $message }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @enderror

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Tugas Akhir <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title') }}" required maxlength="255">
                            @include('partials.similarity')
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan judul penelitian tugas akhir Anda dengan jelas.</small>
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstrak <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract"
                                name="abstract" rows="6" required maxlength="2000">{{ old('abstract') }}</textarea>
                            @error('abstract')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan ringkasan penelitian Anda (maksimal 2000 karakter).</small>
                        </div>

                        <div class="mb-3">
                            <label for="research_field" class="form-label">Bidang Penelitian</label>
                            <input type="text" class="form-control @error('research_field') is-invalid @enderror"
                                id="research_field" name="research_field" value="{{ old('research_field') }}"
                                maxlength="100">
                            @error('research_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Contoh: Sistem Informasi, Keamanan Jaringan, Data Mining, dll.</small>
                        </div>


                        <div class="mb-3">
                            <label for="proposal_file" class="form-label">File Proposal <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('proposal_file') is-invalid @enderror"
                                id="proposal_file" name="proposal_file" accept=".pdf" required>
                            @error('proposal_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: PDF. Maksimal 10MB.</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle"></i> Informasi
                </div>
                <div class="card-body">
                    <h6>Informasi Penting</h6>
                    <ul class="small list-unstyled">
                        @php
                            $prodi = auth()->user()->programStudi;
                            $now = now();
                            $attemptsPerBatch = \App\Models\Setting::getValue('attempts_per_batch', 3);
                            $maxBatches = \App\Models\Setting::getValue('max_batches', 2);
                            $maxTotal = $attemptsPerBatch * $maxBatches;
                            
                            $allSub = auth()->user()->thesisSubmissions()->orderBy('id', 'asc')->get();
                            $totalUsed = $allSub->count();
                            
                            // Current batch progress
                            $currentBatchCount = $totalUsed % $attemptsPerBatch;
                            if ($totalUsed > 0 && $currentBatchCount === 0) {
                                // Check if last batch is fully rejected
                                $lastBatch = $allSub->take(-$attemptsPerBatch);
                                $isBatchFinished = $lastBatch->every(fn($s) => in_array($s->status, ['rejected', 'cancelled']));
                                $slotsInBatch = $isBatchFinished ? $attemptsPerBatch : 0;
                            } else {
                                $slotsInBatch = $attemptsPerBatch - $currentBatchCount;
                            }
                        @endphp
                        
                        <li class="mb-3">
                            <strong>Slot Pengajuan Batch:</strong><br>
                            <span class="badge {{ $slotsInBatch <= 0 ? 'bg-danger' : 'bg-primary' }}">
                                {{ $slotsInBatch }} dari {{ $attemptsPerBatch }} tersedia di batch ini
                            </span>
                            <div class="x-small text-muted mt-1">
                                @if($totalUsed > 0 && $totalUsed % $attemptsPerBatch === 0 && !($isBatchFinished ?? true))
                                    Batch saat ini penuh. Tunggu semua judul ditolak untuk membuka batch baru.
                                @else
                                    Jumlah pengajuan judul yang dapat Anda buat dalam siklus saat ini.
                                @endif
                            </div>
                        </li>

                        <li class="mb-3">
                            <strong>Total Sisa Kesempatan:</strong><br>
                            <span class="badge {{ $totalUsed >= $maxTotal ? 'bg-danger' : 'bg-success' }}">
                                {{ max(0, $maxTotal - $totalUsed) }} kali lagi
                            </span>
                            <div class="x-small text-muted mt-1">Batas total seluruh judul ({{ $maxTotal }} kali).</div>
                        </li>

                        @if($prodi && ($prodi->submission_start || $prodi->submission_end))
                            <li class="mb-2">
                                <strong>Batas Waktu:</strong><br>
                                @if($prodi->submission_start)
                                    <div class="text-{{ $now->lt($prodi->submission_start) ? 'warning' : 'success' }} small">
                                        Mulai: {{ $prodi->submission_start->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                                @if($prodi->submission_end)
                                    <div class="text-{{ $now->gt($prodi->submission_end) ? 'danger' : 'info' }} small">
                                        Berakhir: {{ $prodi->submission_end->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </li>
                        @endif
                    </ul>

                    <hr>

                    <h6>Panduan Pengajuan</h6>
                    <ol class="small">
                        <li>Isi semua kolom yang wajib diisi (*)</li>
                        <li>Pastikan judul sesuai dengan topik penelitian</li>
                        <li>Abstrak harus menjelaskan tujuan, metode, dan kontribusi penelitian</li>
                        <li>Dosen pembimbing akan ditetapkan oleh Kaprodi</li>
                        <li>Upload file proposal dalam format PDF</li>
                    </ol>

                    <hr>

                    <h6>Status Pengajuan</h6>
                    <ul class="small">
                        <li><span class="badge bg-secondary">Draft</span> - Dapat diedit</li>
                        <li><span class="badge bg-info">Diajukan</span> - Menunggu review</li>
                        <li><span class="badge bg-warning text-dark">Ditinjau</span> - Sedang direview</li>
                        <li><span class="badge bg-success">Disetujui</span> - Lanjut ke tahap sidang</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Form validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endpush