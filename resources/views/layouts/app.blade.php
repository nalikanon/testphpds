<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sales Order System')</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #212529;
        }
        .main-navbar {
            background-color: #ffffff;
            border-bottom: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            padding: 0.6rem 1.25rem;
        }
        .navbar-brand-logo {
            font-weight: 700;
            font-size: 1.15rem;
            color: #0d6efd;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar-brand-logo:hover {
            color: #0a58ca;
        }
        .nav-link {
            font-weight: 500;
            color: #495057;
            padding: 0.5rem 0.85rem !important;
            border-radius: 4px;
        }
        .nav-link:hover, .nav-link.active {
            color: #0d6efd;
            background-color: #eef2ff;
        }
        .card {
            border: 1px solid #e9ecef;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
        .table > :not(caption) > * > * {
            vertical-align: middle;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Header & Navigation Bar (5.2 Navigation Menu: User vs Admin) -->
    <nav class="navbar navbar-expand-lg main-navbar sticky-top">
        <div class="container-fluid px-3">
            <!-- [Logo] Sales Order System -->
            <a class="navbar-brand-logo me-4" href="{{ route('dashboard') }}">
                <span class="bg-primary text-white p-1 px-2 rounded small fw-bold">
                    <i class="bi bi-box-seam-fill"></i>
                </span>
                <span>Sales Order System</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>

                    <!-- Sales Orders (ทั้ง User & Admin) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                            <i class="bi bi-receipt me-1"></i> Sales Orders
                        </a>
                    </li>

                    @if(Auth::check() && Auth::user()->isAdmin())
                        <!-- Admin Menu: Masters (Role Full: Customers, Items Master, Users) -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('customers.*') || request()->routeIs('items.*') || request()->routeIs('users.*') ? 'active fw-bold' : '' }}" 
                               href="#" id="mastersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-database me-1"></i> [Masters - Role Full]
                            </a>
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="mastersDropdown">
                                <li><h6 class="dropdown-header text-uppercase small text-muted">Master Data (Full Control)</h6></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                        <span><i class="bi bi-people me-2 text-primary"></i> Customers</span>
                                        <span class="badge bg-success small">Full</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}">
                                        <span><i class="bi bi-box-seam me-2 text-success"></i> Items (Item Master)</span>
                                        <span class="badge bg-success small">Full</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header text-uppercase small text-danger">Administration</h6></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                        <span><i class="bi bi-person-gear me-2 text-danger"></i> Users Management</span>
                                        <span class="badge bg-danger small">Admin</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between py-2 {{ request()->routeIs('db.viewer') ? 'active' : '' }}" href="{{ route('db.viewer') }}">
                                        <span><i class="bi bi-database-check me-2 text-info"></i> DB Live Inspector</span>
                                        <span class="badge bg-info text-dark small">Live DB</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- User Menu: Customers (Read Only) & Items (Read Only) -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                <i class="bi bi-people me-1"></i> Customers <span class="badge bg-secondary" style="font-size: 0.65rem;">Read Only</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}">
                                <i class="bi bi-box-seam me-1"></i> Items <span class="badge bg-secondary" style="font-size: 0.65rem;">Read Only</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Right Nav: User Display & [Logout] Button -->
                <div class="d-flex align-items-center gap-3 ms-auto mt-2 mt-lg-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-5 text-secondary me-2"></i>
                        <span class="fw-semibold text-dark">{{ Auth::user()->username }}</span>
                        <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-primary' }} ms-2" style="font-size: 0.72rem;">
                            {{ strtoupper(Auth::user()->role ?? 'USER') }}
                        </span>
                    </div>

                    <a href="{{ route('profile') }}" class="btn btn-sm btn-outline-secondary" title="เปลี่ยนรหัสผ่าน">
                        <i class="bi bi-key"></i>
                    </a>

                    <!-- [Logout] Form & Button -->
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                            <i class="bi bi-box-arrow-right me-1"></i> [Logout]
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container-fluid px-4 py-4">
        <!-- Flash Alert Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 flex-shrink-0"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> พบข้อผิดพลาด:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center text-muted py-3 mt-4 small border-top bg-white">
        Simple Sales Order Management System &copy; {{ date('Y') }} | Laravel &bull; Bootstrap 5 &bull; MSSQL
    </footer>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
