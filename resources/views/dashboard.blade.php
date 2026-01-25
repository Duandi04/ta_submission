@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->getRoleNames()->first())) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle fs-4 me-3"></i>
                    <div>
                        <strong>Selamat datang, {{ auth()->user()->name }}!</strong><br>
                        <small>Anda masuk sebagai
                            <strong>{{ ucfirst(str_replace('_', ' ', auth()->user()->getRoleNames()->first())) }}</strong>.
                            Login terakhir: {{ now()->format('d F Y, H:i') }} WIB</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @role('mahasiswa')
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card stats-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Total Pengajuan</h6>
                        <h2 class="mb-0">{{ auth()->user()->thesisSubmissions()->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-arrow-up-right"></i> Semua pengajuan
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card stats-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Dalam Proses</h6>
                        <h2 class="mb-0">
                            {{ auth()->user()->thesisSubmissions()->whereIn('status', ['submitted', 'under_review'])->count() }}
                        </h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-hourglass-split"></i> Sedang ditinjau
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card stats-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Selesai</h6>
                        <h2 class="mb-0">{{ auth()->user()->thesisSubmissions()->where('status', 'completed')->count() }}
                        </h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-check-circle"></i> Telah selesai
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul"></i> Pengajuan Terbaru</span>
                    <a href="{{ route('student.submissions.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if(auth()->user()->thesisSubmissions()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->thesisSubmissions()->latest()->take(5)->get() as $submission)
                                        <tr>
                                            <td>
                                                <strong>{{ $submission->title }}</strong>
                                                @if($submission->research_field)
                                                    <br><small class="text-muted">{{ $submission->research_field }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                                    {{ $submission->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $submission->submission_date?->format('d M Y') ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('student.submissions.show', $submission) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3 text-muted">Belum ada pengajuan</h5>
                            <p class="text-muted">Buat pengajuan tugas akhir pertama Anda sekarang.</p>
                            <a href="{{ route('student.submissions.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('admin')
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card stats-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Total Pengguna</h6>
                        <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-people"></i> Semua role
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card stats-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Total Pengajuan</h6>
                        <h2 class="mb-0">{{ \App\Models\ThesisSubmission::count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-file-earmark-text"></i> Semua status
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card stats-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Aktivitas Hari Ini</h6>
                        <h2 class="mb-0">
                            {{ \Spatie\Activitylog\Models\Activity::whereDate('created_at', today())->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-clock-history"></i> {{ now()->format('d M Y') }}
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-activity"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card stats-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Selesai</h6>
                        <h2 class="mb-0">{{ \App\Models\ThesisSubmission::where('status', 'completed')->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-check-circle"></i> Telah lulus
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Pengajuan Terbaru
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\ThesisSubmission::with('student')->latest()->take(5)->get() as $submission)
                                    <tr>
                                        <td>{{ $submission->student->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $submission->getStatusBadgeClass() }}">
                                                {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td><small>{{ $submission->created_at->format('d M Y') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Aktivitas Terbaru
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\Spatie\Activitylog\Models\Activity::with('causer')->latest()->take(5)->get() as $activity)
                                    <tr>
                                        <td><small>{{ $activity->causer?->name ?? 'System' }}</small></td>
                                        <td><span class="badge bg-info">{{ $activity->description }}</span></td>
                                        <td><small>{{ $activity->created_at->diffForHumans() }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('dosen_pembimbing')
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stats-card stats-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Mahasiswa Bimbingan</h6>
                        <h2 class="mb-0">{{ auth()->user()->supervisedTheses()->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-people"></i> Total mahasiswa
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stats-card stats-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Perlu Review</h6>
                        <h2 class="mb-0">{{ auth()->user()->supervisedTheses()->where('status', 'submitted')->count() }}
                        </h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-exclamation-circle"></i> Membutuhkan perhatian
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-list-check"></i> Pengajuan yang Perlu Review
                </div>
                <div class="card-body">
                    @if(auth()->user()->supervisedTheses()->where('status', 'submitted')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Judul</th>
                                        <th>Tanggal Submit</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->supervisedTheses()->where('status', 'submitted')->latest()->get() as $thesis)
                                        <tr>
                                            <td>{{ $thesis->student->name }}</td>
                                            <td>{{ Str::limit($thesis->title, 50) }}</td>
                                            <td>{{ $thesis->submission_date?->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('supervisor.submissions.show', $thesis) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="mt-3 text-muted">Tidak ada pengajuan yang perlu direview</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('dosen_penguji')
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stats-card stats-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Total Penilaian</h6>
                        <h2 class="mb-0">{{ auth()->user()->assessments()->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-clipboard-data"></i> Semua penilaian
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stats-card stats-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="position: relative; z-index: 1;">
                        <h6 class="text-muted text-uppercase">Belum Dinilai</h6>
                        <h2 class="mb-0">{{ auth()->user()->assessments()->where('is_submitted', false)->count() }}</h2>
                        <p class="mb-0 small text-muted mt-2">
                            <i class="bi bi-hourglass-split"></i> Segera selesaikan
                        </p>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endrole

@endsection