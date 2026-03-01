<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BandarKode') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #111118;
            --bg-card: #16161f;
            --bg-hover: #1e1e2a;
            --accent: #00ff88;
            --accent-dim: rgba(0, 255, 136, 0.1);
            --accent-border: rgba(0, 255, 136, 0.3);
            --text-primary: #e8e8f0;
            --text-secondary: #7a7a96;
            --text-muted: #4a4a66;
            --border: rgba(255, 255, 255, 0.06);
            --sidebar-width: 260px;
            --danger: #ff4466;
            --warning: #ffaa00;
            --info: #00aaff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ─── SIDEBAR ─────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Bebas Neue', 'sans-serif';
            font-size: 14px;
            font-weight: 700;
            color: #000;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: var(--text-primary);
        }

        .logo-text span {
            color: var(--accent);
        }

        .logo-badge {
            font-size: 9px;
            font-family: 'Bebas Neue', 'sans-serif';
            letter-spacing: 1.5px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* Nav */
        .sidebar-nav {
            padding: 20px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 9px;
            font-family: 'Bebas Neue', 'sans-serif';
            letter-spacing: 2px;
            color: var(--text-muted);
            text-transform: uppercase;
            padding: 0 12px;
            margin-bottom: 8px;
            margin-top: 16px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .nav-item.active {
            background: var(--accent-dim);
            color: var(--accent);
            border: 1px solid var(--accent-border);
        }

        .nav-item .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .nav-item .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #000;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 20px;
            font-family: 'Bebas Neue', 'sans-serif';
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .user-card:hover {
            background: var(--bg-hover);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent), #00aaff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #000;
            flex-shrink: 0;
        }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 3px;
        }

        .user-info .user-role {
            font-size: 10px;
            font-family: 'Bebas Neue', 'sans-serif';
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-card .user-menu-icon {
            margin-left: auto;
            color: var(--text-muted);
            font-size: 12px;
        }

        /* ─── MAIN ─────────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─── TOPBAR ───────────────────────────────────────────── */
        .topbar {
            height: 64px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 16px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .page-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'Bebas Neue', 'sans-serif';
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb a:hover { color: var(--accent); }

        .topbar-search {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 14px;
            width: 240px;
        }

        .topbar-search i { color: var(--text-muted); font-size: 13px; }

        .topbar-search input {
            background: none;
            border: none;
            outline: none;
            color: var(--text-primary);
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            width: 100%;
        }

        .topbar-search input::placeholder { color: var(--text-muted); }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 14px;
            position: relative;
            transition: all 0.2s;
        }

        .topbar-btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            border-color: var(--accent-border);
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            border: 1.5px solid var(--bg-secondary);
        }

        /* ─── CONTENT ──────────────────────────────────────────── */
        .main-content {
            flex: 1;
            padding: 28px;
            background: var(--bg-primary);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        /* Mobile Toggle */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .topbar-search {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    @include('layouts.navigation')

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <div class="breadcrumb">
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                        <span>/</span>
                        <span>@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>
            </div>

            <div class="topbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search anything...">
            </div>

            <div class="topbar-right">
                <div class="topbar-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-dot"></span>
                </div>
                <div class="topbar-btn" title="Settings">
                    <i class="fas fa-cog"></i>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="topbar-btn" title="Logout" style="cursor:pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>

    </div>

    <!-- Overlay for mobile -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:99;"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
        }
    </script>

    @stack('scripts')
</body>
</html>
