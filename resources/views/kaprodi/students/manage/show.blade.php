@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ' . $student->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Mahasiswa</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'kaprodi.students.manage.show'])
            <div class="ms-3 d-flex">
                <a href="{{ route('kaprodi.students.manage.index', request()->query()) }}"
                    class="btn btn-outline-secondary shadow-none me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('kaprodi.students.manage.edit', $student) }}" class="btn btn-primary shadow-none">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4 mb-4">
                <div class="mb-3">
                    <img src="{{ $student->profile_photo_url }}" class="rounded-circle img-thumbnail shadow-sm"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 class="fw-bold mb-1">{{ $student->name }}</h4>
                <p class="text-muted mb-3">{{ $student->nim_nip ?: 'NIM Belum Diatur' }}</p>
                <div>
                    @if ($student->is_active)
                        <span
                            class="badge bg-soft-success text-success border border-success-subtle px-3 py-2 rounded-pill">Status:
                            Aktif</span>
                    @else
                        <span
                            class="badge bg-soft-danger text-danger border border-danger-subtle px-3 py-2 rounded-pill">Status:
                            Non-Aktif</span>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Kontak</h5>
                <div class="mb-3">
                    <label class="text-muted small d-block">Email</label>
                    <a href="mailto:{{ $student->email }}" class="text-decoration-none">{{ $student->email }}</a>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Nomor Telepon/WA</label>
                    <span>{{ $student->phone ?: '-' }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Alamat</label>
                    <span>{{ $student->address ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Akademik</h5>
                <div class="row">
                    <div class="col-sm-6 mb-4">
                        <label class="text-muted small d-block">Fakultas</label>
                        @if ($student->programStudi && $student->programStudi->faculty)
                            <span class="fw-semibold text-dark">
                                {{ $student->programStudi->faculty->name }}
                            </span>
                        @else
                            <span class="fw-semibold">-</span>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-4">
                        <label class="text-muted small d-block">Program Studi</label>
                        @if ($student->programStudi)
                            <span class="fw-semibold text-primary">
                                <i class="bi bi-mortarboard me-1"></i>{{ $student->programStudi->name }}
                            </span>
                        @else
                            <span class="fw-semibold">-</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Riwayat Pengajuan Tugas Akhir</h5>
                @php
                    $submissions = \App\Models\ThesisSubmission::where('student_id', $student->id)->latest()->get();
                @endphp
                @if ($submissions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($submissions as $submission)
                            <a href="{{ route('kaprodi.submissions.show', $submission) }}"
                                class="list-group-item list-group-item-action px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-primary">{{ $submission->title }}</h6>
                                    <small class="text-muted">{{ $submission->created_at->format('d/m/Y') }}</small>
                                </div>
                                <p class="mb-1 small text-muted text-truncate">{{ $submission->abstract }}</p>
                                <span
                                    class="badge bg-soft-secondary text-secondary border small">{{ $submission->status }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-folder2-open display-4 text-light"></i>
                        <p class="text-muted mt-2">Belum ada riwayat pengajuan tugas akhir.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
