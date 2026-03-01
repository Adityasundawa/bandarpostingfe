@extends('layouts.app')

@section('page-title', 'User Management')
@section('breadcrumb', 'Users')

@section('content')

<style>
    /* ─── PAGE HEADER ──────────────────────────────────── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .page-header-left h2 {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    .page-header-left p {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 4px;
        font-family: 'Space Mono', monospace;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        background: var(--accent);
        color: #000;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #00cc6e;
        transform: translateY(-1px);
        box-shadow: 0 4px 20px rgba(0,255,136,0.25);
    }

    /* ─── FILTER BAR ───────────────────────────────────── */
    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 16px;
        flex: 1;
        min-width: 200px;
        max-width: 360px;
        transition: border-color 0.2s;
    }

    .search-box:focus-within {
        border-color: var(--accent-border);
    }

    .search-box i { color: var(--text-muted); font-size: 13px; }

    .search-box input {
        background: none;
        border: none;
        outline: none;
        color: var(--text-primary);
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        width: 100%;
    }

    .search-box input::placeholder { color: var(--text-muted); }

    .filter-select {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 16px;
        color: var(--text-primary);
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7a96' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .filter-select:focus { border-color: var(--accent-border); }

    .filter-count {
        font-size: 12px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        margin-left: auto;
        white-space: nowrap;
    }

    .filter-count strong { color: var(--accent); }

    /* ─── TABLE CARD ───────────────────────────────────── */
    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border);
    }

    .data-table th {
        padding: 14px 20px;
        font-size: 10px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.2px;
        text-align: left;
        white-space: nowrap;
    }

    .data-table th.sortable {
        cursor: pointer;
        user-select: none;
    }

    .data-table th.sortable:hover { color: var(--accent); }

    .data-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s;
    }

    .data-table tbody tr:last-child { border-bottom: none; }

    .data-table tbody tr:hover { background: rgba(255,255,255,0.02); }

    .data-table td {
        padding: 16px 20px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* User cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .u-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
        font-family: 'Space Mono', monospace;
    }

    .u-name { font-size: 14px; font-weight: 600; color: var(--text-primary); line-height: 1; }
    .u-email { font-size: 11px; color: var(--text-muted); margin-top: 3px; font-family: 'Space Mono', monospace; }

    /* Role badge */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        font-family: 'Space Mono', monospace;
        letter-spacing: 0.3px;
    }

    .role-badge.admin     { background: rgba(180,100,255,0.12); color: #b464ff; border: 1px solid rgba(180,100,255,0.25); }
    .role-badge.developer { background: rgba(0,170,255,0.12);   color: #00aaff; border: 1px solid rgba(0,170,255,0.25); }
    .role-badge.designer  { background: rgba(255,170,0,0.12);    color: #ffaa00; border: 1px solid rgba(255,170,0,0.25); }
    .role-badge.member    { background: rgba(255,255,255,0.06);  color: #7a7a96; border: 1px solid rgba(255,255,255,0.1); }

    /* Status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        font-family: 'Space Mono', monospace;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .status-badge.active   { background: rgba(0,255,136,0.1);  color: #00ff88; border: 1px solid rgba(0,255,136,0.2); }
    .status-badge.pending  { background: rgba(255,170,0,0.1);   color: #ffaa00; border: 1px solid rgba(255,170,0,0.2); }
    .status-badge.inactive { background: rgba(255,68,102,0.1);  color: #ff4466; border: 1px solid rgba(255,68,102,0.2); }

    .status-badge.active .status-dot   { background: #00ff88; box-shadow: 0 0 4px #00ff88; animation: pulse 2s infinite; }
    .status-badge.pending .status-dot  { background: #ffaa00; }
    .status-badge.inactive .status-dot { background: #ff4466; }

    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    /* Action buttons */
    .action-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--bg-hover);
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-secondary);
    }

    .action-btn:hover { transform: translateY(-1px); }
    .action-btn.view:hover  { border-color: rgba(0,170,255,0.4); color: #00aaff; background: rgba(0,170,255,0.1); }
    .action-btn.edit:hover  { border-color: rgba(255,170,0,0.4); color: #ffaa00; background: rgba(255,170,0,0.1); }
    .action-btn.delete:hover{ border-color: rgba(255,68,102,0.4); color: #ff4466; background: rgba(255,68,102,0.1); }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i { font-size: 40px; margin-bottom: 16px; opacity: 0.4; }
    .empty-state p { font-size: 14px; }

    /* Pagination */
    .table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid var(--border);
        background: var(--bg-secondary);
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-info {
        font-size: 12px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
    }

    .pagination {
        display: flex;
        gap: 4px;
    }

    .page-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-family: 'Space Mono', monospace;
        background: var(--bg-card);
        border: 1px solid var(--border);
        color: var(--text-secondary);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .page-btn:hover, .page-btn.active {
        background: var(--accent-dim);
        border-color: var(--accent-border);
        color: var(--accent);
    }

    /* Flash Message */
    .flash-msg {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 600;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }

    .flash-msg.success { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.25); color: #00ff88; }
    .flash-msg.error   { background: rgba(255,68,102,0.1); border: 1px solid rgba(255,68,102,0.25); color: #ff4466; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.open { display: flex; }

    .modal-box {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 32px;
        max-width: 420px;
        width: 90%;
        animation: popIn 0.25s ease;
    }

    @keyframes popIn { from{opacity:0;transform:scale(0.95)} to{opacity:1;transform:scale(1)} }

    .modal-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: rgba(255,68,102,0.12);
        border: 1px solid rgba(255,68,102,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ff4466;
        margin: 0 auto 20px;
    }

    .modal-box h3 {
        text-align: center;
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .modal-box p {
        text-align: center;
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .modal-box p strong { color: var(--text-primary); }

    .modal-actions {
        display: flex;
        gap: 10px;
    }

    .btn-cancel {
        flex: 1;
        padding: 11px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-secondary);
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover { background: var(--bg-secondary); color: var(--text-primary); }

    .btn-danger {
        flex: 1;
        padding: 11px;
        background: rgba(255,68,102,0.15);
        border: 1px solid rgba(255,68,102,0.35);
        border-radius: 10px;
        color: #ff4466;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-danger:hover { background: rgba(255,68,102,0.25); }

    /* Avatar colors */
    .av-0 { background: rgba(0,255,136,0.12); color: #00ff88; }
    .av-1 { background: rgba(0,170,255,0.12); color: #00aaff; }
    .av-2 { background: rgba(255,170,0,0.12);  color: #ffaa00; }
    .av-3 { background: rgba(255,68,102,0.12);  color: #ff4466; }
    .av-4 { background: rgba(180,100,255,0.12); color: #b464ff; }
    .av-5 { background: rgba(0,200,255,0.12);   color: #00c8ff; }
</style>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="flash-msg success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="flash-msg error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div class="page-header-left">
        <h2>User Management</h2>
        <p>{{ $users->total() }} total users terdaftar di sistem</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> Tambah User
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama atau email...">
    </div>
    <select class="filter-select" id="filterRole">
        <option value="">Semua Role</option>
        <option value="admin">Admin</option>
        <option value="developer">Developer</option>
        <option value="designer">Designer</option>
        <option value="member">Member</option>
    </select>
    <select class="filter-select" id="filterStatus">
        <option value="">Semua Status</option>
        <option value="active">Active</option>
        <option value="pending">Pending</option>
        <option value="inactive">Inactive</option>
    </select>
    <span class="filter-count">Showing <strong id="countVisible">{{ $users->total() }}</strong> users</span>
</div>

{{-- Table --}}
<div class="table-card">
    <table class="data-table" id="userTable">
        <thead>
            <tr>
                <th style="width:40px;">
                    <input type="checkbox" id="selectAll" style="accent-color: var(--accent); width:14px; height:14px; cursor:pointer;">
                </th>
                <th class="sortable">User <i class="fas fa-sort" style="opacity:0.4; margin-left:4px;"></i></th>
                <th>Role</th>
                <th class="sortable">Bergabung <i class="fas fa-sort" style="opacity:0.4; margin-left:4px;"></i></th>
                <th>Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            @forelse($users as $index => $user)
            <tr class="user-row"
                data-name="{{ strtolower($user->name) }}"
                data-email="{{ strtolower($user->email) }}"
                data-role="{{ strtolower($user->role) }}"
                data-status="{{ strtolower($user->status) }}">
                <td>
                    <input type="checkbox" class="row-check" style="accent-color: var(--accent); width:14px; height:14px; cursor:pointer;">
                </td>
                <td>
                    <div class="user-cell">
                        <div class="u-avatar av-{{ $index % 6 }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="u-name">{{ $user->name }}</div>
                            <div class="u-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="role-badge {{ strtolower($user->role) }}">
                        @if(strtolower($user->role) === 'admin') <i class="fas fa-shield-alt"></i>
                        @elseif(strtolower($user->role) === 'developer') <i class="fas fa-code"></i>
                        @elseif(strtolower($user->role) === 'designer') <i class="fas fa-palette"></i>
                        @else <i class="fas fa-user"></i>
                        @endif
                        {{ $user->role }}
                    </span>
                </td>
                <td style="color: var(--text-muted); font-family: 'Space Mono', monospace; font-size: 11px;">
                    {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                </td>
                <td>
                    <span class="status-badge {{ strtolower($user->status) }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>
                    <div class="action-group" style="justify-content: flex-end;">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn view" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn edit" title="Edit User">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button class="action-btn delete" title="Hapus User"
                            onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>Belum ada user terdaftar.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-footer">
        <span class="table-info">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari <strong style="color:var(--accent);">{{ $users->total() }}</strong> users
        </span>
        @if($users->hasPages())
        <div class="pagination">
            {{-- Prev --}}
            @if($users->onFirstPage())
                <span class="page-btn" style="opacity:0.3; cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>
            @else
                <a class="page-btn" href="{{ $users->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif

            {{-- Page Numbers --}}
            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a class="page-btn" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($users->hasMorePages())
                <a class="page-btn" href="{{ $users->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="page-btn" style="opacity:0.3; cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3>Hapus User?</h3>
        <p>Kamu yakin ingin menghapus <strong id="deleteUserName"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" style="width:100%;">
                    <i class="fas fa-trash-alt"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Delete Modal
    function openDeleteModal(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = '/admin/users/' + id;
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Live Search + Filter
    const searchInput = document.getElementById('searchInput');
    const filterRole  = document.getElementById('filterRole');
    const filterStatus= document.getElementById('filterStatus');
    const rows        = document.querySelectorAll('.user-row');
    const countEl     = document.getElementById('countVisible');

    function applyFilters() {
        const q      = searchInput.value.toLowerCase();
        const role   = filterRole.value.toLowerCase();
        const status = filterStatus.value.toLowerCase();
        let visible  = 0;

        rows.forEach(row => {
            const matchSearch = row.dataset.name.includes(q) || row.dataset.email.includes(q);
            const matchRole   = !role   || row.dataset.role   === role;
            const matchStatus = !status || row.dataset.status === status;

            if (matchSearch && matchRole && matchStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        countEl.textContent = visible;
    }

    searchInput.addEventListener('input', applyFilters);
    filterRole.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);

    // Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    });

    // Auto hide flash
    setTimeout(() => {
        const flash = document.querySelector('.flash-msg');
        if (flash) flash.style.display = 'none';
    }, 4000);
</script>
@endpush

@endsection
