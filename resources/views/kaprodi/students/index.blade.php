@extends('layouts.app')

@section('title', 'Daftar Proposal Mahasiswa')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Proposal Mahasiswa</h1>
    </div>

    {{-- Search Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('kaprodi.students.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Cari nama, email, atau NIM..." value="{{ request('search') }}" data-auto-search>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div id="ajax-container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Mahasiswa</th>
                                <th>NIM</th>
                                <th>Angkatan</th>
                                <th>Total Pengajuan</th>
                                <th>Status Proposal</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->profile_photo_url }}"
                                                class="rounded-circle me-2 shadow-sm"
                                                style="width: 32px; height: 32px; object-fit: cover;" alt="Avatar">
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                                <div class="text-muted small">{{ $student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->nim_nip }}</td>
                                    <td>{{ $student->angkatan ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3">
                                            {{ $student->thesis_submissions_count }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $accepted = $student->thesisSubmissions->firstWhere('status', 'approved');
                                        @endphp
                                        @if($accepted)
                                            <span class="badge bg-success rounded-pill mb-1">Diterima</span>
                                            <div class="small text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-person me-1"></i>{{ $accepted->supervisor->name ?? 'Belum ada pembimbing' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('kaprodi.students.show', array_merge(['student' => $student->id], request()->query())) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            Lihat Draft
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        @if (request('search'))
                                            <p class="text-muted mb-0">Tidak ditemukan mahasiswa yang sesuai dengan
                                                pencarian
                                                "{{ request('search') }}"</p>
                                            <a href="{{ route('kaprodi.students.index') }}"
                                                class="btn btn-outline-primary mt-2">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Pencarian
                                            </a>
                                        @else
                                            Belum ada mahasiswa terdaftar
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $students->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
