@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $submission->title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Detail Pengajuan</h1>
            <p class="text-muted small mb-0">{{ $submission->student->name }} ({{ $submission->student->nim_nip }})</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0 align-items-center">
            @include('partials.record-navigation', ['route' => 'kaprodi.submissions.show'])
            @php
                $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('kaprodi.submissions.index');
            @endphp
            <a href="{{ $backUrl }}"
                class="btn btn-outline-secondary shadow-none">
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


                    @endif
                </div>
            </div>

            <div class="card border-warning shadow-sm mb-4" id="similarity-analysis-card">
                <div class="card-header bg-warning-subtle text-warning-emphasis py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-search me-2"></i>Analisis Kesamaan Judul (Orisinalitas)</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Ditemukan beberapa pengajuan dengan judul yang serupa dalam sistem.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Judul Pengajuan</th>
                                    <th>Mahasiswa</th>
                                    <th class="text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody id="similarity-results-body">
                                <!-- Results injected here -->
                            </tbody>
                        </table>
                    </div>
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
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submission->assessments as $assessment)
                                        <tr>
                                            <td class="ps-3 align-middle">
                                                <div class="fw-bold">{{ $assessment->evaluator->name ?? 'N/A' }}</div>
                                                <span class="badge bg-secondary text-white smaller-extra">{{ $assessment->getAnonymousLabel() }}</span>
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
                                            <td class="align-middle">
                                                @if ($assessment->is_submitted)
                                                    <a href="{{ route('kaprodi.assessments.show', $assessment->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i> Detail
                                                    </a>
                                                @else
                                                    <span class="text-muted small fs-7 fst-italic">Menunggu
                                                        penilaian...</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($assessment->is_submitted)
                                            <tr>
                                                <td colspan="4" class="bg-light p-3 border-bottom">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-header bg-white border-bottom py-2">
                                                            <small class="fw-bold text-uppercase text-muted">Detail Rubrik
                                                                Penilaian</small>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <table class="table table-sm table-striped mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="ps-3">Kriteria</th>
                                                                        <th class="text-center" width="10%">Bobot</th>
                                                                        <th class="text-center" width="10%">Nilai</th>
                                                                        <th class="text-center" width="15%">Kontribusi
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($assessment->scores as $score)
                                                                        @php
                                                                            $contribution =
                                                                                ($score->score * $score->weight) / 100;
                                                                        @endphp
                                                                        <tr>
                                                                            <td class="ps-3">
                                                                                <span
                                                                                    class="fw-semibold">{{ $score->criterion_name }}</span>
                                                                                @if ($score->criterion_description)
                                                                                    <div class="text-muted small fst-italic"
                                                                                        style="font-size: 0.8rem;">
                                                                                        {{ Str::limit($score->criterion_description, 100) }}
                                                                                    </div>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ number_format($score->weight, 0) }}%
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ number_format($score->score, 1) }}
                                                                            </td>
                                                                            <td class="text-center fw-bold">
                                                                                {{ number_format($contribution, 1) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
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
            <div class="card border-0 shadow-sm mb-4">
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
                                    <h6 class="fw-bold">{{ $assessment->evaluator->name }}</h6>
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-person-plus me-2 text-primary"></i>
                        @if (
                            $submission->status === 'under_review' &&
                                $submission->assessments->count() > 0 &&
                                $submission->assessments->where('is_submitted', false)->count() === 0)
                            Atur Dosen Pembimbing
                        @else
                            Atur Dosen Penilai
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    @if ($submission->status === 'submitted')
                        <form action="{{ route('kaprodi.submissions.assign-lecturers', $submission->id) }}"
                            method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan? \n\nPERINGATAN: Setelah disimpan, Anda TIDAK DAPAT MENGUBAH dosen penilai lagi.')">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Pilih Rubrik Penilaian</label>
                                <select name="rubric_id" class="form-select @error('rubric_id') is-invalid @enderror"
                                    required>
                                    <option value="" disabled selected>Pilih Rubrik...</option>
                                    @foreach ($rubrics as $rubric)
                                        <option value="{{ $rubric->id }}"
                                            {{ $submission->rubric_id == $rubric->id ? 'selected' : '' }}>
                                            {{ $rubric->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rubric_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Dosen Penilai</label>
                                <select id="assessor-select" name="assessor_ids[]" multiple required
                                    placeholder="Pilih dosen penilai...">
                                    @php
                                        $currentAssessorIds = $submission->assessments
                                            ->pluck('evaluator_id')
                                            ->toArray();
                                    @endphp
                                    @foreach ($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}"
                                            {{ in_array($lecturer->id, $currentAssessorIds) ? 'selected' : '' }}>
                                            {{ $lecturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-danger mt-2">
                                    <i class="bi bi-exclamation-triangle"></i> Pastikan pilihan Anda sudah benar.
                                    Data
                                    tidak bisa diubah setelah disimpan.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </form>
                    @elseif ($submission->status === 'under_review')
                        @php
                            $allAssessed =
                                $submission->assessments->count() > 0 &&
                                $submission->assessments->where('is_submitted', false)->count() === 0;
                        @endphp
                        @if ($allAssessed)
                            <div class="alert alert-success mb-3">
                                <i class="bi bi-check-circle me-1"></i> Semua dosen penilai telah memberikan nilai.
                                Anda
                                sekarang dapat menetapkan dosen pembimbing dan menerima pengajuan ini.
                            </div>
                            <form action="{{ route('kaprodi.submissions.accept', $submission->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menerima pengajuan ini dan menetapkan dosen pembimbing?')">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold">Pembimbing 1</label>
                                    <select name="supervisor_id"
                                        class="form-select @error('supervisor_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>Pilih Pembimbing 1...</option>
                                        @foreach ($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}"
                                                {{ $submission->supervisor_id == $lecturer->id ? 'selected' : '' }}
                                                {{ $submission->supervisor_2_id == $lecturer->id ? 'disabled' : '' }}>
                                                {{ $lecturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold">Pembimbing 2</label>
                                    <select name="supervisor_2_id"
                                        class="form-select @error('supervisor_2_id') is-invalid @enderror">
                                        <option value="">Pilih Pembimbing 2 (Opsional)...</option>
                                        @foreach ($lecturers as $lecturer)
                                            <option value="{{ $lecturer->id }}"
                                                {{ $submission->supervisor_2_id == $lecturer->id ? 'selected' : '' }}
                                                {{ $submission->supervisor_id == $lecturer->id ? 'disabled' : '' }}>
                                                {{ $lecturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_2_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pembimbing 2 bersifat opsional.</small>
                                </div>

                                <div class="d-flex gap-2 mb-3">
                                    <button type="submit" class="btn btn-success flex-grow-1">
                                        <i class="bi bi-check-circle me-1"></i> Terima Pengajuan & Tetapkan Pembimbing
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="bi bi-x-circle me-1"></i> Tolak
                                    </button>
                                </div>
                            </form>

                            <h6 class="fw-bold mt-4 border-bottom pb-2">Daftar Penilai</h6>
                            <ul class="list-group list-group-flush mt-2">
                                @foreach ($submission->assessments as $assessment)
                                    <li class="list-group-item bg-transparent px-0 border-0 py-1">
                                        <i class="bi bi-person-check text-success me-2"></i>
                                        <span class="small">{{ $assessment->evaluator->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info mb-3 alert-persistent">
                                <i class="bi bi-info-circle me-1"></i> Dosen penilai sedang melakukan penilaian. Anda
                                dapat
                                menetapkan dosen pembimbing setelah semua nilai terkumpul.
                            </div>

                            <h6 class="fw-bold mt-4 border-bottom pb-2">Daftar Penilai</h6>
                            <ul class="list-group list-group-flush mt-2">
                                @foreach ($submission->assessments as $assessment)
                                    <li class="list-group-item bg-transparent px-0 border-0 py-1">
                                        <i
                                            class="bi {{ $assessment->is_submitted ? 'bi-person-check-fill text-success' : 'bi-hourglass-split text-warning' }} me-2"></i>
                                        <span class="small">{{ $assessment->evaluator->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @elseif (in_array($submission->status, ['completed', 'cancelled', 'approved', 'rejected']))
                        <div class="alert alert-info mb-0 alert-persistent">
                            <i class="bi bi-info-circle me-1"></i> Dosen penilai dan dosen pembimbing sudah ditetapkan.
                            Pengajuan ini telah diproses ({{ $submission->getStatusLabel() }}).
                        </div>

                        <h6 class="fw-bold mt-4 border-bottom pb-2">Program Studi</h6>
                        <p class="small mb-0">{{ $submission->student->programStudi->name ?? 'N/A' }}</p>

                        <h6 class="fw-bold mt-3 border-bottom pb-2">Dosen Pembimbing</h6>
                        <ul class="list-group list-group-flush">
                            @if ($submission->supervisor)
                                <li class="list-group-item bg-transparent px-0 border-0 py-1">
                                    <i class="bi bi-person-check-fill text-primary me-2"></i>
                                    <span class="small fw-bold">Pembimbing 1:</span>
                                    <span class="small d-block ms-4">{{ $submission->supervisor->name }}</span>
                                </li>
                            @endif
                            @if ($submission->supervisor_2_id)
                                <li class="list-group-item bg-transparent px-0 border-0 py-1">
                                    <i class="bi bi-person-check text-primary me-2"></i>
                                    <span class="small fw-bold">Pembimbing 2:</span>
                                    <span class="small d-block ms-4">{{ $submission->supervisor2->name ?? 'N/A' }}</span>
                                </li>
                            @endif
                        </ul>

                        <h6 class="fw-bold mt-3 border-bottom pb-2">Dosen Penilai</h6>
                        <ul class="list-group list-group-flush mt-2">
                            @foreach ($submission->assessments as $assessment)
                                <li class="list-group-item bg-transparent px-0 border-0 py-1">
                                    <i class="bi bi-person-check text-success me-2"></i>
                                    <span class="small">{{ $assessment->evaluator->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-hourglass-top text-muted fs-2 d-block mb-3"></i>
                            <p class="text-muted small mb-0">Dosen penilai dapat diatur setelah mahasiswa mengajukan
                                (submit)
                                pengajuan ini.</p>
                            <span class="badge bg-secondary mt-2">{{ $submission->getStatusLabel() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modals for Rejection -->
            @if ($submission->status === 'under_review')
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('kaprodi.submissions.reject', $submission->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <p class="small text-muted mb-3">Tuliskan alasan penolakan pengajuan proposal skripsi
                                        ini.</p>
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label fw-semibold">Alasan
                                            Penolakan</label>
                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required
                                            placeholder="Jelaskan alasan pengajuan ditolak..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary shadow-none"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                </div>
                            </form>
                        </div>
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




        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#assessor-select', {
                    plugins: ['remove_button'],
                    persist: false,
                    create: false,
                });

                // Prevent selecting same supervisor
                const supervisor1 = document.querySelector('select[name="supervisor_id"]');
                const supervisor2 = document.querySelector('select[name="supervisor_2_id"]');

                if (supervisor1 && supervisor2) {
                    const updateOptions = () => {
                        const val1 = supervisor1.value;
                        const val2 = supervisor2.value;

                        Array.from(supervisor2.options).forEach(opt => {
                            opt.disabled = opt.value && opt.value === val1;
                        });

                        Array.from(supervisor1.options).forEach(opt => {
                            opt.disabled = opt.value && opt.value === val2;
                        });
                    };

                    supervisor1.addEventListener('change', updateOptions);
                    supervisor2.addEventListener('change', updateOptions);
                    updateOptions();
                }

                // Similarity Analysis for Kaprodi
                const title = "{{ $submission->title }}";
                const excludeId = "{{ $submission->id }}";
                const resultsBody = document.getElementById('similarity-results-body');
                const card = document.getElementById('similarity-analysis-card');

                fetch('{{ route('similarity.check') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: title,
                            exclude_id: excludeId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            resultsBody.innerHTML = '';
                            data.data.forEach(item => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                            <td><span class="fw-medium">${item.title}</span> <span class="badge bg-secondary ms-1 small" style="font-size: 0.6rem;">${item.status}</span></td>
                            <td><small>${item.student.name}</small></td>
                            <td class="text-center"><span class="badge bg-warning text-dark">${item.similarity_percentage}%</span></td>
                        `;
                                resultsBody.appendChild(tr);
                            });
                            card.style.display = 'block';
                        } else {
                            resultsBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted small py-3">Tidak ada judul yang mirip ditemukan.</td></tr>';
                            card.style.display = 'block';
                        }
                    })
                    .catch(error => console.error('Error fetching similarity data:', error));
            });
        </script>
    @endpush
