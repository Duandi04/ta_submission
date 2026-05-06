@extends('layouts.app')

@section('title', (isset($programStudi) ? 'Edit' : 'Tambah') . ' Program Studi')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0">
            {{ isset($programStudi) ? 'Edit' : 'Tambah' }} Program Studi
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 d-flex align-items-center">
            @if (isset($programStudi))
                @include('partials.record-navigation', ['route' => 'admin.program-studis.edit'])
            @endif
            <div class="ms-3">
                <a href="{{ route('admin.program-studis.index') }}" class="btn btn-secondary">
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
                        action="{{ isset($programStudi) ? route('admin.program-studis.update', $programStudi) : route('admin.program-studis.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($programStudi))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="faculty_id" class="form-label">Fakultas</label>
                            <select class="form-select @error('faculty_id') is-invalid @enderror" id="faculty_id"
                                name="faculty_id" required>
                                <option value="" disabled
                                    {{ !old('faculty_id', $programStudi->faculty_id ?? '') ? 'selected' : '' }}>Pilih
                                    Fakultas...</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}"
                                        {{ old('faculty_id', $programStudi->faculty_id ?? '') == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Program Studi</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $programStudi->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Program Studi</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $programStudi->code ?? '') }}" required
                                placeholder="Contoh: IF, SI, TI">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="submission_start" class="form-label">Mulai Pengajuan</label>
                                <input type="datetime-local" class="form-control @error('submission_start') is-invalid @enderror" 
                                       id="submission_start" name="submission_start" 
                                       value="{{ old('submission_start', isset($programStudi) && $programStudi->submission_start ? $programStudi->submission_start->format('Y-m-d\TH:i') : '') }}">
                                @error('submission_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="submission_end" class="form-label">Batas Akhir Pengajuan</label>
                                <input type="datetime-local" class="form-control @error('submission_end') is-invalid @enderror" 
                                       id="submission_end" name="submission_end" 
                                       value="{{ old('submission_end', isset($programStudi) && $programStudi->submission_end ? $programStudi->submission_end->format('Y-m-d\TH:i') : '') }}">
                                @error('submission_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-none">
                                <i class="bi bi-check-circle me-1"></i> Simpan Data Prodi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
