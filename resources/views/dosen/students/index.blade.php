@extends('layouts.app')

@section('title', 'Daftar Mahasiswa Bimbingan')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Mahasiswa Bimbingan Saya</h1>
    </div>

    {{-- Search Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dosen.students.index') }}" method="GET" class="row g-3">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari nama atau NIM mahasiswa..." value="{{ request('search') }}" data-auto-search>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        @if($students->count() > 0)
            <div class="row g-4">
                @foreach($students as $student)
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <img src="{{ $student->profile_photo_url }}"
                                    class="rounded-circle img-thumbnail shadow-sm mx-auto mb-3"
                                    style="width: 64px; height: 64px; object-fit: cover;" alt="Foto {{ $student->name }}">
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

            <div class="mt-3">
                {{ $students->appends(request()->query())->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm py-5">
                <div class="card-body text-center">
                    <i class="bi bi-people empty-state-icon"></i>
                    @if(request('search'))
                        <h4 class="mt-3">Tidak Ada Hasil</h4>
                        <p class="text-muted">Tidak ditemukan mahasiswa yang sesuai dengan pencarian "{{ request('search') }}".</p>
                        <a href="{{ route('dosen.students.index') }}" class="btn btn-outline-primary mt-2">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Pencarian
                        </a>
                    @else
                        <h4 class="mt-3">Belum Ada Mahasiswa untuk Dinilai</h4>
                        <p class="text-muted">Anda tidak memiliki mahasiswa yang ditugaskan untuk dinilai oleh Kaprodi.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection