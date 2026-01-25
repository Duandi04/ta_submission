@extends('layouts.app')

@section('title', 'Detail Pengajuan - Supervisor')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Review Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('supervisor.submissions.index') }}" class="btn btn-secondary">
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
                            <td>: {{ $submission->submission_date?->format('d F Y') ?? '-' }}</td>
                        </tr>
                    </table>

                    <hr>

                    <h6><i class="bi bi-file-text"></i> Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>

                    @if($submission->notes)
                        <div class="alert alert-info">
                            <strong><i class="bi bi-sticky"></i> Catatan:</strong><br>
                            {{ $submission->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-paperclip"></i> File Pengajuan
                </div>
                <div class="card-body">
                    @if($submission->files->count() > 0)
                        <div class="list-group">
                            @foreach($submission->files as $file)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                            <strong class="ms-2">{{ $file->file_name }}</strong><br>
                                            <small class="text-muted ms-5">
                                                {{ $file->getFileTypeLabel() }} -
                                                {{ $file->getFormattedFileSize() }} -
                                                {{ $file->created_at->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                        <a href="/storage/{{ $file->file_path }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                    @if($submission->statuses->count() > 0)
                        <div class="timeline">
                            @foreach($submission->statuses->sortByDesc('created_at')->take(5) as $status)
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="bi bi-circle-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $status->new_status)) }}</strong><br>
                                        <small class="text-muted">
                                            {{ $status->created_at->format('d M Y H:i') }}<br>
                                            oleh {{ $status->changer->name }}
                                        </small>
                                        @if($status->comment)
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
        </div>
    </div>
@endsection