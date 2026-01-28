@extends('layouts.app')

@section('title', 'Laporan Tugas Akhir')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Laporan Pengajuan Tugas Akhir</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Draft</h6>
                    <h4 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'draft')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase text-warning">Penilaian</h6>
                    <h4 class="mb-0">
                        {{ \App\Models\ThesisSubmission::whereIn('status', ['submitted', 'under_review'])->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase text-danger">Ditolak</h6>
                    <h4 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'rejected')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase text-success">Diterima</h6>
                    <h4 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'approved')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase text-primary">Selesai</h6>
                    <h4 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'completed')->count() }}</h4>
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
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'student', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                    class="text-dark text-decoration-none">
                                    Mahasiswa
                                    @if (request('sort_by') == 'student')
                                        <i
                                            class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Judul Thesis</th>
                            <th>Pembimbing</th>
                            <th class="text-center">Status</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                    class="text-dark text-decoration-none">
                                    Tgl Pengajuan
                                    @if (request('sort_by') == 'created_at')
                                        <i
                                            class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'numeric-down' : 'numeric-up' }}"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                            <tr>
                                <td class="ps-3">
                                    {{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $submission->student->name }}</div>
                                    <div class="text-muted small">{{ $submission->student->nim_nip }}</div>
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 400px;">
                                        {{ Str::limit($submission->title, 100) }}
                                    </div>
                                </td>
                                <td>{{ $submission->supervisor?->name ?? 'Belum Ditunjuk' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $submission->getStatusBadgeClass() }} rounded-pill px-3">
                                        {{ $submission->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>

    <style>
        @media print {

            .sidebar,
            .navbar,
            .btn-toolbar,
            .pagination {
                display: none !important;
            }

            #main-content {
                margin: 0 !important;
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endsection
