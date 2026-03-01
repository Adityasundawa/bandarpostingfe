@extends('layouts.app')

@section('page-title', 'Detail User')
@section('breadcrumb', 'Users / Detail')

@section('content')

<style>
    .detail-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 20px;
        align-items: start;
    }

    /* ─── PROFILE CARD ──────────────────────────────── */
    .profile-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .profile-banner {
        height: 100px;
        background: linear-gradient(135deg, rgba(0,255,136,0.2) 0%, rgba(0,170,255,0.2) 100%);
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 0;
    }

    .profile-avatar-lg {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: var(--accent-dim);
        border: 3px solid var(--bg-card);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 700;
        font-family: 'Space Mono', monospace;
        color: var(--accent);
        transform: translateY(40px);
    }

    .profile-info {
        padding: 52px 24px 24px;
        text-align: center;
        border-bottom: 1px solid var(--border);
    }

    .profile-name { font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px; }
    .profile-username { font-size: 12px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-bottom: 14px; }

    .profile-badges { display: flex; align-items: center; justify-content: center; gap: 8px; flex-wrap: wrap; }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        font-family: 'Space Mono', monospace;
    }

    .badge-pill.admin     { background: rgba(180,100,255,0.12); color: #b464ff; border: 1px solid rgba(180,100,255,0.25); }
    .badge-pill.developer { background: rgba(0,170,255,0.12);   color: #00aaff; border: 1px solid rgba(0,170,255,0.25); }
    .badge-pill.designer  { background: rgba(255,170,0,0.12);    color: #ffaa00; border: 1px solid rgba(255,170,0,0.25); }
    .badge-pill.member    { background: rgba(255,255,255,0.06);  color: #7a7a96; border: 1px solid rgba(255,255,255,0.1); }
    .badge-pill.active    { background: rgba(0,255,136,0.1);     color: #00ff88; border: 1px solid rgba(0,255,136,0.2); }
    .badge-pill.pending   { background: rgba(255,170,0,0.1);     color: #ffaa00; border: 1px solid rgba(255,170,0,0.2); }
    .badge-pill.inactive  { background: rgba(255,68,102,0.1);    color: #ff4466; border: 1px solid rgba(255,68,102,0.2); }

    /* Stats row */
    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        border-bottom: 1px solid var(--border);
    }

    .profile-stat {
        padding: 18px 12px;
        text-align: center;
        border-right: 1px solid var(--border);
    }

    .profile-stat:last-child { border-right: none; }
    .profile-stat .stat-num  { font-size: 20px; font-weight: 800; color: var(--text-primary); }
    .profile-stat .stat-lbl  { font-size: 10px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 3px; }

    /* Details list */
    .detail-list { padding: 4px 0; }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 24px;
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }

    .detail-row:last-child { border-bottom: none; }
    .detail-row:hover { background: rgba(255,255,255,0.02); }

    .detail-row-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .detail-row-content { flex: 1; min-width: 0; }
    .detail-row-label { font-size: 10px; font-family: 'Space Mono', monospace; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
    .detail-row-value { font-size: 13px; font-weight: 600; color: var(--text-primary); }

    /* Action buttons sidebar */
    .action-sidebar { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }

    .btn-block {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px;
        border-radius: 10px;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }

    .btn-block.primary { background: var(--accent); color: #000; }
    .btn-block.primary:hover { background: #00cc6e; box-shadow: 0 4px 20px rgba(0,255,136,0.25); }
    .btn-block.outline { background: var(--bg-hover); border: 1px solid var(--border); color: var(--text-secondary); }
    .btn-block.outline:hover { background: var(--bg-secondary); color: var(--text-primary); }
    .btn-block.danger  { background: rgba(255,68,102,0.1); border: 1px solid rgba(255,68,102,0.3); color: #ff4466; }
    .btn-block.danger:hover  { background: rgba(255,68,102,0.2); }

    /* ─── RIGHT PANEL ────────────────────────────────── */
    .info-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .info-card:last-child { margin-bottom: 0; }

    .info-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .info-card-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .info-card-sub   { font-size: 11px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 1px; }

    .info-card-body  { padding: 20px 24px; }

    /* Meta grid */
    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .meta-item .meta-label { font-size: 10px; font-family: 'Space Mono', monospace; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .meta-item .meta-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }

    /* Activity Timeline */
    .timeline { display: flex; flex-direction: column; gap: 0; }

    .timeline-item {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .timeline-item:last-child { border-bottom: none; }

    .timeline-dot {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .timeline-content .t-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    .timeline-content .t-desc  { font-size: 12px; color: var(--text-secondary); line-height: 1.5; }
    .timeline-content .t-time  { font-size: 10px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 6px; }

    /* Back button */
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        background: var(--bg-card);
        color: var(--text-secondary);
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        border: 1px solid var(--border);
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-secondary:hover { background: var(--bg-hover); color: var(--text-primary); }

    @media (max-width: 960px) {
        .detail-layout { grid-template-columns: 1fr; }
        .meta-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .meta-grid { grid-template-columns: 1fr; }
        .profile-stats { grid-template-columns: repeat(3,1fr); }
    }
</style>


{{-- Back --}}
<div style="margin-bottom: 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke List
    </a>
    <a href="{{ route('admin.users.edit', $user->id) }}" style="display:inline-flex;align-items:center;gap:8px;padding:11px 20px;background:var(--accent);color:#000;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;border-radius:10px;text-decoration:none;">
        <i class="fas fa-pen"></i> Edit User
    </a>
</div>

<div class="detail-layout">

    {{-- ─── LEFT: PROFILE CARD ─────────────────────── --}}
    <div>
        <div class="profile-card">
            {{-- Banner --}}
            <div class="profile-banner">
                <div class="profile-avatar-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
            </div>

            {{-- Info --}}
            <div class="profile-info">
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-username">@{{ $user->username ?? 'no-username' }}</div>
                <div class="profile-badges">
                    <span class="badge-pill {{ strtolower($user->role) }}">
                        @if($user->role === 'admin') <i class="fas fa-shield-alt"></i>
                        @elseif($user->role === 'developer') <i class="fas fa-code"></i>
                        @elseif($user->role === 'designer') <i class="fas fa-palette"></i>
                        @else <i class="fas fa-user"></i>
                        @endif
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="badge-pill {{ strtolower($user->status) }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
            </div>

            {{-- Stats --}}
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="stat-num">47</div>
                    <div class="stat-lbl">Projects</div>
                </div>
                <div class="profile-stat">
                    <div class="stat-num">1.2k</div>
                    <div class="stat-lbl">Commits</div>
                </div>
                <div class="profile-stat">
                    <div class="stat-num">98%</div>
                    <div class="stat-lbl">Uptime</div>
                </div>
            </div>

            {{-- Detail List --}}
            <div class="detail-list">
                <div class="detail-row">
                    <div class="detail-row-icon" style="background:rgba(0,255,136,0.1);">
                        <i class="fas fa-envelope" style="color:#00ff88;"></i>
                    </div>
                    <div class="detail-row-content">
                        <div class="detail-row-label">Email</div>
                        <div class="detail-row-value">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-icon" style="background:rgba(0,170,255,0.1);">
                        <i class="fas fa-phone" style="color:#00aaff;"></i>
                    </div>
                    <div class="detail-row-content">
                        <div class="detail-row-label">Telepon</div>
                        <div class="detail-row-value">{{ $user->phone ?? '-' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-icon" style="background:rgba(255,170,0,0.1);">
                        <i class="fas fa-calendar" style="color:#ffaa00;"></i>
                    </div>
                    <div class="detail-row-content">
                        <div class="detail-row-label">Bergabung</div>
                        <div class="detail-row-value">{{ \Carbon\Carbon::parse($user->created_at)->format('d F Y') }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-icon" style="background:rgba(180,100,255,0.1);">
                        <i class="fas fa-clock" style="color:#b464ff;"></i>
                    </div>
                    <div class="detail-row-content">
                        <div class="detail-row-label">Login Terakhir</div>
                        <div class="detail-row-value">{{ $user->last_login_at ?? 'Belum pernah login' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-row-icon" style="background:rgba(255,255,255,0.05);">
                        <i class="fas fa-hashtag" style="color:#7a7a96;"></i>
                    </div>
                    <div class="detail-row-content">
                        <div class="detail-row-label">User ID</div>
                        <div class="detail-row-value" style="font-family:'Space Mono',monospace;">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="action-sidebar">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-block primary">
                    <i class="fas fa-pen"></i> Edit User
                </a>
                <a href="#" class="btn-block outline">
                    <i class="fas fa-envelope"></i> Kirim Email
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                      onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-block danger" style="width:100%;">
                        <i class="fas fa-trash"></i> Hapus User
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ─── RIGHT: DETAILS ─────────────────────────── --}}
    <div>
        {{-- Bio --}}
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon" style="background:var(--accent-dim); color:var(--accent);">
                    <i class="fas fa-align-left"></i>
                </div>
                <div>
                    <div class="info-card-title">Bio</div>
                    <div class="info-card-sub">Deskripsi singkat user</div>
                </div>
            </div>
            <div class="info-card-body">
                <p style="font-size:14px; color:var(--text-secondary); line-height:1.7;">
                    {{ $user->bio ?? 'User belum menambahkan bio.' }}
                </p>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon" style="background:rgba(0,170,255,0.1); color:#00aaff;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <div class="info-card-title">Informasi Akun</div>
                    <div class="info-card-sub">Data teknis & metadata</div>
                </div>
            </div>
            <div class="info-card-body">
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">User ID</div>
                        <div class="meta-value" style="font-family:'Space Mono',monospace;">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Username</div>
                        <div class="meta-value">{{ $user->username ?? '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Email Verified</div>
                        <div class="meta-value" style="color:#00ff88;"><i class="fas fa-check-circle"></i> Verified</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">2FA</div>
                        <div class="meta-value" style="color:#ff4466;"><i class="fas fa-times-circle"></i> Disabled</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Total Login</div>
                        <div class="meta-value">148 kali</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">IP Terakhir</div>
                        <div class="meta-value" style="font-family:'Space Mono',monospace;">182.x.x.x</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Dibuat Pada</div>
                        <div class="meta-value">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Last Updated</div>
                        <div class="meta-value">25 Feb 2025</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon" style="background:rgba(255,170,0,0.1); color:#ffaa00;">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <div class="info-card-title">Activity Log</div>
                    <div class="info-card-sub">Riwayat aktivitas terbaru</div>
                </div>
            </div>
            <div class="info-card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:rgba(0,255,136,0.1);">
                            <i class="fas fa-sign-in-alt" style="color:#00ff88; font-size:13px;"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="t-title">Login berhasil</div>
                            <div class="t-desc">Login dari browser Chrome · IP: 182.23.x.x</div>
                            <div class="t-time">25 Feb 2025 · 08:34 WIB</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:rgba(0,170,255,0.1);">
                            <i class="fas fa-upload" style="color:#00aaff; font-size:13px;"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="t-title">Upload file project</div>
                            <div class="t-desc">Mengunggah <strong style="color:var(--text-primary);">project-v2.zip</strong> (4.2 MB)</div>
                            <div class="t-time">24 Feb 2025 · 14:12 WIB</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:rgba(255,170,0,0.1);">
                            <i class="fas fa-edit" style="color:#ffaa00; font-size:13px;"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="t-title">Update profil</div>
                            <div class="t-desc">Mengubah bio dan nomor telepon</div>
                            <div class="t-time">22 Feb 2025 · 10:05 WIB</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:rgba(180,100,255,0.1);">
                            <i class="fas fa-user-plus" style="color:#b464ff; font-size:13px;"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="t-title">Akun dibuat</div>
                            <div class="t-desc">Registrasi akun baru berhasil via form pendaftaran</div>
                            <div class="t-time">24 Feb 2025 · 09:00 WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
