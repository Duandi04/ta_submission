@extends('layouts.app')

@php
    $pageTitle = 'Kelola Pengguna';
    if (request('role_group') == 'lecturer') {
        $pageTitle = 'Daftar Dosen & Kaprodi';
    } elseif (request('role') == 'mahasiswa') {
        $pageTitle = 'Daftar Mahasiswa';
    }
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ $pageTitle }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import
            </button>
            <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah
                {{ request('role') == 'mahasiswa' ? 'Mahasiswa' : (request('role_group') == 'lecturer' ? 'Dosen' : 'Pengguna') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" id="searchInput"
                            placeholder="Cari nama, email, atau NIM/NIP..." value="{{ request('search') }}"
                            data-auto-search>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="program_studi_id" data-auto-submit>
                            <option value="">Semua Program Studi</option>
                            @foreach ($programStudis as $prodi)
                                <option value="{{ $prodi->id }}"
                                    {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if (!request()->has('role_group') || request('role_group') == 'lecturer')
                        <div class="col-md-2">
                            <select class="form-select" name="role" data-auto-submit>
                                <option value="">Semua Role</option>
                                @foreach ($roles as $role)
                                    @if (request('role_group') == 'lecturer')
                                        @if (in_array($role->name, ['dosen', 'kaprodi']))
                                            <option value="{{ $role->name }}"
                                                {{ request('role') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{ $role->name }}"
                                            {{ request('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" width="50">No</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                        class="text-dark text-decoration-none">
                                        Nama
                                        @if (request('sort_by') == 'name')
                                            <i
                                                class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                        @else
                                            <i class="bi bi-hash text-muted small"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <div class="d-flex flex-column">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-dark text-decoration-none">
                                            Email
                                            @if (request('sort_by') == 'email')
                                                <i
                                                    class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'alpha-down' : 'alpha-up' }}"></i>
                                            @endif
                                        </a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nim_nip', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-muted text-decoration-none small">
                                            NIM/NIP
                                            @if (request('sort_by') == 'nim_nip')
                                                <i
                                                    class="bi bi-sort-{{ request('sort_order') == 'asc' ? 'numeric-down' : 'numeric-up' }}"></i>
                                            @endif
                                        </a>
                                    </div>
                                </th>
                                <th>Role & Prodi</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->profile_photo_url }}" class="rounded-circle me-2 shadow-sm" 
                                                style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                                            <span>{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $user->email }}</div>
                                        <div class="text-muted small">NIM/NIP: {{ $user->nim_nip ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @foreach ($user->getRoleNames() as $role)
                                            <span
                                                class="badge bg-primary rounded-pill mb-1">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                                        @endforeach
                                        @if ($user->programStudi)
                                            <div class="small text-dark fw-bold mt-1">
                                                <i class="bi bi-mortarboard me-1"></i>{{ $user->programStudi->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->is_active)
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aktif</span>
                                        @else
                                            <span
                                                class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', array_merge(['user' => $user->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', array_merge(['user' => $user->id], request()->query())) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        data-confirm-delete title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Pengguna via Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle me-1"></i> Pastikan file Excel memiliki kolom berikut:
                                <strong>nama, email, nim_nip, password, telepon, alamat, program_studi, role</strong>.
                                <br>
                                <em>Role yang tersedia: mahasiswa, dosen, kaprodi, koordinator, admin.</em>
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
