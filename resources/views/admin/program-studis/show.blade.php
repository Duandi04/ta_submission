@extends('layouts.app')

@section('title', 'Detail Program Studi - ' . $programStudi->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Program Studi</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'admin.program-studis.show'])
            <div class="ms-3 d-flex">
                <a href="{{ route('admin.program-studis.index') }}" class="btn btn-outline-secondary shadow-none me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.program-studis.edit', $programStudi) }}" class="btn btn-primary shadow-none">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Informasi Prodi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Program Studi</label>
                        <span class="fw-bold">{{ $programStudi->name }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Fakultas</label>
                        <a href="{{ route('admin.faculties.show', $programStudi->faculty) }}"
                            class="text-decoration-none fw-semibold">
                            {{ $programStudi->faculty->name }}
                        </a>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small d-block">Kode Prodi</label>
                        <span class="badge bg-light text-dark border">{{ $programStudi->code }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h3 class="fw-bold mb-0 text-primary">{{ $students->total() }}</h3>
                            <span class="text-muted small">Mahasiswa</span>
                        </div>
                        <div class="col-6">
                            <h3 class="fw-bold mb-0 text-success">{{ $lecturers->total() }}</h3>
                            <span class="text-muted small">Dosen</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <!-- Nav tabs -->
            <ul class="nav nav-pills mb-3" id="prodiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="students-tab" data-bs-toggle="pill" data-bs-target="#students"
                        type="button" role="tab" aria-controls="students" aria-selected="true">
                        <i class="bi bi-people me-1"></i> Daftar Mahasiswa
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lecturers-tab" data-bs-toggle="pill" data-bs-target="#lecturers"
                        type="button" role="tab" aria-controls="lecturers" aria-selected="false">
                        <i class="bi bi-person-badge me-1"></i> Daftar Dosen
                    </button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="prodiTabsContent">
                <div class="tab-pane fade show active" id="students" role="tabpanel" aria-labelledby="students-tab">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-nowrap">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">NIM</th>
                                            <th>Nama Lengkap</th>
                                            <th>Email</th>
                                            <th class="text-end pe-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $student)
                                            <tr>
                                                <td class="ps-3 fw-mono small">{{ $student->nim_nip ?: '-' }}</td>
                                                <td class="fw-semibold">{{ $student->name }}</td>
                                                <td class="text-muted small">{{ $student->email }}</td>
                                                <td class="text-end pe-3">
                                                    <a href="{{ route('admin.students.show', $student) }}"
                                                        class="btn btn-sm btn-link text-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <p class="text-muted mb-0">Belum ada data mahasiswa.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($students->hasPages())
                            <div class="card-footer bg-white">
                                {{ $students->appends(['lecturers_page' => $lecturers->currentPage()])->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="lecturers" role="tabpanel" aria-labelledby="lecturers-tab">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 text-nowrap">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">NIP</th>
                                            <th>Nama Lengkap</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th class="text-end pe-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lecturers as $lecturer)
                                            <tr>
                                                <td class="ps-3 fw-mono small">{{ $lecturer->nim_nip ?: '-' }}</td>
                                                <td class="fw-semibold">{{ $lecturer->name }}</td>
                                                <td class="text-muted small">{{ $lecturer->email }}</td>
                                                <td>
                                                    @foreach ($lecturer->roles as $role)
                                                        <span
                                                            class="badge bg-soft-primary text-primary border border-primary-subtle px-2 py-1">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="text-end pe-3">
                                                    <a href="{{ route('admin.lecturers.show', $lecturer) }}"
                                                        class="btn btn-sm btn-link text-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <p class="text-muted mb-0">Belum ada data dosen.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($lecturers->hasPages())
                            <div class="card-footer bg-white">
                                {{ $lecturers->appends(['students_page' => $students->currentPage()])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
