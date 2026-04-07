@extends('layouts.app')

@section('title', 'Buat Penilaian Baru')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Buat Penilaian Baru</h1>
            <p class="text-muted small mb-0">
                <span class="fw-bold">{{ $submission->student->name }}</span> - {{ $submission->title }}
            </p>
            <p class="text-muted extra-small mb-0">
                <i class="bi bi-clock me-1"></i>Diajukan pada: {{ $submission->created_at->format('d M Y H:i') }}
            </p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            @php
                $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('dosen.assessments.index');
            @endphp
            <a href="{{ $backUrl }}" class="btn btn-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @php
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

        {{-- Right Column: Assessment Form --}}
        <div class="col-lg-5">
            <form action="{{ route('dosen.assessments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="thesis_submission_id" value="{{ $submission->id }}">
                <input type="hidden" name="rubric_id" value="{{ $rubric->id }}">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Penilaian</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $rubric->name }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body px-0 py-2">
                        @if ($rubric && count($rubric->criteria) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small">
                                            <th class="ps-3">Kriteria</th>
                                            <th class="text-center" width="70">Bobot</th>
                                            <th class="text-center" width="90">Nilai</th>
                                            <th class="text-center" width="80">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rubric->criteria as $criterion)
                                            @php
                                                $id = $criterion['id'] ?? $loop->index;
                                                $name = $criterion['name'] ?? 'Kriteria ' . $loop->iteration;
                                                $desc = $criterion['description'] ?? '';
                                                $weight =
                                                    $criterion['weight'] ?? ($criterion['weight_percentage'] ?? 0);
                                                $oldScore = old('scores.' . $id);
                                                $oldContribution = $oldScore ? ($oldScore * $weight) / 100 : 0;
                                            @endphp
                                            <tr class="criterion-row" data-weight="{{ $weight }}">
                                                <td class="ps-3">
                                                    <div class="fw-semibold small">{{ $name }}</div>
                                                    @if ($desc)
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            {{ $desc }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center small">{{ $weight }}%</td>
                                                <td class="text-center">
                                                    <input type="number" name="scores[{{ $id }}]"
                                                        class="form-control form-control-sm text-center score-input"
                                                        min="0" max="100" step="0.1"
                                                        value="{{ $oldScore }}" required>
                                                </td>
                                                <td class="text-center small fw-bold contribution-cell">
                                                    {{ number_format($oldContribution, 1) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold ps-3">Total Nilai</td>
                                            <td class="text-center fw-bold text-primary" id="total-score-display">
                                                {{ number_format(collect($rubric->criteria)->map(fn($c, $i) => (old('scores.' . ($c['id'] ?? $i), 0) * ($c['weight'] ?? ($c['weight_percentage'] ?? 0))) / 100)->sum(), 1) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="px-3 py-2">
                                <div class="alert alert-warning mb-0 small">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Rubrik tidak valid.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-search me-2 text-warning"></i>Judul Serupa</span>
                    </div>
                    <div class="card-body px-0 py-2">
                        @if ($similarSubmissions->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($similarSubmissions as $similar)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 small fw-bold">{{ $similar->title }}</h6>
                                            <span
                                                class="badge {{ $similar->similarity_percentage >= 70 ? 'bg-danger' : ($similar->similarity_percentage >= 40 ? 'bg-warning text-dark' : 'bg-success') }}">
                                                {{ $similar->similarity_percentage }}% Mirip
                                            </span>
                                        </div>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                                            Oleh: {{ $similar->student->name }} |
                                            <i class="bi bi-clock-history me-1"></i>{{ \Carbon\Carbon::parse($similar->created_at)->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-3 py-2">
                                <p class="text-muted small mb-0">Tidak ditemukan judul yang mirip.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-chat-left-text me-2 text-info"></i>Feedback</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small">Komentar Umum</label>
                            <textarea name="comments" class="form-control form-control-sm" rows="3"
                                placeholder="Tuliskan komentar umum...">{{ old('comments') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-success">Kelebihan</label>
                            <textarea name="strengths" class="form-control form-control-sm" rows="2" placeholder="Tuliskan kelebihan...">{{ old('strengths') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-danger">Kelemahan</label>
                            <textarea name="weaknesses" class="form-control form-control-sm" rows="2" placeholder="Tuliskan kelemahan...">{{ old('weaknesses') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-primary">Rekomendasi</label>
                            <textarea name="recommendations" class="form-control form-control-sm" rows="2"
                                placeholder="Tuliskan rekomendasi...">{{ old('recommendations') }}</textarea>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-save me-1"></i> Simpan Draft Penilaian
                            </button>
                            <a href="{{ $backUrl }}"
                                class="btn btn-outline-secondary w-100 shadow-none">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scoreInputs = document.querySelectorAll('.score-input');
            const totalDisplay = document.getElementById('total-score-display');

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.criterion-row').forEach(row => {
                    const weight = parseFloat(row.dataset.weight) || 0;
                    const score = parseFloat(row.querySelector('.score-input').value) || 0;
                    total += (score * weight) / 100;
                });
                totalDisplay.textContent = total.toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                });
            }

            scoreInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('.criterion-row');
                    const weight = parseFloat(row.dataset.weight) || 0;
                    const score = parseFloat(this.value) || 0;
                    const contribution = (score * weight) / 100;

                    const contributionCell = row.querySelector('.contribution-cell');
                    contributionCell.textContent = contribution.toLocaleString('id-ID', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 1
                    });

                    updateTotal();
                });
            });
        });
    </script>
@endpush
