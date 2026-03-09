{{-- resources/views/layouts/navigation.blade.php --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-wrap">
            <div class="logo-icon">BP</div>
            <div>
                <div class="logo-text">Bandar<span>Posting</span></div>
                <div class="logo-badge">
                    {{ auth()->check() && auth()->user()->role == 1 ? 'Admin Panel' : 'Client Panel' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        @if (auth()->check() && auth()->user()->role == 1)
            {{-- ================= ADMIN NAVIGATION ================= --}}
            <div class="nav-label">Main</div>

            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                Dashboard
            </a>

            <div class="nav-label">Management</div>
            <a href="{{ route('admin.users.index') }}"
                class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                User Management
            </a>

            <div class="nav-label">Bot Meta API</div>

            <a href="{{ route('admin.meta.tokens.index') }}"
                class="nav-item {{ request()->routeIs('admin.meta.tokens.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-key"></i></span>
                Token Management
            </a>

            <a href="{{ route('admin.meta.logs.index') }}"
                class="nav-item {{ request()->routeIs('admin.meta.logs.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-stream"></i></span>
                Access Logs
            </a>

        @else
            {{-- ================= CLIENT NAVIGATION ================= --}}
            <div class="nav-label">Main</div>

            <a href="{{ route('client.dashboard') }}"
                class="nav-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                Dashboard
            </a>

            <div class="nav-label">Auto Posting Panels</div>

            <a href="{{ route('client.x.index') }}"
                class="nav-item {{ request()->routeIs('client.x.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fab fa-x-twitter"></i></span>
                X Panel
            </a>

            <a href="{{ route('client.meta.index') }}"
                class="nav-item {{ request()->routeIs('client.meta.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fab fa-facebook"></i></span>
                Meta Panel
            </a>

            <a href="{{ route('client.tiktok.index') }}"
                class="nav-item {{ request()->routeIs('client.tiktok.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fab fa-tiktok"></i></span>
                Tiktok Panel
            </a>

            <a href="{{ route('client.telegram.index') }}"
                class="nav-item {{ request()->routeIs('client.telegram.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fab fa-telegram"></i></span>
                Telegram Panel
            </a>

            <div class="nav-label">Storage</div>

            <a href="{{ route('client.files.index') }}"
                class="nav-item {{ request()->routeIs('client.files.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-hdd"></i></span>
                File Manager
            </a>

        @endif

    </nav>

    {{-- User Footer --}}
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="user-role">
                    {{ Auth::user()?->role_label ?? (Auth::user()?->role == 1 ? 'Admin' : 'Client') }}
                </div>
            </div>
            <i class="fas fa-ellipsis-v user-menu-icon"></i>
        </div>
    </div>

</aside>
