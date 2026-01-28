@extends('layouts.app')

@section('title', 'Detail Penilaian - ' . $assessment->evaluator->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Penilaian</h1>
            <p class="text-muted small mb-0">
                Mahasiswa: {{ $assessment->thesisSubmission->student->name }}
                ({{ $assessment->thesisSubmission->student->nim_nip }})
                | Dosen Penilai: {{ $assessment->evaluator->name }}
            </p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.submissions.show', $assessment->thesis_submission_id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Pengajuan
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-list-ul me-2 text-primary"></i>Rincian Penilaian</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded text-center">
                        <div>
                            <small class="text-muted d-block text-uppercase">Total Nilai</small>
                            <h2 class="fw-bold text-success mb-0">{{ number_format($assessment->total_score, 1) }}</h2>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block text-uppercase">Status</small>
                            @if ($assessment->is_submitted)
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-warning">Belum Selesai</span>
                            @endif
                        </div>
                    </div>

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria Penilaian</th>
                                <th class="text-center" width="100">Bobot</th>
                                <th class="text-center" width="100">Nilai (0-100)</th>
                                <th class="text-center" width="120">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Reconstruct criteria from snapshot
                                $criteriaSnapshot = collect($assessment->rubric_snapshot ?? []);
                            @endphp

                            @foreach ($criteriaSnapshot as $index => $criterionData)
                                @php
                                    $cData = (object) $criterionData;
                                    // Use ID if available, otherwise use index (0, 1, 2...)
                                    $cId = $cData->id ?? $index;

                                    $scoreItem = $assessment->scores->where('criterion_id', $cId)->first();
                                    $scoreVal = $scoreItem ? $scoreItem->score : 0;
                                    $weight = $cData->weight ?? 0;
                                    $contribution = ($scoreVal * $weight) / 100;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $cData->name }}</div>
                                        <small class="text-muted">{{ $cData->description ?? '' }}</small>
                                    </td>
                                    <td class="text-center">{{ $weight }}%</td>
                                    <td class="text-center fw-bold">{{ number_format($scoreVal, 1) }}</td>
                                    <td class="text-center fw-semibold">{{ number_format($contribution, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Nilai Akhir</td>
                                <td class="text-center fw-bold fs-5 text-success">
                                    {{ number_format($assessment->total_score, 1) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if ($assessment->comments || $assessment->strengths || $assessment->weaknesses)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-chat-left-text me-2 text-info"></i>Komentar & Masukan</span>
                    </div>
                    <div class="card-body">
                        @if ($assessment->strengths)
                            <div class="mb-4">
                                <h6 class="fw-bold text-success"><i class="bi bi-plus-circle me-2"></i>Kelebihan</h6>
                                <div class="p-3 bg-success-subtle rounded text-dark">
                                    {{ $assessment->strengths }}
                                </div>
                            </div>
                        @endif

                        @if ($assessment->weaknesses)
                            <div class="mb-4">
                                <h6 class="fw-bold text-danger"><i class="bi bi-dash-circle me-2"></i>Kekurangan & Saran
                                </h6>
                                <div class="p-3 bg-danger-subtle rounded text-dark">
                                    {{ $assessment->weaknesses }}
                                </div>
                            </div>
                        @endif

                        @if ($assessment->comments)
                            <div class="mb-0">
                                <h6 class="fw-bold text-secondary"><i class="bi bi-chat-square-text me-2"></i>Komentar Umum
                                </h6>
                                <div class="p-3 bg-light rounded text-dark border">
                                    {{ $assessment->comments }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @php
                $latestFile = $assessment->submission->getLatestFile();
            @endphp
            @if ($latestFile)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-file-earmark-check me-2 text-success"></i>File yang
                            Dinilai</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-file-earmark-pdf text-danger fs-1 me-3"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 fw-semibold text-truncate" title="{{ $latestFile->file_name }}">
                                    {{ $latestFile->file_name }}</p>
                                <small class="text-muted">{{ $latestFile->getFileTypeLabel() }} -
                                    {{ $latestFile->getFormattedFileSize() }}</small>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            @if (Str::endsWith(strtolower($latestFile->file_name), '.pdf'))
                                <a href="{{ route('files.preview', $latestFile) }}" target="_blank"
                                    class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i> Preview File
                                </a>
                            @endif
                            <a href="{{ route('files.download', $latestFile) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-download me-1"></i> Download File
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
