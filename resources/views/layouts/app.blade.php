<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Field Service Manager') - FSM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-hover: rgba(99,102,241,0.15);
            --sidebar-active: #6366f1;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-muted: #64748b;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--body-bg); color: var(--text-primary); }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            z-index: 1000; overflow-y: auto; transition: transform 0.3s ease;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-brand h5 { color: #fff; font-weight: 700; font-size: 1rem; margin: 0; }
        .sidebar-brand small { color: var(--sidebar-text); font-size: 0.75rem; }
        .sidebar-logo {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--primary); display: inline-flex; align-items: center;
            justify-content: center; margin-right: 0.75rem;
        }
        .sidebar-nav { padding: 1rem 0.75rem; flex: 1; }
        .sidebar-label {
            font-size: 0.65rem; font-weight: 600; letter-spacing: 0.08em;
            color: #475569; text-transform: uppercase; padding: 0.5rem 0.5rem 0.25rem;
            margin-top: 0.5rem;
        }
        .nav-link {
            display: flex; align-items: center; padding: 0.6rem 0.75rem;
            border-radius: 8px; color: var(--sidebar-text); font-size: 0.875rem;
            font-weight: 500; transition: all 0.2s; margin-bottom: 2px;
        }
        .nav-link:hover { background: var(--sidebar-hover); color: #e2e8f0; }
        .nav-link.active { background: var(--primary); color: #fff; }
        .nav-link i { font-size: 1rem; margin-right: 0.7rem; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 1rem 0.75rem; border-top: 1px solid rgba(255,255,255,0.06);
        }
        .user-card {
            background: rgba(255,255,255,0.05); border-radius: 10px;
            padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem;
        }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center;
            justify-content: center; color: #fff; font-weight: 600; font-size: 0.875rem;
            flex-shrink: 0;
        }
        .user-name { color: #e2e8f0; font-size: 0.8rem; font-weight: 600; }
        .user-role { color: var(--sidebar-text); font-size: 0.7rem; }

        /* Main content */
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar {
            background: #fff; padding: 0.875rem 1.5rem; border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar h6 { margin: 0; font-weight: 600; color: var(--text-primary); }
        .page-content { padding: 1.5rem; }

        /* Cards */
        .card {
            border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .card-header {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem; font-weight: 600;
        }
        .stat-card {
            border-radius: 12px; padding: 1.25rem; color: #fff;
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; right: -20px; top: -20px;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .stat-card .icon {
            width: 46px; height: 46px; border-radius: 10px;
            background: rgba(255,255,255,0.2); display: flex;
            align-items: center; justify-content: center; font-size: 1.3rem;
        }
        .stat-card h3 { font-size: 1.75rem; font-weight: 700; margin: 0.5rem 0 0.25rem; }
        .stat-card p { margin: 0; font-size: 0.8rem; opacity: 0.85; }

        /* Badges */
        .badge { font-size: 0.7rem; font-weight: 600; padding: 0.35em 0.65em; }

        /* Status colors */
        .status-pending    { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-completed  { background: #d1fae5; color: #065f46; }
        .status-cancelled  { background: #fee2e2; color: #991b1b; }

        /* Tables */
        .table th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.04em; color: var(--text-muted); background: #f8fafc; }
        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* Buttons */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Forms */
        .form-control, .form-select {
            border-radius: 8px; border-color: #e2e8f0; font-size: 0.875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }
        .form-label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }

        /* Image gallery */
        .img-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 0.75rem; }
        .img-thumb { position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; }
        .img-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .img-thumb .delete-btn {
            position: absolute; top: 4px; right: 4px; background: rgba(220,38,38,0.85);
            border: none; border-radius: 4px; color: #fff; padding: 2px 6px; font-size: 0.7rem;
            cursor: pointer; opacity: 0; transition: opacity 0.2s;
        }
        .img-thumb:hover .delete-btn { opacity: 1; }

        /* Map */
        #job-map { height: 280px; border-radius: 10px; border: 1px solid #e2e8f0; }

        /* Animations */
        .fade-in { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center">
            <div class="sidebar-logo">
                <i class="bi bi-bug text-white fs-5"></i>
            </div>
            <div>
                <h5>FieldServicePro</h5>
                <small>Pest Control Manager</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <span class="sidebar-label">Main</span>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i> Jobs
            </a>
            @role('admin')
            <span class="sidebar-label">Management</span>
            <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Clients
            </a>
            <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Technicians
            </a>
            @endrole
        </nav>
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0 ,1)) }}</div>
                <div class="flex-1">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-sm p-1" style="background:transparent;border:none;">
                        <i class="bi bi-box-arrow-right text-danger"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h6>@yield('page-title', 'Dashboard')</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(session('success'))
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</span>
                @endif
                @yield('topbar-actions')
            </div>
        </header>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="page-content fade-in">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
