<nav class="navbar navbar-expand-lg fixed-top shadow-none border-bottom">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <a class="navbar-brand me-4 d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo_uvers.webp')}}" alt="Logo"
                    height="40" class="me-2 rounded dynamic-logo">
                <span>Universitas Universal</span>
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
                <li class="nav-item me-3">
                    <button class="btn btn-link nav-link p-0 border-0" id="themeToggle" title="Ganti Tema">
                        <i class="bi bi-moon-stars-fill fs-5"></i>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <div class="avatar-circle">
                            <img src="{{ auth()->user()->profile_photo_url }}" class="rounded-circle shadow-sm"
                                style="width: 32px; height: 32px; object-fit: cover;" alt="Profile">
                        </div>
                        <div class="d-none d-sm-block text-start">
                            <span class="fw-semibold d-block lh-1">{{ auth()->user()->name }}</span>
                            @if (auth()->user()->programStudi)
                                <small class="text-muted smaller-extra d-block mt-1">
                                    {{ auth()->user()->programStudi->name }}
                                </small>
                            @endif
                        </div>
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
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
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