@extends('layouts.app')

@section('page-title', 'Asset ID — Meta Panel')
@section('breadcrumb', 'Meta Panel / Asset ID')

@section('content')

<style>
    /* ===== LAYOUT ===== */
    .asset-wrap { display: flex; flex-direction: column; gap: 24px; }

    /* ===== HEADER CARD ===== */
    .asset-header-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .asset-header-left { display: flex; align-items: center; gap: 14px; }
    .asset-header-icon {
        width: 48px; height: 48px;
        background: rgba(24,119,242,0.12);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #1877F2;
    }
    .asset-header-title { font-size: 18px; font-weight: 700; color: var(--text-primary); }
    .asset-header-sub { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

    /* ===== SESSION TABS ===== */
    .session-tabs {
        display: flex; gap: 8px; flex-wrap: wrap;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 20px;
    }
    .session-tab {
        padding: 8px 18px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-main);
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .session-tab:hover { border-color: #1877F2; color: #1877F2; }
    .session-tab.active {
        background: rgba(24,119,242,0.12);
        border-color: #1877F2;
        color: #1877F2;
    }
    .session-tab .badge {
        background: rgba(24,119,242,0.2);
        color: #1877F2;
        border-radius: 20px;
        padding: 1px 8px;
        font-size: 11px;
    }
    .session-tab.active .sync-btn-inline {
        display: inline-flex;
    }
    .sync-btn-inline {
        display: none;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        background: #1877F2;
        color: white;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s;
    }
    .sync-btn-inline:hover { opacity: 0.85; }

    /* ===== ASSET GRID ===== */
    .asset-section { display: none; }
    .asset-section.active { display: block; }

    .asset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .asset-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: border-color 0.2s, transform 0.2s;
        position: relative;
    }
    .asset-card:hover { border-color: #1877F2; transform: translateY(-2px); }

    .asset-card-top { display: flex; align-items: center; gap: 12px; }
    .asset-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: rgba(24,119,242,0.12);
        border: 2px solid rgba(24,119,242,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #1877F2;
        font-weight: 700;
        overflow: hidden;
        flex-shrink: 0;
    }
    .asset-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .asset-info { flex: 1; min-width: 0; }
    .asset-name {
        font-size: 14px; font-weight: 700; color: var(--text-primary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .asset-category { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

    .asset-id-box {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .asset-id-label { font-size: 11px; color: var(--text-muted); font-weight: 600; }
    .asset-id-value {
        font-family: 'Space Mono', monospace;
        font-size: 13px;
        color: var(--accent);
        font-weight: 700;
        flex: 1;
        text-align: center;
    }
    .copy-btn {
        width: 28px; height: 28px;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    .copy-btn:hover { background: var(--accent); color: white; border-color: var(--accent); }

    .asset-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: var(--text-muted);
    }
    .delete-asset-btn {
        background: none;
        border: none;
        color: #EF4444;
        cursor: pointer;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s;
        display: flex; align-items: center; gap: 4px;
    }
    .delete-asset-btn:hover { background: rgba(239,68,68,0.1); }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        background: var(--bg-card);
        border: 1px dashed var(--border);
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
        color: var(--text-muted);
    }
    .empty-state i { font-size: 40px; opacity: 0.3; display: block; margin-bottom: 12px; }
    .empty-state p { font-size: 14px; margin-bottom: 16px; }

    /* ===== SYNC BUTTON MAIN ===== */
    .btn-sync {
        display: inline-flex; align-items: center; gap: 8px;
        background: #1877F2;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-sync:hover { opacity: 0.85; }
    .btn-sync:disabled { opacity: 0.5; cursor: not-allowed; }

    /* ===== TOAST ===== */
    .toast {
        position: fixed; bottom: 24px; right: 24px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 20px;
        display: flex; align-items: center; gap: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        z-index: 9999;
        font-size: 14px;
        transform: translateY(80px); opacity: 0;
        transition: all 0.3s ease;
        max-width: 360px;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.success { border-color: var(--accent); }
    .toast.error { border-color: #EF4444; }
    .toast-icon { font-size: 20px; }
    .toast.success .toast-icon { color: var(--accent); }
    .toast.error .toast-icon { color: #EF4444; }
    .toast-msg { color: var(--text-primary); }

    /* ===== SPINNER ===== */
    .spin { animation: spin 1s linear infinite; display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="asset-wrap">

    {{-- ===== HEADER ===== --}}
    <div class="asset-header-card">
        <div class="asset-header-left">
            <div class="asset-header-icon">
                <i class="fab fa-facebook"></i>
            </div>
            <div>
                <div class="asset-header-title">Asset ID — Facebook Pages</div>
                <div class="asset-header-sub">
                    Asset ID tersimpan di database. Tekan <strong>Sync</strong> pada sesi untuk memperbarui.
                </div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-size:13px; color:var(--text-muted);">
                Total:
                <strong style="color:var(--text-primary);">
                    {{ $assets->flatten()->count() }} asset
                </strong>
            </div>
        </div>
    </div>

    {{-- ===== SESSION TABS ===== --}}
    @if(count($sessions) > 0)
    <div class="session-tabs">
        <span style="font-size:13px; font-weight:600; color:var(--text-muted); align-self:center; margin-right:4px;">
            <i class="fas fa-layer-group" style="margin-right:4px;"></i> Sesi:
        </span>
        @foreach($sessions as $idx => $session)
        @php
            $sName = is_array($session) ? ($session['session_name'] ?? $session) : $session;
            $count = isset($assets[$sName]) ? $assets[$sName]->count() : 0;
        @endphp
        <div class="session-tab {{ $idx === 0 ? 'active' : '' }}"
             data-session="{{ $sName }}"
             onclick="switchSession(this, '{{ $sName }}')">
            <i class="fas fa-user-circle"></i>
            {{ $sName }}
            <span class="badge" id="badge-{{ $sName }}">{{ $count }}</span>
            <button class="sync-btn-inline" id="sync-btn-{{ $sName }}"
                    onclick="event.stopPropagation(); syncAssets('{{ $sName }}')">
                <i class="fas fa-sync-alt"></i> Sync
            </button>
        </div>
        @endforeach
    </div>

    {{-- ===== ASSET SECTIONS ===== --}}
    @foreach($sessions as $idx => $session)
    @php
        $sName = is_array($session) ? ($session['session_name'] ?? $session) : $session;
        $sessionAssets = $assets[$sName] ?? collect();
    @endphp
    <div class="asset-section {{ $idx === 0 ? 'active' : '' }}" id="section-{{ $sName }}">

        {{-- Empty state --}}
        @if($sessionAssets->isEmpty())
        <div class="empty-state">
            <i class="fab fa-facebook"></i>
            <p>Belum ada asset tersimpan untuk sesi <strong>{{ $sName }}</strong>.</p>
            <button class="btn-sync" onclick="syncAssets('{{ $sName }}')">
                <i class="fas fa-sync-alt"></i> Sync Sekarang
            </button>
        </div>
        @else
        {{-- Asset grid --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
            <div style="font-size:14px; color:var(--text-muted);">
                <i class="fas fa-check-circle" style="color:var(--accent); margin-right:6px;"></i>
                <strong style="color:var(--text-primary);">{{ $sessionAssets->count() }}</strong> page tersimpan
            </div>
            <button class="btn-sync" onclick="syncAssets('{{ $sName }}')">
                <i class="fas fa-sync-alt"></i> Sync Ulang
            </button>
        </div>

        <div class="asset-grid" id="grid-{{ $sName }}">
            @foreach($sessionAssets as $asset)
            <div class="asset-card" id="asset-card-{{ $asset->id }}">
                <div class="asset-card-top">
                    <div class="asset-avatar">
                        @if($asset->picture)
                            <img src="{{ $asset->picture }}" alt="{{ $asset->page_name }}"
                                 onerror="this.parentElement.innerHTML='<i class=\'fab fa-facebook\'></i>'">
                        @else
                            {{ strtoupper(substr($asset->page_name ?? 'P', 0, 1)) }}
                        @endif
                    </div>
                    <div class="asset-info">
                        <div class="asset-name" title="{{ $asset->page_name }}">
                            {{ $asset->page_name ?? 'Unknown Page' }}
                        </div>
                        <div class="asset-category">
                            {{ $asset->category ?? 'Facebook Page' }}
                        </div>
                    </div>
                </div>

                <div class="asset-id-box">
                    <span class="asset-id-label">Asset ID</span>
                    <span class="asset-id-value" id="aid-{{ $asset->id }}">{{ $asset->asset_id }}</span>
                    <button class="copy-btn" onclick="copyAssetId('{{ $asset->asset_id }}', this)" title="Copy Asset ID">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <div class="asset-card-footer">
                    <span><i class="fas fa-clock" style="margin-right:4px;"></i>
                        {{ $asset->updated_at->diffForHumans() }}
                    </span>
                    <button class="delete-asset-btn" onclick="deleteAsset({{ $asset->id }}, this)">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach

    @else
    {{-- Tidak ada sesi --}}
    <div class="empty-state">
        <i class="fas fa-exclamation-triangle"></i>
        <p>Tidak ada sesi aktif ditemukan. Silakan login ke sesi Facebook terlebih dahulu.</p>
    </div>
    @endif

</div>

{{-- ===== TOAST ===== --}}
<div class="toast" id="toast">
    <span class="toast-icon" id="toast-icon"></span>
    <span class="toast-msg" id="toast-msg"></span>
</div>

@push('scripts')
<script>
    // ─── Switch Session Tab ───────────────────────────────────────
    function switchSession(el, sessionName) {
        document.querySelectorAll('.session-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.asset-section').forEach(s => s.classList.remove('active'));

        el.classList.add('active');
        const section = document.getElementById('section-' + sessionName);
        if (section) section.classList.add('active');
    }

    // ─── Sync Assets ──────────────────────────────────────────────
    function syncAssets(sessionName) {
        const btn  = document.getElementById('sync-btn-' + sessionName);
        const originalHtml = btn ? btn.innerHTML : '';

        if (btn) {
            btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Sync...';
            btn.disabled = true;
        }

        // Juga disable tombol di empty state / sync ulang
        document.querySelectorAll('.btn-sync').forEach(b => {
            if (b.onclick && b.onclick.toString().includes(sessionName)) {
                b.disabled = true;
                b.innerHTML = '<i class="fas fa-circle-notch spin"></i> Menyinkronkan...';
            }
        });

        fetch('{{ route("client.meta.assets.sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ session_name: sessionName }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('success', '✅ ' + data.message);
                // Reload halaman setelah 1.5 detik agar grid terupdate
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', '❌ ' + (data.message || 'Sync gagal.'));
                resetSyncBtn(btn, originalHtml);
            }
        })
        .catch(err => {
            showToast('error', '❌ Gagal terhubung ke server.');
            console.error(err);
            resetSyncBtn(btn, originalHtml);
        });
    }

    function resetSyncBtn(btn, html) {
        if (btn) {
            btn.innerHTML = html;
            btn.disabled = false;
        }
        document.querySelectorAll('.btn-sync').forEach(b => {
            b.disabled = false;
            b.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Sekarang';
        });
    }

    // ─── Copy Asset ID ────────────────────────────────────────────
    function copyAssetId(id, btn) {
        navigator.clipboard.writeText(id).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check';
            btn.style.background = 'var(--accent)';
            btn.style.color = 'white';
            showToast('success', '📋 Asset ID ' + id + ' disalin!');
            setTimeout(() => {
                icon.className = 'fas fa-copy';
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    }

    // ─── Delete Asset ─────────────────────────────────────────────
    function deleteAsset(id, btn) {
        if (!confirm('Hapus asset ini dari database?')) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch spin"></i>';

        fetch(`{{ url('client-area/meta/assets') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById('asset-card-' + id);
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                card.style.transition = 'all 0.3s';
                setTimeout(() => card.remove(), 300);
                showToast('success', '🗑️ Asset berhasil dihapus.');
            } else {
                showToast('error', '❌ ' + (data.message || 'Gagal menghapus.'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus';
            }
        })
        .catch(() => {
            showToast('error', '❌ Gagal terhubung.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash-alt"></i> Hapus';
        });
    }

    // ─── Toast Notification ───────────────────────────────────────
    let toastTimer;
    function showToast(type, msg) {
        const toast = document.getElementById('toast');
        const icon  = document.getElementById('toast-icon');
        const msgEl = document.getElementById('toast-msg');

        toast.className = 'toast ' + type;
        icon.textContent = type === 'success' ? '✅' : '❌';
        msgEl.textContent = msg;

        toast.classList.add('show');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
    }
</script>
@endpush

@endsection
