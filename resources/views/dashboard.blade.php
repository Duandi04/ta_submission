@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold mb-0">Dashboard</h1>
            <p class="text-muted small mb-0">
                Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>.
                @if (auth()->user()->programStudi)
                    <span class="mx-1 text-secondary opacity-50">|</span>
                    <span class="fw-medium text-dark">{{ auth()->user()->programStudi->name }}</span>
                    @if (auth()->user()->programStudi->faculty)
                        <span class="text-secondary small">({{ auth()->user()->programStudi->faculty->name }})</span>
                        @php @endphp
                    @endif
                @endif
            </p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                <i class="bi bi-person-badge-fill me-1"></i>
                {{ ucfirst(str_replace('_', ' ', auth()->user()->getRoleNames()->first())) }}
            </span>
        </div>
    </div>

    @role('mahasiswa')
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card stats-primary">
                <div class="stats-icon-wrapper">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h6>Total Pengajuan</h6>
                    <h2>{{ auth()->user()->thesisSubmissions()->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-warning">
                <div class="stats-icon-wrapper">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <h6>Dalam Proses</h6>
                    <h2>{{ auth()->user()->thesisSubmissions()->whereIn('status', ['submitted', 'under_review'])->count() }}
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-success">
                <div class="stats-icon-wrapper">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <h6>Disetujui</h6>
                    <h2>{{ auth()->user()->thesisSubmissions()->where('status', 'approved')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <div class="d-flex align-items-center gap-2 fw-bold">
                <i class="bi bi-journal-text text-primary"></i>
                <span>Pengajuan Terbaru</span>
            </div>
            <a href="{{ route('student.submissions.index') }}" class="btn btn-sm btn-light border text-primary px-3">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            @if (auth()->user()->thesisSubmissions()->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Diajukan Pada</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (auth()->user()->thesisSubmissions()->latest()->take(5)->get() as $submission)
                                <tr>
                                    <td class="px-3">
                                        <div class="fw-semibold text-dark">{{ $submission->title }}</div>
                                        <div class="text-muted smaller-text">{{ $submission->research_field ?? 'Umum' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $submission->getStatusBadgeClass() }} rounded-pill">
                                            {{ $submission->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end px-3">
                                        <a href="{{ route('student.submissions.show', $submission) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted fs-1 mb-3 d-block"></i>
                    <h6 class="fw-bold">Belum ada pengajuan</h6>
                    <a href="{{ route('student.submissions.create') }}" class="btn btn-primary mt-2 px-4 shadow-none">
                        Buat Pengajuan
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endrole

    @role('admin')
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card stats-primary">
                <div class="stats-icon-wrapper"><i class="bi bi-people"></i></div>
                <div>
                    <h6>Total Pengguna</h6>
                    <h2>{{ \App\Models\User::count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-info">
                <div class="stats-icon-wrapper"><i class="bi bi-file-earmark-code"></i></div>
                <div>
                    <h6>Total Pengajuan</h6>
                    <h2>{{ \App\Models\ThesisSubmission::count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card stats-success">
                <div class="stats-icon-wrapper"><i class="bi bi-award"></i></div>
                <div>
                    <h6>Selesai</h6>
                    <h2>{{ \App\Models\ThesisSubmission::where('status', 'completed')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <span class="fw-bold"><i class="bi bi-kanban me-2 text-primary"></i>Pengajuan Terkini</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-3">Mahasiswa</th>
                                    <th>Status</th>
                                    <th class="pe-3 text-end">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (\App\Models\ThesisSubmission::with('student')->latest()->take(6)->get() as $submission)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold small text-dark">{{ $submission->student->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $submission->getStatusBadgeClass() }} smallest-badge">
                                                {{ $submission->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-end small text-muted">
                                            {{ $submission->created_at->format('d/m/Y') }}
                                        </td>
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

    @role('kaprodi')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card stats-primary">
                <div class="stats-icon-wrapper"><i class="bi bi-people"></i></div>
                <div>
                    <h6>Total Mahasiswa</h6>
                    <h2>{{ $stats['total_students'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-info">
                <div class="stats-icon-wrapper"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <h6>Total Pengajuan</h6>
                    <h2>{{ $stats['total_submissions'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-warning">
                <div class="stats-icon-wrapper"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <h6>Menunggu Review</h6>
                    <h2>{{ $stats['pending_submissions'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-success">
                <div class="stats-icon-wrapper"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <h6>Disetujui</h6>
                    <h2>{{ $stats['approved_submissions'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Segmented Student Lists --}}
    <div class="row g-4 mb-4">
        {{-- Sudah Mengumpulkan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 border-0">
                    <div class="d-flex align-items-center gap-2 fw-bold">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Sudah Mengumpulkan</span>
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle">{{ $stats['submitted_students_count'] ?? 0 }}</span>
                    </div>
                    <a href="{{ route('kaprodi.submissions.index') }}"
                        class="btn btn-sm btn-light border text-primary px-3">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(($stats['submitted_students'] ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Mahasiswa</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['submitted_students'] as $student)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                                <div class="text-muted smaller-text">{{ $student->nim_nip }}</div>
                                            </td>
                                            <td>
                                                @if($student->thesisSubmissions->first())
                                                    <span
                                                        class="badge bg-{{ $student->thesisSubmissions->first()->getStatusBadgeClass() }} smallest-badge">
                                                        {{ $student->thesisSubmissions->first()->getStatusLabel() }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('kaprodi.students.show', $student->id) }}"
                                                    class="btn btn-sm btn-outline-primary px-3">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted fs-3 d-block mb-2"></i>
                            <span class="text-muted small">Belum ada mahasiswa yang mengumpulkan.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Belum Mengumpulkan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3 border-0">
                    <div class="d-flex align-items-center gap-2 fw-bold">
                        <i class="bi bi-exclamation-circle-fill text-danger"></i>
                        <span>Belum Mengumpulkan</span>
                        <span
                            class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $stats['not_submitted_students_count'] ?? 0 }}</span>
                    </div>
                    <a href="{{ route('kaprodi.students.index') }}" class="btn btn-sm btn-light border text-primary px-3">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(($stats['not_submitted_students'] ?? collect())->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Mahasiswa</th>
                                        <th>NIM</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['not_submitted_students'] as $student)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $student->nim_nip }}</span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('kaprodi.students.show', $student->id) }}"
                                                    class="btn btn-sm btn-outline-secondary px-3">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-all text-success fs-3 d-block mb-2"></i>
                            <span class="text-muted small">Semua mahasiswa sudah mengumpulkan!</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Submissions Awaiting Lecturer Assignment --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <div class="d-flex align-items-center gap-2 fw-bold">
                <i class="bi bi-journal-text text-primary"></i>
                <span>Pengajuan Menunggu Penunjukan Dosen Penilai</span>
            </div>
            <a href="{{ route('kaprodi.submissions.index', ['status' => 'submitted']) }}"
                class="btn btn-sm btn-light border text-primary px-3">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            @php
                $pendingSubmissions = \App\Models\ThesisSubmission::where('status', 'submitted')
                    ->whereHas('student', function ($q) {
                        $q->where('program_studi_id', auth()->user()->program_studi_id);
                    })
                    ->with('student')
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp
            @if ($pendingSubmissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Mahasiswa</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingSubmissions as $submission)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $submission->student->name }}</div>
                                        <div class="text-muted smaller-text">{{ $submission->student->nim_nip }}</div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;">{{ $submission->title }}
                                        </div>
                                    </td>
                                    <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('kaprodi.students.show', $submission->student_id) }}"
                                            class="btn btn-sm btn-outline-primary px-3">
                                            <i class="bi bi-person-plus"></i> Atur Dosen
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success fs-1 mb-3 d-block"></i>
                    <h6 class="fw-bold text-muted">Tidak ada pengajuan yang menunggu.</h6>
                </div>
            @endif
        </div>
    </div>
    @endrole
@endsection