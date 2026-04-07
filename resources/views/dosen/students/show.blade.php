@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ' . $student->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Mahasiswa</h1>
            <p class="text-muted small mb-0">{{ $student->name }} ({{ $student->nim_nip }})</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('dosen.students.index') }}" class="btn btn-outline-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    {{-- Profile Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="h-1 bg-primary"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-4">
                            <img src="{{ $student->profile_photo_url }}" class="rounded-circle border border-4 border-white shadow-sm"
                                style="width: 100px; height: 100px; object-fit: cover;" alt="">
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-dark">{{ $student->name }}</h3>
                            <div class="text-muted mb-2 font-monospace">{{ $student->nim_nip }}</div>
                            <div class="d-flex gap-3">
                                <span class="badge bg-soft-info text-info"><i class="bi bi-mortarboard me-1"></i>{{ $student->programStudi->name ?? '-' }}</span>
                                <span class="badge bg-soft-secondary text-secondary"><i class="bi bi-calendar2-event me-1"></i>Angkatan {{ $student->angkatan ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="ms-md-auto mt-3 mt-md-0 d-flex flex-column gap-2 text-md-end">
                            @if($officialSupervision)
                                <div class="small">
                                    <span class="text-muted d-block extra-small text-uppercase fw-bold">Pembimbing I</span>
                                    <span class="fw-semibold text-dark">{{ $officialSupervision->supervisor->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                                <div class="small">
                                    <span class="text-muted d-block extra-small text-uppercase fw-bold">Pembimbing II</span>
                                    <span class="fw-semibold text-dark">{{ $officialSupervision->supervisor2->name ?? 'Belum Ditentukan' }}</span>
                                </div>
                            @elseif($submissions->where('status', 'under_review')->count() > 0)
                                <div class="small text-muted p-2 rounded bg-light">
                                    <i class="bi bi-info-circle me-1"></i>Pembimbing akan tampil setelah proposal diterima.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submissions/Assessments List --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <span class="fw-bold"><i class="bi bi-journal-check me-2 text-primary"></i>Riwayat Pengajuan & Penilaian</span>
        </div>
        <div class="card-body p-0">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" width="300">Judul Proposal</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Nilai Saya</th>
                                <th class="text-end pe-4">Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                @php
                                    $myAssessment = $submission->assessments->first();
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark small" title="{{ $submission->title }}">{{ Str::limit($submission->title, 70) }}</div>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            <span class="text-muted extra-small"><i class="bi bi-tag-fill me-1"></i>{{ $submission->research_field ?? 'Umum' }}</span>
                                            <span class="extra-small px-2 bg-light rounded text-muted">
                                                <i class="bi bi-people-fill me-1"></i>{{ $submission->supervisor->name }} / {{ $submission->supervisor2->name ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-soft-{{ $submission->getStatusBadgeClass() }} text-{{ $submission->getStatusBadgeClass() }} px-3 py-1 border border-{{ $submission->getStatusBadgeClass() }}-subtle extra-small">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        {{ $submission->created_at->format('d/m/Y') }}
                                        <div class="extra-small opacity-75">{{ $submission->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($myAssessment && $myAssessment->is_submitted)
                                            <span class="h5 mb-0 fw-bold text-primary">{{ number_format($myAssessment->total_score, 1) }}</span>
                                        @elseif($myAssessment)
                                            <span class="badge bg-soft-warning text-warning fw-normal extra-small">Draft</span>
                                        @else
                                            <span class="text-muted small">---</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($myAssessment)
                                            @if($myAssessment->is_submitted)
                                                <a href="{{ route('dosen.assessments.show', $myAssessment) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Lihat Hasil Penilaian">
                                                    <i class="bi bi-eye-fill me-1"></i> Detail
                                                </a>
                                            @else
                                                <a href="{{ route('dosen.assessments.edit', $myAssessment) }}"
                                                    class="btn btn-sm btn-warning rounded-pill px-3 shadow-none ripple text-dark" title="Lanjutkan Draft Penilaian">
                                                    <i class="bi bi-pencil-square me-1"></i> Lanjut Draft
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('dosen.assessments.create', ['submission_id' => $submission->id]) }}"
                                                class="btn btn-sm btn-primary rounded-pill px-3 shadow-none ripple" title="Mulai Penilaian Baru">
                                                <i class="bi bi-plus-lg me-1"></i> Beri Nilai
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x empty-state-icon mb-3"></i>
                    <h5 class="fw-bold text-dark">Belum Ada Pengajuan</h5>
                    <p class="text-muted small">Mahasiswa ini belum memiliki riwayat pengajuan proposal yang perlu Anda review.</p>
                </div>
            @endif
        </div>
    </div>
@endsection