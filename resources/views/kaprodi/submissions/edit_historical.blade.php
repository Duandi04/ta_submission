@extends('layouts.app')

@section('title', 'Edit Data History Pengajuan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Data History Pengajuan</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.submissions.show', $submission->id) }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-light">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Update Data History
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kaprodi.submissions.update-historical', $submission->id) }}" method="POST" id="historicalForm" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="student_id" class="form-label fw-semibold">Mahasiswa <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ (old('student_id', $submission->student_id) == $student->id) ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->nim_nip }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12">
                                <label for="title" class="form-label fw-semibold">Judul Skripsi <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $submission->title) }}" placeholder="Masukkan judul lengkap skripsi" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12">
                                <label for="abstract" class="form-label fw-semibold">Abstrak <span class="text-danger">*</span></label>
                                <textarea name="abstract" id="abstract" rows="5" class="form-control @error('abstract') is-invalid @enderror" placeholder="Masukkan ringkasan atau abstrak skripsi" required>{{ old('abstract', $submission->abstract) }}</textarea>
                                @error('abstract')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12">
                                <label for="research_field" class="form-label fw-semibold">Bidang Penelitian</label>
                                <input type="text" name="research_field" id="research_field" class="form-control @error('research_field') is-invalid @enderror" value="{{ old('research_field', $submission->research_field) }}" placeholder="Contoh: Sistem Informasi / Rekayasa Perangkat Lunak">
                                @error('research_field')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supervisor_id" class="form-label fw-semibold">Pembimbing 1 <span class="text-danger">*</span></label>
                                <select name="supervisor_id" id="supervisor_id" class="form-select @error('supervisor_id') is-invalid @enderror" required>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ (old('supervisor_id', $submission->supervisor_id) == $lecturer->id) ? 'selected' : '' }}>
                                            {{ $lecturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supervisor_2_id" class="form-label fw-semibold">Pembimbing 2</label>
                                <select name="supervisor_2_id" id="supervisor_2_id" class="form-select @error('supervisor_2_id') is-invalid @enderror">
                                    <option value="">-- Pilih Pembimbing 2 (Opsional) --</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ (old('supervisor_2_id', $submission->supervisor_2_id) == $lecturer->id) ? 'selected' : '' }}>
                                            {{ $lecturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_2_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="submission_date" class="form-label fw-semibold">Tanggal Diterima</label>
                                <input type="date" name="submission_date" id="submission_date" class="form-control @error('submission_date') is-invalid @enderror" value="{{ old('submission_date', $submission->submission_date ? $submission->submission_date->format('Y-m-d') : '') }}">
                                @error('submission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr class="my-4 text-light">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Update Data History
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { border-radius: 0.375rem !important; padding: 0.5rem 0.75rem !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Searchable selects
        new TomSelect('#student_id', { create: false, sortField: { field: 'text', direction: 'asc' } });
        new TomSelect('#supervisor_id', { create: false, sortField: { field: 'text', direction: 'asc' } });
        new TomSelect('#supervisor_2_id', { create: false, sortField: { field: 'text', direction: 'asc' } });

        const form = document.getElementById('historicalForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Update',
                text: "Apakah Anda yakin ingin memperbarui data history ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
