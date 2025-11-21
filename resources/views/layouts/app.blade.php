<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--primary-color);
            border-right: 1px solid var(--border-color);
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 15px;
            margin: 2px 0;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .navbar {
            background-color: white !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .navbar-text {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .btn-outline-danger {
            border-color: #e74c3c;
            color: #e74c3c;
        }
        
        .btn-outline-danger:hover {
            background-color: #e74c3c;
            color: white;
        }
        
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
        }
        
        .alert-success {
            border-left-color: #27ae60;
            background-color: #d5f4e6;
        }
        
        .alert-danger {
            border-left-color: #e74c3c;
            background-color: #fadbd8;
        }
        
        .sidebar-heading {
            font-size: 0.75rem;
            font-weight: 600;
            color: #bdc3c7;
        }
        
        hr {
            border-color: #4a5f7a;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .table th {
            background-color: var(--light-bg);
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white mb-1">JIPARTS</h5>
                        <small class="text-muted">Inventory System</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isGudang())
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="fas fa-box me-2"></i>
                                Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="fas fa-tags me-2"></i>
                                Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('stock-ins*') ? 'active' : '' }}" href="{{ route('stock-ins.index') }}">
                                <i class="fas fa-arrow-down me-2"></i>
                                Stok Masuk
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isKasir())
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('stock-outs*') ? 'active' : '' }}" href="{{ route('stock-outs.index') }}">
                                <i class="fas fa-arrow-up me-2"></i>
                                Stok Keluar
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('reports*') ? 'active' : '' }}" href="{{ route('reports.stock') }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Laporan
                            </a>
                        </li>
                        @endif
                    </ul>

                    @if(auth()->user()->isAdmin())
                    <hr>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                        <span>Administrasi</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="#">
                                <i class="fas fa-users me-2"></i>
                                Pengguna
                            </a>
                        </li>
                    </ul>
                    @endif
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="d-flex align-items-center">
                            <span class="navbar-text me-3">
                                <i class="fas fa-user me-1"></i>
                                {{ auth()->user()->name }} 
                                <span class="badge bg-secondary ms-1">{{ auth()->user()->role }}</span>
                            </span>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>