<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-bg: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #e2e8f0 0%, #f8fafc 50%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #1e293b;
            overflow: hidden;
        }

        .error-container {
            max-width: 500px;
            width: 90%;
            text-align: center;
            padding: 3rem;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-out;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        h1 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        p {
            color: #64748b;
            font-size: 1.125rem;
            margin-bottom: 2.5rem;
        }

        .btn-home {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #0f172a;
            border: none;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-home:hover {
            background-color: #334155;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-home:active {
            transform: translateY(0);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Decorative background elements */
        .decor {
            position: absolute;
            z-index: -1;
            filter: blur(60px);
            opacity: 0.5;
        }

        .decor-1 {
            top: 10%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: #93c5fd;
            border-radius: 50%;
        }

        .decor-2 {
            bottom: 10%;
            right: 10%;
            width: 350px;
            height: 350px;
            background: #f9a8d4;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <div class="decor decor-1"></div>
    <div class="decor decor-2"></div>

    <div class="error-container">
        <div class="error-icon">
            @yield('icon')
        </div>
        <div class="error-code">
            @yield('code')
        </div>
        <h1>@yield('message')</h1>
        <p>@yield('description')</p>

        <a href="{{ url('/') }}" class="btn btn-home">
            <i class="bi bi-house-door"></i> Kembali ke Beranda
        </a>
    </div>
</body>

</html>
