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
                <div class="col-md-3">
                    <select name="status" class="form-select" data-auto-submit>
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted
                        </option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under
                            Review
                        </option>
                        {{-- <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option> --}}
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="angkatan" class="form-control" placeholder="Angkatan..."
                        value="{{ request('angkatan') }}" data-auto-submit>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div id="batch-action-bar" class="card border-0 shadow-sm mb-4 d-none">
        <div class="card-body bg-light border rounded">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span id="selected-count" class="fw-bold me-2">0</span> pengajuan terpilih
                </div>
                <div class="d-flex gap-2">
                    <select name="batch_rubric_id" class="form-select w-auto" form="batch-assign-form" required>
                        <option value="">-- Pilih Rubrik --</option>
                        @foreach ($rubrics as $rubric)
                            <option value="{{ $rubric->id }}">{{ $rubric->name }}</option>
                        @endforeach
                    </select>
                    <select name="batch_assessor_ids[]" class="form-select w-auto" multiple form="batch-assign-form"
                        required style="min-width: 200px;">
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" name="action" value="assign" class="btn btn-primary" form="batch-assign-form"
                        onclick="return confirm('Terapkan dosen penilai ke pengajuan terpilih?')">
                        Terapkan Penilai
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetBatch()">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <form id="batch-assign-form" action="{{ route('kaprodi.submissions.batch-assign') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 40px;">
                                    <input type="checkbox" id="check-all" class="form-check-input">
                                </th>
                                <th class="w-40">Judul Pengajuan</th>
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
                                        <input type="checkbox" name="submission_ids[]" value="{{ $submission->id }}"
                                            class="form-check-input submission-check">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 400px;">
                                            {{ $submission->title }}</div>
                                        <div class="text-muted small">
                                            {{ Str::limit($submission->research_field ?? 'Umum', 30) }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $submission->student->profile_photo_url }}"
                                                class="rounded-circle me-2 shadow-sm"
                                                style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                                            <div>
                                                <div class="fw-medium">{{ $submission->student->name }}</div>
                                                <div class="text-muted smaller-text">{{ $submission->student->nim_nip }}
                                                </div>
                                            </div>
                                        </div>
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
                                        <a href="{{ route('kaprodi.submissions.show', array_merge(['submission' => $submission->id], request()->query())) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        @if (request('search') || request('status'))
                                            <p class="text-muted mb-0">Tidak ditemukan pengajuan yang sesuai dengan filter
                                                Anda.</p>
                                            <a href="{{ route('kaprodi.submissions.index') }}"
                                                class="btn btn-outline-primary mt-2">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all');
        const submissionChecks = document.querySelectorAll('.submission-check');
        const batchActionBar = document.getElementById('batch-action-bar');
        const selectedCount = document.getElementById('selected-count');

        function updateBatchUI() {
            const checkedCount = document.querySelectorAll('.submission-check:checked').length;
            selectedCount.textContent = checkedCount;
            if (checkedCount > 0) {
                batchActionBar.classList.remove('d-none');
            } else {
                batchActionBar.classList.add('d-none');
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                submissionChecks.forEach(check => {
                    check.checked = checkAll.checked;
                });
                updateBatchUI();
            });
        }

        submissionChecks.forEach(check => {
            check.addEventListener('change', updateBatchUI);
        });

        window.resetBatch = function() {
            submissionChecks.forEach(check => check.checked = false);
            if (checkAll) checkAll.checked = false;
            updateBatchUI();
        }
    });
</script>
@endpush
