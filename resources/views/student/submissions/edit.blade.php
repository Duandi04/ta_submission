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

                        <div class="mb-3">
                            <label for="proposal_file" class="form-label">File Proposal Baru (Opsional)</label>
                            <input type="file" class="form-control @error('proposal_file') is-invalid @enderror"
                                id="proposal_file" name="proposal_file" accept=".pdf,.doc,.docx">
                            @error('proposal_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah file. Format: PDF, DOC, DOCX. Maksimal 10MB.</small>
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
                    <h6 class="small">File Saat Ini:</h6>
                    <ul class="small">
                        @foreach ($submission->files as $file)
                            <li>{{ $file->file_name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush