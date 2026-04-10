@extends('layouts.app')

@section('title', 'Detail Penilaian')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Penilaian</h1>
            <p class="text-muted small mb-0">
                <span class="fw-bold">{{ $assessment->thesisSubmission->student->name }}</span> - {{ $assessment->thesisSubmission->title }}
            </p>
            <p class="text-muted extra-small mb-0">
                <i class="bi bi-clock me-1"></i>Diajukan pada: {{ $assessment->thesisSubmission->created_at->format('d M Y H:i') }}
            </p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if (!$assessment->is_submitted)
                <a href="{{ route('dosen.assessments.edit', $assessment) }}" class="btn btn-warning me-2 shadow-none ripple text-dark">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
            @endif
            @php
                $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('dosen.assessments.index');
            @endphp
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @php
        $submission = $assessment->thesisSubmission;
        $latestFile = $submission->getLatestFile();
    @endphp

    <div class="row g-4">
        {{-- Left Column: PDF Preview --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm sticky-top" style="top: 85px; height: calc(100vh - 120px);">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Pratinjau Dokumen</span>
                    @if ($latestFile)
                        <a href="{{ route('files.download', $latestFile) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-0 h-100">
                    @if ($latestFile && Str::endsWith(strtolower($latestFile->file_name), '.pdf'))
                        <iframe src="{{ route('files.preview', $latestFile) }}#toolbar=0" width="100%" height="100%"
                            style="border: none;"></iframe>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                            <i class="bi bi-file-earmark-restricted fs-1 mb-2"></i>
                            <p>Pratinjau tidak tersedia untuk format file ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Assessment Detail --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-body text-center">
                    <h6 class="mb-1 opacity-75 small">Total Nilai</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($assessment->total_score ?? 0, 1) }}</h2>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Detail
                            Penilaian</span>
                    </div>
                </div>
                <div class="card-body px-0 py-2">
                    @if ($assessment->scores && $assessment->scores->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small">
                                        <th class="ps-3">Kriteria</th>
                                        <th class="text-center" width="70">Bobot</th>
                                        <th class="text-center" width="70">Nilai</th>
                                        <th class="text-center" width="80">Kontribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assessment->scores as $score)
                                        @php
                                            $contribution = ($score->score * $score->weight) / 100;
                                        @endphp
                                        <tr class="small">
                                            <td class="ps-3">
                                                <div class="fw-semibold">{{ $score->criterion_name }}</div>
                                                @if($score->criterion_description)
                                                    <div class="text-muted extra-small">
                                                        {{ Str::limit($score->criterion_description, 60) }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ number_format($score->weight, 0) }}%</td>
                                            <td class="text-center">{{ number_format($score->score, 1) }}</td>
                                            <td class="text-center fw-bold">{{ number_format($contribution, 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-3 py-2">
                            <div class="alert alert-warning mb-0 small alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i> Data skor tidak tersedia.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @include('partials.similarity', ['isDetailView' => true, 'excludeId' => $submission->id])

            @if ($assessment->comments || $assessment->strengths || $assessment->weaknesses || $assessment->recommendations)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-chat-left-text me-2 text-info"></i>Feedback</span>
                    </div>
                    <div class="card-body py-2">
                        @if ($assessment->comments)
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Komentar Umum</label>
                                <p class="small mb-0">{{ $assessment->comments }}</p>
                            </div>
                        @endif
                        @if ($assessment->strengths)
                            <div class="mb-3">
                                <label class="form-label small text-success mb-1">Kelebihan</label>
                                <p class="small mb-0">{{ $assessment->strengths }}</p>
                            </div>
                        @endif
                        @if ($assessment->weaknesses)
                            <div class="mb-3">
                                <label class="form-label small text-danger mb-1">Kelemahan</label>
                                <p class="small mb-0">{{ $assessment->weaknesses }}</p>
                            </div>
                        @endif
                        @if ($assessment->recommendations)
                            <div class="mb-2">
                                <label class="form-label small text-primary mb-1">Rekomendasi</label>
                                <p class="small mb-0">{{ $assessment->recommendations }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (!$assessment->is_submitted)
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-body">
                        <h6 class="fw-bold text-success mb-2">Finalisasi Penilaian</h6>
                        <p class="small text-muted mb-3">Setelah disubmit, penilaian tidak dapat diubah lagi.</p>
                        <form action="{{ route('dosen.assessments.submit', $assessment) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Apakah Anda yakin ingin submit penilaian ini? Penilaian yang sudah disubmit tidak dapat diubah.')">
                                <i class="bi bi-send-fill me-1"></i> Kirim Penilaian Final
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection