<aside id="sidebar" class="sidebar shadow-none">
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @can('view menu: mahasiswa')
                <div class="sidebar-label">Mahasiswa</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.submissions.*') ? 'active' : '' }}"
                        href="{{ route('student.submissions.index') }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Pengajuan Saya</span>
                    </a>
                </li>
            @endcan

            @can('view menu: dosen')
                <div class="sidebar-label">Dosen</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dosen.students.index') ? 'active' : '' }}"
                        href="{{ route('dosen.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Mahasiswa Bimbingan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dosen.assessments.*') ? 'active' : '' }}"
                        href="{{ route('dosen.assessments.index') }}">
                        <i class="bi bi-clipboard-check-fill"></i>
                        <span>Proposal Mahasiswa</span>
                    </a>
                </li>
            @endcan



            @can('view menu: kaprodi')
                <div class="sidebar-label">Kaprodi</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.students.manage.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.students.manage.index') }}">
                        <i class="bi bi-person-fill-gear"></i>
                        <span>Kelola Mahasiswa</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.students.index') || request()->routeIs('kaprodi.students.show') ? 'active' : '' }}"
                        href="{{ route('kaprodi.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Proposal Mahasiswa</span>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.lecturers.manage.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.lecturers.manage.index') }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Kelola Dosen</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.submissions.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.submissions.index') }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Daftar Pengajuan</span>
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
            @endcan

            @can('view menu: reports')
                <div class="sidebar-label">Laporan & Cetak</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kaprodi.reports.*') ? 'active' : '' }}"
                        href="{{ route('kaprodi.reports.index') }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        <span>Laporan Proposal Diterima</span>
                    </a>
                </li>
            @endcan

            @can('view menu: admin')
                <div class="sidebar-label">Administrator</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.lecturers.*') ? 'active' : '' }}"
                        href="{{ route('admin.lecturers.index') }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Kelola Dosen</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                        href="{{ route('admin.students.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Kelola Mahasiswa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.index') && !request()->has('role_group') && (request('role') != 'mahasiswa' || !request()->has('role')) ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Semua Pengguna</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}"
                        href="{{ route('admin.submissions.index') }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Daftar Pengajuan</span>
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
                @can('manage rbac')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.rbac.*') ? 'active' : '' }}"
                        href="{{ route('admin.rbac.index') }}">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Kelola RBAC</span>
                    </a>
                </li>
                @endcan
            @endcan
        </ul>
    </div>
</aside>
