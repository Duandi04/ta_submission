@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Role: {{ ucfirst(str_replace('_', ' ', $role->name)) }}</h1>
        <div>
            <a href="{{ route('admin.rbac.index') }}" class="btn btn-outline-secondary btn-sm shadow-none">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.rbac.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                value="{{ old('name', $role->name) }}" 
                                {{ in_array($role->name, ['admin', 'kaprodi', 'dosen', 'mahasiswa']) ? 'readonly' : '' }} required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(in_array($role->name, ['admin', 'kaprodi', 'dosen', 'mahasiswa']))
                                <div class="form-text text-warning"><i class="bi bi-info-circle me-1"></i> Role bawaan sistem tidak dapat diubah namanya demi kestabilan sistem.</div>
                            @else
                                <div class="form-text">Gunakan huruf kecil dan garis bawah (_) sebagai pengganti spasi.</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block fw-semibold">Assign Permissions</label>
                            @if($role->name === 'admin')
                                <div class="alert alert-info py-2 small mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i> Role <strong>admin</strong> secara otomatis memiliki semua permission bawaan sistem.
                                </div>
                            @else
                                <div class="card p-3 shadow-none bg-light" style="max-height: 400px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach($permissions as $perm)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                                        {{ is_array(old('permissions', $rolePermissions)) && in_array($perm->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                                        {{ $perm->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.rbac.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
