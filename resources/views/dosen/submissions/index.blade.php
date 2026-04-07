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
                                    <th>Judul Thesis</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Nilai Final</th>
                                    <th class="text-end pe-3">Aksi Review</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="position-relative me-3">
                                                    <img src="{{ $submission->student->profile_photo_url }}" class="rounded-circle border border-2 border-white shadow-sm"
                                                        style="width: 42px; height: 42px; object-fit: cover;" alt="">
                                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" style="width: 12px; height: 12px;"></span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $submission->student->name }}</div>
                                                    <div class="text-muted small font-monospace">{{ $submission->student->nim_nip }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-truncate-2 small" style="max-width: 300px;" title="{{ $submission->title }}">
                                                {{ $submission->title }}
                                            </div>
                                            <div class="text-muted extra-small mt-1">
                                                <i class="bi bi-tag-fill me-1"></i>{{ $submission->research_field ?? 'Umum' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-soft-{{ $submission->getStatusBadgeClass() }} text-{{ $submission->getStatusBadgeClass() }} px-3 py-2 border border-{{ $submission->getStatusBadgeClass() }}-subtle">
                                                <i class="bi bi-circle-fill me-1 small"></i> {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($submission->final_score)
                                                <span class="h5 mb-0 fw-bold text-primary">{{ number_format($submission->final_score, 1) }}</span>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            @php
                                                $myAssessment = $submission->assessments->where('evaluator_id', auth()->id())->first();
                                            @endphp

                                            @if($myAssessment)
                                                @if($myAssessment->is_submitted)
                                                    <a href="{{ route('dosen.assessments.show', $myAssessment) }}"
                                                        class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" title="Lihat Hasil Penilaian">
                                                        <i class="bi bi-check2-all me-1"></i> Teruji
                                                    </a>
                                                @else
                                                    <a href="{{ route('dosen.assessments.edit', $myAssessment) }}"
                                                        class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm" title="Lanjutkan Draft Penilaian">
                                                        <i class="bi bi-pencil-square me-1"></i> Lanjut Draft
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('dosen.assessments.create', ['submission_id' => $submission->id]) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm hover-elevate" title="Mulai Penilaian Baru">
                                                    <i class="bi bi-plus-lg me-1"></i> Beri Nilai
                                                </a>
                                            @endif
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