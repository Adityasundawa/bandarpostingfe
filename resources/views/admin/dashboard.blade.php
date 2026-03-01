@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')

<style>
    /* ─── STATS CARDS ──────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 22px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, border-color 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent-border);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        opacity: 0.06;
        transform: translate(20px, -20px);
    }

    .stat-card.green::before { background: #00ff88; }
    .stat-card.blue::before  { background: #00aaff; }
    .stat-card.orange::before{ background: #ffaa00; }
    .stat-card.red::before   { background: #ff4466; }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        margin-bottom: 16px;
    }

    .stat-icon.green  { background: rgba(0,255,136,0.12); color: #00ff88; }
    .stat-icon.blue   { background: rgba(0,170,255,0.12); color: #00aaff; }
    .stat-icon.orange { background: rgba(255,170,0,0.12);  color: #ffaa00; }
    .stat-icon.red    { background: rgba(255,68,102,0.12);  color: #ff4466; }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -1px;
        line-height: 1;
        margin-bottom: 6px;
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-secondary);
        font-family: 'Space Mono', monospace;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-trend.up   { color: #00ff88; }
    .stat-trend.down { color: #ff4466; }
    .stat-trend span { color: var(--text-muted); font-weight: 400; }

    /* ─── BOTTOM GRID ──────────────────────────── */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 16px;
        margin-bottom: 24px;
    }

    /* ─── CARDS ────────────────────────────────── */
    .card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-title i { color: var(--accent); font-size: 13px; }

    .card-subtitle {
        font-size: 11px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .card-action {
        font-size: 12px;
        color: var(--accent);
        text-decoration: none;
        font-weight: 600;
        font-family: 'Space Mono', monospace;
        transition: opacity 0.2s;
    }

    .card-action:hover { opacity: 0.7; }

    .card-body { padding: 20px 24px; }

    /* ─── RECENT USERS TABLE ───────────────────── */
    .user-table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-table th {
        font-size: 10px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 12px 12px 0;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .user-table td {
        padding: 14px 12px 14px 0;
        font-size: 13px;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .user-table tr:last-child td { border-bottom: none; }

    .user-table tr:hover td { background: rgba(255,255,255,0.01); }

    .user-info-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-ava {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-ava.a { background: rgba(0,255,136,0.15); color: #00ff88; }
    .user-ava.b { background: rgba(0,170,255,0.15); color: #00aaff; }
    .user-ava.c { background: rgba(255,170,0,0.15);  color: #ffaa00; }
    .user-ava.d { background: rgba(255,68,102,0.15);  color: #ff4466; }
    .user-ava.e { background: rgba(180,100,255,0.15); color: #b464ff; }

    .user-nm { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .user-em { font-size: 11px; color: var(--text-muted); margin-top: 1px; font-family: 'Space Mono', monospace; }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-family: 'Space Mono', monospace;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .badge.active { background: rgba(0,255,136,0.12); color: #00ff88; border: 1px solid rgba(0,255,136,0.25); }
    .badge.pending { background: rgba(255,170,0,0.12); color: #ffaa00; border: 1px solid rgba(255,170,0,0.25); }
    .badge.inactive { background: rgba(255,68,102,0.1); color: #ff4466; border: 1px solid rgba(255,68,102,0.2); }

    /* ─── ACTIVITY FEED ────────────────────────── */
    .activity-list { display: flex; flex-direction: column; gap: 0; }

    .activity-item {
        display: flex;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .activity-item:last-child { border-bottom: none; }

    .activity-dot {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .activity-dot.reg   { background: rgba(0,255,136,0.12); color: #00ff88; }
    .activity-dot.login { background: rgba(0,170,255,0.12); color: #00aaff; }
    .activity-dot.edit  { background: rgba(255,170,0,0.12);  color: #ffaa00; }
    .activity-dot.del   { background: rgba(255,68,102,0.12);  color: #ff4466; }
    .activity-dot.upload{ background: rgba(180,100,255,0.12); color: #b464ff; }

    .activity-content .act-text {
        font-size: 13px;
        color: var(--text-primary);
        line-height: 1.4;
    }

    .activity-content .act-text strong { color: var(--accent); font-weight: 700; }

    .activity-content .act-time {
        font-size: 10px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ─── MINI CHART PLACEHOLDER ───────────────── */
    .chart-area {
        height: 160px;
        display: flex;
        align-items: flex-end;
        gap: 6px;
        padding: 0 0 0 0;
    }

    .bar-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        flex: 1;
    }

    .bar {
        width: 100%;
        border-radius: 4px 4px 0 0;
        background: var(--accent-dim);
        border: 1px solid var(--accent-border);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent);
        opacity: 0.8;
    }

    .bar:hover { background: rgba(0,255,136,0.2); }

    .bar-label {
        font-size: 9px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        text-align: center;
    }

    /* ─── THIRD ROW ────────────────────────────── */
    .third-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    /* Progress bars */
    .progress-item { margin-bottom: 16px; }
    .progress-item:last-child { margin-bottom: 0; }
    .progress-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .progress-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .progress-val  { font-size: 12px; font-family: 'Space Mono', monospace; color: var(--accent); }
    .progress-bar-bg {
        height: 6px;
        background: var(--bg-hover);
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, var(--accent), #00aaff);
    }

    /* Quick actions */
    .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .quick-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 18px 12px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        border-radius: 12px;
        text-decoration: none;
        color: var(--text-secondary);
        font-size: 11px;
        font-family: 'Space Mono', monospace;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .quick-btn:hover {
        border-color: var(--accent-border);
        color: var(--accent);
        background: var(--accent-dim);
    }

    .quick-btn i { font-size: 18px; }

    /* Server stats */
    .server-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .server-row:last-child { border-bottom: none; }
    .server-name { font-size: 13px; color: var(--text-primary); font-weight: 500; }
    .server-meta { font-size: 11px; color: var(--text-muted); font-family: 'Space Mono', monospace; margin-top: 2px; }
    .server-status {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        box-shadow: 0 0 6px var(--accent);
        animation: pulse 2s infinite;
    }
    .server-status.warn { background: #ffaa00; box-shadow: 0 0 6px #ffaa00; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .content-grid { grid-template-columns: 1fr; }
        .third-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .third-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- ─── ROW 1: STATS ─────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-value">2,847</div>
        <div class="stat-label">Total Users</div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i> +12.4%
            <span>vs last month</span>
        </div>
    </div>

    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-code"></i></div>
        <div class="stat-value">14,923</div>
        <div class="stat-label">Total Projects</div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i> +8.1%
            <span>vs last month</span>
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-value">Rp 48M</div>
        <div class="stat-label">Monthly Revenue</div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i> +21.6%
            <span>vs last month</span>
        </div>
    </div>

    <div class="stat-card red">
        <div class="stat-icon red"><i class="fas fa-exclamation-circle"></i></div>
        <div class="stat-value">36</div>
        <div class="stat-label">Open Tickets</div>
        <div class="stat-trend down">
            <i class="fas fa-arrow-down"></i> -4 tickets
            <span>since yesterday</span>
        </div>
    </div>
</div>

{{-- ─── ROW 2: TABLE + ACTIVITY ──────────────────────── --}}
<div class="content-grid">

    {{-- Recent Users --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="fas fa-user-clock"></i> Recent Registered Users</div>
                <div class="card-subtitle">Latest 5 user registrations</div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="card-action">View All →</a>
        </div>
        <div class="card-body" style="padding: 0 24px;">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-ava a">AW</div>
                                <div>
                                    <div class="user-nm">Aldi Wirawan</div>
                                    <div class="user-em">aldi@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">Developer</td>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px;">24 Feb 2025</td>
                        <td><span class="badge active">Active</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-ava b">RN</div>
                                <div>
                                    <div class="user-nm">Rina Novita</div>
                                    <div class="user-em">rina.nv@yahoo.com</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">Designer</td>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px;">22 Feb 2025</td>
                        <td><span class="badge active">Active</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-ava c">BH</div>
                                <div>
                                    <div class="user-nm">Bagas Hermawan</div>
                                    <div class="user-em">bagas.h@bandarkode.com</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">Admin</td>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px;">20 Feb 2025</td>
                        <td><span class="badge pending">Pending</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-ava d">SF</div>
                                <div>
                                    <div class="user-nm">Siti Fatimah</div>
                                    <div class="user-em">siti.f@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">Member</td>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px;">18 Feb 2025</td>
                        <td><span class="badge inactive">Inactive</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info-cell">
                                <div class="user-ava e">DP</div>
                                <div>
                                    <div class="user-nm">Dika Pratama</div>
                                    <div class="user-em">dika.p@hotmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">Developer</td>
                        <td style="color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px;">15 Feb 2025</td>
                        <td><span class="badge active">Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="fas fa-stream"></i> Recent Activity</div>
                <div class="card-subtitle">System activity log</div>
            </div>
        </div>
        <div class="card-body">
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-dot reg"><i class="fas fa-user-plus"></i></div>
                    <div class="activity-content">
                        <div class="act-text"><strong>Aldi Wirawan</strong> registered as new user</div>
                        <div class="act-time">2 minutes ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot login"><i class="fas fa-sign-in-alt"></i></div>
                    <div class="activity-content">
                        <div class="act-text"><strong>Rina Novita</strong> logged into the system</div>
                        <div class="act-time">15 minutes ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot upload"><i class="fas fa-upload"></i></div>
                    <div class="activity-content">
                        <div class="act-text"><strong>Bagas</strong> uploaded new project files</div>
                        <div class="act-time">42 minutes ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot edit"><i class="fas fa-edit"></i></div>
                    <div class="activity-content">
                        <div class="act-text"><strong>Siti Fatimah</strong> updated her profile info</div>
                        <div class="act-time">1 hour ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot del"><i class="fas fa-trash"></i></div>
                    <div class="activity-content">
                        <div class="act-text">Admin deleted <strong>3 inactive accounts</strong></div>
                        <div class="act-time">3 hours ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ─── ROW 3: CHART + QUICK ACTIONS + SERVER ────────── --}}
<div class="third-grid">

    {{-- Bar Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="fas fa-chart-bar"></i> User Growth</div>
                <div class="card-subtitle">Last 7 days</div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <div class="bar-wrap">
                    <div class="bar" style="height:60px;"></div>
                    <div class="bar-label">Mon</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:95px;"></div>
                    <div class="bar-label">Tue</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:72px;"></div>
                    <div class="bar-label">Wed</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:130px;"></div>
                    <div class="bar-label">Thu</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:105px;"></div>
                    <div class="bar-label">Fri</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:85px;"></div>
                    <div class="bar-label">Sat</div>
                </div>
                <div class="bar-wrap">
                    <div class="bar" style="height:155px;"></div>
                    <div class="bar-label">Sun</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="fas fa-bolt"></i> Quick Actions</div>
                <div class="card-subtitle">Shortcuts</div>
            </div>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="{{ route('admin.users.create') }}" class="quick-btn">
                    <i class="fas fa-user-plus" style="color:#00ff88;"></i>
                    Add User
                </a>
                <a href="{{ route('admin.users.index') }}" class="quick-btn">
                    <i class="fas fa-users" style="color:#00aaff;"></i>
                    All Users
                </a>
                <a href="#" class="quick-btn">
                    <i class="fas fa-file-export" style="color:#ffaa00;"></i>
                    Export CSV
                </a>
                <a href="#" class="quick-btn">
                    <i class="fas fa-envelope" style="color:#b464ff;"></i>
                    Send Email
                </a>
                <a href="#" class="quick-btn">
                    <i class="fas fa-shield-alt" style="color:#ff4466;"></i>
                    Security
                </a>
                <a href="#" class="quick-btn">
                    <i class="fas fa-cog" style="color:#7a7a96;"></i>
                    Settings
                </a>
            </div>
        </div>
    </div>

    {{-- Server Status --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="fas fa-server"></i> System Status</div>
                <div class="card-subtitle">Live infrastructure</div>
            </div>
        </div>
        <div class="card-body" style="padding-top: 8px; padding-bottom: 8px;">
            <div class="server-row">
                <div>
                    <div class="server-name">Web Server</div>
                    <div class="server-meta">Nginx 1.24 · 99.98% uptime</div>
                </div>
                <div class="server-status"></div>
            </div>
            <div class="server-row">
                <div>
                    <div class="server-name">Database</div>
                    <div class="server-meta">MySQL 8.0 · 12ms latency</div>
                </div>
                <div class="server-status"></div>
            </div>
            <div class="server-row">
                <div>
                    <div class="server-name">Cache (Redis)</div>
                    <div class="server-meta">Redis 7.2 · 2ms latency</div>
                </div>
                <div class="server-status"></div>
            </div>
            <div class="server-row">
                <div>
                    <div class="server-name">Queue Worker</div>
                    <div class="server-meta">Laravel Horizon · High load</div>
                </div>
                <div class="server-status warn"></div>
            </div>

            <div style="margin-top: 16px;">
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-name">CPU Usage</span>
                        <span class="progress-val">42%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:42%;"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-name">Memory</span>
                        <span class="progress-val">68%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:68%;"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-name">Disk</span>
                        <span class="progress-val">31%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:31%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
