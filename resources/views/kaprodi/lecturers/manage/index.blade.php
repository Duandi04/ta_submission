@extends('layouts.app')

@section('title', 'Manajemen Dosen')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Dosen</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <button type="button" class="btn btn-outline-success shadow-none" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import
            </button>
            <a href="{{ route('kaprodi.lecturers.manage.export', request()->query()) }}" class="btn btn-outline-primary shadow-none">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="{{ route('kaprodi.lecturers.manage.create') }}" class="btn btn-primary shadow-none">
                <i class="bi bi-plus-circle me-1"></i> Tambah Dosen
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('kaprodi.lecturers.manage.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari nama, email, atau NIP..." value="{{ request('search') }}" data-auto-search>
                    </div>
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
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nim_nip', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        NIP
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
                                <th>Role</th>
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
                            @forelse($lecturers as $lecturer)
                                <tr>
                                    <td class="ps-3 text-muted small">
                                        {{ $loop->iteration + ($lecturers->currentPage() - 1) * $lecturers->perPage() }}
                                    </td>
                                    <td class="fw-mono small">{{ $lecturer->nim_nip ?: '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $lecturer->profile_photo_url }}" class="rounded-circle me-2 shadow-sm"
                                                style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                                            <div class="fw-semibold">{{ $lecturer->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach ($lecturer->roles as $role)
                                            <span
                                                class="badge bg-soft-primary text-primary border border-primary-subtle px-2 py-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-muted small">{{ $lecturer->email }}</td>
                                    <td>
                                        @if ($lecturer->is_active)
                                            <span
                                                class="badge bg-soft-success text-success border border-success-subtle">Aktif</span>
                                        @else
                                            <span
                                                class="badge bg-soft-danger text-danger border border-danger-subtle">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('kaprodi.lecturers.manage.show', array_merge(['manage' => $lecturer->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('kaprodi.lecturers.manage.edit', array_merge(['manage' => $lecturer->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('kaprodi.lecturers.manage.destroy', $lecturer) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm-delete
                                                    data-confirm-message="Hapus data dosen ini?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <p class="text-muted mb-0">Data dosen tidak ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $lecturers->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kaprodi.lecturers.manage.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Dosen via Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle me-1"></i> Pastikan file Excel memiliki kolom berikut:
                                <strong>nama, email, nim_nip, password, telepon, alamat, program_studi</strong>.
                                <br>
                                <em>Data yang sudah ada (berdasarkan Email atau NIP) akan diperbarui otomatis.</em>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Unggah & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection