@extends('layouts.app')

@section('title', 'Detail Pengajuan - Supervisor')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Review Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            @php
                $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('dosen.submissions.index');
            @endphp
            <a href="{{ $backUrl }}"
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


                </div>
            </div>

            @include('partials.similarity', ['isDetailView' => true, 'excludeId' => $submission->id])

        </div>

        <div class="col-md-4">
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


    </div>
@endsection