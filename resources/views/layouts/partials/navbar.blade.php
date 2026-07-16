<nav class="navbar navbar-expand-lg fixed-top shadow-none border-bottom">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a class="navbar-brand ms-2 ms-lg-4 d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo_uvers.webp')}}" alt="Logo"
                    height="32" class="me-2 rounded dynamic-logo d-none d-sm-block">
                <span class="d-none d-sm-inline text-truncate" style="max-width: 200px;">Universitas Universal</span>
                <span class="d-inline d-sm-none fw-bold">UVERS TA</span>
            </a>
        </div>

        <div class="d-flex align-items-center gap-2 gap-sm-3 ms-auto">
            <button class="btn btn-link nav-link p-0 border-0 me-1" id="themeToggle" title="Ganti Tema">
                <i class="bi bi-moon-stars-fill fs-5"></i>
            </button>
            <div class="dropdown">
                <a class="nav-link d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right:0;">
                    <div class="avatar-circle">
                        <img src="{{ auth()->user()->profile_photo_url }}" class="rounded-circle shadow-sm"
                            style="width: 32px; height: 32px; object-fit: cover;" alt="Profile">
                    </div>
                    <div class="d-none d-sm-block text-start pe-2">
                        <span class="fw-semibold d-block lh-1">{{ auth()->user()->name }}</span>
                        @if (auth()->user()->programStudi)
                            <small class="text-muted smaller-extra d-block mt-1">
                                {{ auth()->user()->programStudi->name }}
                            </small>
                        @endif
                    </div>
                    <i class="bi bi-chevron-down d-none d-sm-block text-muted" style="font-size: 0.8rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 position-absolute">
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profil</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>