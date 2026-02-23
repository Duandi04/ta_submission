@extends('layouts.app')

@section('title', 'Detail Pengajuan - Supervisor')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Review Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('dosen.submissions.index', request()->query()) }}"
                class="btn btn-outline-secondary shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-file-text"></i> Informasi Pengajuan
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Mahasiswa</th>
                            <td>: <strong>{{ $submission->student->name }}</strong> ({{ $submission->student->nim_nip }})
                            </td>
                        </tr>
                        <tr>
                            <th>Pembimbing 1</th>
                            <td>: {{ $submission->supervisor->name ?? '-' }}</td>
                        </tr>
                        @if($submission->supervisor_2_id)
                        <tr>
                            <th>Pembimbing 2</th>
                            <td>: {{ $submission->supervisor2->name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Judul</th>
                            <td>: <strong>{{ $submission->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Bidang Penelitian</th>
                            <td>: {{ $submission->research_field ?? '-' }}</td>
                        </tr>
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
                    </table>

                    <hr>

                    <h6><i class="bi bi-file-text"></i> Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>

                    @endif
                </div>
            </div>

            <div class="card mb-3 border-warning shadow-sm" id="similarity-analysis-card" style="display: none;">
                <div class="card-header bg-warning text-dark py-2">
                    <i class="bi bi-search me-2"></i><strong>Analisis Kesamaan Judul (Orisinalitas)</strong>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">Sistem menemukan pengajuan lain dengan judul serupa. Gunakan data ini untuk mengevaluasi keaslian topik.</p>
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

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-paperclip"></i> File Pengajuan
                </div>
                <div class="card-body">
                    @if ($submission->files->count() > 0)
                        @php
                            $proposalFiles = $submission->files->where('file_type', 'proposal');
                            $revisionFiles = $submission->files->where('file_type', 'revision');
                        @endphp

                        @if ($proposalFiles->count() > 0)
                            <p class="small text-muted mb-1"><strong>Proposal</strong></p>
                            <div class="list-group mb-3">
                                @foreach ($proposalFiles as $file)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-danger fs-5 me-2"></i>
                                            <strong>{{ $file->file_name }}</strong>
                                            <br>
                                            <small class="text-muted ms-4">{{ $file->getFormattedFileSize() }} -
                                                {{ $file->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div>
                                            @if (Str::endsWith(strtolower($file->file_name), '.pdf'))
                                                <a href="{{ route('files.preview', $file) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary me-1" title="Preview PDF">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-primary"
                                                title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($revisionFiles->count() > 0)
                            <p class="small text-muted mb-1"><strong>Revisi</strong></p>
                            <div class="list-group">
                                @foreach ($revisionFiles as $file)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-warning fs-5 me-2"></i>
                                            <strong>{{ $file->file_name }}</strong>
                                            <br>
                                            <small class="text-muted ms-4">{{ $file->getFormattedFileSize() }} -
                                                {{ $file->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div>
                                            @if (Str::endsWith(strtolower($file->file_name), '.pdf'))
                                                <a href="{{ route('files.preview', $file) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary me-1" title="Preview PDF">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-primary"
                                                title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center mb-0">Belum ada file terlampir.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-check-circle"></i> Tindakan
                </div>
                <div class="card-body">
                    <p class="small text-muted">Berikan feedback untuk mahasiswa Anda:</p>

                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ubah Status</label>
                            <select class="form-select" name="status">
                                <option value="under_review">Sedang Direview</option>
                                <option value="revision_required">Perlu Revisi</option>
                                <option value="approved">Disetujui</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="4"
                                placeholder="Berikan catatan atau feedback..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Riwayat Status
                </div>
                <div class="card-body">
                    @if ($submission->statuses->count() > 0)
                        <div class="timeline">
                            @foreach ($submission->statuses->sortByDesc('created_at')->take(5) as $status)
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="bi bi-circle-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $status->new_status)) }}</strong><br>
                                        <small class="text-muted">
                                            {{ $status->created_at->format('d/m/Y H:i') }}<br>
                                            oleh {{ $status->changer->name }}
                                        </small>
                                        @if ($status->comment)
                                            <p class="mt-1 mb-0 small">{{ $status->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center small mb-0">Belum ada riwayat.</p>
                    @endif
                </div>
            </div>

            @php
                $latestFile = $submission->getLatestFile();
            @endphp
            @if ($latestFile)
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-file-earmark-check"></i> Dokumen Utama (Terbaru)
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
                            <a href="{{ route('files.download', $latestFile) }}" class="btn btn-outline-secondary flex-grow-1">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-activity"></i> Riwayat Aktivitas Mahasiswa
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
                                            <small class="fw-semibold text-dark">{{ $activity->causer?->name ?? 'System' }}</small>
                                            <p class="mb-0 small text-muted">{{ $activity->description }}</p>
                                        </div>
                                        <small class="text-muted text-nowrap">{{ $activity->created_at->diffForHumans() }}</small>
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