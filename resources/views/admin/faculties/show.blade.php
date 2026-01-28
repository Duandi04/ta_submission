@extends('layouts.app')

@section('title', 'Detail Fakultas - ' . $faculty->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Detail Fakultas</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'admin.faculties.show'])
            <div class="ms-3 d-flex">
                <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary shadow-none me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-primary shadow-none">
                    <i class="bi bi-pencil me-1"></i> Edit Data
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Informasi Fakultas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Fakultas</label>
                        <span class="fw-bold">{{ $faculty->name }}</span>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small d-block">Kode Fakultas</label>
                        <span class="badge bg-light text-dark border">{{ $faculty->code }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Program Studi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Nama Prodi</th>
                                    <th>Kode</th>
                                    <th>Mahasiswa</th>
                                    <th>Dosen</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculty->programStudis as $prodi)
                                    <tr>
                                        <td class="ps-3 fw-semibold text-primary">
                                            <a href="{{ route('admin.program-studis.show', $prodi) }}"
                                                class="text-decoration-none">
                                                {{ $prodi->name }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $prodi->code }}</span></td>
                                        <td>{{ $prodi->users_count }}</td>
                                        <td>{{ $prodi->lecturers_count }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.program-studis.show', $prodi) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <p class="text-muted mb-0">Belum ada program studi di fakultas ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
