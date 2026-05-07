@extends('layouts.app')

@section('title', 'Daftar Semua Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Semua Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.submissions.create') }}" class="btn btn-primary shadow-sm rounded-pill">
                <i class="bi bi-journal-plus me-1"></i> Input Data History
            </a>
        </div>
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
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Sudah Diajukan</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        {{-- <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option> --}}
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="tahun_pengajuan" class="form-control" placeholder="Tahun Pengajuan..."
                        value="{{ request('tahun_pengajuan') }}" data-auto-submit>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div id="ajax-container">
        <div id="batch-action-bar" class="card border-0 shadow-sm mb-4 d-none">
            <div class="card-body bg-light border rounded">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="text-nowrap">
                    <span id="selected-count" class="fw-bold me-2">0</span> pengajuan terpilih
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 flex-grow-1 justify-content-lg-end">
                    <select name="batch_rubric_id" class="form-select" style="width: auto; min-width: 180px;" form="batch-assign-form" required>
                        <option value="">-- Pilih Rubrik --</option>
                        @foreach ($rubrics as $rubric)
                            <option value="{{ $rubric->id }}">{{ $rubric->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex-grow-1" style="min-width: 250px; max-width: 500px;">
                        <select name="batch_assessor_ids[]" id="batch-assessor-select" class="form-select" multiple form="batch-assign-form"
                            required data-placeholder="Pilih Dosen Penilai...">
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="assign" class="btn btn-primary text-nowrap" form="batch-assign-form"
                            onclick="return confirm('Terapkan dosen penilai ke pengajuan terpilih?')">
                            Terapkan Penilai
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetBatch()">Batal</button>
                    </div>
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
                                        @if($submission->status === 'submitted')
                                            <input type="checkbox" name="submission_ids[]" value="{{ $submission->id }}"
                                                class="form-check-input submission-check">
                                        @endif
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
                                        <div class="btn-group">
                                            <a href="{{ route('kaprodi.submissions.show', array_merge(['submission' => $submission->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-primary px-3">
                                                Detail
                                            </a>
                                            @if($submission->status === 'submitted')
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                    data-submission-id="{{ $submission->id }}"
                                                    data-submission-title="{{ $submission->title }}">
                                                    Tolak
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
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
    </form>
    {{-- Rejection Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Tuliskan alasan penolakan untuk pengajuan: <br><strong id="modal-submission-title"></strong></p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label fw-semibold">Alasan Penolakan</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                placeholder="Jelaskan alasan pengajuan ditolak..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary shadow-none" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    @endsection

@push('scripts')
<script>
    function initBatchUI() {
        const checkAll = document.getElementById('check-all');
        const submissionChecks = document.querySelectorAll('.submission-check');
        const batchActionBar = document.getElementById('batch-action-bar');
        const selectedCount = document.getElementById('selected-count');

        function updateBatchUI() {
            const checkedCount = document.querySelectorAll('.submission-check:checked').length;
            if (selectedCount) {
                selectedCount.textContent = checkedCount;
            }
            if (batchActionBar) {
                if (checkedCount > 0) {
                    batchActionBar.classList.remove('d-none');
                } else {
                    batchActionBar.classList.add('d-none');
                }
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const currentChecks = document.querySelectorAll('.submission-check');
                currentChecks.forEach(check => {
                    check.checked = checkAll.checked;
                });
                updateBatchUI();
            });
        }

        const freshChecks = document.querySelectorAll('.submission-check');
        freshChecks.forEach(check => {
            check.addEventListener('change', updateBatchUI);
        });

        window.resetBatch = function() {
            const allChecks = document.querySelectorAll('.submission-check');
            allChecks.forEach(check => check.checked = false);
            const masterCheck = document.getElementById('check-all');
            if (masterCheck) masterCheck.checked = false;
            updateBatchUI();
        }

        if (typeof TomSelect !== 'undefined') {
            const selectEl = document.getElementById('batch-assessor-select');
            if (selectEl) {
                if (selectEl.tomselect) {
                    selectEl.tomselect.destroy();
                }
                new TomSelect('#batch-assessor-select', {
                    plugins: ['remove_button'],
                    persist: false,
                    create: false,
                    placeholder: 'Pilih Dosen Penilai...'
                });
            }
        }

        // Rejection Modal Handler
        const rejectModal = document.getElementById('rejectModal');
        if (rejectModal) {
            rejectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const submissionId = button.getAttribute('data-submission-id');
                const submissionTitle = button.getAttribute('data-submission-title');
                
                const form = rejectModal.querySelector('#rejectForm');
                const titleEl = rejectModal.querySelector('#modal-submission-title');
                
                titleEl.textContent = submissionTitle;
                form.action = `/kaprodi/submissions/${submissionId}/reject`;
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initBatchUI);
    document.addEventListener('ajaxContentLoaded', initBatchUI);
</script>
@endpush
