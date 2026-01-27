@extends('layouts.app')

@php
    $pageTitle = 'Kelola Pengguna';
    if (request('role_group') == 'lecturer')
        $pageTitle = 'Daftar Dosen & Kaprodi';
    elseif (request('role') == 'mahasiswa')
        $pageTitle = 'Daftar Mahasiswa';
@endphp

@section('title', $pageTitle)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ $pageTitle }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah {{ request('role') == 'mahasiswa' ? 'Mahasiswa' : (request('role_group') == 'lecturer' ? 'Dosen' : 'Pengguna') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" id="searchInput"
                            placeholder="Cari nama, email, atau NIM/NIP..." value="{{ request('search') }}" data-auto-search>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="program_studi_id" data-auto-submit>
                            <option value="">Semua Program Studi</option>
                            @foreach($programStudis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request('role_group') == 'lecturer' || !request()->hasAny(['role', 'role_group']))
                    <div class="col-md-2">
                        <select class="form-select" name="role" data-auto-submit>
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                @if(request('role_group') == 'lecturer')
                                    @if(in_array($role->name, ['dosen', 'kaprodi']))
                                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
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
                                <th>Nama</th>
                                <th>Identitas & Kontak</th>
                                <th>Role & Prodi</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <div class="small fw-bold">{{ $user->email }}</div>
                                        <div class="text-muted small">NIM/NIP: {{ $user->nim_nip ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @foreach($user->getRoleNames() as $role)
                                            <span
                                                class="badge bg-primary rounded-pill mb-1">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                                        @endforeach
                                        @if($user->programStudi)
                                            <div class="small text-dark fw-bold mt-1">
                                                <i class="bi bi-mortarboard me-1"></i>{{ $user->programStudi->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm-delete title="Hapus">
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
@endsection