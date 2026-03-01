@extends('layouts.app')
@section('page-title', 'Access Logs')
@section('breadcrumb', 'Logs')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; gap:16px; flex-wrap:wrap; }
    .page-header-left h2 { font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-.5px; }
    .page-header-left p  { font-size:13px; color:var(--text-secondary); margin-top:4px; font-family:'Space Mono',monospace; }

    /* Filter card */
    .filter-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:20px 24px; margin-bottom:20px; }
    .filter-grid { display:grid; grid-template-columns:repeat(3,1fr) 1fr 1fr; gap:12px; align-items:end; }
    .filter-group label { display:block; font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
    .filter-control { width:100%; background:var(--bg-secondary); border:1px solid var(--border); border-radius:9px; padding:9px 14px; color:var(--text-primary); font-family:'Syne',sans-serif; font-size:13px; outline:none; transition:border-color .2s; }
    .filter-control:focus { border-color:var(--accent-border); }
    .filter-control::placeholder { color:var(--text-muted); }
    select.filter-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%237a7a96' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-color:var(--bg-secondary); padding-right:34px; cursor:pointer; }
    select.filter-control option { background:var(--bg-card); }
    .btn-filter { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 18px; background:var(--accent); color:#000; font-family:'Syne',sans-serif; font-size:13px; font-weight:700; border-radius:9px; border:none; cursor:pointer; transition:all .2s; white-space:nowrap; }
    .btn-filter:hover { background:#00cc6e; }
    .btn-reset { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--bg-hover); border:1px solid var(--border); color:var(--text-secondary); font-family:'Syne',sans-serif; font-size:13px; font-weight:600; border-radius:9px; text-decoration:none; transition:all .2s; white-space:nowrap; }
    .btn-reset:hover { background:var(--bg-secondary); color:var(--text-primary); }

    /* Shortcut filter pills */
    .filter-pills { display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; padding-top:14px; border-top:1px solid var(--border); }
    .filter-pill { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:11px; font-family:'Space Mono',monospace; font-weight:700; cursor:pointer; text-decoration:none; transition:all .2s; }
    .filter-pill.all      { background:var(--bg-hover);          border:1px solid var(--border);              color:var(--text-secondary); }
    .filter-pill.success  { background:rgba(0,255,136,.08);       border:1px solid rgba(0,255,136,.2);        color:#00ff88; }
    .filter-pill.rejected { background:rgba(255,68,102,.08);      border:1px solid rgba(255,68,102,.2);       color:#ff4466; }
    .filter-pill.invalid  { background:rgba(255,170,0,.08);       border:1px solid rgba(255,170,0,.2);        color:#ffaa00; }
    .filter-pill:hover    { transform:translateY(-1px); }
    .filter-pill.active   { opacity:1; box-shadow:0 2px 10px rgba(0,0,0,.2); }

    /* Table */
    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead { background:var(--bg-secondary); border-bottom:1px solid var(--border); }
    .data-table th { padding:12px 16px; font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; text-align:left; white-space:nowrap; }
    .data-table tbody tr { border-bottom:1px solid var(--border); transition:background .15s; }
    .data-table tbody tr:last-child { border-bottom:none; }
    .data-table tbody tr:hover { background:rgba(255,255,255,.02); }
    .data-table td { padding:13px 16px; font-size:12px; color:var(--text-primary); vertical-align:middle; }

    .method-badge { display:inline-block; padding:2px 8px; border-radius:5px; font-size:10px; font-weight:700; font-family:'Space Mono',monospace; }
    .method-badge.GET    { background:rgba(0,255,136,.1);   color:#00ff88;  border:1px solid rgba(0,255,136,.2); }
    .method-badge.POST   { background:rgba(0,170,255,.1);   color:#00aaff;  border:1px solid rgba(0,170,255,.2); }
    .method-badge.PATCH  { background:rgba(255,170,0,.1);   color:#ffaa00;  border:1px solid rgba(255,170,0,.2); }
    .method-badge.DELETE { background:rgba(255,68,102,.1);  color:#ff4466;  border:1px solid rgba(255,68,102,.2); }
    .method-badge.PUT    { background:rgba(180,100,255,.1); color:#b464ff;  border:1px solid rgba(180,100,255,.2); }

    .status-dot { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-family:'Space Mono',monospace; font-weight:700; }
    .status-dot::before { content:''; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .status-dot.s200::before { background:#00ff88; box-shadow:0 0 4px #00ff88; }
    .status-dot.s401::before { background:#ffaa00; }
    .status-dot.s403::before { background:#ff4466; }
    .status-dot.s500::before { background:#ff4466; }
    .status-dot.s200 { color:#00ff88; }
    .status-dot.s401 { color:#ffaa00; }
    .status-dot.s403 { color:#ff4466; }
    .status-dot.s500 { color:#ff4466; }

    .endpoint-text { font-family:'Space Mono',monospace; font-size:11px; color:var(--text-secondary); }
    .ip-text       { font-family:'Space Mono',monospace; font-size:11px; color:var(--text-muted); }
    .time-text     { font-family:'Space Mono',monospace; font-size:11px; color:var(--text-muted); }
    .client-name   { font-size:12px; font-weight:600; color:var(--text-primary); }
    .session-tag   { font-family:'Space Mono',monospace; font-size:11px; background:var(--bg-hover); border:1px solid var(--border); padding:2px 8px; border-radius:5px; color:var(--text-secondary); }

    /* Stats strip */
    .log-stats { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
    .log-stat { background:var(--bg-card); border:1px solid var(--border); border-radius:10px; padding:14px 18px; flex:1; min-width:120px; }
    .log-stat-val { font-size:20px; font-weight:800; color:var(--text-primary); }
    .log-stat-lbl { font-size:10px; font-family:'Space Mono',monospace; color:var(--text-muted); margin-top:3px; }

    .table-footer { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--border); background:var(--bg-secondary); flex-wrap:wrap; gap:10px; }
    .table-info { font-size:12px; font-family:'Space Mono',monospace; color:var(--text-muted); }

    .flash-msg { display:flex; align-items:center; gap:12px; padding:13px 18px; border-radius:10px; margin-bottom:18px; font-size:13px; font-weight:600; animation:slideDown .3s ease; }
    @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
    .flash-msg.error { background:rgba(255,68,102,.1); border:1px solid rgba(255,68,102,.25); color:#ff4466; }

    .empty-state { text-align:center; padding:60px 20px; color:var(--text-muted); }
    .empty-state i { font-size:40px; opacity:.3; display:block; margin-bottom:14px; }

    @media(max-width:1100px) { .filter-grid{grid-template-columns:1fr 1fr 1fr;} }
    @media(max-width:700px)  { .filter-grid{grid-template-columns:1fr 1fr;} }
    @media(max-width:480px)  { .filter-grid{grid-template-columns:1fr;} }
</style>

@if(isset($error))
<div class="flash-msg error"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
@endif

<div class="page-header">
    <div class="page-header-left">
        <h2>Access Logs</h2>
        <p>Monitor semua request ke Bot Meta API</p>
    </div>
</div>

{{-- Stats --}}
@php
    $total200 = collect($logs)->where('status_code', 200)->count();
    $total401 = collect($logs)->where('status_code', 401)->count();
    $total403 = collect($logs)->where('status_code', 403)->count();
@endphp
<div class="log-stats">
    <div class="log-stat">
        <div class="log-stat-val">{{ number_format($total) }}</div>
        <div class="log-stat-lbl">Total Log</div>
    </div>
    <div class="log-stat">
        <div class="log-stat-val" style="color:#00ff88;">{{ $total200 }}</div>
        <div class="log-stat-lbl">Sukses (200)</div>
    </div>
    <div class="log-stat">
        <div class="log-stat-val" style="color:#ff4466;">{{ $total403 }}</div>
        <div class="log-stat-lbl">Ditolak (403)</div>
    </div>
    <div class="log-stat">
        <div class="log-stat-val" style="color:#ffaa00;">{{ $total401 }}</div>
        <div class="log-stat-lbl">Token Invalid (401)</div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-card">
    <form method="GET" action="{{ route('admin.meta.logs.index') }}" id="filterForm">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Token ID</label>
                <input type="number" name="token_id" class="filter-control" placeholder="cth: 2" value="{{ $filters['token_id'] ?? '' }}">
            </div>
            <div class="filter-group">
                <label>Endpoint</label>
                <input type="text" name="endpoint" class="filter-control" placeholder="cth: /schedule" value="{{ $filters['endpoint'] ?? '' }}">
            </div>
            <div class="filter-group">
                <label>Status Code</label>
                <select name="status_code" class="filter-control">
                    <option value="">Semua</option>
                    <option value="200" {{ ($filters['status_code'] ?? '') == '200' ? 'selected' : '' }}>200 — Sukses</option>
                    <option value="401" {{ ($filters['status_code'] ?? '') == '401' ? 'selected' : '' }}>401 — Token Invalid</option>
                    <option value="403" {{ ($filters['status_code'] ?? '') == '403' ? 'selected' : '' }}>403 — Akses Ditolak</option>
                    <option value="500" {{ ($filters['status_code'] ?? '') == '500' ? 'selected' : '' }}>500 — Error</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Limit</label>
                <select name="limit" class="filter-control">
                    <option value="50"  {{ ($filters['limit'] ?? 100) == 50  ? 'selected' : '' }}>50</option>
                    <option value="100" {{ ($filters['limit'] ?? 100) == 100 ? 'selected' : '' }}>100</option>
                    <option value="250" {{ ($filters['limit'] ?? 100) == 250 ? 'selected' : '' }}>250</option>
                    <option value="500" {{ ($filters['limit'] ?? 100) == 500 ? 'selected' : '' }}>500</option>
                </select>
            </div>
            <div class="filter-group" style="display:flex;gap:8px;">
                <button type="submit" class="btn-filter" style="flex:1;"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.meta.logs.index') }}" class="btn-reset"><i class="fas fa-times"></i></a>
            </div>
        </div>

        {{-- Quick pills --}}
        <div class="filter-pills">
            <span style="font-size:11px;color:var(--text-muted);font-family:'Space Mono',monospace;align-self:center;">Quick:</span>
            <a href="{{ route('admin.meta.logs.index') }}" class="filter-pill all {{ empty($filters) ? 'active' : '' }}">
                <i class="fas fa-list"></i> Semua
            </a>
            <a href="{{ route('admin.meta.logs.index', ['status_code'=>200]) }}" class="filter-pill success {{ ($filters['status_code'] ?? '') == 200 ? 'active' : '' }}">
                <i class="fas fa-check"></i> 200 Sukses
            </a>
            <a href="{{ route('admin.meta.logs.index', ['status_code'=>403]) }}" class="filter-pill rejected {{ ($filters['status_code'] ?? '') == 403 ? 'active' : '' }}">
                <i class="fas fa-ban"></i> 403 Ditolak
            </a>
            <a href="{{ route('admin.meta.logs.index', ['status_code'=>401]) }}" class="filter-pill invalid {{ ($filters['status_code'] ?? '') == 401 ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> 401 Token Invalid
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Klien</th>
                <th>Sesi</th>
                <th>Status</th>
                <th>IP</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            @php
                $statusCode = $log['status_code'] ?? 0;
                $statusClass = match(true) {
                    $statusCode >= 200 && $statusCode < 300 => 's200',
                    $statusCode == 401 => 's401',
                    $statusCode == 403 => 's403',
                    default => 's500',
                };
            @endphp
            <tr>
                <td style="color:var(--text-muted);font-family:'Space Mono',monospace;font-size:11px;">{{ $log['id'] }}</td>
                <td><span class="method-badge {{ $log['method'] ?? 'GET' }}">{{ $log['method'] ?? 'GET' }}</span></td>
                <td><span class="endpoint-text">{{ $log['endpoint'] ?? '—' }}</span></td>
                <td>
                    <div class="client-name">{{ $log['client_name'] ?? '—' }}</div>
                    @if(isset($log['token_id']))<div style="font-size:10px;color:var(--text-muted);font-family:'Space Mono',monospace;">ID #{{ $log['token_id'] }}</div>@endif
                </td>
                <td>
                    @if(!empty($log['session_name']))
                        <span class="session-tag">{{ $log['session_name'] }}</span>
                    @else
                        <span style="color:var(--text-muted);">—</span>
                    @endif
                </td>
                <td>
                    <span class="status-dot {{ $statusClass }}">{{ $statusCode }}</span>
                    @if(!empty($log['message']))<div style="font-size:10px;color:var(--text-muted);margin-top:2px;">{{ Str::limit($log['message'], 30) }}</div>@endif
                </td>
                <td><span class="ip-text">{{ $log['ip_address'] ?? '—' }}</span></td>
                <td>
                    <span class="time-text">
                        @if(!empty($log['created_at']))
                            {{ \Carbon\Carbon::parse($log['created_at'])->format('d/m H:i:s') }}
                        @else —
                        @endif
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="8">
                <div class="empty-state">
                    <i class="fas fa-stream"></i>
                    <p>Tidak ada log{{ !empty($filters) ? ' yang sesuai filter' : '' }}.</p>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="table-footer">
        <span class="table-info">
            Menampilkan <strong style="color:var(--accent);">{{ count($logs) }}</strong> dari {{ number_format($total) }} total log
        </span>
        @if(count($logs) < $total)
        <a href="{{ route('admin.meta.logs.index', array_merge($filters, ['limit' => ($filters['limit'] ?? 100) + 100])) }}"
            style="font-size:12px;color:var(--accent);text-decoration:none;font-family:'Space Mono',monospace;">
            Load more →
        </a>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Auto-submit form on select change
document.querySelectorAll('#filterForm select').forEach(el => {
    el.addEventListener('change', () => document.getElementById('filterForm').submit());
});
</script>
@endpush
@endsection
