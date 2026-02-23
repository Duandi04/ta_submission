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

    <div id="ajax-container">
        @if($submissions->count() > 0)
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold">{{ $submissions->total() }}</h3>
                            <small class="text-muted">Total Bimbingan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold text-primary">{{ auth()->user()->supervisedTheses()->where('status', 'submitted')->count() }}</h3>
                            <small class="text-muted">Perlu Review</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold text-warning">{{ auth()->user()->supervisedTheses()->where('status', 'under_review')->count() }}</h3>
                            <small class="text-muted">Sedang Direview</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold text-success">{{ auth()->user()->supervisedTheses()->where('status', 'completed')->count() }}</h3>
                            <small class="text-muted">Selesai</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Judul</th>
                                    <th>Bidang</th>
                                    <th>Status</th>
                                    <th>Tanggal Submit</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td class="ps-3">{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $submission->student->profile_photo_url }}" class="rounded-circle me-2"
                                                    style="width: 32px; height: 32px; object-fit: cover;" alt="">
                                                <div>
                                                    <div class="fw-bold">{{ $submission->student->name }}</div>
                                                    <div class="text-muted small">{{ $submission->student->nim_nip }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($submission->title, 50) }}</td>
                                        <td>{{ $submission->research_field ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-soft-{{ $submission->getStatusBadgeClass() }} text-{{ $submission->getStatusBadgeClass() }} border border-{{ $submission->getStatusBadgeClass() }}-subtle">
                                                {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">{{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('dosen.submissions.show', $submission) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $submissions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm py-5">
                <div class="card-body text-center">
                    <i class="bi bi-inbox empty-state-icon"></i>
                    <h4 class="mt-3">Belum Ada Mahasiswa Bimbingan</h4>
                    <p class="text-muted">Anda belum memiliki mahasiswa bimbingan yang sesuai dengan filter ini.</p>
                </div>
            </div>
        @endif
    </div>
@endsection