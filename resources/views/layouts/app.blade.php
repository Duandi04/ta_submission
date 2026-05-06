<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::getValue('campus_name', 'Sistem Pengajuan TA'))</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/uvers_logo_blue.webp') }}">

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tom Select for tag-style multi-select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    @stack('styles')

    <!-- Immediate Sidebar State Initialization -->
    <script>
        (function() {
            // Sidebar State
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }

            // Theme State
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
</head>

<body>
    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <!-- Sidebar -->
    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <main id="main-content">
        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-none mb-4 py-2 small alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.8rem 1rem;"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-none mb-4 py-2 small alert-persistent alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    @if (session('import_errors'))
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach (session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.8rem 1rem;"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info border-0 shadow-none mb-4 py-2 small alert-persistent alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.8rem 1rem;"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('themeToggle');
            const icon = toggleBtn.querySelector('i');
            const html = document.documentElement;

            // Set initial icon
            const currentTheme = html.getAttribute('data-bs-theme');
            icon.className = currentTheme === 'dark' ? 'bi bi-sun-fill fs-5' : 'bi bi-moon-stars-fill fs-5';

            toggleBtn.addEventListener('click', () => {
                const newTheme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                icon.className = newTheme === 'dark' ? 'bi bi-sun-fill fs-5' : 'bi bi-moon-stars-fill fs-5';
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
