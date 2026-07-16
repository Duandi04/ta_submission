@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Role Baru</h1>
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
                    <form action="{{ route('admin.rbac.roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: staff_akademik, koordinator_ta" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan huruf kecil dan garis bawah (_) sebagai pengganti spasi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block fw-semibold">Assign Permissions</label>
                            <div class="card p-3 shadow-none bg-light" style="max-height: 400px; overflow-y: auto;">
                                <div class="row">
                                    @foreach($permissions as $perm)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                                    {{ is_array(old('permissions')) && in_array($perm->name, old('permissions')) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.rbac.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
