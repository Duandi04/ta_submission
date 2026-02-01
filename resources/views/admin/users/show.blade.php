@extends('layouts.app')

@section('title', 'Detail Pengguna - ' . $user->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Detail Pengguna</h1>
                <div class="d-flex align-items-center">
                    @include('partials.record-navigation', ['route' => 'admin.users.show'])
                    <div class="ms-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning shadow-sm">
                            <i class="bi bi-pencil-square me-1"></i> Edit Pengguna
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Profile -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4 border-0 overflow-hidden">
                    <div class="card-header bg-gradient-primary py-5 text-center position-relative">
                        <div class="position-absolute top-0 end-0 p-2">
                            @if ($user->is_active)
                                <span class="badge bg-success rounded-pill px-3 shadow-sm border border-light">Aktif</span>
                            @else
                                <span
                                    class="badge bg-danger rounded-pill px-3 shadow-sm border border-light">Non-Aktif</span>
                            @endif
                        </div>
                        <div class="text-center mb-3">
                            <img src="{{ $user->profile_photo_url }}" class="rounded-circle img-thumbnail shadow-lg"
                                style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <h4 class="text-white fw-bold mb-0">{{ $user->name }}</h4>
                        <p class="text-white-50 mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="card-body pt-4">
                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Role Utama</label>
                            <div>
                                @foreach ($user->getRoleNames() as $role)
                                    <span
                                        class="badge bg-primary rounded-pill px-3">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                                @endforeach
                            </div>
                        </div>

                        @if ($user->programStudi)
                            <div class="mb-4">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Informasi Akademik</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-mortarboard text-primary me-2 mt-1"></i>
                                    <div>
                                        <div class="fw-bold">{{ $user->programStudi->name }}</div>
                                        <div class="small text-muted">{{ $user->programStudi->faculty->name ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="text-muted small text-uppercase fw-bold mb-1">Statistik Cepat</label>
                            <div class="row g-2">
                                @if ($user->hasRole('mahasiswa'))
                                    <div class="col-6 text-center border-end">
                                        <div class="h5 mb-0 fw-bold">{{ $user->thesisSubmissions->count() }}</div>
                                        <div class="small text-muted">Laporan</div>
                                    </div>
                                @endif
                                @if ($user->hasRole(['dosen', 'kaprodi']))
                                    <div class="col-6 text-center border-end">
                                        {{-- Assuming a relationship or logic for supervised theses --}}
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\ThesisSubmission::where('supervisor_id', $user->id)->count() }}
                                        </div>
                                        <div class="small text-muted">Bimbingan</div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="h5 mb-0 fw-bold">{{ $user->assessments->count() }}</div>
                                        <div class="small text-muted">Penilaian</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card shadow mb-4 border-0 border-start border-danger border-4">
                    <div class="card-body">
                        <h5 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona
                            Berbahaya</h5>
                        <p class="small text-muted mb-4">Tindakan berikut akan menghapus seluruh data user secara permanen
                            dari sistem.</p>
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" data-confirm-delete
                                    data-confirm-message="Apakah Anda yakin ingin menghapus user ini? Seluruh data terkait juga akan terhapus.">
                                    <i class="bi bi-trash3 me-1"></i> Hapus User
                                </button>
                            </form>
                        @else
                            <div class="alert alert-light border small">Anda tidak dapat menghapus akun sendiri.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-xl-8 col-lg-7">
                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-general-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-general" type="button" role="tab">Informasi Umum</button>
                    </li>
                    @if ($user->hasRole('mahasiswa'))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-reports-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-reports" type="button" role="tab">Daftar Laporan</button>
                        </li>
                    @endif
                    @if ($user->hasRole(['dosen', 'kaprodi']))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-assessments-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-assessments" type="button" role="tab">Riwayat Penilaian</button>
                        </li>
                    @endif
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-activity-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-activity" type="button" role="tab">Aktivitas</button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <!-- General Info Tab -->
                    <div class="tab-pane fade show active" id="pills-general" role="tabpanel">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Biodata Pengguna</h5>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Nama Lengkap</div>
                                    <div class="col-sm-8 fw-bold">{{ $user->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">NIM / NIP</div>
                                    <div class="col-sm-8 px-2 py-1 bg-light d-inline-block rounded fw-mono">
                                        {{ $user->nim_nip ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Email</div>
                                    <div class="col-sm-8 text-primary">{{ $user->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Nomor Telepon</div>
                                    <div class="col-sm-8">{{ $user->phone ?? '-' }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Alamat</div>
                                    <div class="col-sm-8">{{ $user->address ?? '-' }}</div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col-sm-4 text-muted">Terdaftar Pada</div>
                                    <div class="col-sm-8 small text-muted">{{ $user->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reports Tab (Student) -->
                    @if ($user->hasRole('mahasiswa'))
                        <div class="tab-pane fade" id="pills-reports" role="tabpanel">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-4 border-bottom pb-2">Daftar Laporan Tugas Akhir</h5>
                                    @if ($user->thesisSubmissions->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Judul Laporan</th>
                                                        <th>Tahun</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($user->thesisSubmissions as $submission)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-bold text-dark">
                                                                    {{ Str::limit($submission->title, 70) }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    {{ $submission->created_at->format('d/m/Y') }}</div>
                                                            </td>
                                                            <td>{{ $submission->created_at->format('Y') }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $submission->getStatusBadgeClass() }} rounded-pill px-3">
                                                                    {{ $submission->getStatusLabel() }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.thesis-submissions.show', $submission) }}"
                                                                    class="btn btn-sm btn-outline-primary">Detail</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-file-earmark-x text-muted display-1"></i>
                                            <p class="mt-3 text-muted">Belum ada laporan yang diajukan.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Assessments Tab (Dosen) -->
                    @if ($user->hasRole(['dosen', 'kaprodi']))
                        <div class="tab-pane fade" id="pills-assessments" role="tabpanel">
                            <div class="card shadow border-0">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-4 border-bottom pb-2">Daftar Mahasiswa yang Diaudit</h5>
                                    @if ($user->assessments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Mahasiswa</th>
                                                        <th>Judul Laporan</th>
                                                        <th>Tipe Mentor</th>
                                                        <th>Status Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($user->assessments as $assessment)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-bold text-dark">
                                                                    {{ $assessment->thesisSubmission->student->name }}
                                                                </div>
                                                                <div class="small text-muted">
                                                                    {{ $assessment->thesisSubmission->student->nim_nip }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="small text-truncate"
                                                                    style="max-width: 200px;">
                                                                    {{ $assessment->thesisSubmission->title }}</div>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-info-subtle text-info border border-info-subtle">{{ $assessment->getEvaluatorTypeLabel() }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($assessment->is_submitted)
                                                                    <span class="badge bg-success rounded-pill px-3">
                                                                        <i class="bi bi-check-circle me-1"></i>Final:
                                                                        {{ $assessment->total_score }}
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-warning text-dark rounded-pill px-3">
                                                                        <i class="bi bi-clock me-1"></i>Draft
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-person-workspace text-muted display-1"></i>
                                            <p class="mt-3 text-muted">Guro ini belum pernah memberikan penilaian.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="pills-activity" role="tabpanel">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <h5 class="fw-bold mb-4 border-bottom pb-2">Aktivitas Terakhir</h5>
                                @if ($activities->count() > 0)
                                    <div class="timeline-small mt-3">
                                        @foreach ($activities as $activity)
                                            <div class="item d-flex mb-4">
                                                <div class="icon me-3">
                                                    @if ($activity->event === 'created')
                                                        <i class="bi bi-plus-circle-fill text-success"></i>
                                                    @elseif($activity->event === 'updated')
                                                        <i class="bi bi-pencil-fill text-warning"></i>
                                                    @elseif($activity->event === 'deleted')
                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                    @else
                                                        <i class="bi bi-dot text-primary"
                                                            style="font-size: 2rem; margin-top: -10px;"></i>
                                                    @endif
                                                </div>
                                                <div class="content flex-grow-1 border-bottom pb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span
                                                            class="badge bg-light text-dark border">{{ $activity->description }}</span>
                                                        <small
                                                            class="text-muted">{{ $activity->created_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                    <div class="small text-dark">
                                                        @if ($activity->causer_id === $user->id)
                                                            <strong>Anda</strong> melakukan tindakan pada
                                                        @else
                                                            <strong>{{ $activity->causer?->name ?? 'Sistem' }}</strong>
                                                            melakukan tindakan pada
                                                        @endif
                                                        <span
                                                            class="text-primary">{{ class_basename($activity->subject_type) }}</span>
                                                    </div>
                                                    @if (isset($activity->properties['attributes']))
                                                        <div class="bg-light p-2 rounded mt-2 small">
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($activity->properties['attributes'] as $key => $value)
                                                                    @if (!in_array($key, ['updated_at', 'created_at', 'password']))
                                                                        <li>
                                                                            <span
                                                                                class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                            @if (isset($activity->properties['old'][$key]))
                                                                                <span
                                                                                    class="text-danger text-decoration-line-through">{{ $activity->properties['old'][$key] }}</span>
                                                                                <i class="bi bi-arrow-right mx-1"></i>
                                                                            @endif
                                                                            <span
                                                                                class="text-success fw-bold">{{ $value }}</span>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="bi bi-clock-history text-muted display-1"></i>
                                        <p class="mt-3 text-muted">Belum ada aktivitas yang tercatat untuk pengguna ini.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .fw-mono {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .nav-pills .nav-link.active {
            background-color: #4e73df;
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
        }

        .card {
            border-radius: 12px;
        }
    </style>
@endsection
