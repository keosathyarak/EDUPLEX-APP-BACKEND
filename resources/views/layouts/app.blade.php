<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EduPlex Dashboard')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
            font-family: 'Segoe UI', sans-serif;
        }
        .icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 270px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0b1220, #111827);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform .3s ease;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
        }

        .sidebar-header {
            padding: 20px;
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        /* ===== PROFILE ===== */
        .profile-box {
            padding: 16px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .avatar {
            width: 44px;
            height: 44px;
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== MENU ===== */
        .menu-title {
            font-size: 11px;
            letter-spacing: .08em;
            color: #9ca3af;
            padding: 14px 20px 6px;
            text-transform: uppercase;
        }

        .menu a {
            padding: 12px 20px;
            color: #d1d5db;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all .2s ease;
        }

        .menu a i {
            font-size: 18px;
        }

        .menu a:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .menu a.active {
            background: rgba(37,99,235,.25);
            color: #fff;
            border-left: 4px solid #2563eb;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 270px;
            transition: margin-left .3s ease;
        }

        @media (max-width: 991px) {
            .content {
                margin-left: 0;
            }
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 20px;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside id="sidebar" class="sidebar">

    <div class="sidebar-header d-flex justify-content-between align-items-center">
        EduPlex
        <button class="btn btn-sm btn-outline-light d-lg-none" onclick="toggleSidebar()">
            <i class="bi bi-x"></i>
        </button>
    </div>

    {{-- PROFILE --}}
    <div class="profile-box d-flex align-items-center gap-3">
        <div class="avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div>
            <div class="text-white fw-semibold">
                {{ auth()->user()->name ?? 'Guest User' }}
            </div>
            <small class="text-secondary">
                {{ auth()->user()->email ?? '' }}
            </small>
        </div>
    </div>

    {{-- MENU --}}
    <nav class="menu mt-2">

        <div class="menu-title">Main</div>
        <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="menu-title">Management</div>
        <a href="/courses" class="{{ request()->is('courses*') ? 'active' : '' }}">
            <i class="bi bi-book"></i> Courses
        </a>
        <a href="#">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a href="/user" class="{{ request()->is('user') ? 'active' : '' }}">
             <i class="bi bi-person-circle"></i> Users
        </a>

        <div class="menu-title">Analytics</div>
        <a href="#">
            <i class="bi bi-bar-chart"></i> Reports
        </a>

        <div class="menu-title">Account</div>
        <a href="#">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        <a href="#">
            <i class="bi bi-gear"></i> Settings
        </a>

        {{-- LOGOUT --}}
        <form method="POST" action="/api/logout" class="px-3 mt-3">
            @csrf
            <button class="btn btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>

    </nav>
</aside>

{{-- CONTENT --}}
<div class="content">

    {{-- TOPBAR --}}
    <div class="topbar d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary d-lg-none" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>

        <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
    </div>

    {{-- PAGE --}}
    <main class="p-4">
        @yield('content')
    </main>

</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
    }
</script>

@stack('scripts')
</body>
</html>
