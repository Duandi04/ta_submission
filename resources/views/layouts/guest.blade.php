<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengajuan TA')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS (includes guest/login styles) -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
    
    <script>
        // Immediate Theme Initialization
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
</head>

<body class="guest-layout">
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="login-container">
        @yield('content')
    </div>

    <!-- Theme Toggle Button -->
    <div class="position-fixed top-0 end-0 p-4" style="z-index: 1050;">
        <button class="btn btn-light shadow-sm rounded-circle d-flex align-items-center justify-content-center p-2" 
                id="themeToggle" 
                style="width: 45px; height: 45px; background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);"
                tabindex="4">
            <i class="bi bi-moon-stars-fill fs-5"></i>
        </button>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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