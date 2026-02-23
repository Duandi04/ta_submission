@extends('layouts.app')

@section('title', 'Daftar Semua Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Semua Pengajuan</h1>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('kaprodi.submissions.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari judul, nama, atau NIM..." value="{{ request('search') }}" data-auto-search>
                    </div>
                </div>
                <div class="col-md-5">
                    <select name="status" class="form-select" data-auto-submit>
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review
                        </option>
                        <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
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
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 400px;">
                                            {{ $submission->title }}</div>
                                        <div class="text-muted small">
                                            {{ Str::limit($submission->research_field ?? 'Umum', 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $submission->student->name }}</div>
                                        <div class="text-muted small">{{ $submission->student->nim_nip }}</div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-soft-{{ $submission->getStatusBadgeClass() }} text-{{ $submission->getStatusBadgeClass() }} border border-{{ $submission->getStatusBadgeClass() }}-subtle">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">{{ $submission->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small" style="font-size: 0.75rem">
                                            {{ $submission->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('kaprodi.submissions.show', array_merge(['submissionId' => $submission->id], request()->query())) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        @if(request('search') || request('status'))
                                            <p class="text-muted mb-0">Tidak ditemukan pengajuan yang sesuai dengan filter Anda.</p>
                                            <a href="{{ route('kaprodi.submissions.index') }}" class="btn btn-outline-primary mt-2">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                            </a>
                                        @else
                                            Belum ada pengajuan mahasiswa
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $submissions->appends(request()->query())->links() }}
        </div>
    </div>
@endsection