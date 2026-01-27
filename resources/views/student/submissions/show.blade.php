@extends('layouts.app ')

@section('title', 'Detail Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                @if($submission->canBeEditedByStudent())
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

    <div class="row">
        <div class="col-md-8">
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
                            <th>Pembimbing</th>
                            <td>: Dosen Pembimbing</td>
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
                            <td>: {{ $submission->submission_date?->format('d F Y') ?? '-' }}</td>
                        </tr>
                        @if($submission->defense_date)
                            <tr>
                                <th>Tanggal Sidang</th>
                                <td>: {{ $submission->defense_date->format('d F Y') }}</td>
                            </tr>
                        @endif
                        @if($submission->final_score)
                            <tr>
                                <th>Nilai Akhir</th>
                                <td>: <strong class="text-success">{{ $submission->final_score }}</strong></td>
                            </tr>
                        @endif
                    </table>

                    <hr>

                    <h6>Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>

                    @if($submission->notes)
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
                    @php $proposalFile = $submission->files->where('file_type', 'proposal')->first(); @endphp
                    @if($submission->files->count() > 0)
                        <div class="list-group mb-3">
                            @foreach($submission->files as $file)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-wrapper me-3">
                                            @if(str_contains($file->mime_type, 'pdf'))
                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                            @else
                                                <i class="bi bi-file-earmark-word-fill text-primary fs-4"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $file->file_name }}</h6>
                                            <small class="text-muted">
                                                {{ $file->getFileTypeLabel() }} • {{ $file->getFormattedFileSize() }} • {{ $file->created_at->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ Storage::url($file->file_path) }}" class="btn btn-sm btn-outline-primary"
                                            target="_blank">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                        <a href="{{ Storage::url($file->file_path) }}" class="btn btn-sm btn-outline-secondary" download>
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($proposalFile && str_contains($proposalFile->mime_type, 'pdf'))
                            <div class="pdf-preview-container mt-4">
                                <h6 class="mb-3"><i class="bi bi-eye"></i> Pratinjau Proposal (PDF)</h6>
                                <div class="ratio ratio-16x9 border rounded overflow-hidden shadow-sm" style="height: 600px;">
                                    <iframe src="{{ Storage::url($proposalFile->file_path) }}#toolbar=0" title="PDF Preview"></iframe>
                                </div>
                                <div class="mt-2 text-center">
                                    <small class="text-muted">Gunakan tombol 'Lihat' di atas jika pratinjau tidak muncul.</small>
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
            @if($submission->assessments->where('is_submitted', true)->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-clipboard-data"></i> Penilaian
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Penilai</th>
                                        <th>Tipe</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->assessments->where('is_submitted', true) as $assessment)
                                        <tr>
                                            <td>Penilai {{ $loop->iteration }}</td>
                                            <td>{{ $assessment->getEvaluatorTypeLabel() }}</td>
                                            <td><strong>{{ $assessment->total_score }}</strong></td>
                                            <td>{{ $assessment->submitted_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Status Timeline -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Riwayat Status
                </div>
                <div class="card-body">
                    @if($submission->statuses->count() > 0)
                        <div class="timeline">
                            @foreach($submission->statuses->sortByDesc('created_at') as $status)
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="bi bi-circle-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $status->new_status)) }}</strong><br>
                                        <small class="text-muted">
                                            {{ $status->created_at->format('d M Y H:i') }}<br>
                                            oleh {{ $status->changer->hasRole('mahasiswa') ? 'Mahasiswa' : 'Dosen/Admin' }}
                                        </small>
                                        @if($status->comment)
                                            <p class="mt-1 mb-0 small">{{ $status->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada riwayat status.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection