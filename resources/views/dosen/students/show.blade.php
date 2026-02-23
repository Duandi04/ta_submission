@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ' . $student->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Mahasiswa</h1>
            <p class="text-muted small mb-0">{{ $student->name }} ({{ $student->nim_nip }})</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('dosen.students.index', request()->query()) }}" class="btn btn-outline-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <span class="fw-bold"><i class="bi bi-journal-text me-2 text-primary"></i>Daftar Draft Proposal</span>
        </div>
        <div class="card-body p-0">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Judul</th>
                                <th>Status</th>
                                <th>Tanggal Submit</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $submission->title }}</div>
                                        <div class="text-muted smaller-text">{{ $submission->research_field ?? 'Umum' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }} rounded-pill">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('dosen.submissions.show', $submission) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            Detail & Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox empty-state-icon"></i>
                    <h6 class="fw-bold">Belum ada draft proposal</h6>
                </div>
            @endif
        </div>
    </div>
@endsection