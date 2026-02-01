@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Pengajuan</h1>
            <p class="text-muted small mb-0">{{ $submission->student->name }} ({{ $submission->student->nim_nip }})</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0 align-items-center">
            @include('partials.record-navigation', ['route' => 'admin.submissions.show'])
            <a href="{{ route('admin.submissions.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-file-text me-2 text-primary"></i>Informasi Pengajuan</span>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold">{{ $submission->title }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-tag me-1"></i>{{ $submission->research_field ?? 'Umum' }}
                        <span class="mx-2">|</span>
                        <span
                            class="badge bg-{{ $submission->getStatusBadgeClass() }}">{{ $submission->getStatusLabel() }}</span>
                    </p>
                    <hr>
                    <h6 class="fw-bold mb-2">Abstrak</h6>
                    <p class="text-muted" style="white-space: pre-line;">{{ $submission->abstract }}</p>

                    @if ($submission->files->count() > 0)
                        <hr>
                        <h6 class="fw-bold mb-2">Lampiran File</h6>
                        @php
                            $proposalFiles = $submission->files->where('file_type', 'proposal');
                            $revisionFiles = $submission->files->where('file_type', 'revision');
                        @endphp

                        @if ($proposalFiles->count() > 0)
                            <p class="small text-muted mb-1"><strong>Proposal</strong></p>
                            <ul class="list-unstyled mb-3">
                                @foreach ($proposalFiles as $file)
                                    <li class="mb-2 d-flex align-items-center">
                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                        <span class="flex-grow-1">{{ $file->file_name }} <span
                                                class="text-muted small">({{ $file->getFormattedFileSize() }})</span></span>
                                        @if (Str::endsWith(strtolower($file->file_name), '.pdf'))
                                            <a href="{{ route('files.preview', $file) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary me-1" title="Preview PDF">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('files.download', $file) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($revisionFiles->count() > 0)
                            <p class="small text-muted mb-1"><strong>Revisi</strong></p>
                            <ul class="list-unstyled mb-3">
                                @foreach ($revisionFiles as $file)
                                    <li class="mb-2 d-flex align-items-center">
                                        <i class="bi bi-file-earmark-pdf text-warning me-2"></i>
                                        <span class="flex-grow-1">{{ $file->file_name }} <span
                                                class="text-muted small">({{ $file->getFormattedFileSize() }})</span></span>
                                        @if (Str::endsWith(strtolower($file->file_name), '.pdf'))
                                            <a href="{{ route('files.preview', $file) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary me-1" title="Preview PDF">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('files.download', $file) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Hasil Penilaian
                        Dosen</span>
                </div>
                <div class="card-body p-0">
                    @if ($submission->assessments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Dosen Penilai</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->assessments as $assessment)
                                        <tr>
                                            <td class="ps-3 align-middle">
                                                <div class="fw-bold">{{ $assessment->evaluator->name ?? 'N/A' }}</div>
                                                <span
                                                    class="badge bg-secondary text-white small">{{ $assessment->getEvaluatorTypeLabel() }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($assessment->is_submitted)
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Selesai
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                        <i class="bi bi-hourglass-split me-1"></i>Belum Dinilai
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($assessment->is_submitted)
                                                    <span
                                                        class="fw-bold fs-5 text-dark">{{ number_format($assessment->total_score, 1) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-slash text-muted fs-1 d-block mb-3"></i>
                            <h6 class="fw-bold text-muted">Belum ada dosen penilai yang ditunjuk.</h6>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comment Summary Section -->
            <div class="card border-0 shadow-sm mb-4 mt-4">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-chat-quote me-2 text-info"></i>Rekapitulasi Komentar</span>
                </div>
                <div class="card-body">
                    @php
                        $hasComments = false;
                        foreach ($submission->assessments as $a) {
                            if ($a->is_submitted && ($a->comments || $a->strengths || $a->weaknesses)) {
                                $hasComments = true;
                                break;
                            }
                        }
                    @endphp

                    @if ($hasComments)
                        @foreach ($submission->assessments as $assessment)
                            @if ($assessment->is_submitted && ($assessment->comments || $assessment->strengths || $assessment->weaknesses))
                                <div class="mb-4 pb-3 border-bottom last-no-border">
                                    <h6 class="fw-bold">{{ $assessment->evaluator->name }} <span
                                            class="text-muted small fw-normal">({{ $assessment->getEvaluatorTypeLabel() }})</span>
                                    </h6>
                                    @if ($assessment->strengths)
                                        <div class="mb-2">
                                            <strong class="text-success small"><i
                                                    class="bi bi-plus-circle me-1"></i>Kelebihan:</strong>
                                            <p class="mb-1 small">{{ $assessment->strengths }}</p>
                                        </div>
                                    @endif
                                    @if ($assessment->weaknesses)
                                        <div class="mb-2">
                                            <strong class="text-danger small"><i
                                                    class="bi bi-dash-circle me-1"></i>Kekurangan:</strong>
                                            <p class="mb-1 small">{{ $assessment->weaknesses }}</p>
                                        </div>
                                    @endif
                                    @if ($assessment->comments)
                                        <div class="mb-0">
                                            <strong class="text-secondary small"><i
                                                    class="bi bi-chat-left-text me-1"></i>Catatan:</strong>
                                            <p class="mb-0 small">{{ $assessment->comments }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted text-center small my-3">Belum ada komentar dari dosen penilai.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
           
            @if ($submission->final_score)
                <div class="card border-0 shadow-sm bg-success-subtle mb-4">
                    <div class="card-body text-center">
                        <h6 class="text-success mb-1">Nilai Akhir</h6>
                        <h2 class="fw-bold text-success mb-0">{{ number_format($submission->final_score, 1) }}
                        </h2>
                    </div>
                </div>
            @endif

            @php
                $latestFile = $submission->getLatestFile();
            @endphp
            @if ($latestFile)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-file-earmark-check me-2 text-success"></i>Dokumen
                            Utama
                            (Terbaru)</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-pdf text-danger fs-2 me-3"></i>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-semibold">{{ $latestFile->file_name }}</p>
                                <small class="text-muted">{{ $latestFile->getFileTypeLabel() }} -
                                    {{ $latestFile->getFormattedFileSize() }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            @if (Str::endsWith(strtolower($latestFile->file_name), '.pdf'))
                                <a href="{{ route('files.preview', $latestFile) }}" target="_blank"
                                    class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </a>
                            @endif
                            <a href="{{ route('files.download', $latestFile) }}"
                                class="btn btn-outline-secondary flex-grow-1">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat
                        Aktivitas</span>
                </div>
                <div class="card-body p-0">
                    @php
                        $activities = $submission->getActivityLogs();
                    @endphp
                    @if ($activities->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($activities->take(8) as $activity)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small
                                                class="fw-semibold text-dark">{{ $activity->causer?->name ?? 'System' }}</small>
                                            <p class="mb-0 small text-muted">{{ $activity->description }}</p>
                                        </div>
                                        <small
                                            class="text-muted text-nowrap">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4 text-muted small">
                            Belum ada riwayat aktivitas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
