<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth') - Marketing CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: #0a0c12;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .auth-card {
            background: #161923;
            border: 1px solid #1e2130;
            border-radius: 16px;
            padding: 2.5rem;
        }

        .auth-logo {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: white;
            margin: 0 auto 1.5rem;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .auth-subtitle {
            color: #8892a4;
            text-align: center;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .form-control {
            background: #1a1f2e;
            border-color: #1e2130;
            color: #e2e8f0;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: #1a1f2e;
            border-color: #6366f1;
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-control::placeholder { color: #4a5568; }
        .form-label { color: #94a3b8; font-size: 0.85rem; font-weight: 500; }

        .btn-primary {
            background: #6366f1;
            border-color: #6366f1;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #4f52d9;
            border-color: #4f52d9;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #4a5568;
            font-size: 0.8rem;
            margin: 1.25rem 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #1e2130;
        }

        .btn-google {
            background: #1a1f2e;
            border: 1px solid #1e2130;
            color: #e2e8f0;
            padding: 0.75rem;
            font-weight: 500;
            width: 100%;
            transition: background 0.15s;
        }

        .btn-google:hover {
            background: #242938;
            color: #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-logo">
            <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <div class="auth-card">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @yield('content')
        </div>
        <div class="text-center mt-3 text-muted small">
            &copy; {{ date('Y') }} Marketing CRM. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
