@extends('layouts.app')

@section('title', 'Manajemen RBAC')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Role & Permission (RBAC)</h1>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="rbacTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-medium" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="true">
                <i class="bi bi-shield-lock me-2"></i>Kelola Roles
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-medium" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab" aria-controls="permissions" aria-selected="false">
                <i class="bi bi-key me-2"></i>Kelola Permissions
            </button>
        </li>
    </ul>

    <div class="tab-content" id="rbacTabsContent">
        <!-- Roles Tab -->
        <div class="tab-pane fade show active" id="roles" role="tabpanel" aria-labelledby="roles-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 text-muted small uppercase fw-bold">Daftar Roles</h4>
                <a href="{{ route('admin.rbac.roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Role
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width: 50px;">No</th>
                                    <th>Nama Role</th>
                                    <th>Permissions</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            <span class="badge bg-light text-dark border ms-1">{{ $role->name }}</span>
                                        </td>
                                        <td>
                                            @if($role->name === 'admin')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Semua Permission (Super Admin)</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-1" style="max-width: 600px;">
                                                    @forelse($role->permissions as $perm)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle small-badge">{{ $perm->name }}</span>
                                                    @empty
                                                        <span class="text-muted small">Tidak ada permission</span>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.rbac.roles.edit', $role) }}" class="btn btn-sm btn-outline-warning" title="Edit Role & Permissions">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                @if(!in_array($role->name, ['admin', 'kaprodi', 'dosen', 'mahasiswa']))
                                                    <form action="{{ route('admin.rbac.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Role ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Role Bawaan Sistem Tidak Bisa Dihapus">
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <p class="text-muted mb-0">Belum ada data role.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Tab -->
        <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 text-muted small uppercase fw-bold">Daftar Permissions</h4>
                <a href="{{ route('admin.rbac.permissions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Permission
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width: 50px;">No</th>
                                    <th>Nama Permission</th>
                                    <th>Guard Name</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permissions as $permission)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold text-primary">{{ $permission->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $permission->guard_name }}</span></td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group">
                                                <a href="{{ route('admin.rbac.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.rbac.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Permission ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <p class="text-muted mb-0">Belum ada data permission.</p>
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
    
    <style>
        .small-badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
        }
    </style>
@endsection
