@extends('layouts.app')

@section('title', 'Detail Dosen - ' . $lecturer->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Dosen</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'admin.lecturers.show'])
            <div class="ms-3 d-flex">
                <a href="{{ route('admin.lecturers.index', request()->query()) }}"
                    class="btn btn-outline-secondary shadow-none">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.lecturers.edit', $lecturer) }}" class="btn btn-primary shadow-none">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-4 mb-4">
                <div class="mb-3">
                    <img src="{{ $lecturer->profile_photo_url }}" class="rounded-circle img-thumbnail shadow-sm"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>
                <h4 class="fw-bold mb-1">{{ $lecturer->name }}</h4>
                <p class="text-muted mb-3">{{ $lecturer->nim_nip ?: 'NIP Belum Diatur' }}</p>
                <div class="mb-3">
                    @foreach ($lecturer->roles as $role)
                        <span
                            class="badge bg-soft-primary text-primary border border-primary-subtle px-3 py-2 rounded-pill">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </div>
                <div>
                    @if ($lecturer->is_active)
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
                    <a href="mailto:{{ $lecturer->email }}" class="text-decoration-none">{{ $lecturer->email }}</a>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Nomor Telepon</label>
                    <span>{{ $lecturer->phone ?: '-' }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Alamat</label>
                    <span>{{ $lecturer->address ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Akademik</h5>
                <div class="row">
                    <div class="col-sm-6 mb-4">
                        <label class="text-muted small d-block">Fakultas</label>
                        @if($lecturer->programStudi && $lecturer->programStudi->faculty)
                            <a href="{{ route('admin.faculties.show', $lecturer->programStudi->faculty) }}"
                                class="fw-semibold text-decoration-none">
                                {{ $lecturer->programStudi->faculty->name }}
                            </a>
                        @else
                            <span class="fw-semibold">-</span>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-4">
                        <label class="text-muted small d-block">Program Studi</label>
                        @if($lecturer->programStudi)
                            <a href="{{ route('admin.program-studis.show', $lecturer->programStudi) }}"
                                class="fw-semibold text-primary text-decoration-none">
                                <i class="bi bi-mortarboard me-1"></i>{{ $lecturer->programStudi->name }}
                            </a>
                        @else
                            <span class="fw-semibold">-</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Daftar Bimbingan (Mahasiswa)</h5>
                @php
                    $supervisedSubmissions = \App\Models\ThesisSubmission::where('supervisor_id', $lecturer->id)
                        ->with('student')
                        ->latest()
                        ->get();
                @endphp
                @if ($supervisedSubmissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Judul TA</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supervisedSubmissions as $submission)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $submission->student->name }}</div>
                                            <div class="small text-muted">{{ $submission->student->nim_nip }}</div>
                                        </td>
                                        <td class="small">{{ $submission->title }}</td>
                                        <td>
                                            <span
                                                class="badge bg-soft-secondary text-secondary border small">{{ $submission->status }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people display-4 text-light"></i>
                        <p class="text-muted mt-2">Belum membimbing mahasiswa.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection