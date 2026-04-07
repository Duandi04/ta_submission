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
                    @php
                        // Get the latest supervised submission and the lecturer's assessment for it
                        $submission = $student->thesisSubmissions->first();
                        $myAssessment = $submission ? $submission->assessments->first() : null;
                    @endphp
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm card-hover-effect overflow-hidden">
                            {{-- Top accent or gradient --}}
                            <div class="h-1 bg-{{ $submission?->getStatusBadgeClass() ?? 'secondary' }}"></div>
                            
                            <div class="card-body text-center p-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img src="{{ $student->profile_photo_url }}"
                                        class="rounded-circle border border-3 border-white shadow-sm"
                                        style="width: 72px; height: 72px; object-fit: cover;" alt="Foto {{ $student->name }}">
                                    @if($submission)
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $submission->getStatusBadgeClass() }} border border-2 border-white rounded-circle shadow-sm" style="width: 16px; height: 16px;" title="{{ $submission->getStatusLabel() }}"></span>
                                    @endif
                                </div>

                                <h5 class="card-title fw-bold mb-1 text-dark text-truncate">{{ $student->name }}</h5>
                                <p class="text-muted small font-monospace mb-3">{{ $student->nim_nip }}</p>

                                @if($submission)
                                    <div class="bg-light p-2 rounded-3 mb-3 text-start">
                                        <div class="extra-small text-muted mb-1 text-uppercase letter-spacing-1 fw-bold">Judul Terakhir:</div>
                                        <div class="small fw-semibold text-dark text-truncate-2" style="min-height: 2.4rem;">
                                            {{ $submission->title }}
                                        </div>
                                    </div>

                                    @if($submission->final_score)
                                    <div class="mb-3">
                                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                                            <i class="bi bi-star-fill me-1"></i> Nilai: {{ number_format($submission->final_score, 1) }}
                                        </span>
                                    </div>
                                    @endif

                                    <div class="d-grid">
                                        <a href="{{ route('dosen.students.show', $student) }}" class="btn btn-primary btn-sm rounded-pill py-2 shadow-none ripple">
                                            <i class="bi bi-folder2-open me-1"></i> Lihat Draft
                                        </a>
                                    </div>
                                @else
                                    <div class="alert alert-light border-0 small mb-0 py-2">
                                        Mahasiswa belum memiliki pengajuan.
                                    </div>
                                @endif
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