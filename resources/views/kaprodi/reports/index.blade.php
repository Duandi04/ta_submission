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
                            <th>Catatan / Komentar</th>
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
                                    @php
                                        $feedbacks = $accepted ? $accepted->assessments->where('is_submitted', true)->filter(function($a) {
                                            return !empty($a->comments) || !empty($a->strengths) || !empty($a->weaknesses) || !empty($a->recommendations);
                                        }) : collect();
                                    @endphp
                                    @if($feedbacks->isNotEmpty())
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($feedbacks as $assessment)
                                                <li class="mb-2">
                                                    <strong class="text-primary" style="font-size: 0.8rem;">{{ $assessment->getAnonymousLabel() }}:</strong>
                                                    <div class="ps-2 text-dark small" style="line-height: 1.3;">
                                                        @if(!empty($assessment->strengths))
                                                            <div><span class="text-muted">Kelebihan:</span> {{ $assessment->strengths }}</div>
                                                        @endif
                                                        @if(!empty($assessment->weaknesses))
                                                            <div><span class="text-muted">Kekurangan:</span> {{ $assessment->weaknesses }}</div>
                                                        @endif
                                                        @if(!empty($assessment->comments))
                                                            <div><span class="text-muted">Komentar:</span> {{ $assessment->comments }}</div>
                                                        @endif
                                                        @if(!empty($assessment->recommendations))
                                                            <div><span class="text-muted">Saran:</span> {{ $assessment->recommendations }}</div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($accepted)
                                        <div class="mb-2">
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Pembimbing 1:</small>
                                            <span class="fw-semibold text-dark"><i class="bi bi-person me-1 text-primary"></i>{{ $accepted->supervisor ? $accepted->supervisor->name : '-' }}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Pembimbing 2:</small>
                                            <span class="text-dark"><i class="bi bi-person me-1 text-secondary"></i>{{ $accepted->supervisor2 ? $accepted->supervisor2->name : '-' }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
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
