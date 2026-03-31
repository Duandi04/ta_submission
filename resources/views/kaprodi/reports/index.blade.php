@extends('layouts.app')

@section('title', 'Laporan Pengajuan Proposal')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2">Laporan Pengajuan Proposal Diterima</h1>
            <p class="text-muted small mb-0">Preview laporan sebelum dicetak PDF.</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('kaprodi.reports.print') }}" target="_blank" class="btn btn-primary shadow-none">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 50px;">No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Judul Proposal Diterima</th>
                            <th>Dosen Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            @php
                                $accepted = $student->thesisSubmissions->first();
                            @endphp
                            <tr>
                                <td class="ps-3 text-muted small">{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $student->name }}</td>
                                <td class="fw-mono small">{{ $student->nim_nip }}</td>
                                <td>{{ $accepted ? $accepted->title : '-' }}</td>
                                <td>
                                    @if($accepted && $accepted->supervisor)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person me-2 text-primary"></i>
                                            {{ $accepted->supervisor->name }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                                    Belum ada mahasiswa yang memiliki proposal diterima untuk program studi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
