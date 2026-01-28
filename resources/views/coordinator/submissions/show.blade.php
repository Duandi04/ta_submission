@extends('layouts.app')

@section('title', 'Detail Pengajuan - Koordinator')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#assignExaminersModal">
                    <i class="bi bi-people"></i> Assign Penguji
                </button>
                <a href="{{ route('coordinator.submissions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
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
                            <th>Email</th>
                            <td>: {{ $submission->student->email }}</td>
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
                            <th>Pembimbing</th>
                            <td>: {{ $submission->supervisor?->name ?? '-' }}</td>
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
                            <td>: {{ $submission->submission_date?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                        @if($submission->defense_date)
                            <tr>
                                <th>Jadwal Sidang</th>
                                <td>: <strong class="text-primary">{{ $submission->defense_date->format('d/m/Y') }}</strong>
                                </td>
                            </tr>
                        @endif
                    </table>

                    <hr>

                    <h6><i class="bi bi-file-text"></i> Abstrak</h6>
                    <p class="text-justify">{{ $submission->abstract }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-paperclip"></i> File Terlampir
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
                                                {{ $file->getFormattedFileSize() }} -
                                                {{ $file->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <a href="/storage/{{ $file->file_path }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada file.</p>
                    @endif
                </div>
            </div>

            @if($submission->assessments->where('is_submitted', true)->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-clipboard-data"></i> Hasil Penilaian
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
                                            <td>{{ $assessment->evaluator->name }}</td>
                                            <td>{{ $assessment->getEvaluatorTypeLabel() }}</td>
                                            <td><span class="badge bg-success fs-6">{{ $assessment->total_score }}</span></td>
                                            <td>{{ $assessment->submitted_at->format('d/m/Y') }}</td>
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
            <div class="card mb-3">
                <div class="card-header bg-warning text-white">
                    <i class="bi bi-gear"></i> Manajemen Status
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="under_review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="scheduled_for_defense">Jadwalkan Sidang</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jadwal Sidang (Opsional)</label>
                            <input type="datetime-local" class="form-control" name="defense_date">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Update Status
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
                                            {{ $status->created_at->format('d/m/Y H:i') }}<br>
                                            {{ $status->changer->name }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small text-center mb-0">Belum ada riwayat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection