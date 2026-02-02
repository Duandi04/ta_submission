@extends('layouts.app')

@section('title', 'Mahasiswa Bimbingan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Mahasiswa Bimbingan Saya</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-filter"></i> Filter Status
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('dosen.submissions.index') }}">Semua</a></li>
                    <li><a class="dropdown-item" href="?status=submitted">Submitted</a></li>
                    <li><a class="dropdown-item" href="?status=under_review">Under Review</a></li>
                    <li><a class="dropdown-item" href="?status=approved">Approved</a></li>
                </ul>
            </div>
        </div>
    </div>

    @if($submissions->count() > 0)
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $submissions->total() }}</h3>
                        <small class="text-muted">Total Bimbingan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-0">{{ auth()->user()->supervisedTheses()->where('status', 'submitted')->count() }}</h3>
                        <small class="text-muted">Perlu Review</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-0">{{ auth()->user()->supervisedTheses()->where('status', 'under_review')->count() }}</h3>
                        <small class="text-muted">Sedang Direview</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="mb-0">{{ auth()->user()->supervisedTheses()->where('status', 'completed')->count() }}</h3>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mahasiswa</th>
                                <th>Judul</th>
                                <th>Bidang</th>
                                <th>Status</th>
                                <th>Tanggal Submit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                    <td>
                                        <strong>{{ $submission->student->name }}</strong>
                                        <br><small class="text-muted">{{ $submission->student->nim_nip }}</small>
                                    </td>
                                    <td>{{ Str::limit($submission->title, 50) }}</td>
                                    <td>{{ $submission->research_field ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dosen.submissions.show', $submission) }}"
                                                class="btn btn-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
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
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox empty-state-icon"></i>
                <h4 class="mt-3">Belum Ada Mahasiswa Bimbingan</h4>
                <p class="text-muted">Anda belum ditugaskan membimbing mahasiswa.</p>
            </div>
        </div>
    @endif
@endsection