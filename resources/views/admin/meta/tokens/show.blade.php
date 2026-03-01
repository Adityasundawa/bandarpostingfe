@extends('layouts.app')
@section('page-title', 'Detail Token')
@section('breadcrumb', 'Tokens / Detail')

@section('content')
<style>
    .detail-layout { display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start; }
    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:16px; }
    .card:last-child { margin-bottom:0; }
    .card-header { padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px; }
    .card-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .card-title { font-size:14px; font-weight:700; color:var(--text-primary); }
    .card-sub   { font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); margin-top:1px; }
    .card-body  { padding:20px 24px; }

    /* Token info rows */
    .info-row { display:flex; align-items:center; padding:13px 0; border-bottom:1px solid var(--border); gap:12px; }
    .info-row:last-child { border-bottom:none; }
    .info-row-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
    .info-row-key { font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
    .info-row-val { font-size:13px; font-weight:600; color:var(--text-primary); word-break:break-all; }

    /* Role / status badges */
    .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; font-family:'Space Mono',monospace; }
    .badge.admin   { background:rgba(180,100,255,.12); color:#b464ff; border:1px solid rgba(180,100,255,.25); }
    .badge.client  { background:rgba(0,170,255,.12);   color:#00aaff; border:1px solid rgba(0,170,255,.25); }
    .badge.active  { background:rgba(0,255,136,.1);    color:#00ff88; border:1px solid rgba(0,255,136,.2); }
    .badge.inactive{ background:rgba(255,68,102,.1);   color:#ff4466; border:1px solid rgba(255,68,102,.2); }
    .badge.expired { background:rgba(255,68,102,.08);  color:#ff4466; border:1px solid rgba(255,68,102,.15); }
    .badge.soon    { background:rgba(255,170,0,.1);    color:#ffaa00; border:1px solid rgba(255,170,0,.2); }

    /* Token string */
    .token-full { font-family:'Space Mono',monospace; font-size:12px; background:var(--bg-secondary); border:1px solid var(--border); border-radius:8px; padding:10px 14px; word-break:break-all; display:flex; align-items:center; justify-content:space-between; gap:10px; color:var(--text-secondary); }
    .copy-btn { background:var(--accent); color:#000; border:none; padding:5px 12px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:'Space Mono',monospace; white-space:nowrap; transition:all .2s; flex-shrink:0; }
    .copy-btn:hover { background:#00cc6e; }

    /* Action buttons */
    .btn-sm { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-size:12px; font-weight:700; font-family:'Syne',sans-serif; cursor:pointer; border:none; text-decoration:none; transition:all .2s; }
    .btn-sm.primary { background:var(--accent); color:#000; }
    .btn-sm.primary:hover { background:#00cc6e; }
    .btn-sm.outline { background:var(--bg-hover); border:1px solid var(--border); color:var(--text-secondary); }
    .btn-sm.outline:hover { background:var(--bg-secondary); color:var(--text-primary); }
    .btn-sm.danger  { background:rgba(255,68,102,.1); border:1px solid rgba(255,68,102,.3); color:#ff4466; }
    .btn-sm.danger:hover { background:rgba(255,68,102,.2); }
    .btn-sm.warning { background:rgba(255,170,0,.1); border:1px solid rgba(255,170,0,.3); color:#ffaa00; }
    .btn-sm.warning:hover { background:rgba(255,170,0,.2); }

    /* Session list */
    .session-list { display:flex; flex-direction:column; gap:0; }
    .session-item { display:flex; align-items:center; justify-content:space-between; padding:13px 0; border-bottom:1px solid var(--border); gap:12px; }
    .session-item:last-child { border-bottom:none; }
    .session-name { font-family:'Space Mono',monospace; font-size:13px; color:var(--text-primary); font-weight:600; }
    .session-date { font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); }

    /* Assign form */
    .assign-box { background:var(--bg-secondary); border:1px solid var(--border); border-radius:10px; padding:16px; margin-bottom:16px; }
    .assign-box textarea { width:100%; background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:10px 14px; color:var(--text-primary); font-family:'Space Mono',monospace; font-size:12px; outline:none; resize:vertical; min-height:80px; transition:border-color .2s; }
    .assign-box textarea:focus { border-color:var(--accent-border); }
    .assign-box textarea::placeholder { color:var(--text-muted); }
    .assign-label { font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
    .assign-hint  { font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); margin-top:6px; }

    /* Update form fields */
    .form-control { width:100%; background:var(--bg-secondary); border:1px solid var(--border); border-radius:10px; padding:10px 14px; color:var(--text-primary); font-family:'Syne',sans-serif; font-size:13px; outline:none; transition:border-color .2s; }
    .form-control:focus { border-color:var(--accent-border); }
    .form-group { margin-bottom:14px; }
    .form-group:last-child { margin-bottom:0; }
    .form-label { display:block; font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }

    /* Flash */
    .flash-msg { display:flex; align-items:center; gap:12px; padding:13px 18px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:600; animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
    .flash-msg.success { background:rgba(0,255,136,.1); border:1px solid rgba(0,255,136,.25); color:#00ff88; }
    .flash-msg.error   { background:rgba(255,68,102,.1); border:1px solid rgba(255,68,102,.25); color:#ff4466; }

    .btn-back { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:var(--bg-card); color:var(--text-secondary); font-family:'Syne',sans-serif; font-size:13px; font-weight:600; border-radius:10px; border:1px solid var(--border); text-decoration:none; transition:all .2s; }
    .btn-back:hover { background:var(--bg-hover); color:var(--text-primary); }

    .empty-sessions { text-align:center; padding:30px 0; color:var(--text-muted); font-size:13px; }
    .empty-sessions i { font-size:28px; opacity:.3; display:block; margin-bottom:10px; }

    @media(max-width:960px) { .detail-layout{grid-template-columns:1fr;} }
</style>

@if(session('success'))
<div class="flash-msg success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash-msg error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- Toolbar --}}
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.meta.tokens.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        {{-- Toggle aktif --}}
        <form action="{{ route('admin.meta.tokens.toggle', $token['id']) }}" method="POST">
            @csrf
            <input type="hidden" name="is_active" value="{{ $token['is_active'] ? 0 : 1 }}">
            <button type="submit" class="btn-sm {{ $token['is_active'] ? 'warning' : 'primary' }}">
                <i class="fas fa-{{ $token['is_active'] ? 'pause' : 'play' }}"></i>
                {{ $token['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
        {{-- Hapus --}}
        <button class="btn-sm danger" onclick="document.getElementById('deleteForm').submit()">
            <i class="fas fa-trash"></i> Hapus Token
        </button>
        <form id="deleteForm" action="{{ route('admin.meta.tokens.destroy', $token['id']) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
    </div>
</div>

<div class="detail-layout">

    {{-- ── LEFT: Token Info ───────────────────────────── --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:var(--accent-dim);color:var(--accent);"><i class="fas fa-key"></i></div>
                <div>
                    <div class="card-title">Info Token</div>
                    <div class="card-sub">Detail & konfigurasi</div>
                </div>
            </div>
            <div class="card-body" style="padding-top:4px;padding-bottom:4px;">
                <div class="info-row">
                    <div class="info-row-icon" style="background:rgba(0,255,136,.08);"><i class="fas fa-user" style="color:#00ff88;font-size:12px;"></i></div>
                    <div style="flex:1">
                        <div class="info-row-key">Nama Klien</div>
                        <div class="info-row-val">{{ $token['client_name'] }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-row-icon" style="background:rgba(0,170,255,.08);"><i class="fas fa-hashtag" style="color:#00aaff;font-size:12px;"></i></div>
                    <div>
                        <div class="info-row-key">Token ID</div>
                        <div class="info-row-val" style="font-family:'Space Mono',monospace;">#{{ $token['id'] }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-row-icon" style="background:rgba(180,100,255,.08);"><i class="fas fa-shield-alt" style="color:#b464ff;font-size:12px;"></i></div>
                    <div>
                        <div class="info-row-key">Role</div>
                        <div class="info-row-val">
                            <span class="badge {{ $token['role'] }}">{{ ucfirst($token['role']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-row-icon" style="background:rgba(0,255,136,.08);"><i class="fas fa-circle" style="color:{{ $token['is_active'] ? '#00ff88' : '#ff4466' }};font-size:10px;"></i></div>
                    <div>
                        <div class="info-row-key">Status</div>
                        <div class="info-row-val">
                            <span class="badge {{ $token['is_active'] ? 'active' : 'inactive' }}">{{ $token['is_active'] ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-row-icon" style="background:rgba(255,170,0,.08);"><i class="fas fa-calendar" style="color:#ffaa00;font-size:12px;"></i></div>
                    <div>
                        <div class="info-row-key">Expired At</div>
                        <div class="info-row-val">
                            @if($token['expired_at'])
                                @php $expDate = \Carbon\Carbon::parse($token['expired_at']); @endphp
                                <span class="badge {{ $expDate->isPast() ? 'expired' : ($expDate->diffInDays(now()) <= 30 ? 'soon' : 'active') }}">
                                    {{ $expDate->format('d M Y, H:i') }}
                                </span>
                            @else
                                <span style="color:var(--text-muted);font-family:'Space Mono',monospace;font-size:12px;">Tidak Expired</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Token String --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(255,170,0,.1);color:#ffaa00;"><i class="fas fa-code"></i></div>
                <div>
                    <div class="card-title">Bearer Token</div>
                    <div class="card-sub">Dipakai di header Authorization</div>
                </div>
            </div>
            <div class="card-body">
                <div class="token-full">
                    <span style="word-break:break-all;">{{ $token['token'] }}</span>
                    <button class="copy-btn" onclick="copyToken('{{ $token['token'] }}', this)"><i class="fas fa-copy"></i> Copy</button>
                </div>
            </div>
        </div>

        {{-- Update --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(0,170,255,.1);color:#00aaff;"><i class="fas fa-edit"></i></div>
                <div>
                    <div class="card-title">Update Token</div>
                    <div class="card-sub">Ganti nama / expired</div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.meta.tokens.update', $token['id']) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label class="form-label">Nama Klien</label>
                        <input type="text" name="client_name" class="form-control" value="{{ $token['client_name'] }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expired At</label>
                        <input type="text" name="expired_at" class="form-control"
                            placeholder="2027-12-31 23:59:59 (kosong = hapus expired)"
                            value="{{ $token['expired_at'] ? \Carbon\Carbon::parse($token['expired_at'])->format('Y-m-d H:i:s') : '' }}">
                    </div>
                    <button type="submit" class="btn-sm primary" style="width:100%;justify-content:center;">
                        <i class="fas fa-save"></i> Simpan Update
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Session Management ──────────────────── --}}
    <div>
        {{-- Assign Sesi --}}
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:var(--accent-dim);color:var(--accent);"><i class="fas fa-plus-circle"></i></div>
                <div>
                    <div class="card-title">Assign Sesi</div>
                    <div class="card-sub">Tambah sesi Facebook ke token ini</div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.meta.tokens.sessions.assign', $token['id']) }}" method="POST">
                    @csrf
                    <div class="assign-box">
                        <div class="assign-label"><i class="fas fa-list"></i> &nbsp;Nama Sesi</div>
                        <textarea name="sessions" placeholder="akun_1&#10;akun_2&#10;akun_3&#10;&#10;(satu per baris, atau pisah dengan koma)"></textarea>
                        <div class="assign-hint">Nama sesi harus sama persis dengan folder di <code style="color:var(--accent);">session/meta/</code></div>
                    </div>
                    <button type="submit" class="btn-sm primary" style="width:100%;justify-content:center;padding:10px;">
                        <i class="fas fa-plus"></i> Assign Sesi
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Sesi --}}
        <div class="card">
            <div class="card-header" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="card-icon" style="background:rgba(0,170,255,.1);color:#00aaff;"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <div class="card-title">Sesi Aktif</div>
                        <div class="card-sub">Sesi yang di-assign ke token ini</div>
                    </div>
                </div>
                @php
                    $sessions = is_array($token['sessions'] ?? null)
                        ? $token['sessions']
                        : array_filter(array_map('trim', explode(',', $token['sessions'] ?? '')));
                @endphp
                <span style="background:var(--accent-dim);border:1px solid var(--accent-border);color:var(--accent);font-size:12px;font-family:'Space Mono',monospace;padding:3px 10px;border-radius:20px;">
                    {{ count($sessions) }} sesi
                </span>
            </div>
            <div class="card-body" style="padding-top:4px;padding-bottom:4px;">
                @if(count($sessions) === 0)
                    <div class="empty-sessions">
                        <i class="fas fa-mobile-alt"></i>
                        Belum ada sesi di-assign.
                    </div>
                @else
                <div class="session-list">
                    @foreach($sessions as $session)
                    @php $sName = is_array($session) ? $session['session_name'] : $session; $sDate = is_array($session) ? ($session['created_at'] ?? null) : null; @endphp
                    <div class="session-item">
                        <div>
                            <div class="session-name"><i class="fas fa-mobile-alt" style="color:var(--accent);font-size:11px;margin-right:6px;"></i>{{ $sName }}</div>
                            @if($sDate)<div class="session-date">Ditambah: {{ \Carbon\Carbon::parse($sDate)->format('d M Y, H:i') }}</div>@endif
                        </div>
                        <form action="{{ route('admin.meta.tokens.sessions.revoke', $token['id']) }}" method="POST"
                              onsubmit="return confirm('Cabut sesi {{ $sName }}?')">
                            @csrf
                            <input type="hidden" name="session" value="{{ $sName }}">
                            <button type="submit" class="btn-sm danger" style="padding:6px 10px;">
                                <i class="fas fa-times"></i> Cabut
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function copyToken(token, btn) {
    navigator.clipboard.writeText(token).then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i> Copy', 2000);
    });
}
setTimeout(() => { const f = document.querySelector('.flash-msg'); if(f) f.style.display='none'; }, 4000);
</script>
@endpush
@endsection
