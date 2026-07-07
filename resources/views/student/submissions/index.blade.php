@extends('layouts.app')

@section('title', 'Pengajuan Saya')

@section('content')
    @php
        $user = auth()->user();
        $prodi = $user->programStudi;
        $now = now();
        $maxSub = \App\Models\Setting::getValue('max_submissions', 3);
        $count = $user->thesisSubmissions()->count();
        $isWithinDeadline = true;
        
        if ($prodi && ($prodi->submission_start || $prodi->submission_end)) {
            if ($prodi->submission_start && $now->lt($prodi->submission_start)) $isWithinDeadline = false;
            if ($prodi->submission_end && $now->gt($prodi->submission_end)) $isWithinDeadline = false;
        }
    @endphp

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Pengajuan Tugas Akhir Saya</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if ($isWithinDeadline)
                <a href="{{ route('student.submissions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                </a>
            @else
                <button class="btn btn-secondary" disabled title="Masa pengajuan ditutup">
                    <i class="bi bi-lock-fill"></i> Buat Pengajuan Baru (Tutup)
                </button>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-calendar-event fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Batas Waktu Pengajuan</h6>
                        @if($prodi && ($prodi->submission_start || $prodi->submission_end))
                            <div class="small">
                                @if($prodi->submission_start)
                                    <span class="text-{{ $now->lt($prodi->submission_start) ? 'warning' : 'success' }}">
                                        Buka: {{ $prodi->submission_start->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                                @if($prodi->submission_end)
                                    <span class="mx-1 text-muted">•</span>
                                    <span class="text-{{ $now->gt($prodi->submission_end) ? 'danger' : 'info' }}">
                                        Tutup: {{ $prodi->submission_end->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small">Tidak ada batasan waktu khusus.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        @php
            $attemptsPerBatch = $prodi ? (int) ($prodi->attempts_per_batch ?? 3) : (int) \App\Models\Setting::getValue('attempts_per_batch', 3);
            $maxBatches = $prodi ? (int) ($prodi->max_batches ?? 2) : (int) \App\Models\Setting::getValue('max_batches', 2);
            $maxTotal = $attemptsPerBatch * $maxBatches;
            
            $allSub = $user->thesisSubmissions()->orderBy('id', 'asc')->get();
            $count = $allSub->count();

            // Batch logic
            $currentBatchCount = $count % $attemptsPerBatch;
            if ($count > 0 && $currentBatchCount === 0) {
                $lastBatch = $allSub->take(-$attemptsPerBatch);
                $isBatchFinished = $lastBatch->every(fn($s) => in_array($s->status, ['rejected', 'cancelled']));
                $slotsInBatch = $isBatchFinished ? $attemptsPerBatch : 0;
            } else {
                $slotsInBatch = $attemptsPerBatch - $currentBatchCount;
            }
        @endphp

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-{{ $slotsInBatch <= 0 ? 'danger' : 'primary' }} bg-opacity-10 p-3 me-3">
                        <i class="bi bi-activity fs-4 text-{{ $slotsInBatch <= 0 ? 'danger' : 'primary' }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Slot Pengajuan Batch</h6>
                        <span class="badge {{ $slotsInBatch <= 0 ? 'bg-danger' : 'bg-primary' }}">
                            {{ $slotsInBatch }} dari {{ $attemptsPerBatch }} tersedia
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-{{ $count >= $maxTotal ? 'danger' : 'success' }} bg-opacity-10 p-3 me-3">
                        <i class="bi bi-journal-check fs-4 text-{{ $count >= $maxTotal ? 'danger' : 'success' }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Total Jatah Pengajuan</h6>
                        <span class="badge {{ $count >= $maxTotal ? 'bg-danger' : 'bg-success' }}">
                            {{ max(0, $maxTotal - $count) }} kali lagi
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user->can_exceed_submission_limit)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
            <div>
                <strong>Pemberitahuan Khusus:</strong> Anda mendapatkan pengecualian untuk membuat pengajuan melebihi batas maksimal reguler.
            </div>
        </div>
    @endif


    <div id="ajax-container">
        @if($submissions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Tgl Pengajuan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td>{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                        <td>
                                            <strong>{{ Str::limit($submission->title, 50) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $submission->research_field }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                                {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $submission->submission_date?->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('student.submissions.show', $submission) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($submission->canBeEditedByStudent())
                                                    <a href="{{ route('student.submissions.edit', $submission) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if($submission->status === 'draft')
                                                    <form action="{{ route('student.submissions.destroy', $submission) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm-delete
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox empty-state-icon text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Belum Ada Pengajuan</h4>
                    <p class="text-muted">Buat pengajuan tugas akhir pertama Anda sekarang.</p>
                    @if ($isWithinDeadline)
                        <a href="{{ route('student.submissions.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                        </a>
                    @else
                        <button class="btn btn-secondary mt-2" disabled>
                            <i class="bi bi-lock-fill"></i> Buat Pengajuan Baru (Tutup)
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection