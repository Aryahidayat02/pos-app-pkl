<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS App')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --pos-primary: #4f46e5;
            --pos-sidebar-bg: #1e1e2f;
            --pos-bg: #f8fafc;
        }

        body {
            background-color: var(--pos-bg);
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: var(--pos-sidebar-bg);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-size: 1.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-menu {
            padding: 15px 12px;
            list-style: none;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin: 15px 12px 8px;
            font-weight: 700;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .sidebar-menu li a:hover {
            background-color: rgba(255,255,255,0.05);
            color: #fff;
        }

        .sidebar-menu li a.active {
            background-color: var(--pos-primary);
            color: #fff;
            font-weight: 600;
        }

        .sidebar-menu li a i {
            font-size: 1.1rem;
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Atas */
        .topbar {
            background: #fff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .btn-logout {
            border: 1px solid #ef4444;
            color: #ef4444;
            background: transparent;
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: #fff;
        }

        .content-wrapper {
            padding: 30px;
            flex-grow: 1;
        }

        /* Alert Flash Message */
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shop"></i> {{ \App\Models\Setting::get('store_name', 'POS App') }}
        </div>
        
        <ul class="sidebar-menu">
            <li class="menu-header">Utama</li>
            
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <li>
                <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                    <i class="bi bi-cart"></i> Kasir
                </a>
            </li>

            <li>
                <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Riwayat Transaksi
                </a>
            </li>

            @if(auth()->user()->role === 'admin')
                <li class="menu-header">Master Data</li>
                
                <li>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Supplier
                    </a>
                </li>
                <li>
                    <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                    </a>
                </li>

                <li class="menu-header">Laporan & Setting</li>
                <li>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i> Manajemen User
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Pengaturan
                    </a>
                </li>

                {{-- MENU LOG AKTIVITAS B3 DENGAN WARNA KUNING --}}
                <li>
                    <a href="{{ route('logs.index') }}" class="{{ request()->routeIs('logs.*') ? 'active' : '' }}" style="color: #fbbf24; font-weight: bold;">
                        <i class="bi bi-clock-history"></i> LOG AKTIVITAS
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="topbar">
            <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            
            <div class="user-info">
                <span>{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>