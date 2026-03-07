@extends('layouts.app')

@section('page-title', 'File Manager')
@section('breadcrumb', 'File Manager')

@section('content')
<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes prog{from{width:0}to{width:var(--w)}}
@keyframes pulse2{0%,100%{opacity:1}50%{opacity:.5}}

.spin{animation:spin 1s linear infinite;display:inline-block}
.fade-up{animation:fadeUp .3s ease}

/* ── Layout ── */
.fm-wrap{display:grid;grid-template-columns:220px 1fr;gap:0;background:var(--bg-card);border:1px solid var(--border);border-radius:16px;overflow:hidden;min-height:calc(100vh - 160px)}

/* ── Sidebar ── */
.fm-sidebar{border-right:1px solid var(--border);display:flex;flex-direction:column}
.fm-sidebar-head{padding:18px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.fm-sidebar-title{font-size:12px;font-weight:700;color:var(--text-muted);letter-spacing:1.5px;text-transform:uppercase}
.btn-new-folder{width:28px;height:28px;border-radius:6px;background:rgba(255,255,255,.04);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:12px;transition:all .2s}
.btn-new-folder:hover{background:rgba(255,255,255,.08);color:var(--text-primary);border-color:rgba(255,255,255,.15)}

.folder-list{padding:10px 8px;flex:1;overflow-y:auto}
.folder-item{display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:8px;cursor:pointer;transition:all .2s;margin-bottom:2px;border:1px solid transparent}
.folder-item:hover{background:rgba(255,255,255,.04);border-color:var(--border)}
.folder-item.active{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.12)}
.folder-item.active .folder-icon{color:var(--accent)}
.folder-item.active .folder-name{color:var(--text-primary);font-weight:600}
.folder-icon{font-size:14px;color:var(--text-muted);width:16px;text-align:center;flex-shrink:0}
.folder-name{font-size:13px;color:var(--text-secondary);flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.folder-count{font-size:10px;font-weight:700;background:rgba(255,255,255,.06);color:var(--text-muted);padding:1px 7px;border-radius:20px;flex-shrink:0}

.fm-sidebar-footer{padding:14px 16px;border-top:1px solid var(--border)}
.storage-bar-wrap{margin-bottom:8px}
.storage-bar-label{display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:6px}
.storage-bar{height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden}
.storage-bar-fill{height:100%;background:linear-gradient(90deg,var(--accent),#00aaff);border-radius:2px;transition:width .5s ease}

/* ── Main area ── */
.fm-main{display:flex;flex-direction:column}
.fm-toolbar{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.fm-path{font-size:13px;color:var(--text-muted);display:flex;align-items:center;gap:6px;flex:1}
.fm-path-root{color:var(--text-muted);font-size:12px}
.fm-path-sep{opacity:.4}
.fm-path-cur{color:var(--text-primary);font-weight:600}
.toolbar-right{display:flex;align-items:center;gap:8px}

.btn-upload{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--accent);color:#000;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .2s}
.btn-upload:hover{opacity:.85}
.btn-view{width:32px;height:32px;border:1px solid var(--border);border-radius:7px;background:var(--bg-main);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:13px;transition:all .2s}
.btn-view:hover,.btn-view.active{border-color:rgba(255,255,255,.2);color:var(--text-primary);background:rgba(255,255,255,.06)}

/* ── Search ── */
.fm-search{position:relative;flex:1;max-width:260px}
.fm-search input{width:100%;padding:7px 12px 7px 32px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;box-sizing:border-box}
.fm-search input:focus{outline:none;border-color:rgba(255,255,255,.2)}
.fm-search i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:12px}

/* ── File info bar ── */
.fm-infobar{padding:9px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;font-size:12px;color:var(--text-muted)}
.info-sep{width:1px;height:14px;background:var(--border)}

/* ── Grid view ── */
.fm-files-grid{padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;overflow-y:auto;flex:1}
.file-card{background:var(--bg-main);border:1px solid var(--border);border-radius:10px;padding:14px;cursor:pointer;transition:all .2s;position:relative;display:flex;flex-direction:column;gap:8px;animation:fadeUp .25s ease}
.file-card:hover{border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.03);transform:translateY(-1px)}
.file-card.selected{border-color:var(--accent);background:rgba(255,255,255,.04)}
.file-thumb{width:100%;aspect-ratio:1;border-radius:7px;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;font-size:36px;position:relative;overflow:hidden}
.file-thumb img{width:100%;height:100%;object-fit:cover;border-radius:7px}
.file-type-badge{position:absolute;bottom:6px;right:6px;background:rgba(0,0,0,.7);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;text-transform:uppercase;letter-spacing:.5px}
.file-name{font-size:12px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.file-size{font-size:11px;color:var(--text-muted)}
.file-actions{display:flex;gap:5px;opacity:0;transition:opacity .2s}
.file-card:hover .file-actions{opacity:1}
.file-action-btn{width:26px;height:26px;border-radius:5px;border:1px solid var(--border);background:var(--bg-main);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:11px;color:var(--text-muted);transition:all .2s}
.file-action-btn:hover{border-color:rgba(255,255,255,.2);color:var(--text-primary)}
.file-action-btn.del:hover{border-color:#EF4444;color:#EF4444;background:rgba(239,68,68,.08)}

/* ── List view ── */
.fm-files-list{flex:1;overflow-y:auto}
.file-row{display:grid;grid-template-columns:32px 1fr 90px 100px 100px 80px;align-items:center;gap:12px;padding:11px 20px;border-bottom:1px solid rgba(255,255,255,.04);transition:background .15s;animation:fadeUp .2s ease}
.file-row:hover{background:rgba(255,255,255,.025)}
.file-row-head{display:grid;grid-template-columns:32px 1fr 90px 100px 100px 80px;gap:12px;padding:9px 20px;border-bottom:1px solid var(--border);font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;position:sticky;top:0;background:var(--bg-card);z-index:2}
.file-icon-sm{width:32px;height:32px;border-radius:6px;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.file-list-name{font-size:13px;font-weight:600;color:var(--text-primary);min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.file-list-meta{font-size:12px;color:var(--text-muted)}
.file-row-actions{display:flex;gap:5px;opacity:0;transition:opacity .2s}
.file-row:hover .file-row-actions{opacity:1}

/* ── Empty ── */
.fm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;flex:1;color:var(--text-muted)}
.fm-empty i{font-size:48px;opacity:.2;margin-bottom:16px}
.fm-empty p{font-size:14px;margin:0 0 20px}

/* ── Upload dropzone modal ── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.modal-box{background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:28px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.dropzone{border:2px dashed var(--border);border-radius:12px;padding:40px 20px;text-align:center;cursor:pointer;transition:all .2s;position:relative}
.dropzone.dragover{border-color:var(--accent);background:rgba(255,255,255,.03)}
.dropzone i{font-size:40px;color:var(--text-muted);opacity:.4;margin-bottom:16px}
.dropzone p{font-size:14px;color:var(--text-muted);margin:0}
.dropzone input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}

.upload-list{margin-top:16px;display:flex;flex-direction:column;gap:8px;max-height:220px;overflow-y:auto}
.upload-item{display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg-main);border:1px solid var(--border);border-radius:8px}
.upload-item-icon{font-size:20px;flex-shrink:0}
.upload-item-info{flex:1;min-width:0}
.upload-item-name{font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.upload-item-size{font-size:11px;color:var(--text-muted)}
.upload-item-prog{height:3px;background:rgba(255,255,255,.06);border-radius:2px;margin-top:6px;overflow:hidden}
.upload-item-prog-fill{height:100%;background:var(--accent);border-radius:2px;width:0;transition:width .3s}
.upload-item-status{font-size:11px;flex-shrink:0}

/* ── Rename modal ── */
.rename-modal{background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:24px;width:100%;max-width:400px}
.modal-input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;box-sizing:border-box;margin-bottom:14px}
.modal-input:focus{outline:none;border-color:rgba(255,255,255,.2)}

/* ── Toast ── */
.toast{position:fixed;bottom:24px;right:24px;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:12px 18px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);z-index:99999;font-size:13px;max-width:360px;transform:translateY(80px);opacity:0;transition:all .3s ease}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{border-left:3px solid var(--accent)}
.toast.error{border-left:3px solid #EF4444}

/* File type colors */
.type-video .file-thumb{background:rgba(168,85,247,.08)} .type-video .file-icon-sm{background:rgba(168,85,247,.08)}
.type-image .file-thumb{background:rgba(24,119,242,.08)} .type-image .file-icon-sm{background:rgba(24,119,242,.08)}
.type-pdf   .file-thumb{background:rgba(239,68,68,.08)}   .type-pdf   .file-icon-sm{background:rgba(239,68,68,.08)}
.type-archive .file-thumb{background:rgba(245,158,11,.08)} .type-archive .file-icon-sm{background:rgba(245,158,11,.08)}

@media(max-width:768px){.fm-wrap{grid-template-columns:1fr}.fm-sidebar{display:none}.file-row,.file-row-head{grid-template-columns:32px 1fr 80px}}
</style>

<div class="fm-wrap">

    {{-- ===== SIDEBAR ===== --}}
    <div class="fm-sidebar">
        <div class="fm-sidebar-head">
            <span class="fm-sidebar-title">Folders</span>
            <button class="btn-new-folder" onclick="openNewFolderModal()" title="Buat Folder Baru"><i class="fas fa-plus"></i></button>
        </div>

        <div class="folder-list" id="folder-list">
            @foreach($userFolders as $f)
            @php
                $icon = match($f) { 'meta'=>'fab fa-facebook', 'x'=>'fab fa-x-twitter', 'tiktok'=>'fab fa-tiktok', 'telegram'=>'fab fa-telegram', default=>'fas fa-folder' };
                $cnt  = $folderStats[$f]['count'] ?? 0;
            @endphp
            <div class="folder-item {{ $folder === $f ? 'active' : '' }}" id="folder-{{ $f }}" onclick="switchFolder('{{ $f }}')">
                <i class="{{ $icon }} folder-icon"></i>
                <span class="folder-name">{{ $f }}</span>
                <span class="folder-count">{{ $cnt }}</span>
            </div>
            @endforeach
        </div>

        <div class="fm-sidebar-footer">
            <div class="storage-bar-wrap">
                @php
                    $totalSize = collect($folderStats)->sum('size');
                    $maxSize   = 500 * 1024 * 1024; // 500 MB display limit
                    $pct       = min(100, round($totalSize / $maxSize * 100, 1));
                    $humanTotal = $totalSize < 1048576 ? round($totalSize/1024,1).' KB' : round($totalSize/1048576,1).' MB';
                @endphp
                <div class="storage-bar-label">
                    <span>Storage</span>
                    <span>{{ $humanTotal }}</span>
                </div>
                <div class="storage-bar"><div class="storage-bar-fill" style="width:{{ $pct }}%"></div></div>
            </div>
            <div style="font-size:11px;color:var(--text-muted);display:flex;justify-content:space-between">
                <span>{{ $pct }}% terpakai</span>
                <span>500 MB limit</span>
            </div>
        </div>
    </div>

    {{-- ===== MAIN ===== --}}
    <div class="fm-main">

        {{-- Toolbar --}}
        <div class="fm-toolbar">
            <div class="fm-path">
                <span class="fm-path-root"><i class="fas fa-hdd"></i></span>
                <span class="fm-path-sep">/</span>
                <span class="fm-path-cur" id="current-folder-label">{{ $folder }}</span>
            </div>

            <div class="fm-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari file..." id="search-input" oninput="searchFiles(this.value)">
            </div>

            <div class="toolbar-right">
                <button class="btn-view active" id="btn-grid" onclick="setView('grid')" title="Grid View"><i class="fas fa-th"></i></button>
                <button class="btn-view" id="btn-list" onclick="setView('list')" title="List View"><i class="fas fa-list"></i></button>
                <button class="btn-upload" onclick="openUploadModal()">
                    <i class="fas fa-cloud-upload-alt"></i> Upload
                </button>
            </div>
        </div>

        {{-- Info bar --}}
        <div class="fm-infobar">
            <span id="info-count"><i class="fas fa-file" style="margin-right:4px"></i> {{ count($files) }} file</span>
            <div class="info-sep"></div>
            <span id="info-size"><i class="fas fa-weight-hanging" style="margin-right:4px"></i>
                @php $sz = $files->sum('size'); echo $sz < 1048576 ? round($sz/1024,1).' KB' : round($sz/1048576,1).' MB'; @endphp
            </span>
            <div class="info-sep"></div>
            <span style="color:var(--accent)"><i class="fas fa-folder-open" style="margin-right:4px"></i> /{{ $folder }}</span>
        </div>

        {{-- Files Grid --}}
        <div class="fm-files-grid" id="fm-grid" style="display:grid">
            @forelse($files as $f)
            @include('client.files._file_card', ['f' => $f])
            @empty
            <div id="grid-empty" style="grid-column:1/-1;display:flex;flex-direction:column;align-items:center;padding:60px 20px;color:var(--text-muted)">
                <i class="fas fa-folder-open" style="font-size:48px;opacity:.2;margin-bottom:16px"></i>
                <p style="font-size:14px;margin:0 0 20px">Folder ini masih kosong.</p>
                <button class="btn-upload" onclick="openUploadModal()"><i class="fas fa-cloud-upload-alt"></i> Upload File</button>
            </div>
            @endforelse
        </div>

        {{-- Files List (hidden by default) --}}
        <div class="fm-files-list" id="fm-list" style="display:none">
            <div class="file-row-head">
                <div></div>
                <div>Nama File</div>
                <div>Ukuran</div>
                <div>Tipe</div>
                <div>Tanggal</div>
                <div></div>
            </div>
            <div id="fm-list-body">
                @foreach($files as $f)
                @include('client.files._file_row', ['f' => $f])
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ===== MODAL UPLOAD ===== --}}
<div class="modal-overlay" id="upload-modal" style="display:none" onclick="if(event.target===this)closeUploadModal()">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <div>
                <div style="font-size:17px;font-weight:700;color:var(--text-primary)"><i class="fas fa-cloud-upload-alt" style="color:var(--accent);margin-right:8px"></i>Upload File</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">Folder: <span id="upload-folder-label" style="color:var(--accent);font-weight:600">/meta</span></div>
            </div>
            <button onclick="closeUploadModal()" style="background:none;border:none;color:var(--text-muted);font-size:18px;cursor:pointer"><i class="fas fa-times"></i></button>
        </div>

        <div class="dropzone" id="dropzone"
             ondrop="handleDrop(event)" ondragover="event.preventDefault();this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')"
             onclick="el('file-input').click()">
            <input type="file" id="file-input" multiple onchange="handleFileSelect(this.files)" style="display:none">
            <i class="fas fa-cloud-upload-alt" style="display:block;margin-bottom:12px"></i>
            <p style="font-weight:600;color:var(--text-primary);margin-bottom:4px">Drag & drop file di sini</p>
            <p style="font-size:12px">atau klik untuk pilih file · Max 500 MB/file</p>
        </div>

        <div class="upload-list" id="upload-list"></div>

        <div style="display:flex;gap:10px;margin-top:20px">
            <button onclick="closeUploadModal()" style="flex:1;padding:11px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;font-weight:600;cursor:pointer">Batal</button>
            <button id="btn-do-upload" onclick="doUpload()" style="flex:2;padding:11px;border-radius:8px;border:none;background:var(--accent);color:#000;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px" disabled>
                <i class="fas fa-cloud-upload-alt"></i> Upload Sekarang
            </button>
        </div>
    </div>
</div>

{{-- ===== MODAL RENAME ===== --}}
<div class="modal-overlay" id="rename-modal" style="display:none" onclick="if(event.target===this)closeRenameModal()">
    <div class="rename-modal">
        <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px"><i class="fas fa-pen" style="color:var(--accent);margin-right:8px"></i>Rename File</div>
        <input type="hidden" id="rename-file-id">
        <input type="text" class="modal-input" id="rename-input" placeholder="Nama baru...">
        <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:6px">Deskripsi (opsional)</label>
        <textarea class="modal-input" id="rename-desc" rows="2" placeholder="Catatan untuk file ini..." style="resize:none"></textarea>
        <div style="display:flex;gap:10px">
            <button onclick="closeRenameModal()" style="flex:1;padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;font-weight:600;cursor:pointer">Batal</button>
            <button onclick="doRename()" style="flex:2;padding:10px;border-radius:8px;border:none;background:var(--accent);color:#000;font-size:13px;font-weight:700;cursor:pointer">Simpan</button>
        </div>
    </div>
</div>

{{-- ===== MODAL NEW FOLDER ===== --}}
<div class="modal-overlay" id="new-folder-modal" style="display:none" onclick="if(event.target===this)closeNewFolderModal()">
    <div class="rename-modal" style="max-width:360px">
        <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px"><i class="fas fa-folder-plus" style="color:#F59E0B;margin-right:8px"></i>Folder Baru</div>
        <input type="text" class="modal-input" id="new-folder-input" placeholder="Nama folder (contoh: instagram)">
        <small style="display:block;font-size:11px;color:var(--text-muted);margin-top:-8px;margin-bottom:14px">Hanya huruf, angka, - dan _</small>
        <div style="display:flex;gap:10px">
            <button onclick="closeNewFolderModal()" style="flex:1;padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;font-weight:600;cursor:pointer">Batal</button>
            <button onclick="doCreateFolder()" style="flex:2;padding:10px;border-radius:8px;border:none;background:#F59E0B;color:#000;font-size:13px;font-weight:700;cursor:pointer"><i class="fas fa-folder-plus" style="margin-right:6px"></i>Buat Folder</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"><span id="toast-msg" style="color:var(--text-primary)"></span></div>

<script>
const el  = id => document.getElementById(id);
const qs  = s  => document.querySelector(s);
const qsa = s  => document.querySelectorAll(s);

let currentFolder = '{{ $folder }}';
let currentView   = 'grid';
let pendingFiles  = [];
let allFilesData  = @json($files->map(fn($f) => [
    'id'            => $f->id,
    'original_name' => $f->original_name,
    'extension'     => $f->extension,
    'type'          => $f->type,
    'size'          => $f->size,
    'human_size'    => $f->human_size,
    'description'   => $f->description,
    'created_at'    => $f->created_at->format('d M Y, H:i'),
    'download_url'  => $f->download_url,
]));

// ── Folder switching ───────────────────────────────────────────
function switchFolder(folder) {
    if (folder === currentFolder) return;
    currentFolder = folder;

    // Update sidebar active
    qsa('.folder-item').forEach(i => i.classList.remove('active'));
    el('folder-' + folder)?.classList.add('active');
    el('current-folder-label').textContent = folder;
    el('upload-folder-label').textContent  = '/' + folder;
    el('search-input').value = '';

    // Show loading
    el('fm-grid').innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted)"><i class="fas fa-circle-notch spin" style="font-size:28px;color:var(--accent);display:block;margin-bottom:12px"></i>Memuat file...</div>`;

    fetch(`{{ route('client.files.list') }}?folder=${encodeURIComponent(folder)}`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        allFilesData = d.files;
        renderFiles(d.files);
        el('info-count').innerHTML = `<i class="fas fa-file" style="margin-right:4px"></i> ${d.total} file`;
        el('info-size').innerHTML  = `<i class="fas fa-weight-hanging" style="margin-right:4px"></i> ${d.total_size}`;
    })
    .catch(() => showToast('error', '❌ Gagal memuat file.'));
}

// ── Render ─────────────────────────────────────────────────────
function renderFiles(files) {
    const typeIcon = {
        video: '🎬', image: '🖼️', pdf: '📄', archive: '🗜️', text: '📝', file: '📁'
    };
    const typeCss = { video:'#a855f7', image:'#1877F2', pdf:'#EF4444', archive:'#F59E0B', text:'#10B981', file:'var(--text-muted)' };

    if (currentView === 'grid') {
        if (!files.length) {
            el('fm-grid').innerHTML = `<div style="grid-column:1/-1;display:flex;flex-direction:column;align-items:center;padding:60px 20px;color:var(--text-muted)">
                <i class="fas fa-folder-open" style="font-size:48px;opacity:.2;margin-bottom:16px"></i>
                <p style="font-size:14px;margin:0 0 20px">Folder ini masih kosong.</p>
                <button class="btn-upload" onclick="openUploadModal()"><i class="fas fa-cloud-upload-alt"></i> Upload File</button>
            </div>`;
            return;
        }
        el('fm-grid').innerHTML = files.map(f => `
            <div class="file-card type-${f.type}" onclick="selectFile(this)">
                <div class="file-thumb">
                    <span>${typeIcon[f.type]||'📁'}</span>
                    <span class="file-type-badge" style="background:${typeCss[f.type]||'#555'}">${f.extension||f.type}</span>
                </div>
                <div class="file-name" title="${esc(f.original_name)}">${esc(f.original_name)}</div>
                <div class="file-size">${f.human_size}</div>
                <div class="file-actions">
                    <a href="${f.download_url}" class="file-action-btn" title="Download" onclick="event.stopPropagation()"><i class="fas fa-download"></i></a>
                    <button class="file-action-btn" title="Rename" onclick="event.stopPropagation();openRename(${f.id},'${esc(f.original_name)}','${esc(f.description||'')}')"><i class="fas fa-pen"></i></button>
                    <button class="file-action-btn del" title="Hapus" onclick="event.stopPropagation();deleteFile(${f.id},this)"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>`).join('');
    } else {
        el('fm-list-body').innerHTML = files.map(f => `
            <div class="file-row">
                <div class="file-icon-sm type-${f.type}" style="font-size:18px;text-align:center">${typeIcon[f.type]||'📁'}</div>
                <div>
                    <div class="file-list-name" title="${esc(f.original_name)}">${esc(f.original_name)}</div>
                    ${f.description?`<div class="file-list-meta" style="margin-top:2px">${esc(f.description)}</div>`:''}
                </div>
                <div class="file-list-meta">${f.human_size}</div>
                <div><span style="background:rgba(255,255,255,.05);color:${typeCss[f.type]||'var(--text-muted)'};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">${f.extension||f.type}</span></div>
                <div class="file-list-meta">${f.created_at}</div>
                <div class="file-row-actions">
                    <a href="${f.download_url}" class="file-action-btn" title="Download"><i class="fas fa-download"></i></a>
                    <button class="file-action-btn" title="Rename" onclick="openRename(${f.id},'${esc(f.original_name)}','${esc(f.description||'')}')"><i class="fas fa-pen"></i></button>
                    <button class="file-action-btn del" title="Hapus" onclick="deleteFile(${f.id},this)"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>`).join('');
    }
}

function selectFile(card) { qsa('.file-card').forEach(c=>c.classList.remove('selected')); card.classList.add('selected'); }

// ── View toggle ────────────────────────────────────────────────
function setView(v) {
    currentView = v;
    el('fm-grid').style.display = v==='grid'?'grid':'none';
    el('fm-list').style.display = v==='list'?'flex':'none';
    el('btn-grid').classList.toggle('active', v==='grid');
    el('btn-list').classList.toggle('active', v==='list');
    renderFiles(allFilesData);
}

// ── Search ─────────────────────────────────────────────────────
function searchFiles(q) {
    const filtered = q.trim() ? allFilesData.filter(f => f.original_name.toLowerCase().includes(q.toLowerCase())) : allFilesData;
    renderFiles(filtered);
    el('info-count').innerHTML = `<i class="fas fa-file" style="margin-right:4px"></i> ${filtered.length} file${q?' (filtered)':''}`;
}

// ── Upload ─────────────────────────────────────────────────────
function openUploadModal() { el('upload-modal').style.display='flex'; el('upload-folder-label').textContent='/'+currentFolder; pendingFiles=[]; el('upload-list').innerHTML=''; el('btn-do-upload').disabled=true; }
function closeUploadModal() { el('upload-modal').style.display='none'; pendingFiles=[]; }

function handleFileSelect(files) { addPendingFiles(Array.from(files)); }
function handleDrop(e) { e.preventDefault(); el('dropzone').classList.remove('dragover'); addPendingFiles(Array.from(e.dataTransfer.files)); }

function addPendingFiles(files) {
    pendingFiles = [...pendingFiles, ...files];
    renderUploadList();
    el('btn-do-upload').disabled = pendingFiles.length === 0;
}
function renderUploadList() {
    const icons = { video:'🎬', image:'🖼️', pdf:'📄' };
    el('upload-list').innerHTML = pendingFiles.map((f,i) => {
        const type = f.type.startsWith('video')?'video':f.type.startsWith('image')?'image':f.type.includes('pdf')?'pdf':'file';
        const size = f.size < 1048576 ? (f.size/1024).toFixed(1)+' KB' : (f.size/1048576).toFixed(1)+' MB';
        return `<div class="upload-item" id="uitem-${i}">
            <span class="upload-item-icon">${icons[type]||'📁'}</span>
            <div class="upload-item-info">
                <div class="upload-item-name">${esc(f.name)}</div>
                <div class="upload-item-size">${size}</div>
                <div class="upload-item-prog"><div class="upload-item-prog-fill" id="uprog-${i}"></div></div>
            </div>
            <span class="upload-item-status" id="ustatus-${i}" style="color:var(--text-muted)"><i class="fas fa-clock"></i></span>
        </div>`;
    }).join('');
}

async function doUpload() {
    if (!pendingFiles.length) return;
    const btn = el('btn-do-upload');
    btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Mengupload...'; btn.disabled = true;

    const formData = new FormData();
    formData.append('folder', currentFolder);
    formData.append('_token', '{{ csrf_token() }}');
    pendingFiles.forEach((f,i) => formData.append('files[]', f));

    // Fake progress per file (XHR untuk progress real)
    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', e => {
        if (e.lengthComputable) {
            const pct = Math.round(e.loaded / e.total * 100);
            pendingFiles.forEach((_,i) => { const p = el('uprog-'+i); if(p) p.style.width = pct + '%'; });
        }
    });

    xhr.onload = function() {
        const data = JSON.parse(xhr.responseText);
        if (data.success) {
            pendingFiles.forEach((_,i) => {
                const s = el('ustatus-'+i); if(s){ s.innerHTML='<i class="fas fa-check-circle" style="color:var(--accent)"></i>'; }
            });
            showToast('success', '✅ ' + data.message);
            setTimeout(() => { closeUploadModal(); switchFolder(currentFolder); }, 1000);
        } else {
            showToast('error', '❌ Upload gagal: ' + (data.message||'Error'));
            btn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Coba Lagi'; btn.disabled = false;
        }
    };
    xhr.onerror = () => { showToast('error', '❌ Koneksi gagal.'); btn.innerHTML='<i class="fas fa-cloud-upload-alt"></i> Upload Sekarang'; btn.disabled=false; };

    xhr.open('POST', '{{ route("client.files.upload") }}');
    xhr.send(formData);
}

// ── Delete ─────────────────────────────────────────────────────
function deleteFile(id, btn) {
    if (!confirm('Hapus file ini dari server?')) return;
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-circle-notch spin"></i>'; btn.disabled = true;
    fetch(`{{ url('client-area/files/delete') }}/${id}`, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} })
    .then(r=>r.json())
    .then(d => {
        if (d.success) {
            showToast('success', '🗑️ File dihapus.');
            allFilesData = allFilesData.filter(f=>f.id!==id);
            renderFiles(allFilesData);
            el('info-count').innerHTML = `<i class="fas fa-file" style="margin-right:4px"></i> ${allFilesData.length} file`;
        } else { showToast('error', '❌ Gagal.'); btn.innerHTML=origHtml; btn.disabled=false; }
    })
    .catch(()=>{ showToast('error','❌ Error.'); btn.innerHTML=origHtml; btn.disabled=false; });
}

// ── Rename ─────────────────────────────────────────────────────
function openRename(id, name, desc) { el('rename-file-id').value=id; el('rename-input').value=name; el('rename-desc').value=desc||''; el('rename-modal').style.display='flex'; setTimeout(()=>el('rename-input').focus(),100); }
function closeRenameModal() { el('rename-modal').style.display='none'; }
function doRename() {
    const id   = el('rename-file-id').value;
    const name = el('rename-input').value.trim();
    const desc = el('rename-desc').value.trim();
    if (!name) return;
    fetch(`{{ url('client-area/files/update') }}/${id}`, {
        method:'PATCH', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ original_name: name, description: desc })
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            showToast('success','✅ File diperbarui.');
            closeRenameModal();
            const idx = allFilesData.findIndex(f=>f.id==id);
            if(idx>-1){ allFilesData[idx].original_name=name; allFilesData[idx].description=desc; }
            renderFiles(allFilesData);
        } else showToast('error','❌ Gagal.');
    })
    .catch(()=>showToast('error','❌ Error.'));
}

// ── New Folder ─────────────────────────────────────────────────
function openNewFolderModal() { el('new-folder-input').value=''; el('new-folder-modal').style.display='flex'; setTimeout(()=>el('new-folder-input').focus(),100); }
function closeNewFolderModal() { el('new-folder-modal').style.display='none'; }
function doCreateFolder() {
    const name = el('new-folder-input').value.trim().toLowerCase();
    if (!name) return;
    fetch('{{ route("client.files.folder.create") }}', {
        method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ name })
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            showToast('success','📁 '+d.message);
            closeNewFolderModal();
            // Tambah ke sidebar tanpa reload
            const list = el('folder-list');
            const div = document.createElement('div');
            div.className = 'folder-item'; div.id = 'folder-'+name; div.onclick = ()=>switchFolder(name);
            div.innerHTML = `<i class="fas fa-folder folder-icon"></i><span class="folder-name">${name}</span><span class="folder-count">0</span>`;
            list.insertBefore(div, list.lastElementChild);
        } else showToast('error','❌ '+d.message);
    })
    .catch(()=>showToast('error','❌ Error.'));
}

// ── Keyboard shortcuts ─────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key==='Escape') { closeUploadModal(); closeRenameModal(); closeNewFolderModal(); }
    if ((e.ctrlKey||e.metaKey) && e.key==='u') { e.preventDefault(); openUploadModal(); }
});

// ── Helpers ────────────────────────────────────────────────────
function esc(str) { const d=document.createElement('div'); d.appendChild(document.createTextNode(String(str||''))); return d.innerHTML; }
let toastT;
function showToast(type, msg) { const t=el('toast'); t.className='toast '+type+' show'; el('toast-msg').textContent=msg; clearTimeout(toastT); toastT=setTimeout(()=>t.classList.remove('show'),3500); }
</script>

@endsection
