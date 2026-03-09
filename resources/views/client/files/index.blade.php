@extends('layouts.app')
@section('page-title', 'File Manager')
@section('breadcrumb', 'File Manager')
@section('content')
<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
.spin{animation:spin .8s linear infinite;display:inline-block}

.fm-wrap{display:grid;grid-template-columns:210px 1fr;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden;height:calc(100vh - 130px)}

/* Sidebar */
.fm-sb{border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
.fm-sb-head{padding:10px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.fm-sb-title{font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1.5px;text-transform:uppercase}
.btn-nf{width:24px;height:24px;border-radius:5px;background:rgba(255,255,255,.04);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-secondary);font-size:11px;transition:all .15s}
.btn-nf:hover{background:rgba(255,255,255,.08);color:var(--accent);border-color:rgba(255,255,255,.15)}

/* Tree */
.folder-tree{padding:6px;flex:1;overflow-y:auto}
.tree-node{position:relative}
.tree-item{display:flex;align-items:center;gap:0;border-radius:5px;cursor:pointer;transition:all .12s;border:1px solid transparent;margin-bottom:1px;min-height:26px}
.tree-item:hover{background:rgba(255,255,255,.03)}
.tree-item.active{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.08)}
.tree-item.active .ti-name{color:var(--text-primary);font-weight:600}
.tree-item.active .ti-icon{color:var(--accent)}
.ti-toggle{width:20px;height:20px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--text-muted);font-size:8px;transition:transform .15s;cursor:pointer;border-radius:3px}
.ti-toggle:hover{background:rgba(255,255,255,.06);color:var(--text-primary)}
.ti-toggle.open{transform:rotate(90deg)}
.ti-toggle.leaf{cursor:default;opacity:0}
.ti-icon{font-size:11px;color:var(--text-muted);width:14px;text-align:center;flex-shrink:0}
.ti-label{display:flex;align-items:center;gap:5px;flex:1;padding:3px 4px 3px 2px;min-width:0}
.ti-name{font-size:11.5px;color:var(--text-secondary);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ti-cnt{font-size:9px;font-weight:600;background:rgba(255,255,255,.05);color:var(--text-muted);padding:0 5px;border-radius:8px;flex-shrink:0;line-height:15px}
.ti-add{width:18px;height:18px;display:flex;align-items:center;justify-content:center;border-radius:3px;background:transparent;border:none;cursor:pointer;color:var(--text-muted);font-size:9px;opacity:.35;transition:all .12s;flex-shrink:0;margin-right:3px}
.tree-item:hover .ti-add{opacity:1}
.ti-add:hover{background:rgba(255,255,255,.08);color:var(--accent);opacity:1!important}
.tree-children{margin-left:13px;border-left:1px solid rgba(255,255,255,.06);padding-left:3px;overflow:hidden;transition:max-height .2s ease}
.tree-children.collapsed{max-height:0!important}

/* Sidebar footer */
.fm-sb-foot{padding:9px 12px;border-top:1px solid var(--border)}
.stor-lbl{display:flex;justify-content:space-between;font-size:10px;color:var(--text-muted);margin-bottom:4px}
.stor-bar{height:3px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden}
.stor-fill{height:100%;background:linear-gradient(90deg,var(--accent),#00aaff);border-radius:2px}
.stor-info{font-size:10px;color:var(--text-muted);display:flex;justify-content:space-between;margin-top:4px}

/* Main */
.fm-main{display:flex;flex-direction:column;overflow:hidden}
.fm-toolbar{padding:8px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:7px;flex-wrap:nowrap}
.fm-breadcrumb{display:flex;align-items:center;gap:3px;flex:1;min-width:0;overflow:hidden}
.bc-item{font-size:12px;color:var(--text-muted);cursor:pointer;white-space:nowrap;padding:2px 5px;border-radius:4px;transition:all .12s}
.bc-item:hover{background:rgba(255,255,255,.05);color:var(--text-primary)}
.bc-item.cur{color:var(--text-primary);font-weight:600;cursor:default}
.bc-item.cur:hover{background:transparent}
.bc-sep{color:var(--text-muted);opacity:.3;font-size:10px;flex-shrink:0}
.fm-search{position:relative;width:160px;flex-shrink:0}
.fm-search input{width:100%;padding:5px 9px 5px 24px;border-radius:6px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:11px;box-sizing:border-box;outline:none}
.fm-search input:focus{border-color:rgba(255,255,255,.18)}
.fm-search i{position:absolute;left:8px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:10px}
.btn-view{width:26px;height:26px;border:1px solid var(--border);border-radius:5px;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:11px;transition:all .12s;flex-shrink:0}
.btn-view:hover,.btn-view.active{background:rgba(255,255,255,.06);color:var(--text-primary);border-color:rgba(255,255,255,.15)}
.btn-up{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:var(--accent);color:#000;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0}
.btn-up:hover{opacity:.85}
.fm-infobar{padding:4px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;font-size:10px;color:var(--text-muted)}
.ib-sep{width:1px;height:10px;background:var(--border)}

/* Grid */
.fm-grid{padding:10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:7px;overflow-y:auto;flex:1;align-content:start}
.fc{background:var(--bg-secondary);border:1px solid var(--border);border-radius:7px;padding:9px;cursor:pointer;transition:all .12s;position:relative;display:flex;flex-direction:column;gap:5px;animation:fadeUp .18s ease}
.fc:hover{border-color:rgba(255,255,255,.14);transform:translateY(-1px)}
.fc.selected{border-color:var(--accent)}
.fc-thumb{width:100%;aspect-ratio:1;border-radius:5px;background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center;font-size:26px;position:relative;overflow:hidden}
.fc-ext{position:absolute;bottom:3px;right:3px;font-size:8px;font-weight:700;padding:1px 4px;border-radius:3px;text-transform:uppercase;color:#fff}
.fc-name{font-size:10.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fc-size{font-size:9.5px;color:var(--text-muted)}
.fc-actions{display:flex;gap:3px;opacity:0;transition:opacity .12s}
.fc:hover .fc-actions{opacity:1}
.fa-btn{width:20px;height:20px;border-radius:3px;border:1px solid var(--border);background:var(--bg-card);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:9px;color:var(--text-muted);transition:all .12s;text-decoration:none}
.fa-btn:hover{border-color:rgba(255,255,255,.2);color:var(--text-primary)}
.fa-btn.del:hover{border-color:#EF4444;color:#EF4444;background:rgba(239,68,68,.06)}
.fa-btn.pub-on{border-color:rgba(0,255,136,.3);color:var(--accent)}
.fa-btn.pub-on:hover{background:rgba(0,255,136,.06)}

/* List */
.fm-list{flex:1;overflow-y:auto;display:flex;flex-direction:column}
.fl-head{display:grid;grid-template-columns:26px 1fr 65px 75px 105px 60px;gap:8px;padding:6px 12px;border-bottom:1px solid var(--border);font-size:9.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;position:sticky;top:0;background:var(--bg-card);z-index:2}
.fl-row{display:grid;grid-template-columns:26px 1fr 65px 75px 105px 60px;gap:8px;align-items:center;padding:6px 12px;border-bottom:1px solid rgba(255,255,255,.03);transition:background .1s;animation:fadeUp .12s ease}
.fl-row:hover{background:rgba(255,255,255,.02)}
.fl-ico{width:26px;height:26px;border-radius:5px;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;font-size:13px}
.fl-name{font-size:11.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fl-meta{font-size:10.5px;color:var(--text-muted)}
.fl-actions{display:flex;gap:3px;opacity:0;transition:opacity .12s}
.fl-row:hover .fl-actions{opacity:1}

/* Empty */
.fm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;flex:1;color:var(--text-muted);text-align:center}
.fm-empty i{font-size:32px;opacity:.18;display:block;margin-bottom:10px}
.fm-empty p{font-size:12px;margin:0 0 14px}

/* Modals */
.modal-ov{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(3px)}
.modal-box{background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:18px;width:100%;max-width:440px;box-shadow:0 16px 48px rgba(0,0,0,.4)}
.mini-modal{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:16px;width:100%;max-width:320px;box-shadow:0 12px 36px rgba(0,0,0,.4)}
.modal-title{font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:14px;cursor:pointer}
.dropzone{border:2px dashed var(--border);border-radius:7px;padding:22px 14px;text-align:center;cursor:pointer;transition:all .15s}
.dropzone:hover,.dropzone.dragover{border-color:var(--accent);background:rgba(255,255,255,.02)}
.dropzone i{font-size:24px;color:var(--text-muted);opacity:.3;display:block;margin-bottom:7px}
.dropzone p{font-size:11px;color:var(--text-muted);margin:0}
.upload-list{margin-top:9px;display:flex;flex-direction:column;gap:5px;max-height:160px;overflow-y:auto}
.ui-item{display:flex;align-items:center;gap:7px;padding:6px 9px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:5px}
.ui-ico{font-size:14px;flex-shrink:0}
.ui-info{flex:1;min-width:0}
.ui-name{font-size:10.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ui-sz{font-size:9.5px;color:var(--text-muted)}
.ui-prog{height:2px;background:rgba(255,255,255,.05);border-radius:1px;margin-top:3px;overflow:hidden}
.ui-prog-fill{height:100%;background:var(--accent);border-radius:1px;width:0;transition:width .2s}
.modal-footer{display:flex;gap:7px;margin-top:12px}
.btn-cancel{flex:1;padding:7px;border-radius:5px;border:1px solid var(--border);background:transparent;color:var(--text-secondary);font-size:11px;font-weight:600;cursor:pointer}
.btn-submit{flex:2;padding:7px;border-radius:5px;border:none;background:var(--accent);color:#000;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px}
.btn-submit:disabled{opacity:.4;cursor:not-allowed}
.btn-submit:hover:not(:disabled){opacity:.87}
.m-lbl{font-size:9.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:3px}
.m-input{width:100%;padding:6px 9px;border-radius:5px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-primary);font-size:11px;box-sizing:border-box;outline:none;margin-bottom:9px}
.m-input:focus{border-color:rgba(255,255,255,.2)}

/* Toast */
.toast{position:fixed;bottom:18px;right:18px;background:var(--bg-card);border:1px solid var(--border);border-radius:7px;padding:8px 12px;display:flex;align-items:center;gap:7px;box-shadow:0 6px 20px rgba(0,0,0,.3);z-index:99999;font-size:11px;max-width:280px;transform:translateY(50px);opacity:0;transition:all .22s ease}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{border-left:3px solid var(--accent)}
.toast.error{border-left:3px solid #EF4444}

/* type tints */
.t-video .fc-thumb,.t-video .fl-ico{background:rgba(168,85,247,.08)}
.t-image .fc-thumb,.t-image .fl-ico{background:rgba(24,119,242,.08)}
.t-pdf   .fc-thumb,.t-pdf   .fl-ico{background:rgba(239,68,68,.08)}
.t-archive .fc-thumb,.t-archive .fl-ico{background:rgba(245,158,11,.08)}

@media(max-width:680px){.fm-wrap{grid-template-columns:1fr}.fm-sb{display:none}.fl-head,.fl-row{grid-template-columns:26px 1fr 55px 50px}}
</style>

<div class="fm-wrap">

    {{-- ── SIDEBAR ── --}}
    <div class="fm-sb">
        <div class="fm-sb-head">
            <span class="fm-sb-title">Explorer</span>
            <button class="btn-nf" onclick="openNewFolderModal(null)" title="Folder Baru di Root">
                <i class="fas fa-folder-plus"></i>
            </button>
        </div>

        <div class="folder-tree" id="folder-tree">
            {{-- Root item --}}
            <div class="tree-item active" id="tree-root" onclick="navigateTo(null)" style="padding-left:4px">
                <span class="ti-toggle leaf"><i class="fas fa-chevron-right"></i></span>
                <i class="fas fa-hdd ti-icon"></i>
                <div class="ti-label">
                    <span class="ti-name">Root</span>
                    <span class="ti-cnt" id="root-cnt">{{ count($files) }}</span>
                </div>
            </div>
            {{-- Dynamic tree rendered by JS --}}
            <div id="js-tree"></div>
        </div>

        <div class="fm-sb-foot">
            @php
                $maxSz  = 500*1024*1024;
                $pct    = min(100, round($totalSize/$maxSz*100,1));
                $hSz    = formatBytes($totalSize);
            @endphp
            <div class="stor-lbl"><span>Storage</span><span>{{ $hSz }}</span></div>
            <div class="stor-bar"><div class="stor-fill" style="width:{{ $pct }}%"></div></div>
            <div class="stor-info"><span>{{ $pct }}% terpakai</span><span>500 MB</span></div>
        </div>
    </div>

    {{-- ── MAIN ── --}}
    <div class="fm-main">

        <div class="fm-toolbar">
            <div class="fm-breadcrumb" id="fm-breadcrumb">
                <span class="bc-item cur"><i class="fas fa-hdd"></i> Root</span>
            </div>
            <div class="fm-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari file..." id="search-inp" oninput="searchFiles(this.value)">
            </div>
            <button class="btn-view active" id="btn-grid" onclick="setView('grid')" title="Grid"><i class="fas fa-th"></i></button>
            <button class="btn-view" id="btn-list" onclick="setView('list')" title="List"><i class="fas fa-list"></i></button>
            <button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button>
        </div>

        <div class="fm-infobar">
            <span id="ib-count"><i class="fas fa-file" style="margin-right:3px"></i>{{ count($files) }} file</span>
            <div class="ib-sep"></div>
            <span id="ib-size">{{ formatBytes($files->sum('size')) }}</span>
            <div class="ib-sep"></div>
            <span style="color:var(--accent);font-family:monospace" id="ib-folder">/ Root</span>
        </div>

        <div class="fm-grid" id="fm-grid"></div>

        <div class="fm-list" id="fm-list" style="display:none">
            <div class="fl-head"><div></div><div>Nama File</div><div>Ukuran</div><div>Tipe</div><div>Tanggal</div><div></div></div>
            <div id="fm-list-body"></div>
        </div>

    </div>
</div>

{{-- ── UPLOAD MODAL -- langsung di body lewat JS, bukan di sini --}}

{{-- ── RENAME MODAL ── --}}
<div class="modal-ov" id="rename-modal" style="display:none" onclick="if(event.target===this)closeRenameModal()">
    <div class="mini-modal">
        <div class="modal-title"><span><i class="fas fa-pen" style="color:var(--accent);margin-right:5px"></i>Edit File</span><button class="modal-close" onclick="closeRenameModal()"><i class="fas fa-times"></i></button></div>
        <input type="hidden" id="rn-id">
        <label class="m-lbl">Nama File</label>
        <input type="text" class="m-input" id="rn-name" placeholder="Nama file...">
        <label class="m-lbl">Catatan</label>
        <textarea class="m-input" id="rn-desc" rows="2" placeholder="Opsional..." style="resize:none;height:50px;margin-bottom:0"></textarea>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeRenameModal()">Batal</button>
            <button class="btn-submit" onclick="doRename()"><i class="fas fa-check"></i> Simpan</button>
        </div>
    </div>
</div>

{{-- ── NEW FOLDER MODAL ── --}}
<div class="modal-ov" id="nf-modal" style="display:none" onclick="if(event.target===this)closeNewFolderModal()">
    <div class="mini-modal" style="max-width:300px">
        <div class="modal-title">
            <span><i class="fas fa-folder-plus" style="color:#F59E0B;margin-right:5px"></i>Folder Baru</span>
            <button class="modal-close" onclick="closeNewFolderModal()"><i class="fas fa-times"></i></button>
        </div>
        <input type="hidden" id="nf-parent-id">
        <label class="m-lbl">Di dalam</label>
        <div id="nf-parent-display" style="font-size:11px;color:var(--accent);font-family:monospace;background:var(--bg-secondary);border:1px solid var(--border);padding:5px 9px;border-radius:5px;margin-bottom:9px">/ Root</div>
        <label class="m-lbl">Nama Folder</label>
        <input type="text" class="m-input" id="nf-name" placeholder="contoh: dokumen" style="margin-bottom:3px"
               oninput="this.value=this.value.replace(/[^a-zA-Z0-9_\-]/g,'')">
        <small style="font-size:9.5px;color:var(--text-muted);display:block;margin-bottom:10px">Huruf, angka, - dan _ saja</small>
        <div class="modal-footer" style="margin-top:0">
            <button class="btn-cancel" onclick="closeNewFolderModal()">Batal</button>
            <button class="btn-submit" onclick="doCreateFolder()" style="background:#F59E0B;color:#000"><i class="fas fa-folder-plus"></i> Buat</button>
        </div>
    </div>
</div>

{{-- ── PUBLIC URL MODAL ── --}}
<div class="modal-ov" id="pub-modal" style="display:none" onclick="if(event.target===this)closePublicModal()">
    <div class="mini-modal" style="max-width:420px">
        <div class="modal-title">
            <span><i class="fas fa-link" style="color:var(--accent);margin-right:5px"></i>Link Publik Aktif</span>
            <button class="modal-close" onclick="closePublicModal()"><i class="fas fa-times"></i></button>
        </div>
        <div style="font-size:10.5px;color:var(--text-muted);margin-bottom:8px">
            File <strong id="pub-filename" style="color:var(--text-primary)"></strong> bisa diakses publik via:
        </div>
        <div style="display:flex;gap:6px;align-items:center">
            <input type="text" id="pub-url-input" readonly
                style="flex:1;padding:7px 10px;border-radius:5px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--accent);font-size:10px;font-family:monospace;outline:none;cursor:text;min-width:0">
            <button onclick="copyPublicUrl()"
                style="padding:7px 12px;border-radius:5px;border:none;background:var(--accent);color:#000;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap">
                <i class="fas fa-copy"></i> Salin
            </button>
        </div>
        <div style="margin-top:10px;padding:8px 10px;background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.15);border-radius:5px;font-size:10px;color:#F59E0B">
            <i class="fas fa-exclamation-triangle" style="margin-right:4px"></i>
            Link ini bisa diakses siapa saja tanpa login.
        </div>
        <div class="modal-footer" style="margin-top:10px">
            <button class="btn-cancel" onclick="closePublicModal()">Tutup</button>
            <button class="btn-submit" onclick="copyPublicUrl();closePublicModal()"><i class="fas fa-copy"></i> Salin & Tutup</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"><span id="toast-msg" style="color:var(--text-primary)"></span></div>

<script>
// ══════════════════════════════════════════════════════════
// CONFIG — semua route pakai folder_id, bukan path
// ══════════════════════════════════════════════════════════
const ROUTES = {
    contents      : '{{ route("client.files.folder.contents") }}',
    upload        : '{{ route("client.files.upload") }}',
    download      : (id) => '{{ url("client-area/files/download") }}/' + id,
    destroy       : (id) => '{{ url("client-area/files/file") }}/' + id,
    togglePublic  : (id) => '{{ url("client-area/files/file") }}/' + id + '/toggle-public',
    update        : (id) => '{{ url("client-area/files/file") }}/' + id,
    folderCreate  : '{{ route("client.files.folder.create") }}',
    folderDestroy : (id) => '{{ url("client-area/files/folder") }}/' + id,
};
const CSRF = '{{ csrf_token() }}';

// ══════════════════════════════════════════════════════════
// STATE
// ══════════════════════════════════════════════════════════
let curFolderId  = null;   // null = root
let curFolderName = 'Root';
let curView      = 'grid';
let pendingFiles = [];
let allFiles     = @json($files);
let folderTree   = @json($folderTree);

const TICON  = {video:'🎬',image:'🖼️',pdf:'📄',archive:'🗜️',text:'📝',file:'📁'};
const TCOLOR = {video:'#a855f7',image:'#1877F2',pdf:'#EF4444',archive:'#F59E0B',text:'#10B981',file:'var(--text-muted)'};

// ══════════════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    buildUploadModal();
    renderTree(folderTree, document.getElementById('js-tree'), 0);
    renderFiles(allFiles);
});

// ══════════════════════════════════════════════════════════
// NAVIGATE
// ══════════════════════════════════════════════════════════
function navigateTo(folderId, folderName) {
    curFolderId   = folderId;
    curFolderName = folderName || 'Root';
    el('search-inp').value = '';

    // Highlight tree
    document.querySelectorAll('.tree-item').forEach(i => i.classList.remove('active'));
    if (folderId === null) {
        el('tree-root').classList.add('active');
    } else {
        const ti = document.getElementById('ti-' + folderId);
        if (ti) ti.classList.add('active');
    }

    // Update infobar
    el('ib-folder').textContent = '/ ' + curFolderName;

    // Loading
    el('fm-grid').innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted)"><i class="fas fa-circle-notch spin" style="font-size:20px;color:var(--accent);display:block;margin-bottom:7px"></i></div>`;

    fetch(ROUTES.contents + '?folder_id=' + (folderId || ''), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(d => {
        allFiles = d.files;
        renderBreadcrumb(d.breadcrumb);
        renderFiles(d.files);
        el('ib-count').innerHTML = `<i class="fas fa-file" style="margin-right:3px"></i>${d.files.length} file`;
        el('ib-size').textContent = formatSize(d.files.reduce((a,f) => a + f.size, 0));
    })
    .catch(() => showToast('error', '❌ Gagal memuat folder.'));
}

function reload() {
    navigateTo(curFolderId, curFolderName);
}

// ══════════════════════════════════════════════════════════
// TREE
// ══════════════════════════════════════════════════════════
function renderTree(nodes, container, depth) {
    container.innerHTML = '';
    nodes.forEach(node => {
        const wrap = document.createElement('div');
        wrap.className = 'tree-node';
        wrap.id = 'tn-' + node.id;
        const hasChildren = node.children && node.children.length > 0;
        const indent = depth * 13 + 4;
        wrap.innerHTML = `
            <div class="tree-item" id="ti-${node.id}" style="padding-left:${indent}px"
                 onclick="navigateTo(${node.id}, '${esc(node.name)}')">
                <span class="ti-toggle ${hasChildren ? '' : 'leaf'}" id="tg-${node.id}"
                      onclick="event.stopPropagation();toggleNode(${node.id})">
                    <i class="fas fa-chevron-right"></i>
                </span>
                <i class="fas fa-folder ti-icon"></i>
                <div class="ti-label">
                    <span class="ti-name" title="${esc(node.name)}">${esc(node.name)}</span>
                    ${node.count > 0 ? `<span class="ti-cnt">${node.count}</span>` : ''}
                </div>
                <button class="ti-add" onclick="event.stopPropagation();openNewFolderModal(${node.id},'${esc(node.name)}')" title="Subfolder baru">
                    <i class="fas fa-plus"></i>
                </button>
            </div>`;
        if (hasChildren) {
            const childWrap = document.createElement('div');
            childWrap.className = 'tree-children collapsed';
            childWrap.id = 'tc-' + node.id;
            renderTree(node.children, childWrap, depth + 1);
            wrap.appendChild(childWrap);
        }
        container.appendChild(wrap);
    });
}

function toggleNode(id) {
    const ch = el('tc-' + id), tg = el('tg-' + id);
    if (!ch || !tg) return;
    const collapsed = ch.classList.toggle('collapsed');
    tg.classList.toggle('open', !collapsed);
}

// ══════════════════════════════════════════════════════════
// BREADCRUMB
// ══════════════════════════════════════════════════════════
function renderBreadcrumb(crumbs) {
    let html = `<span class="bc-item" onclick="navigateTo(null,'Root')"><i class="fas fa-hdd"></i></span>`;
    (crumbs || []).forEach((c, i) => {
        const isLast = i === crumbs.length - 1;
        html += `<span class="bc-sep"><i class="fas fa-chevron-right"></i></span>`;
        html += isLast
            ? `<span class="bc-item cur">${esc(c.name)}</span>`
            : `<span class="bc-item" onclick="navigateTo(${c.id},'${esc(c.name)}')">${esc(c.name)}</span>`;
    });
    el('fm-breadcrumb').innerHTML = html;
}

// ══════════════════════════════════════════════════════════
// RENDER FILES
// ══════════════════════════════════════════════════════════
function renderFiles(files) {
    if (curView === 'grid') {
        el('fm-list').style.display = 'none';
        el('fm-grid').style.display = 'grid';
        if (!files.length) {
            el('fm-grid').innerHTML = `<div style="grid-column:1/-1"><div class="fm-empty"><i class="fas fa-folder-open"></i><p>Folder ini kosong.</p><button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button></div></div>`;
            return;
        }
        el('fm-grid').innerHTML = files.map(f => `
            <div class="fc t-${f.type}" onclick="selectCard(this)">
                <div class="fc-thumb"><span>${TICON[f.type]||'📁'}</span><span class="fc-ext" style="background:${TCOLOR[f.type]||'#555'}">${f.extension||f.type}</span></div>
                <div class="fc-name" title="${esc(f.original_name)}">${esc(f.original_name)}</div>
                <div class="fc-size">${f.human_size}</div>
                <div class="fc-actions">
                    <button class="fa-btn ${f.is_public?'pub-on':''}" title="${f.is_public?'Salin Link':'Buat Link'}" onclick="event.stopPropagation();togglePublic(${f.id})"><i class="fas fa-${f.is_public?'link':'lock'}"></i></button>
                    <a href="${ROUTES.download(f.id)}" class="fa-btn" title="Download" onclick="event.stopPropagation()"><i class="fas fa-download"></i></a>
                    <button class="fa-btn" title="Edit" onclick="event.stopPropagation();openRename(${f.id},'${esc(f.original_name)}','${esc(f.description||'')}')"><i class="fas fa-pen"></i></button>
                    <button class="fa-btn del" title="Hapus" onclick="event.stopPropagation();deleteFile(${f.id})"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>`).join('');
    } else {
        el('fm-grid').style.display = 'none';
        el('fm-list').style.display = 'flex';
        el('fm-list-body').innerHTML = files.length ? files.map(f => `
            <div class="fl-row">
                <div class="fl-ico t-${f.type}">${TICON[f.type]||'📁'}</div>
                <div><div class="fl-name" title="${esc(f.original_name)}">${esc(f.original_name)}</div>${f.description?`<div class="fl-meta">${esc(f.description)}</div>`:''}</div>
                <div class="fl-meta">${f.human_size}</div>
                <div><span style="background:rgba(255,255,255,.05);color:${TCOLOR[f.type]};padding:1px 5px;border-radius:3px;font-size:9.5px;font-weight:600">${f.extension||f.type}</span></div>
                <div class="fl-meta">${f.created_at}</div>
                <div class="fl-actions">
                    <button class="fa-btn ${f.is_public?'pub-on':''}" onclick="togglePublic(${f.id})"><i class="fas fa-${f.is_public?'link':'lock'}"></i></button>
                    <a href="${ROUTES.download(f.id)}" class="fa-btn"><i class="fas fa-download"></i></a>
                    <button class="fa-btn" onclick="openRename(${f.id},'${esc(f.original_name)}','${esc(f.description||'')}')"><i class="fas fa-pen"></i></button>
                    <button class="fa-btn del" onclick="deleteFile(${f.id})"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>`).join('')
        : `<div class="fm-empty"><i class="fas fa-folder-open"></i><p>Folder ini kosong.</p><button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button></div>`;
    }
}

function selectCard(c) { document.querySelectorAll('.fc').forEach(x => x.classList.remove('selected')); c.classList.add('selected'); }

function setView(v) {
    curView = v;
    el('btn-grid').classList.toggle('active', v === 'grid');
    el('btn-list').classList.toggle('active', v === 'list');
    renderFiles(allFiles);
}

function searchFiles(q) {
    const f = q.trim() ? allFiles.filter(x => x.original_name.toLowerCase().includes(q.toLowerCase())) : allFiles;
    renderFiles(f);
    el('ib-count').innerHTML = `<i class="fas fa-file" style="margin-right:3px"></i>${f.length} file${q?' (filter)':''}`;
}

// ══════════════════════════════════════════════════════════
// UPLOAD MODAL — dibuat via JS, di-append ke body
// ══════════════════════════════════════════════════════════
function buildUploadModal() {
    const ov = document.createElement('div');
    ov.id = 'upload-modal';
    ov.className = 'modal-ov';
    ov.style.display = 'none';
    ov.addEventListener('click', e => { if (e.target === ov) closeUploadModal(); });
    ov.innerHTML = `
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-title">
                <span><i class="fas fa-upload" style="color:var(--accent);margin-right:6px"></i>Upload ke <code id="up-folder-lbl" style="color:var(--accent);font-size:11px">/ Root</code></span>
                <button class="modal-close" onclick="closeUploadModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="dropzone" id="fm-dropzone"
                 onclick="el('fm-file-input').click()"
                 ondrop="handleDrop(event)"
                 ondragover="event.preventDefault();this.classList.add('dragover')"
                 ondragleave="this.classList.remove('dragover')">
                <i class="fas fa-cloud-upload-alt"></i>
                <p><strong style="color:var(--text-primary)">Klik atau drag & drop</strong> file di sini</p>
                <p style="margin-top:4px;font-size:9.5px;opacity:.5">Semua tipe · Max 500 MB · Multiple OK</p>
            </div>
            <input type="file" id="fm-file-input" multiple style="display:none" onchange="handleFileSelect(this.files)">
            <div class="upload-list" id="fm-upload-list"></div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeUploadModal()">Batal</button>
                <button class="btn-submit" id="fm-btn-upload" onclick="doUpload()" disabled>
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </div>`;
    document.body.appendChild(ov);
}

function openUploadModal() {
    pendingFiles = [];
    el('fm-upload-list').innerHTML = '';
    el('fm-btn-upload').disabled = true;
    el('fm-file-input').value = '';
    el('up-folder-lbl').textContent = '/ ' + curFolderName;
    el('upload-modal').style.display = 'flex';
}

function closeUploadModal() {
    el('upload-modal').style.display = 'none';
    pendingFiles = [];
}

function handleFileSelect(files) { addFiles(Array.from(files)); }
function handleDrop(ev) { ev.preventDefault(); el('fm-dropzone').classList.remove('dragover'); addFiles(Array.from(ev.dataTransfer.files)); }
function addFiles(files) { pendingFiles = [...pendingFiles, ...files]; renderUpList(); el('fm-btn-upload').disabled = pendingFiles.length === 0; }

function renderUpList() {
    el('fm-upload-list').innerHTML = pendingFiles.map((f, i) => {
        const tp = f.type.startsWith('video') ? 'video' : f.type.startsWith('image') ? 'image' : f.type.includes('pdf') ? 'pdf' : 'file';
        const sz = formatSize(f.size);
        return `<div class="ui-item"><span class="ui-ico">${TICON[tp]}</span><div class="ui-info"><div class="ui-name">${esc(f.name)}</div><div class="ui-sz">${sz}</div><div class="ui-prog"><div class="ui-prog-fill" id="up-${i}"></div></div></div></div>`;
    }).join('');
}

function doUpload() {
    if (!pendingFiles.length) return;
    const btn = el('fm-btn-upload');
    btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Uploading...';
    btn.disabled = true;

    const fd = new FormData();
    fd.append('_token', CSRF);
    if (curFolderId) fd.append('folder_id', curFolderId);
    pendingFiles.forEach(f => fd.append('files[]', f));

    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', ev => {
        if (ev.lengthComputable) {
            const p = Math.round(ev.loaded / ev.total * 100);
            pendingFiles.forEach((_, i) => { const pb = el('up-' + i); if (pb) pb.style.width = p + '%'; });
        }
    });
    xhr.onload = function () {
        btn.innerHTML = '<i class="fas fa-upload"></i> Upload';
        try {
            const d = JSON.parse(xhr.responseText);
            if (d.success) {
                closeUploadModal();
                reload();
                // Tampilkan modal list URL public
                if (d.files && d.files.length > 0) {
                    showUploadedUrlsModal(d.files);
                } else {
                    showToast('success', '✅ ' + d.message);
                }
            } else {
                showToast('error', '❌ ' + (d.message || 'Gagal'));
                btn.disabled = false;
            }
        } catch (e) {
            showToast('error', '❌ Response error.');
            btn.disabled = false;
        }
    };
    xhr.onerror = () => { showToast('error', '❌ Koneksi gagal.'); btn.innerHTML = '<i class="fas fa-upload"></i> Upload'; btn.disabled = false; };
    xhr.open('POST', ROUTES.upload);
    xhr.send(fd);
}

// ══════════════════════════════════════════════════════════
// FILE ACTIONS
// ══════════════════════════════════════════════════════════
function deleteFile(id) {
    if (!confirm('Hapus file ini?')) return;
    fetch(ROUTES.destroy(id), { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast('success', '🗑️ Dihapus.'); allFiles = allFiles.filter(f => f.id !== id); renderFiles(allFiles); }
        else showToast('error', '❌ Gagal.');
    }).catch(() => showToast('error', '❌ Error.'));
}

function openRename(id, name, desc) {
    el('rn-id').value = id; el('rn-name').value = name; el('rn-desc').value = desc || '';
    el('rename-modal').style.display = 'flex';
    setTimeout(() => el('rn-name').select(), 80);
}
function closeRenameModal() { el('rename-modal').style.display = 'none'; }
function doRename() {
    const id = el('rn-id').value, name = el('rn-name').value.trim(), desc = el('rn-desc').value.trim();
    if (!name) return;
    fetch(ROUTES.update(id), {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ original_name: name, description: desc })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast('success', '✅ Disimpan.'); closeRenameModal();
            const idx = allFiles.findIndex(f => f.id == id);
            if (idx > -1) { allFiles[idx].original_name = name; allFiles[idx].description = desc; }
            renderFiles(allFiles);
        } else showToast('error', '❌ Gagal.');
    }).catch(() => showToast('error', '❌ Error.'));
}

function togglePublic(id) {
    fetch(ROUTES.togglePublic(id), { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            const idx = allFiles.findIndex(f => f.id == id);
            if (idx > -1) {
                allFiles[idx].is_public  = d.is_public;
                allFiles[idx].public_url = d.public_url;
            }
            renderFiles(allFiles);
            if (d.is_public && d.public_url) {
                const name = allFiles.find(f => f.id == id)?.original_name || '';
                openPublicModal(d.public_url, name);
            } else {
                showToast('success', '🔒 Akses publik dicabut.');
            }
        } else {
            showToast('error', '❌ ' + (d.message || 'Gagal.'));
        }
    })
    .catch(err => { console.error(err); showToast('error', '❌ Error.'); });
}

// ══════════════════════════════════════════════════════════
// FOLDER ACTIONS
// ══════════════════════════════════════════════════════════
function openNewFolderModal(parentId, parentName) {
    el('nf-parent-id').value = parentId || '';
    el('nf-parent-display').textContent = '/ ' + (parentName || 'Root');
    el('nf-name').value = '';
    el('nf-modal').style.display = 'flex';
    setTimeout(() => el('nf-name').focus(), 80);
}
function closeNewFolderModal() { el('nf-modal').style.display = 'none'; }
function doCreateFolder() {
    const parentId = el('nf-parent-id').value || null;
    const name = el('nf-name').value.trim();
    if (!name) return;
    fetch(ROUTES.folderCreate, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ name, parent_id: parentId })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast('success', '📁 Folder dibuat.');
            closeNewFolderModal();
            // Reload halaman untuk refresh tree
            window.location.reload();
        } else showToast('error', '❌ ' + d.message);
    }).catch(() => showToast('error', '❌ Error.'));
}

// ══════════════════════════════════════════════════════════
// UPLOADED URLs MODAL
// ══════════════════════════════════════════════════════════
function showUploadedUrlsModal(files) {
    // Hapus modal lama kalau ada
    const old = document.getElementById('urls-modal');
    if (old) old.remove();

    // Simpan URLs di variable global sementara
    window._uploadedUrls = files.map(f => f.public_url || '').filter(Boolean);

    const ov = document.createElement('div');
    ov.id = 'urls-modal';
    ov.className = 'modal-ov';
    ov.addEventListener('click', e => { if (e.target === ov) ov.remove(); });

    const urlRows = files.map((f, i) => `
        <div style="display:flex;align-items:center;gap:6px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04)">
            <span style="font-size:13px;flex-shrink:0">${TICON[f.type]||'📁'}</span>
            <div style="flex:1;min-width:0">
                <div style="font-size:10.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(f.original_name)}</div>
                <input readonly id="url-inp-${i}" onclick="this.select()" value="${esc(f.public_url || '')}"
                    style="width:100%;margin-top:3px;padding:4px 7px;border-radius:4px;border:1px solid rgba(255,255,255,.08);background:var(--bg-secondary);color:var(--accent);font-size:10px;font-family:monospace;outline:none;cursor:text;box-sizing:border-box">
            </div>
            <button data-url="${esc(f.public_url||'')}" onclick="copyBtnClick(this)"
                style="padding:5px 8px;border-radius:4px;border:none;background:var(--accent);color:#000;font-size:10px;font-weight:700;cursor:pointer;flex-shrink:0">
                <i class="fas fa-copy"></i>
            </button>
        </div>`).join('');

    ov.innerHTML = `
        <div class="modal-box" onclick="event.stopPropagation()" style="max-width:500px">
            <div class="modal-title">
                <span><i class="fas fa-check-circle" style="color:var(--accent);margin-right:6px"></i>Upload Berhasil — ${files.length} File</span>
                <button class="modal-close" onclick="document.getElementById('urls-modal').remove()"><i class="fas fa-times"></i></button>
            </div>
            <div style="font-size:10.5px;color:var(--text-muted);margin-bottom:10px">
                Semua file sudah dapat diakses publik. Salin link di bawah:
            </div>
            <div style="max-height:320px;overflow-y:auto">${urlRows}</div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="document.getElementById('urls-modal').remove()">Tutup</button>
                <button class="btn-submit" onclick="copyAllUrls()">
                    <i class="fas fa-copy"></i> Salin Semua
                </button>
            </div>
        </div>`;

    document.body.appendChild(ov);
}

function copyBtnClick(btn) {
    const url = btn.getAttribute('data-url');
    copyText(url, btn);
}

function copyText(text, btn) {
    navigator.clipboard?.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}

function copyAllUrls() {
    const urls = (window._uploadedUrls || []).join('\n');
    if (!urls) return;
    navigator.clipboard?.writeText(urls).then(() => {
        showToast('success', '✅ Semua URL disalin!');
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = urls; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        showToast('success', '✅ Semua URL disalin!');
    });
}
function openPublicModal(url, name) {
    el('pub-url-input').value = url;
    el('pub-filename').textContent = name;
    el('pub-modal').style.display = 'flex';
}
function closePublicModal() { el('pub-modal').style.display = 'none'; }
function copyPublicUrl() {
    const inp = el('pub-url-input');
    inp.select();
    navigator.clipboard?.writeText(inp.value)
        .then(() => showToast('success', '✅ Link disalin!'))
        .catch(() => { document.execCommand('copy'); showToast('success', '✅ Link disalin!'); });
}

// ══════════════════════════════════════════════════════════
// UTILS
// ══════════════════════════════════════════════════════════
document.addEventListener('keydown', ev => {
    if (ev.key === 'Escape') { closeUploadModal(); closeRenameModal(); closeNewFolderModal(); closePublicModal(); }
    if ((ev.ctrlKey || ev.metaKey) && ev.key === 'u') { ev.preventDefault(); openUploadModal(); }
});

function el(id) { return document.getElementById(id); }
function esc(s) { const d = document.createElement('div'); d.appendChild(document.createTextNode(String(s || ''))); return d.innerHTML; }
function formatSize(b) {
    if (b >= 1073741824) return (b/1073741824).toFixed(1) + ' GB';
    if (b >= 1048576)    return (b/1048576).toFixed(1) + ' MB';
    if (b >= 1024)       return (b/1024).toFixed(1) + ' KB';
    return b + ' B';
}
let tT;
function showToast(type, msg) {
    const t = el('toast'); t.className = 'toast ' + type + ' show'; el('toast-msg').textContent = msg;
    clearTimeout(tT); tT = setTimeout(() => t.classList.remove('show'), 2800);
}
</script>

@endsection
