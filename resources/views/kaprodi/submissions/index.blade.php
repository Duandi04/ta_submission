@extends('layouts.app')

@section('title', 'Daftar Semua Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Semua Pengajuan</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 w-40">Judul Pengajuan</th>
                            <th>Mahasiswa</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 400px;">{{ $submission->title }}</div>
                                    <div class="text-muted small">{{ Str::limit($submission->research_field ?? 'Umum', 30) }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $submission->student->name }}</div>
                                    <div class="text-muted small">{{ $submission->student->nim_nip }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">{{ $submission->getStatusLabel() }}</span>
                                </td>
                                <td>
                                    <div class="small">{{ $submission->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem">{{ $submission->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('kaprodi.submissions.show', $submission->id) }}"
                                        class="btn btn-sm btn-outline-primary px-3">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada pengajuan mahasiswa
                                </td>
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
