<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::getValue('campus_name', 'Sistem Pengajuan TA'))</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-none">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <a class="navbar-brand me-4" href="{{ route('dashboard') }}">
                    <i
                        class="bi bi-mortarboard-fill me-2"></i>{{ \App\Models\Setting::getValue('campus_name', 'Sistem TA') }}
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent">
                <i class="bi bi-three-dots-vertical"></i>
            </button>

            <div class="collapse navbar-collapse px-3" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <div
                                class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center avatar-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="fw-semibold d-none d-sm-block">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                            <li><a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profil</a></li>
                            <li>
                                <hr class="dropdown-divider opacity-50">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar shadow-none">
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @role('mahasiswa')
                <div class="sidebar-label">Mahasiswa</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.submissions.*') ? 'active' : '' }}"
                        href="{{ route('student.submissions.index') }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Pengajuan Saya</span>
                    </a>
                </li>
                @endrole

                @role('dosen_pembimbing')
                <div class="sidebar-label">Dosen</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('supervisor.students.*') ? 'active' : '' }}"
                        href="{{ route('supervisor.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Mahasiswa Bimbingan</span>
                    </a>
                </li>
                @role('dosen_penguji')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('examiner.assessments.*') ? 'active' : '' }}"
                        href="{{ route('examiner.assessments.index') }}">
                        <i class="bi bi-clipboard-check-fill"></i>
                        <span>Penilaian</span>
                    </a>
                </li>
                @endrole
                @endrole

                @role('koordinator')
                <div class="sidebar-label">Koordinator</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('coordinator.students.*') ? 'active' : '' }}"
                        href="{{ route('coordinator.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Daftar Mahasiswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('coordinator.reports.*') ? 'active' : '' }}"
                        href="{{ route('coordinator.reports.index') }}">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                @endrole

                @role('kaprodi')
                <div class="sidebar-label">Kaprodi</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.students.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Seluruh Mahasiswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.rubrics.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.rubrics.index') }}">
                        <i class="bi bi-clipboard-data-fill"></i>
                        <span>Rubrik Penilaian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.settings.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.settings.index') }}">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan TA</span>
                    </a>
                </li>
                @endrole

                @role('admin')
                <div class="sidebar-label">Administrator</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.faculties.*') ? 'active' : '' }}"
                        href="{{ route('admin.faculties.index') }}">
                        <i class="bi bi-building-fill"></i>
                        <span>Kelola Fakultas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.program-studis.*') ? 'active' : '' }}"
                        href="{{ route('admin.program-studis.index') }}">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>Kelola Prodi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.configuration.*') ? 'active' : '' }}"
                        href="{{ route('admin.configuration.index') }}">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}"
                        href="{{ route('admin.activity-logs.index') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Log Aktivitas</span>
                    </a>
                </li>
                @endrole
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main id="main-content">
        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-none mb-4 py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-none mb-4 py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>