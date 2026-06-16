@extends('layouts.app')

@section('title', 'Edit Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Edit Pengajuan Tugas Akhir</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'student.submissions.edit'])
            <div class="ms-3">
                @php
                    $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('student.submissions.show', $submission);
                @endphp
                <a href="{{ $backUrl }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Form Edit Pengajuan
                </div>
                <div class="card-body">
                    <form action="{{ route('student.submissions.update', $submission) }}" method="POST"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Tugas Akhir <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                name="title" value="{{ old('title', $submission->title) }}" required maxlength="255">
                            @include('partials.similarity', ['excludeId' => $submission->id])
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstrak <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract"
                                name="abstract" rows="6" required
                                maxlength="2000">{{ old('abstract', $submission->abstract) }}</textarea>
                            @error('abstract')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="research_field" class="form-label">Bidang Penelitian</label>
                            <input type="text" class="form-control @error('research_field') is-invalid @enderror"
                                id="research_field" name="research_field"
                                value="{{ old('research_field', $submission->research_field) }}" maxlength="100">
                            @error('research_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">File Proposal Saat Ini</label>
                            @php
                                $currentProposal = $submission->files->where('file_type', 'proposal')->sortByDesc('created_at')->first();
                            @endphp
                            @if ($currentProposal)
                                <div class="d-flex align-items-center p-3 border border-light rounded shadow-sm bg-light mb-2">
                                    <div class="me-3 text-center" style="width: 32px;">
                                        @if (str_contains($currentProposal->mime_type, 'pdf'))
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                        @else
                                            <i class="bi bi-file-earmark-word-fill text-primary fs-3"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <h6 class="mb-0 text-truncate text-dark fw-bold" style="font-size: 0.9rem;">{{ $currentProposal->file_name }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ $currentProposal->getFileTypeLabel() }} • {{ $currentProposal->getFormattedFileSize() }}
                                        </small>
                                    </div>
                                    <div class="btn-group ms-3">
                                        <a href="{{ route('files.preview', $currentProposal) }}"
                                            class="btn btn-sm btn-outline-primary border-0" target="_blank" title="Pratinjau">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('files.download', $currentProposal->id) }}"
                                            class="btn btn-sm btn-outline-secondary border-0" title="Unduh">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 small d-flex align-items-center mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                                    <span>Belum ada file proposal. Silakan unggah file di bawah ini.</span>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="proposal_file" class="form-label">File Proposal Baru (Opsional)</label>
                            <input type="file" class="form-control @error('proposal_file') is-invalid @enderror"
                                id="proposal_file" name="proposal_file" accept=".pdf">
                            @error('proposal_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah file. Format: PDF. Maksimal 10MB.</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ $backUrl }}" class="btn btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="bi bi-exclamation-triangle"></i> Perhatian
                </div>
                <div class="card-body">
                    <p class="small">
                        <strong>Status saat ini:</strong>
                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                            {{ $submission->getStatusLabel() }}
                        </span>
                    </p>
                    <p class="small">
                        Anda hanya dapat mengedit pengajuan dengan status <strong>Draft</strong> atau <strong>Perlu
                            Revisi</strong>.
                    </p>
                    <hr>
                    <h6 class="small fw-bold">File Saat Ini:</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach ($submission->files as $file)
                            <li class="mb-2 text-truncate small d-flex align-items-center">
                                @if ($file->file_type === 'proposal')
                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>
                                @elseif ($file->file_type === 'final_document')
                                    <i class="bi bi-file-earmark-text-fill text-success me-2"></i>
                                @elseif ($file->file_type === 'presentation')
                                    <i class="bi bi-file-earmark-play-fill text-primary me-2"></i>
                                @else
                                    <i class="bi bi-file-earmark-fill text-secondary me-2"></i>
                                @endif
                                <span title="{{ $file->file_name }}" class="text-truncate" style="max-width: 180px;">{{ $file->file_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush