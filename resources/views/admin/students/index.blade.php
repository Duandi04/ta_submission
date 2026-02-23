@extends('layouts.app')

@section('title', 'Manajemen Mahasiswa')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Mahasiswa</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary shadow-none">
                <i class="bi bi-plus-circle me-1"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.students.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari nama, email, atau NIM..." value="{{ request('search') }}" data-auto-search>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="program_studi_id" class="form-select" data-auto-submit>
                        <option value="">-- Semua Program Studi --</option>
                        @foreach ($programStudis as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nim_nip', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        NIM
                                        @if (request('sort_by') == 'nim_nip')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'numeric-down' : 'numeric-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Nama Lengkap
                                        @if (request('sort_by') == 'name')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Program Studi</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Email
                                        @if (request('sort_by') == 'email')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-3 text-muted small">
                                        {{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}
                                    </td>
                                    <td class="fw-mono small">{{ $student->nim_nip ?: '-' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $student->name }}</div>
                                    </td>
                                    <td>{{ $student->programStudi->name ?? '-' }}</td>
                                    <td class="text-muted small">{{ $student->email }}</td>
                                    <td>
                                        @if ($student->is_active)
                                            <span
                                                class="badge bg-soft-success text-success border border-success-subtle">Aktif</span>
                                        @else
                                            <span
                                                class="badge bg-soft-danger text-danger border border-danger-subtle">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.students.show', array_merge(['student' => $student->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', array_merge(['student' => $student->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm-delete
                                                    data-confirm-message="Hapus data mahasiswa ini?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <p class="text-muted mb-0">Data mahasiswa tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $students->appends(request()->query())->links() }}
        </div>
    </div>
@endsection