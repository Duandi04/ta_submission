@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">Edit Pengguna</h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @include('partials.record-navigation', ['route' => 'admin.users.edit'])
            <div class="ms-3">
                <a href="{{ route('admin.users.index', request()->query()) }}" class="btn btn-outline-secondary shadow-none">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Photo Upload --}}
                        @include('partials.photo-upload', ['user' => $user])

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin
                                mengubah)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>

                        <div class="mb-3">
                            <label for="nim_nip" class="form-label">NIM/NIP</label>
                            <input type="text" class="form-control @error('nim_nip') is-invalid @enderror" id="nim_nip"
                                name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}">
                            @error('nim_nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="program_studi_id" class="form-label">Program Studi <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('program_studi_id') is-invalid @enderror"
                                id="program_studi_id" name="program_studi_id">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}"
                                        {{ old('program_studi_id', $user->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                        [{{ $prodi->code }}] {{ $prodi->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text mt-1">Wajib diisi untuk mahasiswa, dosen, dan kaprodi.</div>
                            @error('program_studi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Akun Aktif</label>
                            </div>
                            <div class="form-text">Jika tidak aktif, user tidak akan bisa login.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block fw-semibold">Roles <span class="text-danger">*</span></label>
                            @error('roles')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                            <div class="card shadow-sm p-3">
                                <div class="row">
                                    @foreach ($roles as $role)
                                        <div class="col-md-3 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input @error('roles') is-invalid @enderror" type="checkbox" name="roles[]" 
                                                    value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                    {{ (is_array(old('roles', $user->roles->pluck('name')->toArray())) && in_array($role->name, old('roles', $user->roles->pluck('name')->toArray()))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block fw-semibold">Direct Permissions (Opsional)</label>
                            <div class="card shadow-sm p-3" style="max-height: 250px; overflow-y: auto;">
                                <div class="row">
                                    @foreach ($permissions as $permission)
                                        <div class="col-md-4 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                    value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                    {{ (is_array(old('permissions', $user->permissions->pluck('name')->toArray())) && in_array($permission->name, old('permissions', $user->permissions->pluck('name')->toArray()))) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-text mt-1 text-muted">Permission langsung ini akan mengoverride/menambah permission yang didapat dari Role.</div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
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
