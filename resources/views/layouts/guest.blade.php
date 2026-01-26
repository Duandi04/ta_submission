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

    <style>
        :root {
            --primary-light: #e0f2fe;
            --primary-100: #bae6fd;
            --primary-200: #7dd3fc;
            --primary-300: #38bdf8;
            --primary-400: #0ea5e9;
            --primary-500: #0284c7;
            --primary-600: #0369a1;
            --primary-700: #075985;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f9ff 50%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow:
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.08),
                0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(186, 230, 253, 0.5);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-400) 0%, var(--primary-500) 100%);
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
            animation: shimmer 8s infinite linear;
        }

        @keyframes shimmer {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .login-header .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 1;
        }

        .login-header .icon-wrapper i {
            font-size: 2.5rem;
            color: white;
        }

        .login-header h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1rem;
            height: auto;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-400);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
            background: white;
        }

        .form-floating label {
            color: #64748b;
            font-weight: 500;
        }

        .form-check {
            margin: 1rem 0 1.5rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
        }

        .form-check-input:checked {
            background-color: var(--primary-400);
            border-color: var(--primary-400);
        }

        .form-check-label {
            color: #475569;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-400) 0%, var(--primary-500) 100%);
            color: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            padding: 0 1rem;
        }

        .demo-credentials {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }

        .demo-credentials h6 {
            color: var(--primary-600);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-credentials p {
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .demo-credentials strong {
            color: var(--primary-700);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
        }

        .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: -1;
        }

        .floating-shapes .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.5;
        }

        .floating-shapes .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--primary-100);
            top: -100px;
            right: -100px;
            animation: float 20s infinite ease-in-out;
        }

        .floating-shapes .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--primary-200);
            bottom: -50px;
            left: -50px;
            animation: float 25s infinite ease-in-out reverse;
        }

        .floating-shapes .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--primary-100);
            top: 50%;
            left: 10%;
            animation: float 30s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(20px, -20px) rotate(5deg);
            }

            50% {
                transform: translate(-10px, 20px) rotate(-5deg);
            }

            75% {
                transform: translate(-20px, -10px) rotate(3deg);
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="login-container">
        @yield('content')
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>