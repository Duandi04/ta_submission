@extends('layouts.app')

@section('title', 'Pengajuan Saya')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Pengajuan Tugas Akhir Saya</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('student.submissions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
            </a>
        </div>
    </div>

    <div id="ajax-container">
        @if($submissions->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Tgl Pengajuan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $submission)
                                    <tr>
                                        <td>{{ $loop->iteration + ($submissions->currentPage() - 1) * $submissions->perPage() }}</td>
                                        <td>
                                            <strong>{{ Str::limit($submission->title, 50) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $submission->research_field }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                                {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $submission->submission_date?->format('d M Y') ?? '-' }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('student.submissions.show', $submission) }}"
                                                    class="btn btn-sm btn-outline-primary px-3" title="Lihat Detail">
                                                    Detail
                                                </a>
                                                @if($submission->canBeEditedByStudent())
                                                    <a href="{{ route('student.submissions.edit', $submission) }}"
                                                        class="btn btn-sm btn-outline-warning px-3" title="Edit">
                                                        Edit
                                                    </a>
                                                @endif
                                                @if($submission->status === 'draft')
                                                    <form action="{{ route('student.submissions.destroy', $submission) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger px-3" data-confirm-delete
                                                            title="Hapus">
                                                            Hapus
                                                        </button>
                                                    </form>
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
                {{ $submissions->links() }}
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox empty-state-icon text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Belum Ada Pengajuan</h4>
                    <p class="text-muted">Buat pengajuan tugas akhir pertama Anda sekarang.</p>
                    <a href="{{ route('student.submissions.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection