@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Penilaian</h1>
            <p class="text-muted small mb-0">{{ $assessment->thesisSubmission->student->name }} -
                {{ $assessment->thesisSubmission->title }}</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if (!$assessment->is_submitted)
                <a href="{{ route('dosen.assessments.edit', $assessment) }}" class="btn btn-warning me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
            <a href="{{ route('dosen.assessments.index') }}" class="btn btn-secondary">
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
                    <table class="table table-borderless">
                        <tr>
                            <th width="180">Mahasiswa</th>
                            <td>: <strong>{{ $assessment->thesisSubmission->student->name }}</strong>
                                ({{ $assessment->thesisSubmission->student->nim_nip }})</td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <td>: {{ $assessment->thesisSubmission->title }}</td>
                        </tr>
                        <tr>
                            <th>Bidang Penelitian</th>
                            <td>: {{ $assessment->thesisSubmission->research_field ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status Submission</th>
                            <td>: <span
                                    class="badge bg-{{ $assessment->thesisSubmission->getStatusBadgeClass() }}">{{ $assessment->thesisSubmission->getStatusLabel() }}</span>
                            </td>
                        </tr>
                    </table>

                    <hr>
                    <h6 class="fw-bold">Abstrak</h6>
                    <p class="text-muted" style="white-space: pre-line;">{{ $assessment->thesisSubmission->abstract }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Detail Penilaian</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Tipe Penilaian</p>
                            <p class="fw-semibold">{{ $assessment->getEvaluatorTypeLabel() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Status</p>
                            @if ($assessment->is_submitted)
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i
                                        class="bi bi-check-circle me-1"></i>Sudah Disubmit</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i
                                        class="bi bi-clock me-1"></i>Draft</span>
                            @endif
                        </div>
                    </div>

                    @if ($assessment->scores && $assessment->scores->count() > 0)
                        <hr>
                        <h6 class="fw-bold mb-3">Nilai per Kriteria</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th class="text-center" width="100">Bobot</th>
                                        <th class="text-center" width="100">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assessment->scores as $score)
                                        <tr>
                                            <td>{{ $score->criterion->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $score->criterion->weight ?? 0 }}%</td>
                                            <td class="text-center fw-bold">{{ $score->score }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($assessment->comments || $assessment->strengths || $assessment->weaknesses || $assessment->recommendations)
                        <hr>
                        <h6 class="fw-bold mb-3">Komentar & Feedback</h6>
                        @if ($assessment->comments)
                            <p class="mb-2"><strong>Komentar:</strong> {{ $assessment->comments }}</p>
                        @endif
                        @if ($assessment->strengths)
                            <p class="mb-2"><strong>Kelebihan:</strong> {{ $assessment->strengths }}</p>
                        @endif
                        @if ($assessment->weaknesses)
                            <p class="mb-2"><strong>Kelemahan:</strong> {{ $assessment->weaknesses }}</p>
                        @endif
                        @if ($assessment->recommendations)
                            <p class="mb-2"><strong>Rekomendasi:</strong> {{ $assessment->recommendations }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-body text-center">
                    <h6 class="mb-1 opacity-75">Total Nilai</h6>
                    <h1 class="mb-0 fw-bold">{{ number_format($assessment->total_score ?? 0, 1) }}</h1>
                </div>
            </div>

            @if (!$assessment->is_submitted)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-send me-2 text-success"></i>Submit Penilaian</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Setelah disubmit, penilaian tidak dapat diubah lagi.</p>
                        <form action="{{ route('dosen.assessments.submit', $assessment) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Apakah Anda yakin ingin submit penilaian ini? Penilaian yang sudah disubmit tidak dapat diubah.')">
                                <i class="bi bi-send me-1"></i> Submit Penilaian
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
