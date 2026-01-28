@extends('layouts.app')

@section('title', (isset($faculty) ? 'Edit' : 'Tambah') . ' Fakultas')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">
            {{ isset($faculty) ? 'Edit' : 'Tambah' }} Fakultas
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @if (isset($faculty))
                @include('partials.record-navigation', ['route' => 'admin.faculties.edit'])
            @endif
            <div class="ms-3">
                <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form
                        action="{{ isset($faculty) ? route('admin.faculties.update', $faculty) : route('admin.faculties.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($faculty))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Fakultas</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $faculty->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Fakultas</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $faculty->code ?? '') }}" required
                                placeholder="Contoh: FT, FEB, FH">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-none">
                                <i class="bi bi-check-circle me-1"></i> Simpan Data Fakultas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
