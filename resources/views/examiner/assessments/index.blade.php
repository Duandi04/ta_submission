@extends('layouts.app')

@section('title', 'Penilaian Saya')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Penilaian Tugas Akhir</h1>
    </div>

    @if($assessments->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mahasiswa</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                                <tr>
                                    <td>{{ $loop->iteration + ($assessments->currentPage() - 1) * $assessments->perPage() }}</td>
                                    <td>
                                        <strong>{{ $assessment->thesisSubmission->student->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assessment->thesisSubmission->student->nim_nip }}</small>
                                    </td>
                                    <td>{{ Str::limit($assessment->thesisSubmission->title, 50) }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $assessment->getEvaluatorTypeLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assessment->is_submitted)
                                            <strong class="text-success">{{ $assessment->total_score }}</strong>
                                        @else
                                            <span class="text-muted">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assessment->is_submitted)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Sudah Submit
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('examiner.assessments.show', $assessment) }}" class="btn btn-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$assessment->is_submitted)
                                                <a href="{{ route('examiner.assessments.edit', $assessment) }}" class="btn btn-warning">
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
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-data" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">Belum Ada Penilaian</h4>
                <p class="text-muted">Anda belum ditugaskan untuk menilai tugas akhir apapun.</p>
            </div>
        </div>
    @endif
@endsection