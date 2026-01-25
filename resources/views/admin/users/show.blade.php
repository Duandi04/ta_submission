@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Pengguna</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-person"></i> Informasi Pengguna
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Lengkap</th>
                            <td>: <strong>{{ $user->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>NIM/NIP</th>
                            <td>: {{ $user->nim_nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Telepon</th>
                            <td>: {{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ $user->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>:
                                <span class="badge bg-primary">
                                    {{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'No Role')) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>:
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Terdaftar Sejak</th>
                            <td>: {{ $user->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>: {{ $user->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($user->hasRole('mahasiswa') && $user->thesisSubmissions->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-file-text"></i> Pengajuan Tugas Akhir
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->thesisSubmissions as $submission)
                                        <tr>
                                            <td>{{ Str::limit($submission->title, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                                    {{ $submission->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $submission->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->hasRole('dosen_pembimbing') && $user->supervisedTheses->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-people"></i> Mahasiswa Bimbingan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->supervisedTheses as $thesis)
                                        <tr>
                                            <td>{{ $thesis->student->name }}</td>
                                            <td>{{ Str::limit($thesis->title, 40) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $thesis->getStatusBadgeClass() }}">
                                                    {{ $thesis->getStatusLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if(($user->hasRole('dosen_penguji') || $user->hasRole('dosen_pembimbing')) && $user->assessments->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-clipboard-data"></i> Penilaian
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Tipe</th>
                                        <th>Nilai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->assessments as $assessment)
                                        <tr>
                                            <td>{{ $assessment->thesisSubmission->student->name }}</td>
                                            <td>{{ $assessment->getEvaluatorTypeLabel() }}</td>
                                            <td>{{ $assessment->total_score }}</td>
                                            <td>
                                                @if($assessment->is_submitted)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-warning">Draft</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-bar-chart"></i> Statistik
                </div>
                <div class="card-body">
                    @if($user->hasRole('mahasiswa'))
                        <p class="mb-2">
                            <strong>Total Pengajuan:</strong><br>
                            <span class="h4">{{ $user->thesisSubmissions->count() }}</span>
                        </p>
                    @endif

                    @if($user->hasRole('dosen_pembimbing'))
                        <p class="mb-2">
                            <strong>Mahasiswa Bimbingan:</strong><br>
                            <span class="h4">{{ $user->supervisedTheses->count() }}</span>
                        </p>
                    @endif

                    @if($user->hasRole('dosen_penguji') || $user->hasRole('dosen_pembimbing'))
                        <p class="mb-2">
                            <strong>Total Penilaian:</strong><br>
                            <span class="h4">{{ $user->assessments->count() }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Sudah Submit:</strong><br>
                            <span class="h4">{{ $user->assessments->where('is_submitted', true)->count() }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-trash"></i> Zona Berbahaya
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Hapus pengguna ini secara permanen. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100" data-confirm-delete>
                                <i class="bi bi-trash"></i> Hapus Pengguna
                            </button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">Anda tidak dapat menghapus akun sendiri.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection