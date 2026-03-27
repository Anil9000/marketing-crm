<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Marketing CRM</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Custom theme stylesheet (loaded before inline styles so inline overrides take priority) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f1117;
            --sidebar-border: #1e2130;
            --card-bg: #161923;
            --card-border: #1e2130;
            --accent: #6366f1;
            --accent-hover: #4f52d9;
            --text-muted-custom: #8892a4;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background-color: #0a0c12;
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: white;
        }

        .sidebar-brand .brand-name {
            font-weight: 700;
            font-size: 1rem;
            color: #f1f5f9;
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.65rem;
            color: var(--text-muted-custom);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted-custom);
            padding: 0.5rem 1.25rem;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem;
            color: #94a3b8;
            border-radius: 0;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s;
            text-decoration: none;
            margin: 1px 0.5rem;
            border-radius: 8px;
        }

        .sidebar-nav .nav-link:hover {
            color: #f1f5f9;
            background: rgba(99, 102, 241, 0.1);
        }

        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(99, 102, 241, 0.2);
            border-left: 3px solid var(--accent);
        }

        .sidebar-nav .nav-link i {
            width: 18px;
            font-size: 1rem;
        }

        .sidebar-footer {
            border-top: 1px solid var(--sidebar-border);
            padding: 1rem;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 10px;
            transition: background 0.15s;
            cursor: pointer;
        }

        .user-card:hover { background: rgba(255,255,255,0.05); }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .user-info .user-role {
            font-size: 0.7rem;
            color: var(--text-muted-custom);
        }

        /* Main content */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top navbar */
        .top-navbar {
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--sidebar-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Cards */
        .crm-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.25rem;
            transition: transform 0.15s, border-color 0.15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--accent);
        }

        .stat-icon {
            width: 52px; height: 52px;
            min-width: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
            margin-left: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f1f5f9;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted-custom);
        }

        /* Table */
        .crm-table {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--card-border);
        }

        .crm-table .table {
            margin: 0;
            color: #e2e8f0;
        }

        .crm-table .table thead th {
            background: #1a1f2e;
            border-color: var(--card-border);
            color: var(--text-muted-custom);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.875rem 1rem;
        }

        .crm-table .table tbody td {
            border-color: var(--card-border);
            padding: 0.875rem 1rem;
            vertical-align: middle;
        }

        .crm-table .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        /* Buttons */
        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .btn-accent:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: white;
        }

        /* Page header */
        .page-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin: 0;
        }

        .page-content { padding: 1.5rem; }

        /* Badge overrides */
        .badge { font-weight: 500; }

        /* Alerts */
        .alert { border: none; border-radius: 10px; }

        /* Form controls */
        .form-control, .form-select {
            background: #1a1f2e;
            border-color: var(--card-border);
            color: #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            background: #1a1f2e;
            border-color: var(--accent);
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-control::placeholder { color: #4a5568; }

        .form-label { color: #94a3b8; font-size: 0.85rem; font-weight: 500; }

        /* Pagination — override all Bootstrap defaults */
        .pagination .page-link {
            background: var(--card-bg);
            border-color: var(--card-border);
            color: #94a3b8;
            font-size: 0.8rem !important;
            line-height: 1.5 !important;
            padding: 0.3rem 0.6rem !important;
        }

        .pagination .page-link:hover,
        .pagination .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        /* Prevent Bootstrap Icons CDN from injecting huge icons into pagination */
        .pagination .page-link::before,
        .pagination .page-link::after {
            content: none !important;
            display: none !important;
        }

        /* Mobile */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2d3748; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #4a5568; }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-lightning-charge-fill"></i></div>
        <div>
            <div class="brand-name">Marketing CRM</div>
            <div class="brand-sub">Platform v1.0</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="nav-section-label">Marketing</div>

        <a href="{{ route('campaigns.index') }}" class="nav-link {{ request()->routeIs('campaigns.*') && !request()->routeIs('campaigns.calendar') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i> Campaigns
        </a>

        <a href="{{ route('campaigns.calendar') }}" class="nav-link {{ request()->routeIs('campaigns.calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i> Calendar
        </a>

        <a href="{{ route('email-templates.index') }}" class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}">
            <i class="bi bi-envelope-paper"></i> Email Templates
        </a>

        <div class="nav-section-label">Audience</div>

        <a href="{{ route('contacts.index') }}" class="nav-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Contacts
        </a>

        <a href="{{ route('segments.index') }}" class="nav-link {{ request()->routeIs('segments.*') ? 'active' : '' }}">
            <i class="bi bi-funnel"></i> Segments
        </a>

        <div class="nav-section-label">Growth</div>

        <a href="{{ route('lead-forms.index') }}" class="nav-link {{ request()->routeIs('lead-forms.*') ? 'active' : '' }}">
            <i class="bi bi-ui-checks"></i> Lead Forms
        </a>

        <a href="{{ route('analytics.index') }}" class="nav-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Analytics
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="dropdown">
            <div class="user-card" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="user-avatar">
                <div class="user-info flex-grow-1">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role->label() }}</div>
                </div>
                <i class="bi bi-three-dots-vertical text-muted"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div id="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Home</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success rounded-pill small">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Live
            </span>
            <a href="#" class="btn btn-sm btn-outline-secondary position-relative">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">3</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mx-3 mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mx-3 mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mx-3 mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Page Content -->
    @yield('content')
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Custom app JS (Chart helpers, AJAX helpers, Toast, Segment builder) -->
<script src="{{ asset('js/app.js') }}"></script>

<script>
    // Sidebar toggle for mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            bootstrap.Alert.getOrCreateInstance(alert)?.close();
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
