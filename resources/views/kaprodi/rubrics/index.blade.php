@extends('layouts.app')

@section('title', 'Manajemen Rubrik Penilaian')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Rubrik Penilaian</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.rubrics.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Rubrik
            </a>
        </div>
    </div>

    @if($rubrics->count() > 0)
        <div class="row g-4">
            @foreach($rubrics as $rubric)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">{{ $rubric->name }}</span>
                            @if($rubric->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3">Aktif</span>
                            @else
                                <span
                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3">Non-aktif</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">{{ $rubric->description }}</p>
                            <h6>Kriteria Penilaian:</h6>
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($rubric->criteria as $criterion)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <div class="fw-semibold small">{{ $criterion['name'] }}</div>
                                            <div class="text-muted smallest-text">{{ $criterion['description'] ?? '' }}</div>
                                        </div>
                                        <span class="badge bg-light text-dark border">{{ $criterion['weight'] }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex gap-2">
                                <a href="{{ route('kaprodi.rubrics.edit', $rubric->id) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('kaprodi.rubrics.destroy', $rubric->id) }}" method="POST" class="flex-grow-1 d-inline" onsubmit="return confirmDelete(event, this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm py-5">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-x empty-state-icon text-muted"></i>
                <h4 class="mt-3">Belum Ada Rubrik</h4>
                <p class="text-muted">Buat rubrik penilaian pertama untuk evaluasi Tugas Akhir.</p>
            </div>
        </div>
    @endif
@endsection