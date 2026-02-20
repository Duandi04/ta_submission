@extends('layouts.app')

@section('title', 'Penilaian Saya')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Penilaian Tugas Akhir</h1>
    </div>

    <div id="ajax-container">
        @if($assessments->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Judul</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assessments as $assessment)
                                    <tr>
                                        <td class="ps-3">{{ $loop->iteration + ($assessments->currentPage() - 1) * $assessments->perPage() }}</td>
                                        <td>
                                            <strong>{{ $assessment->thesisSubmission->student->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assessment->thesisSubmission->student->nim_nip }}</small>
                                        </td>
                                        <td>{{ Str::limit($assessment->thesisSubmission->title, 50) }}</td>
                                        <td>
                                            @if($assessment->is_submitted)
                                                <strong class="text-success">{{ $assessment->total_score }}</strong>
                                            @else
                                                <span class="text-muted">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assessment->is_submitted)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                    <i class="bi bi-check-circle me-1"></i>Sudah Submit
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle text-dark">
                                                    <i class="bi bi-clock me-1"></i>Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group">
                                                <a href="{{ route('dosen.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary" title="Lihat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(!$assessment->is_submitted)
                                                    <a href="{{ route('dosen.assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                {{ $assessments->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-clipboard-data empty-state-icon text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Belum Ada Penilaian</h4>
                    <p class="text-muted">Anda belum ditugaskan untuk menilai tugas akhir apapun.</p>
                </div>
            </div>
        @endif
    </div>
@endsection