@extends('layouts.app')

@section('title', 'Draft Mahasiswa - ' . $student->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Review Draft Mahasiswa</h1>
            <p class="text-muted small mb-0">{{ $student->name }} ({{ $student->nim_nip }})</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <span class="fw-bold"><i class="bi bi-journal-text me-2 text-primary"></i>Daftar Draft Proposal</span>
        </div>
        <div class="card-body p-0">
            @if ($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Judul</th>
                                <th>Pembimbing</th>
                                <th>Status</th>
                                <th>Tanggal Submit</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $submission->title }}</div>
                                        <div class="text-muted smaller-text">{{ $submission->research_field ?? 'Umum' }}
                                        </div>
                                    </td>
                                    <td>{{ $submission->supervisor?->name ?? 'Belum Ditentukan' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }} rounded-pill">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->submission_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary px-3"
                                            data-bs-toggle="modal" data-bs-target="#assignModal{{ $submission->id }}">
                                            <i class="bi bi-person-plus"></i> Atur Dosen
                                        </button>
                                    </td>
                                </tr>

                                <!-- Assign Modal -->
                                <div class="modal fade" id="assignModal{{ $submission->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form
                                                action="{{ route('kaprodi.submissions.assign-lecturers', $submission->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Atur Dosen Penilai -
                                                        {{ Str::limit($submission->title, 30) }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label">Dosen Penilai <span
                                                                class="text-danger">*</span></label>
                                                        <select name="assessor_ids[]" class="form-select" multiple
                                                            size="6" required>
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
                                                        <div class="form-text">Tahan Ctrl (Cmd di Mac) untuk memilih
                                                            beberapa dosen.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox empty-state-icon"></i>
                    <h6 class="fw-bold">Belum ada draft proposal</h6>
                </div>
            @endif
        </div>
    </div>
@endsection
