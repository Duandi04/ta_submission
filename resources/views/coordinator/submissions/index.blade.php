@extends('layouts.app')

@section('title', 'Manajemen Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Pengajuan Tugas Akhir</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel"></i> Filter & Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card stats-primary">
                <h6 class="text-muted text-uppercase">Total Pengajuan</h6>
                <h2 class="mb-0">{{ $submissions->total() }}</h2>
                <p class="mb-0 small text-muted mt-2">
                    <i class="bi bi-file-text"></i> Semua pengajuan
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-warning">
                <h6 class="text-muted text-uppercase">Perlu Tindakan</h6>
                <h2 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'submitted')->count() }}</h2>
                <p class="mb-0 small text-muted mt-2">
                    <i class="bi bi-exclamation-triangle"></i> Belum direview
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-info">
                <h6 class="text-muted text-uppercase">Dalam Proses</h6>
                <h2 class="mb-0">
                    {{ \App\Models\ThesisSubmission::whereIn('status', ['under_review', 'scheduled_for_defense'])->count() }}
                </h2>
                <p class="mb-0 small text-muted mt-2">
                    <i class="bi bi-hourglass-split"></i> Sedang berjalan
                </p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-success">
                <h6 class="text-muted text-uppercase">Selesai</h6>
                <h2 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'completed')->count() }}</h2>
                <p class="mb-0 small text-muted mt-2">
                    <i class="bi bi-check-circle"></i> Telah lulus
                </p>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list"></i> Daftar Pengajuan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Judul</th>
                            <th>Pembimbing</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td>{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                <td>
                                    <strong>{{ $submission->student->name }}</strong>
                                    <br><small class="text-muted">{{ $submission->student->nim_nip }}</small>
                                </td>
                                <td>{{ Str::limit($submission->title, 40) }}</td>
                                <td>{{ $submission->supervisor?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                        {{ $submission->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('coordinator.submissions.show', $submission) }}"
                                            class="btn btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada data pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
@endsection