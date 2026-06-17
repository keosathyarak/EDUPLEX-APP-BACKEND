<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduPlex Dashboard')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Google Fonts - Kantumruy Pro --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-body: #f4f6fb;
            --sidebar-bg: linear-gradient(180deg, #0b1220, #111827);
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --border-color: rgba(0,0,0,0.05);
        }

        [data-bs-theme="dark"] {
            --bg-body: #0f172a;
            --sidebar-bg: #1e293b;
            --card-bg: #1e293b;
            --text-main: #f1f5f9;
            --border-color: rgba(255,255,255,0.05);
        }

        body {
            background: var(--bg-body);
            color: var(--text-main);
            font-family: 'Kantumruy Pro', 'Segoe UI', sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        .card { background: var(--card-bg); border-color: var(--border-color); }
        
        /* Dark mode global overrides */
        [data-bs-theme="dark"] .bg-white { background-color: var(--card-bg) !important; }
        [data-bs-theme="dark"] .bg-light { background-color: rgba(255,255,255,0.05) !important; color: var(--text-main) !important; }
        [data-bs-theme="dark"] .text-dark { color: var(--text-main) !important; }
        [data-bs-theme="dark"] .text-muted, [data-bs-theme="dark"] .text-secondary { color: #94a3b8 !important; }
        [data-bs-theme="dark"] .border-bottom, [data-bs-theme="dark"] .border-top, [data-bs-theme="dark"] .border { border-color: var(--border-color) !important; }
        [data-bs-theme="dark"] .card { background-color: var(--card-bg); border-color: var(--border-color); }
        [data-bs-theme="dark"] .modal-content { background-color: var(--card-bg); color: var(--text-main); border-color: var(--border-color); }
        [data-bs-theme="dark"] .list-group-item { background-color: var(--card-bg); color: var(--text-main); border-color: var(--border-color); }
        [data-bs-theme="dark"] .table { color: var(--text-main); --bs-table-bg: transparent; border-color: var(--border-color); }
        [data-bs-theme="dark"] .dropdown-menu { background-color: var(--card-bg); border-color: var(--border-color); }
        [data-bs-theme="dark"] .dropdown-item { color: var(--text-main); }
        [data-bs-theme="dark"] .dropdown-item:hover { background-color: rgba(255,255,255,0.05); color: #fff; }
        [data-bs-theme="dark"] input, [data-bs-theme="dark"] select, [data-bs-theme="dark"] textarea { 
            background-color: rgba(255,255,255,0.05) !important; 
            border-color: var(--border-color) !important; 
            color: var(--text-main) !important; 
        }
        [data-bs-theme="dark"] .form-control:focus { background-color: rgba(255,255,255,0.08) !important; }

        /* SweetAlert2 Dark Mode */
        [data-bs-theme="dark"] .swal2-popup {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
        }
        [data-bs-theme="dark"] .swal2-title, [data-bs-theme="dark"] .swal2-html-container {
            color: var(--text-main) !important;
        }
        [data-bs-theme="dark"] .swal2-footer {
            border-top-color: var(--border-color) !important;
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
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform .3s ease, background 0.3s;
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
            padding: 24px 20px;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }

        /* ===== PROFILE ===== */
        .profile-box {
            padding: 20px;
            transition: background 0.2s;
        }
        
        .profile-box:hover {
            background: rgba(255,255,255,0.03);
        }

        /* ===== MENU ===== */
        .menu-title {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            color: #4b5563;
            padding: 24px 20px 8px;
            text-transform: uppercase;
        }

        .menu a {
            padding: 12px 20px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all .2s ease;
            font-weight: 500;
        }

        .menu a i {
            font-size: 18px;
            opacity: 0.7;
        }

        .menu a:hover {
            background: rgba(255,255,255,.05);
            color: #fff;
        }

        .menu a:hover i {
            opacity: 1;
        }

        .menu a.active {
            background: rgba(37,99,235,.15);
            color: #3b82f6;
            border-right: 3px solid #3b82f6;
        }

        .menu a.active i {
            color: #3b82f6;
            opacity: 1;
        }

        /* ===== CONTENT ===== */
        .content {
            margin-left: 270px;
            transition: margin-left .3s ease;
            min-height: 100vh;
        }

        @media (max-width: 991px) {
            .content {
                margin-left: 0;
            }
        }

        .btn-white {
            background: var(--card-bg);
            color: var(--text-main);
            border-color: var(--border-color);
        }
        
        .btn-white:hover {
            opacity: 0.9;
        }

        [data-bs-theme="dark"] .breadcrumb-item a { color: #94a3b8 !important; }
        [data-bs-theme="dark"] h4 { color: #f1f5f9; }
        [data-bs-theme="dark"] .notification-item:hover { background-color: rgba(255,255,255,0.05); }
        .notification-item:hover { background-color: #f8f9fa; }
    </style>

    <script>
        // Apply theme immediately to prevent flicker
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
            
            if (theme === 'dark') {
                const customColors = JSON.parse(localStorage.getItem('dark_mode_colors') || '{}');
                if (Object.keys(customColors).length > 0) {
                    const style = document.createElement('style');
                    style.id = 'custom-dark-colors';
                    let css = '[data-bs-theme="dark"] {\n';
                    if (customColors.bgBody) css += `  --bg-body: ${customColors.bgBody} !important;\n`;
                    if (customColors.sidebarBg) css += `  --sidebar-bg: ${customColors.sidebarBg} !important;\n`;
                    if (customColors.cardBg) css += `  --card-bg: ${customColors.cardBg} !important;\n`;
                    if (customColors.textMain) css += `  --text-main: ${customColors.textMain} !important;\n`;
                    css += '}';
                    style.innerHTML = css;
                    document.head.appendChild(style);
                }
            }
        })();
    </script>

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
    <a href="{{ route('profile.edit') }}" class="profile-box d-flex align-items-center gap-3 text-decoration-none border-bottom border-secondary border-opacity-10">
        <img src="{{ auth()->user()->profile_picture_url }}" 
             class="rounded-circle border border-2 border-primary" 
             style="width: 48px; height: 48px; object-fit: cover;">
        <div class="overflow-hidden">
            <div class="text-white fw-semibold text-truncate">
                {{ auth()->user()->name ?? __('Guest User') }}
            </div>
            <small class="text-secondary d-block text-truncate">
                {{ auth()->user()->email ?? '' }}
            </small>
        </div>
    </a>

    {{-- MENU --}}
    <nav class="menu mt-2">

        <div class="menu-title">{{ __('Main') }}</div>
        <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> {{ __('Dashboard') }}
        </a>

        <div class="menu-title">{{ __('Management') }}</div>
        <a href="/courses" class="{{ request()->is('courses*') ? 'active' : '' }}">
            <i class="bi bi-book"></i> {{ __('Courses') }}
        </a>
        <a href="#">
            <i class="bi bi-tags"></i> {{ __('Categories') }}
        </a>
        <a href="/user" class="{{ request()->is('user') ? 'active' : '' }}">
             <i class="bi bi-person-circle"></i> {{ __('Users') }}
        </a>

        <div class="menu-title">{{ __('Analytics') }}</div>
        <a href="{{ route('reports.payments') }}" class="{{ request()->is('reports/payments') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> {{ __('Reports') }}
        </a>

        <div class="menu-title">{{ __('Account') }}</div>
        <a href="{{ route('profile.edit') }}" class="{{ request()->is('profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> {{ __('Profile') }}
        </a>
        <a href="{{ route('settings') }}" class="{{ request()->is('settings*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> {{ __('Settings') }}
        </a>

        {{-- LOGOUT --}}
        <div class="px-3 mt-3">
            <button class="btn btn-outline-danger w-100" type="button" onclick="logoutUser()">
                <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
            </button>
        </div>

    </nav>
</aside>

{{-- CONTENT --}}
<div class="content">

    {{-- PAGE HEADER (Replaces Topbar) --}}
    <div class="px-4 pt-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-white border shadow-sm d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item small"><a href="/" class="text-decoration-none text-muted">EduPlex</a></li>
                        <li class="breadcrumb-item small active text-primary" aria-current="page">{{ __('Dashboard') }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold">@yield('page-title', __('Dashboard'))</h4>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-white border shadow-sm position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBtn">
                    <i class="bi bi-bell"></i>
                    <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light d-none" style="font-size: 10px; padding: 3px 6px;">
                        0
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-0 overflow-hidden" style="width: 320px; border-radius: 16px;">
                    <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">{{ __('Notifications') }}</h6>
                        <span class="badge bg-white text-primary rounded-pill small" id="notificationCount">0</span>
                    </div>
                    <div id="notificationList" class="overflow-auto" style="max-height: 350px;">
                        <div class="p-4 text-center text-muted small">
                            {{ __('No new notifications') }}
                        </div>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="{{ route('reports.payments') }}" class="text-decoration-none small fw-bold text-primary">{{ __('View All Payments') }}</a>
                    </div>
                </div>
            </div>

            {{-- Language switcher --}}
            <div class="dropdown ms-2">
                <button class="btn btn-white border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('locale.switch', 'en') }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('locale.switch', 'km') }}">ភាសាខ្មែរ</a></li>
                </ul>
            </div>

            {{-- Theme toggle --}}
            <button id="themeToggle" class="btn btn-white border shadow-sm ms-2" type="button" title="{{ __('Toggle theme') }}">
                <i class="bi" id="themeIcon"></i>
            </button>
        </div>
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

    function logoutUser() {
        Swal.fire({
            title: "{{ __('Logout?') }}",
            text: "{{ __('You will need to sign in again to access the dashboard.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Yes, logout') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                const token = localStorage.getItem('token');

                // Clear tokens immediately and thoroughly
                localStorage.removeItem('token');
                document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "api_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Lax";

                if (!token) {
                    window.location.href = '/login';
                    return;
                }

                // Show loading
                Swal.fire({
                    title: "{{ __('Signing out...') }}",
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .finally(() => {
                    window.location.href = '/login';
                });
            }
        });
    }

    // Notification System
    let latestPaymentId = parseInt(localStorage.getItem('last_seen_payment_id')) || 0;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
    });

    function fetchNotifications() {
        const token = localStorage.getItem('token');
        if (!token) return;

        fetch('/api/admin/recent-payments', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.payments.length > 0) {
                const list = document.getElementById('notificationList');
                const badge = document.getElementById('notificationBadge');
                const count = document.getElementById('notificationCount');

                // Get the newest ID from current results
                const newestId = data.payments[0].id;

                // How many are new since last seen
                const newPayments = data.payments.filter(p => p.id > latestPaymentId);
                const newCount = newPayments.length;

                let html = '';
                data.payments.forEach(p => {
                    const time = new Date(p.created_at).toLocaleTimeString([], { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: true 
                    });
                    html += `
                        <div class="p-3 border-bottom notification-item" style="transition: background 0.2s;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-danger rounded-pill" style="font-size: 10px;">{{ __('New') }}</span>
                                        <small class="text-muted">${time}</small>
                                    </div>
                                    <div class="fw-bold text-truncate small">${p.user.name}</div>
                                    <div class="text-muted text-truncate" style="font-size: 11px;">${p.course.title}</div>
                                    <div class="fw-bold text-success small mt-1">$${parseFloat(p.amount).toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                list.innerHTML = html;
                count.innerText = data.payments.length;

                // Show badge count for new payments
                if (newCount > 0) {
                    badge.classList.remove('d-none');
                    badge.innerText = newCount;

                    // Show a subtle toast when new payments arrive (but not on first load)
                    if (latestPaymentId !== 0) {
                        Toast.fire({
                            icon: 'info',
                            title: `${newCount} new payment${newCount > 1 ? 's' : ''}`
                        });
                    }
                } else {
                    badge.classList.add('d-none');
                }

                // Update global newestId for click handler
                window.currentNewestId = newestId;
            }
        })
        .catch(err => console.error('Notification Error:', err));
    }

    // Initial fetch and poll every 10 seconds for faster alerts
    fetchNotifications();
    setInterval(fetchNotifications, 10000);

    // Clear badge and update last seen ID when dropdown opened
    document.getElementById('notificationBtn')?.addEventListener('click', () => {
        if (window.currentNewestId) {
            latestPaymentId = window.currentNewestId;
            localStorage.setItem('last_seen_payment_id', latestPaymentId);
        }
        document.getElementById('notificationBadge')?.classList.add('d-none');
    });
</script>

<script>
    (function(){
        function updateThemeIcon(){
            const icon = document.getElementById('themeIcon');
            if(!icon) return;
            const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            if(theme === 'dark'){
                icon.className = 'bi bi-sun-fill text-warning';
            } else {
                icon.className = 'bi bi-moon-fill';
            }
        }

        document.getElementById('themeToggle')?.addEventListener('click', function(){
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon();
        });

        // Initialize icon on load (also called by the inline theme applier earlier)
        updateThemeIcon();
    })();
</script>

@stack('scripts')
</body>
</html>
