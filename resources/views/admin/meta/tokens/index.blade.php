@extends('layouts.app')

@section('page-title', 'Token Management')
@section('breadcrumb', 'Tokens')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; gap:16px; flex-wrap:wrap; }
    .page-header-left h2 { font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; }
    .page-header-left p  { font-size:13px; color:var(--text-secondary); margin-top:4px; font-family:'Space Mono',monospace; }

    .btn-primary { display:inline-flex; align-items:center; gap:8px; padding:11px 20px; background:var(--accent); color:#000; font-family:'Syne',sans-serif; font-size:13px; font-weight:700; border-radius:10px; text-decoration:none; border:none; cursor:pointer; transition:all .2s; }
    .btn-primary:hover { background:#00cc6e; transform:translateY(-1px); box-shadow:0 4px 20px rgba(0,255,136,.25); }

    /* Stats row */
    .token-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
    .t-stat { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:18px 20px; display:flex; align-items:center; gap:14px; }
    .t-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .t-stat-val  { font-size:22px; font-weight:800; color:var(--text-primary); line-height:1; }
    .t-stat-lbl  { font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); margin-top:3px; }

    /* Table */
    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead { background:var(--bg-secondary); border-bottom:1px solid var(--border); }
    .data-table th { padding:14px 20px; font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.2px; text-align:left; white-space:nowrap; }
    .data-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
    .data-table tbody tr:last-child { border-bottom:none; }
    .data-table tbody tr:hover { background:rgba(255,255,255,.02); }
    .data-table td { padding:16px 20px; font-size:13px; color:var(--text-primary); vertical-align:middle; }

    .token-str { font-family:'Space Mono',monospace; font-size:11px; color:var(--text-muted); background:var(--bg-hover); padding:4px 10px; border-radius:6px; letter-spacing:.5px; cursor:pointer; transition:all .2s; border:1px solid var(--border); }
    .token-str:hover { border-color:var(--accent-border); color:var(--accent); }

    .role-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; font-family:'Space Mono',monospace; }
    .role-badge.admin  { background:rgba(180,100,255,.12); color:#b464ff; border:1px solid rgba(180,100,255,.25); }
    .role-badge.client { background:rgba(0,170,255,.12);   color:#00aaff; border:1px solid rgba(0,170,255,.25); }

    .status-toggle { display:flex; align-items:center; gap:8px; }
    .toggle-switch { position:relative; width:40px; height:22px; cursor:pointer; }
    .toggle-switch input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; inset:0; background:var(--bg-hover); border:1px solid var(--border); border-radius:22px; transition:.3s; }
    .toggle-slider:before { content:''; position:absolute; width:16px; height:16px; left:2px; bottom:2px; background:var(--text-muted); border-radius:50%; transition:.3s; }
    .toggle-switch input:checked + .toggle-slider { background:rgba(0,255,136,.15); border-color:var(--accent-border); }
    .toggle-switch input:checked + .toggle-slider:before { transform:translateX(18px); background:var(--accent); }

    .status-lbl { font-size:11px; font-family:'Space Mono',monospace; }
    .status-lbl.on  { color:var(--accent); }
    .status-lbl.off { color:var(--text-muted); }

    .session-chips { display:flex; flex-wrap:wrap; gap:5px; max-width:200px; }
    .chip { background:var(--bg-hover); border:1px solid var(--border); border-radius:5px; padding:2px 8px; font-size:10px; font-family:'Space Mono',monospace; color:var(--text-secondary); }
    .chip-more { background:var(--accent-dim); border-color:var(--accent-border); color:var(--accent); }

    .exp-badge { font-size:11px; font-family:'Space Mono',monospace; }
    .exp-badge.never   { color:var(--text-muted); }
    .exp-badge.active  { color:#00ff88; }
    .exp-badge.expired { color:#ff4466; }
    .exp-badge.soon    { color:#ffaa00; }

    .action-group { display:flex; align-items:center; gap:6px; justify-content:flex-end; }
    .action-btn { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; text-decoration:none; border:1px solid var(--border); background:var(--bg-hover); cursor:pointer; transition:all .2s; color:var(--text-secondary); }
    .action-btn:hover { transform:translateY(-1px); }
    .action-btn.view:hover   { border-color:rgba(0,170,255,.4);  color:#00aaff; background:rgba(0,170,255,.1); }
    .action-btn.delete:hover { border-color:rgba(255,68,102,.4); color:#ff4466; background:rgba(255,68,102,.1); }

    /* Flash */
    .flash-msg { display:flex; align-items:flex-start; gap:12px; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:600; animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
    .flash-msg.success { background:rgba(0,255,136,.1); border:1px solid rgba(0,255,136,.25); color:#00ff88; }
    .flash-msg.error   { background:rgba(255,68,102,.1); border:1px solid rgba(255,68,102,.25); color:#ff4466; }

    /* New token reveal box */
    .token-reveal { background:rgba(0,255,136,.06); border:1px solid rgba(0,255,136,.3); border-radius:12px; padding:16px 20px; margin-bottom:20px; }
    .token-reveal-label { font-size:11px; font-family:'Space Mono',monospace; color:var(--accent); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
    .token-reveal-value { font-family:'Space Mono',monospace; font-size:14px; color:var(--text-primary); word-break:break-all; background:var(--bg-secondary); padding:10px 14px; border-radius:8px; border:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .copy-btn { background:var(--accent); color:#000; border:none; padding:5px 12px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:'Space Mono',monospace; white-space:nowrap; transition:all .2s; }
    .copy-btn:hover { background:#00cc6e; }
    .token-reveal-warn { font-size:11px; color:var(--text-muted); margin-top:8px; display:flex; align-items:center; gap:6px; }

    /* Delete modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; padding:32px; max-width:420px; width:90%; animation:popIn .25s ease; }
    @keyframes popIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
    .modal-icon { width:56px; height:56px; border-radius:14px; background:rgba(255,68,102,.12); border:1px solid rgba(255,68,102,.25); display:flex; align-items:center; justify-content:center; font-size:22px; color:#ff4466; margin:0 auto 20px; }
    .modal-box h3 { text-align:center; font-size:18px; font-weight:800; color:var(--text-primary); margin-bottom:8px; }
    .modal-box p  { text-align:center; font-size:13px; color:var(--text-secondary); margin-bottom:24px; line-height:1.6; }
    .modal-actions { display:flex; gap:10px; }
    .btn-cancel { flex:1; padding:11px; background:var(--bg-hover); border:1px solid var(--border); border-radius:10px; color:var(--text-secondary); font-family:'Syne',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; }
    .btn-cancel:hover { background:var(--bg-secondary); color:var(--text-primary); }
    .btn-danger { flex:1; padding:11px; background:rgba(255,68,102,.15); border:1px solid rgba(255,68,102,.35); border-radius:10px; color:#ff4466; font-family:'Syne',sans-serif; font-size:13px; font-weight:700; cursor:pointer; transition:all .2s; }
    .btn-danger:hover { background:rgba(255,68,102,.25); }

    .empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
    .empty-state i { font-size:40px; margin-bottom:16px; opacity:.4; display:block; }

    @media(max-width:1100px) { .token-stats{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:640px)  { .token-stats{grid-template-columns:1fr 1fr;} }
</style>

{{-- Flash --}}
@if(session('success'))
<div class="flash-msg success"><i class="fas fa-check-circle" style="font-size:16px;flex-shrink:0;"></i><div>{{ session('success') }}</div></div>
@endif
@if(session('error') || isset($error))
<div class="flash-msg error"><i class="fas fa-exclamation-circle" style="font-size:16px;flex-shrink:0;"></i><div>{{ session('error') ?? $error }}</div></div>
@endif

{{-- New token reveal --}}
@if(session('new_token'))
<div class="token-reveal">
    <div class="token-reveal-label"><i class="fas fa-key"></i> &nbsp;Token Baru — Simpan Sekarang!</div>
    <div class="token-reveal-value">
        <span id="newTokenVal">{{ session('new_token') }}</span>
        <button class="copy-btn" onclick="copyToken()"><i class="fas fa-copy"></i> Copy</button>
    </div>
    <div class="token-reveal-warn"><i class="fas fa-exclamation-triangle"></i> Token ini hanya tampil sekali. Tidak bisa diambil ulang!</div>
</div>
@endif

{{-- Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>Token Management</h2>
        <p>{{ count($tokens) }} token terdaftar · Bot Meta API</p>
    </div>
    <a href="{{ route('admin.meta.tokens.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Buat Token
    </a>
</div>

{{-- Stats --}}
@php
    $totalTokens  = count($tokens);
    $activeTokens = collect($tokens)->where('is_active', 1)->count();
    $adminTokens  = collect($tokens)->where('role', 'admin')->count();
    $clientTokens = collect($tokens)->where('role', 'client')->count();
@endphp
<div class="token-stats">
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(0,255,136,.1);color:#00ff88;"><i class="fas fa-key"></i></div>
        <div><div class="t-stat-val">{{ $totalTokens }}</div><div class="t-stat-lbl">Total Token</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(0,170,255,.1);color:#00aaff;"><i class="fas fa-check-circle"></i></div>
        <div><div class="t-stat-val">{{ $activeTokens }}</div><div class="t-stat-lbl">Aktif</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(180,100,255,.1);color:#b464ff;"><i class="fas fa-shield-alt"></i></div>
        <div><div class="t-stat-val">{{ $adminTokens }}</div><div class="t-stat-lbl">Admin</div></div>
    </div>
    <div class="t-stat">
        <div class="t-stat-icon" style="background:rgba(255,170,0,.1);color:#ffaa00;"><i class="fas fa-users"></i></div>
        <div><div class="t-stat-val">{{ $clientTokens }}</div><div class="t-stat-lbl">Client</div></div>
    </div>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Klien</th>
                <th>Token</th>
                <th>Role</th>
                <th>Sesi</th>
                <th>Expired</th>
                <th>Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tokens as $token)
            @php
                $sessions = is_array($token['sessions'] ?? null)
                    ? $token['sessions']
                    : array_filter(array_map('trim', explode(',', $token['sessions'] ?? '')));
                $expiredAt = $token['expired_at'] ?? null;
                $expClass  = 'never';
                $expLabel  = 'Tidak Expired';
                if ($expiredAt) {
                    $expDate  = \Carbon\Carbon::parse($expiredAt);
                    $now      = now();
                    if ($expDate->isPast()) { $expClass='expired'; $expLabel='Expired'; }
                    elseif ($expDate->diffInDays($now) <= 30) { $expClass='soon'; $expLabel=$expDate->diffForHumans(); }
                    else { $expClass='active'; $expLabel=$expDate->format('d M Y'); }
                }
            @endphp
            <tr>
                <td>
                    <div style="font-weight:700;color:var(--text-primary);">{{ $token['client_name'] }}</div>
                    <div style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;">ID #{{ $token['id'] }}</div>
                </td>
                <td>
                    <span class="token-str" onclick="copyText('{{ $token['token'] }}', this)" title="Klik untuk copy">
                        {{ substr($token['token'], 0, 16) }}···
                    </span>
                </td>
                <td>
                    <span class="role-badge {{ $token['role'] }}">
                        @if($token['role'] === 'admin')<i class="fas fa-shield-alt"></i>@else<i class="fas fa-user"></i>@endif
                        {{ ucfirst($token['role']) }}
                    </span>
                </td>
                <td>
                    @if(count($sessions) === 0)
                        <span style="color:var(--text-muted);font-size:12px;">—</span>
                    @else
                    <div class="session-chips">
                        @foreach(array_slice($sessions, 0, 3) as $s)
                            <span class="chip">{{ is_array($s) ? $s['session_name'] : $s }}</span>
                        @endforeach
                        @if(count($sessions) > 3)
                            <span class="chip chip-more">+{{ count($sessions) - 3 }}</span>
                        @endif
                    </div>
                    @endif
                </td>
                <td><span class="exp-badge {{ $expClass }}">{{ $expLabel }}</span></td>
               <td>
    <div class="status-toggle" style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
        <form action="{{ route('admin.meta.tokens.toggle', $token['id']) }}" method="POST" style="margin: 0; line-height: 0;">
            @csrf
            <input type="hidden" name="is_active" value="{{ $token['is_active'] ? 0 : 1 }}">
            <label class="toggle-switch" title="{{ $token['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}" style="margin-bottom: 0;">
                <input type="checkbox" onchange="this.closest('form').submit()" {{ $token['is_active'] ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </form>

        <span class="status-lbl {{ $token['is_active'] ? 'on' : 'off' }}" style="font-size: 0.75rem; white-space: nowrap;">
            {{ $token['is_active'] ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
</td>
                <td>
                    <div class="action-group">
                        <a href="{{ route('admin.meta.tokens.show', $token['id']) }}" class="action-btn view" title="Detail & Sesi">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="action-btn delete" title="Hapus Token"
                            onclick="openDeleteModal({{ $token['id'] }}, '{{ addslashes($token['client_name']) }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7">
                <div class="empty-state">
                    <i class="fas fa-key"></i>
                    <p>Belum ada token. <a href="{{ route('admin.meta.tokens.create') }}" style="color:var(--accent);">Buat token pertama →</a></p>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Delete Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-trash-alt"></i></div>
        <h3>Hapus Token?</h3>
        <p>Token untuk <strong id="deleteClientName"></strong> akan dihapus permanen. Semua sesi yang di-assign juga akan terhapus!</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <form id="deleteTokenForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger" style="width:100%"><i class="fas fa-trash-alt"></i> Hapus</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteClientName').textContent = name;
    document.getElementById('deleteTokenForm').action = '/admin/meta/tokens/' + id;
    document.getElementById('deleteModal').classList.add('open');
}
function closeModal() { document.getElementById('deleteModal').classList.remove('open'); }
document.getElementById('deleteModal').addEventListener('click', function(e) { if(e.target===this) closeModal(); });

function copyText(text, el) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = el.textContent;
        el.textContent = '✓ Copied!';
        el.style.color = '#00ff88';
        setTimeout(() => { el.textContent = orig; el.style.color = ''; }, 2000);
    });
}

function copyToken() {
    const val = document.getElementById('newTokenVal').textContent;
    navigator.clipboard.writeText(val).then(() => {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i> Copy', 2000);
    });
}

setTimeout(() => { const f = document.querySelector('.flash-msg'); if(f) f.style.display='none'; }, 5000);
</script>
@endpush
@endsection
