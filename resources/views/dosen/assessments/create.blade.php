@extends('layouts.app')

@section('title', 'Buat Penilaian Baru')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Buat Penilaian Baru</h1>
            <p class="text-muted small mb-0">{{ $submission->student->name }} - {{ $submission->title }}</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('dosen.submissions.show', $submission) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('dosen.assessments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="thesis_submission_id" value="{{ $submission->id }}">

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
                                <td>: <strong>{{ $submission->student->name }}</strong>
                                    ({{ $submission->student->nim_nip }})</td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>: {{ $submission->title }}</td>
                            </tr>
                            <tr>
                                <th>Bidang Penelitian</th>
                                <td>: {{ $submission->research_field ?? '-' }}</td>
                            </tr>
                        </table>

                        @php
                            $latestFile = $submission->getLatestFile();
                        @endphp
                        @if ($latestFile)
                            <hr>
                            <h6 class="fw-bold mb-2">Dokumen Utama</h6>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-pdf text-danger fs-3 me-2"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-semibold">{{ $latestFile->file_name }}</p>
                                    <small class="text-muted">{{ $latestFile->getFormattedFileSize() }}</small>
                                </div>
                                <a href="{{ route('files.preview', $latestFile) }}" target="_blank"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                                <a href="{{ route('files.download', $latestFile) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Evaluator Role Selection --}}
                @if(auth()->id() == $submission->supervisor_id)
                    <input type="hidden" name="evaluator_type" value="supervisor">
                @else
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-0">
                            <span class="fw-bold"><i class="bi bi-person-badge me-2 text-warning"></i>Peran Penilai</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Anda menilai sebagai:</label>
                                <select name="evaluator_type" class="form-select" required>
                                    <option value="" disabled selected>Pilih Peran...</option>
                                    <option value="examiner_1">Penguji 1</option>
                                    <option value="examiner_2">Penguji 2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-clipboard-check me-2 text-success"></i>Penilaian</span>
                    </div>
                    <div class="card-body">
                        @if ($rubric && count($rubric->criteria) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kriteria</th>
                                            <th class="text-center" width="100">Bobot</th>
                                            <th class="text-center" width="150">Nilai (0-100)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rubric->criteria as $criterion)
                                            @php
                                                // Handle array access because of JSON cast
                                                $id = $criterion['id'] ?? $loop->index;
                                                $name = $criterion['name'] ?? 'Kriteria ' . ($loop->iteration);
                                                $desc = $criterion['description'] ?? '';
                                                $weight = $criterion['weight'] ?? $criterion['weight_percentage'] ?? 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $name }}</strong>
                                                    @if ($desc)
                                                        <br><small class="text-muted">{{ $desc }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $weight }}%</td>
                                                <td class="text-center">
                                                    <input type="number" name="scores[{{ $id }}]"
                                                        class="form-control text-center" min="0" max="100"
                                                        step="0.1"
                                                        value="{{ old('scores.' . $id) }}"
                                                        required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i> Belum ada rubrik penilaian yang aktif.
                                Hubungi Kaprodi untuk mengatur rubrik.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-chat-left-text me-2 text-info"></i>Komentar & Feedback</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Komentar Umum</label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="Tuliskan komentar umum...">{{ old('comments') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelebihan</label>
                            <textarea name="strengths" class="form-control" rows="2" placeholder="Tuliskan kelebihan dari pengajuan...">{{ old('strengths') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelemahan</label>
                            <textarea name="weaknesses" class="form-control" rows="2" placeholder="Tuliskan kelemahan atau area perbaikan...">{{ old('weaknesses') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rekomendasi</label>
                            <textarea name="recommendations" class="form-control" rows="2"
                                placeholder="Tuliskan rekomendasi untuk perbaikan...">{{ old('recommendations') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-white py-3 border-0">
                        <span class="fw-bold"><i class="bi bi-save me-2 text-primary"></i>Simpan</span>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Simpan sebagai draft terlebih dahulu. Anda bisa submit setelah yakin
                            dengan penilaian.</p>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-1"></i> Simpan Draft
                        </button>
                        <a href="{{ route('dosen.submissions.show', $submission) }}"
                            class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
