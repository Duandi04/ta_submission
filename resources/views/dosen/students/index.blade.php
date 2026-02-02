@extends('layouts.app')

@section('title', 'Daftar Mahasiswa Bimbingan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Mahasiswa Bimbingan Saya</h1>
    </div>

    @if($students->count() > 0)
        <div class="row g-4">
            @foreach($students as $student)
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 64px; height: 64px; font-size: 1.5rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-1">{{ $student->name }}</h5>
                            <p class="text-muted small mb-3">{{ $student->nim_nip }}</p>
                            <div class="d-grid">
                                <a href="{{ route('dosen.students.show', $student) }}" class="btn btn-primary shadow-none">
                                    Lihat Draft
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm py-5">
            <div class="card-body text-center">
                <i class="bi bi-people empty-state-icon"></i>
                <h4 class="mt-3">Belum Ada Mahasiswa Bimbingan</h4>
                <p class="text-muted">Anda tidak memiliki mahasiswa yang sedang dibimbing.</p>
            </div>
        </div>
    @endif
@endsection