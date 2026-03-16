@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'student.submissions.show'])
            <div class="ms-3 btn-group">
                @if ($submission->canBeEditedByStudent())
                    <a href="{{ route('student.submissions.edit', $submission) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                @endif
                <a href="{{ route('student.submissions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @php $hasSidebar = $submission->status === 'draft'; @endphp
    <div class="row">
        <div class="{{ $hasSidebar ? 'col-md-8' : 'col-md-12' }}">
            <!-- Main Info Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-file-text"></i> Informasi Pengajuan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Judul</th>
                            <td>: <strong>{{ $submission->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Bidang Penelitian</th>
                            <td>: {{ $submission->research_field ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th width="200">Pembimbing 1</th>
                            <td>: {{ $submission->supervisor->name ?? 'Belum ditentukan' }}</td>
                        </tr>
                        @if ($submission->supervisor_2_id)
                            <tr>
                                <th>Pembimbing 2</th>
                                <td>: {{ $submission->supervisor2->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>:
                                <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                    {{ $submission->getStatusLabel() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>: {{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @if ($submission->defense_date)
                            <tr>
                                <th>Tanggal Sidang</th>
                                <td>: {{ $submission->defense_date->format('d/m/Y') }}</td>
                            </tr>
                        @endif
                        @if ($submission->final_score)
                            <tr>
                                <th>Nilai Akhir</th>
                                <td>: <strong class="text-success">{{ $submission->final_score }}</strong></td>
                            </tr>
                        @endif
                    </table>

                    <hr>

                    <h6>Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>

                    @if ($submission->notes)
                        <div class="alert alert-info">
                            <strong><i class="bi bi-sticky"></i> Catatan:</strong><br>
                            {{ $submission->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-paperclip"></i> File Terlampir</span>
                </div>
                <div class="card-body">
                    @if ($submission->files->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-medical text-primary"></i> Dokumen Utama
                            </h6>
                        </div>
                        @php
                            $mainFiles = $submission->files->whereIn('file_type', [
                                'proposal',
                                'final_document',
                                'presentation',
                            ]);
                            $revisionFiles = $submission->files->where('file_type', 'revision');
                            $proposalFile = $submission->files->where('file_type', 'proposal')->first();
                        @endphp

                        @if ($mainFiles->count() > 0)
                            <div class="list-group mb-4">
                                @foreach ($mainFiles as $file)
                                    <div
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 shadow-sm mb-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-wrapper me-3">
                                                @if (str_contains($file->mime_type, 'pdf'))
                                                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                                @else
                                                    <i class="bi bi-file-earmark-word-fill text-primary fs-4"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 smaller-text fw-bold text-dark">{{ $file->file_name }}</h6>
                                                <small class="text-muted smaller-extra">
                                                    {{ $file->getFileTypeLabel() }} • {{ $file->getFormattedFileSize() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('files.preview', $file) }}"
                                                class="btn btn-sm btn-outline-primary border-0" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}"
                                                class="btn btn-sm btn-outline-secondary border-0">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-warning"></i> Riwayat Revisi</h6>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#uploadRevisionModal">
                                <i class="bi bi-plus"></i> Unggah Revisi
                            </button>
                        </div>

                        @if ($revisionFiles->count() > 0)
                            <div class="list-group mb-3">
                                @foreach ($revisionFiles->sortByDesc('created_at') as $file)
                                    <div
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 shadow-sm mb-2 rounded bg-light">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-wrapper me-3">
                                                <i class="bi bi-file-earmark-arrow-up-fill text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 smaller-text fw-bold">{{ $file->file_name }}</h6>
                                                <small class="text-muted smaller-extra">
                                                    {{ $file->created_at->format('d/m/Y H:i') }} •
                                                    {{ $file->getFormattedFileSize() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <a href="{{ route('files.preview', $file) }}"
                                                class="btn btn-sm btn-outline-primary border-0" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('files.download', $file->id) }}"
                                                class="btn btn-sm btn-outline-secondary border-0">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 bg-light rounded border border-dashed">
                                <i class="bi bi-file-earmark-x text-muted fs-1 mb-2 d-block"></i>
                                <p class="text-muted mb-0 small">Belum ada file revisi yang diunggah.</p>
                            </div>
                        @endif

                        <!-- Upload Revision Modal -->
                        <div class="modal fade" id="uploadRevisionModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('student.submissions.revision', $submission->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="upload_type" value="local">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Unggah File Revisi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Pilih File Revisi (PDF/Doc/Docx)</label>
                                                <input type="file" name="revision_file" id="revision_file"
                                                    class="form-control" required>
                                                <small class="text-muted">Maksimal 10MB</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Unggah Sekarang</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($proposalFile && str_contains($proposalFile->mime_type, 'pdf'))
                            <div class="pdf-preview-container mt-4">
                                <h6 class="mb-3"><i class="bi bi-eye"></i> Pratinjau Proposal (PDF)</h6>
                                <div class="ratio ratio-16x9 border rounded overflow-hidden shadow-sm"
                                    style="height: 600px;">
                                    <iframe src="{{ route('files.preview', $proposalFile) }}#toolbar=0"
                                        title="PDF Preview"></iframe>
                                </div>
                                <div class="mt-2 text-center">
                                    <small class="text-muted">Gunakan tombol 'Lihat' di atas jika pratinjau tidak
                                        muncul.</small>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x text-muted fs-1 mb-2 d-block"></i>
                            <p class="text-muted mb-0">Belum ada file terlampir.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assessments Card -->
            @if ($submission->assessments->where('is_submitted', true)->count() > 0)
                <h5 class="mb-3 mt-4"><i class="bi bi-clipboard-data me-2"></i>Hasil Penilaian</h5>
                @foreach ($submission->assessments->where('is_submitted', true) as $assessment)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">
                                <i class="bi bi-person-badge me-2"></i>{{ $assessment->getAnonymousLabel() }}
                            </span>
                            <div class="text-end">
                                <span class="small text-muted d-block" style="font-size: 0.75rem;">Total Nilai</span>
                                <span
                                    class="badge bg-primary fs-6">{{ number_format($assessment->total_score, 2) }}</span>
                            </div>
                        </div>
                        <div class="card-body border-top border-light">
                            <h6 class="fw-bold mb-3 small text-muted">DETAIL RUBRIK PENILAIAN</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-uppercase" style="font-size: 0.7rem;">
                                            <th class="ps-3">Kriteria</th>
                                            <th class="text-center" width="80">Bobot</th>
                                            <th class="text-center" width="80">Nilai</th>
                                            <th class="text-center" width="100">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assessment->scores as $score)
                                            @php
                                                $contribution = ($score->score * $score->weight) / 100;
                                            @endphp
                                            <tr class="small">
                                                <td class="ps-3">
                                                    <div class="fw-semibold text-dark">{{ $score->criterion_name }}</div>
                                                    @if ($score->criterion_description)
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            {{ $score->criterion_description }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($score->weight, 0) }}%</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-light text-dark border">{{ number_format($score->score, 1) }}</span>
                                                </td>
                                                <td class="text-center fw-bold text-primary">
                                                    {{ number_format($contribution, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light border-top-0">
                                        <tr class="small fw-bold">
                                            <td class="ps-3">TOTAL</td>
                                            <td class="text-center">100%</td>
                                            <td colspan="2" class="text-end pe-4 text-primary fs-6">
                                                {{ number_format($assessment->total_score, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if ($assessment->comments || $assessment->strengths || $assessment->weaknesses || $assessment->recommendations)
                                <h6 class="fw-bold mb-2 small text-muted text-uppercase">Feedback & Catatan</h6>
                                <div class="row g-3">
                                    @if ($assessment->comments)
                                        <div class="col-12">
                                            <div class="p-3 rounded bg-light border-start border-4 border-info">
                                                <label class="small fw-bold text-info mb-1 d-block">Komentar Umum</label>
                                                <p class="small mb-0 text-dark">{{ $assessment->comments }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($assessment->strengths)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded bg-light border-start border-4 border-success h-100">
                                                <label class="small fw-bold text-success mb-1 d-block">Kelebihan</label>
                                                <p class="small mb-0 text-dark">{{ $assessment->strengths }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($assessment->weaknesses)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded bg-light border-start border-4 border-danger h-100">
                                                <label class="small fw-bold text-danger mb-1 d-block">Kelemahan</label>
                                                <p class="small mb-0 text-dark">{{ $assessment->weaknesses }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($assessment->recommendations)
                                        <div class="col-12">
                                            <div class="p-3 rounded bg-light border-start border-4 border-primary">
                                                <label class="small fw-bold text-primary mb-1 d-block">Rekomendasi</label>
                                                <p class="small mb-0 text-dark">{{ $assessment->recommendations }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0 py-3 text-end">
                            <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> Disubmit pada:
                                {{ $assessment->submitted_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if ($hasSidebar)
            <div class="col-md-4">
                <!-- Submit Button (Only for Draft) -->
                @if ($submission->status === 'draft')
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="bi bi-send"></i> Ajukan Proposal
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">Jika Anda yakin dengan draft ini, silakan ajukan untuk
                                direview
                                oleh Kaprodi.</p>
                            <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                                <small>Setelah diajukan, pengajuan <strong>tidak dapat dibatalkan</strong>.</small>
                            </div>
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                data-bs-target="#confirmSubmitModal">
                                <i class="bi bi-send me-1"></i> Ajukan Sekarang
                            </button>
                        </div>
                    </div>

                    {{-- Confirm Submit Modal --}}
                    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-send-check me-2 text-success"></i>Konfirmasi Pengajuan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-3">Anda akan mengajukan proposal berikut:</p>
                                    <div class="bg-light rounded p-3 mb-3">
                                        <strong class="small d-block text-truncate">{{ $submission->title }}</strong>
                                    </div>
                                    <div class="alert alert-danger py-2 px-3 d-flex align-items-start gap-2 mb-0">
                                        <i class="bi bi-exclamation-octagon-fill mt-1 flex-shrink-0"></i>
                                        <small><strong>Perhatian:</strong> Setelah diajukan, pengajuan ini <strong>tidak
                                                dapat dibatalkan</strong> dan tidak dapat diedit kembali sampai ada
                                            instruksi revisi dari Kaprodi.</small>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Kembali</button>
                                    <form action="{{ route('student.submissions.submit', $submission) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-send me-1"></i> Ya, Ajukan Sekarang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Cancel Submission Button -->
                @if ($submission->status === 'draft')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <i class="bi bi-x-circle"></i> Batalkan Pengajuan
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">Anda dapat membatalkan pengajuan ini selama masih dalam status
                                Draft.</p>
                            <form action="{{ route('student.submissions.cancel', $submission) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
@endpush
