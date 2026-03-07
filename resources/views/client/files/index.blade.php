@extends('layouts.app')
@section('page-title', 'File Manager')
@section('breadcrumb', 'File Manager')
@section('content')
<style>
@keyframes fadeUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
.spin{animation:spin .8s linear infinite;display:inline-block}

/* ── Layout ── */
.fm-wrap{display:grid;grid-template-columns:210px 1fr;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden;height:calc(100vh - 130px)}

/* ── Sidebar ── */
.fm-sb{border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
.fm-sb-head{padding:10px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.fm-sb-title{font-size:10px;font-weight:700;color:var(--text-muted);letter-spacing:1.5px;text-transform:uppercase}
.btn-nf{width:24px;height:24px;border-radius:5px;background:rgba(255,255,255,.04);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-secondary);font-size:11px;transition:all .15s}
.btn-nf:hover{background:rgba(255,255,255,.08);color:var(--accent);border-color:rgba(255,255,255,.15)}

/* ── Folder Tree ── */
.folder-tree{padding:6px 6px;flex:1;overflow-y:auto}
.tree-node{position:relative}
.tree-item{display:flex;align-items:center;gap:0;border-radius:5px;cursor:pointer;transition:all .12s;border:1px solid transparent;margin-bottom:1px;min-height:26px}
.tree-item:hover{background:rgba(255,255,255,.03)}
.tree-item.active{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.08)}
.tree-item.active .ti-name{color:var(--text-primary);font-weight:600}
.tree-item.active .ti-icon{color:var(--accent)}

/* Toggle expand arrow */
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

/* Children indent */
.tree-children{margin-left:13px;border-left:1px solid rgba(255,255,255,.06);padding-left:3px;overflow:hidden;transition:max-height .2s ease}
.tree-children.collapsed{max-height:0!important}

/* ── Sidebar footer ── */
.fm-sb-foot{padding:9px 12px;border-top:1px solid var(--border)}
.stor-lbl{display:flex;justify-content:space-between;font-size:10px;color:var(--text-muted);margin-bottom:4px}
.stor-bar{height:3px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden}
.stor-fill{height:100%;background:linear-gradient(90deg,var(--accent),#00aaff);border-radius:2px}
.stor-info{font-size:10px;color:var(--text-muted);display:flex;justify-content:space-between;margin-top:4px}

/* ── Main ── */
.fm-main{display:flex;flex-direction:column;overflow:hidden}
.fm-toolbar{padding:8px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:7px;flex-wrap:nowrap}

/* Breadcrumb path */
.fm-breadcrumb{display:flex;align-items:center;gap:3px;flex:1;min-width:0;overflow:hidden}
.bc-item{font-size:12px;color:var(--text-muted);cursor:pointer;white-space:nowrap;padding:2px 5px;border-radius:4px;transition:all .12s}
.bc-item:hover{background:rgba(255,255,255,.05);color:var(--text-primary)}
.bc-item.cur{color:var(--text-primary);font-weight:600;cursor:default}
.bc-item.cur:hover{background:transparent}
.bc-sep{color:var(--text-muted);opacity:.3;font-size:10px;flex-shrink:0}

.fm-search{position:relative;width:160px;flex-shrink:0}
.fm-search input{width:100%;padding:5px 9px 5px 24px;border-radius:6px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:11px;box-sizing:border-box;outline:none}
.fm-search input:focus{border-color:rgba(255,255,255,.18)}
.fm-search i{position:absolute;left:8px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:10px}

.btn-view{width:26px;height:26px;border:1px solid var(--border);border-radius:5px;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:11px;transition:all .12s;flex-shrink:0}
.btn-view:hover,.btn-view.active{background:rgba(255,255,255,.06);color:var(--text-primary);border-color:rgba(255,255,255,.15)}
.btn-up{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:var(--accent);color:#000;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0}
.btn-up:hover{opacity:.85}

.fm-infobar{padding:4px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;font-size:10px;color:var(--text-muted)}
.ib-sep{width:1px;height:10px;background:var(--border)}

/* ── Grid ── */
.fm-grid{padding:10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:7px;overflow-y:auto;flex:1;align-content:start}
.fc{background:var(--bg-main);border:1px solid var(--border);border-radius:7px;padding:9px;cursor:pointer;transition:all .12s;position:relative;display:flex;flex-direction:column;gap:5px;animation:fadeUp .18s ease}
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

/* ── List ── */
.fm-list{flex:1;overflow-y:auto;display:flex;flex-direction:column}
.fl-head{display:grid;grid-template-columns:26px 1fr 65px 75px 105px 60px;gap:8px;padding:6px 12px;border-bottom:1px solid var(--border);font-size:9.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;position:sticky;top:0;background:var(--bg-card);z-index:2}
.fl-row{display:grid;grid-template-columns:26px 1fr 65px 75px 105px 60px;gap:8px;align-items:center;padding:6px 12px;border-bottom:1px solid rgba(255,255,255,.03);transition:background .1s;animation:fadeUp .12s ease}
.fl-row:hover{background:rgba(255,255,255,.02)}
.fl-ico{width:26px;height:26px;border-radius:5px;background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;font-size:13px}
.fl-name{font-size:11.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fl-meta{font-size:10.5px;color:var(--text-muted)}
.fl-actions{display:flex;gap:3px;opacity:0;transition:opacity .12s}
.fl-row:hover .fl-actions{opacity:1}

/* ── Empty ── */
.fm-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;flex:1;color:var(--text-muted);text-align:center}
.fm-empty i{font-size:32px;opacity:.18;display:block;margin-bottom:10px}
.fm-empty p{font-size:12px;margin:0 0 14px}

/* ── Modals ── */
.modal-ov{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(3px)}
.modal-box{background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:18px;width:100%;max-width:440px;box-shadow:0 16px 48px rgba(0,0,0,.4)}
.mini-modal{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:16px;width:100%;max-width:320px;box-shadow:0 12px 36px rgba(0,0,0,.4)}
.modal-title{font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
.modal-close{background:none;border:none;color:var(--text-muted);font-size:14px;cursor:pointer}

.dropzone{border:2px dashed var(--border);border-radius:7px;padding:22px 14px;text-align:center;cursor:pointer;transition:all .15s}
.dropzone:hover,.dropzone.dragover{border-color:var(--accent);background:rgba(255,255,255,.02)}
.dropzone i{font-size:24px;color:var(--text-muted);opacity:.3;display:block;margin-bottom:7px}
.dropzone p{font-size:11px;color:var(--text-muted);margin:0}
.dz-btn{display:inline-block;margin-top:6px;padding:3px 10px;border:1px solid var(--border);border-radius:4px;background:var(--bg-main);color:var(--text-secondary);font-size:10px;cursor:pointer}
.dz-btn:hover{border-color:rgba(255,255,255,.2);color:var(--text-primary)}

.upload-list{margin-top:9px;display:flex;flex-direction:column;gap:5px;max-height:160px;overflow-y:auto}
.ui-item{display:flex;align-items:center;gap:7px;padding:6px 9px;background:var(--bg-main);border:1px solid var(--border);border-radius:5px}
.ui-ico{font-size:14px;flex-shrink:0}
.ui-info{flex:1;min-width:0}
.ui-name{font-size:10.5px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ui-sz{font-size:9.5px;color:var(--text-muted)}
.ui-prog{height:2px;background:rgba(255,255,255,.05);border-radius:1px;margin-top:3px;overflow:hidden}
.ui-prog-fill{height:100%;background:var(--accent);border-radius:1px;width:0;transition:width .2s}
.ui-st{font-size:11px;flex-shrink:0}

.modal-footer{display:flex;gap:7px;margin-top:12px}
.btn-cancel{flex:1;padding:7px;border-radius:5px;border:1px solid var(--border);background:transparent;color:var(--text-secondary);font-size:11px;font-weight:600;cursor:pointer}
.btn-submit{flex:2;padding:7px;border-radius:5px;border:none;background:var(--accent);color:#000;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px}
.btn-submit:disabled{opacity:.4;cursor:not-allowed}
.btn-submit:hover:not(:disabled){opacity:.87}

.m-lbl{font-size:9.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;display:block;margin-bottom:3px}
.m-input{width:100%;padding:6px 9px;border-radius:5px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:11px;box-sizing:border-box;outline:none;margin-bottom:9px}
.m-input:focus{border-color:rgba(255,255,255,.2)}

/* ── Toast ── */
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

    {{-- ── SIDEBAR TREE ── --}}
    <div class="fm-sb">
        <div class="fm-sb-head">
            <span class="fm-sb-title">Explorer</span>
            <button class="btn-nf" onclick="openNewFolderModal(curPath)" title="Folder Baru">
                <i class="fas fa-folder-plus"></i>
            </button>
        </div>

        <div class="folder-tree" id="folder-tree">
            <?php
            // Pure PHP recursive — tidak boleh mix Blade directives di dalam fungsi rekursif
            function renderTreeNodeHtml(array $node, string $activePath, array $pathStats, int $depth = 0): string {
                $path     = $node['path'];
                $name     = $node['name'];
                $children = $node['children'] ?? [];
                $cnt      = $pathStats[$path]['count'] ?? 0;
                $hasChild = !empty($children);
                $isActive = $activePath === $path;
                $id       = str_replace('/', '-', $path);
                $indent   = $depth * 13;

                $icon = match(true) {
                    $path === 'meta'     => 'fab fa-facebook',
                    $path === 'x'        => 'fab fa-x-twitter',
                    $path === 'tiktok'   => 'fab fa-tiktok',
                    $path === 'telegram' => 'fab fa-telegram',
                    default              => 'fas fa-folder',
                };

                $activeClass  = $isActive ? 'active' : '';
                $toggleClass  = $hasChild ? '' : 'leaf';
                $cntHtml      = $cnt > 0 ? "<span class=\"ti-cnt\">{$cnt}</span>" : '';

                $html = "
                <div class=\"tree-node\" id=\"tn-{$id}\">
                    <div class=\"tree-item {$activeClass}\" style=\"padding-left:{$indent}px\" onclick=\"switchPath('{$path}')\">
                        <span class=\"ti-toggle {$toggleClass}\" id=\"tg-{$id}\"
                              onclick=\"event.stopPropagation();toggleNode('{$id}')\" title=\"Expand/Collapse\">
                            <i class=\"fas fa-chevron-right\"></i>
                        </span>
                        <i class=\"{$icon} ti-icon\"></i>
                        <div class=\"ti-label\">
                            <span class=\"ti-name\" title=\"{$path}\">{$name}</span>
                            {$cntHtml}
                        </div>
                        <button class=\"ti-add\" onclick=\"event.stopPropagation();openNewFolderModal('{$path}')\" title=\"Tambah subfolder\">
                            <i class=\"fas fa-plus\"></i>
                        </button>
                    </div>";

                if ($hasChild) {
                    $html .= "<div class=\"tree-children\" id=\"tc-{$id}\" style=\"max-height:999px\">";
                    foreach ($children as $child) {
                        $html .= renderTreeNodeHtml($child, $activePath, $pathStats, $depth + 1);
                    }
                    $html .= "</div>";
                }

                $html .= "</div>";
                return $html;
            }

            foreach ($folderTree as $node) {
                echo renderTreeNodeHtml($node, $activePath, $pathStats, 0);
            }
            ?>
        </div>

        <div class="fm-sb-foot">
            @php
                $totalSz = collect($pathStats)->sum('size');
                $maxSz   = 500*1024*1024;
                $pct     = min(100, round($totalSz/$maxSz*100,1));
                $hSz     = $totalSz < 1048576 ? round($totalSz/1024,1).' KB' : round($totalSz/1048576,1).' MB';
            @endphp
            <div class="stor-lbl"><span>Storage</span><span>{{ $hSz }}</span></div>
            <div class="stor-bar"><div class="stor-fill" style="width:{{ $pct }}%"></div></div>
            <div class="stor-info"><span>{{ $pct }}% terpakai</span><span>500 MB</span></div>
        </div>
    </div>

    {{-- ── MAIN AREA ── --}}
    <div class="fm-main">

        {{-- Toolbar --}}
        <div class="fm-toolbar">
            <div class="fm-breadcrumb" id="fm-breadcrumb">
                {{-- rendered by JS --}}
            </div>
            <div class="fm-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari file..." id="search-inp" oninput="searchFiles(this.value)">
            </div>
            <button class="btn-view active" id="btn-grid" onclick="setView('grid')" title="Grid"><i class="fas fa-th"></i></button>
            <button class="btn-view" id="btn-list" onclick="setView('list')" title="List"><i class="fas fa-list"></i></button>
            <button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button>
        </div>

        {{-- Info bar --}}
        <div class="fm-infobar">
            <span id="ib-count"><i class="fas fa-file" style="margin-right:3px"></i>{{ count($files) }} file</span>
            <div class="ib-sep"></div>
            <span id="ib-size">@php $sz=$files->sum('size'); echo $sz<1048576?round($sz/1024,1).' KB':round($sz/1048576,1).' MB'; @endphp</span>
            <div class="ib-sep"></div>
            <span style="color:var(--accent);font-family:'Space Mono',monospace" id="ib-path">/{{ $activePath }}</span>
        </div>

        {{-- Grid --}}
        <div class="fm-grid" id="fm-grid">
            @forelse($files as $f)
                @include('client.files._file_card', ['f'=>$f])
            @empty
                <div id="grid-empty" style="grid-column:1/-1">
                    <div class="fm-empty">
                        <i class="fas fa-folder-open"></i>
                        <p>Folder ini masih kosong.</p>
                        <button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload File</button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- List --}}
        <div class="fm-list" id="fm-list" style="display:none">
            <div class="fl-head"><div></div><div>Nama File</div><div>Ukuran</div><div>Tipe</div><div>Tanggal</div><div></div></div>
            <div id="fm-list-body">
                @foreach($files as $f)
                    @include('client.files._file_row', ['f'=>$f])
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- ── UPLOAD PANEL (inline, bukan modal) ── --}}
<div id="upload-panel" style="display:none;position:fixed;bottom:24px;right:24px;width:360px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:16px;box-shadow:0 16px 48px rgba(0,0,0,.5);z-index:9999">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <span style="font-size:13px;font-weight:700;color:var(--text-primary)"><i class="fas fa-upload" style="color:var(--accent);margin-right:6px"></i>Upload ke <code id="up-path-lbl" style="color:var(--accent);font-size:11px">/meta</code></span>
        <button onclick="closeUploadModal()" style="background:none;border:none;color:var(--text-muted);font-size:14px;cursor:pointer"><i class="fas fa-times"></i></button>
    </div>
    <input type="file" id="file-input" multiple style="display:none" onchange="handleFileSelect(this.files)">
    <div class="dropzone" id="dropzone"
         onclick="el('file-input').click()"
         ondrop="handleDrop(event)"
         ondragover="event.preventDefault();this.classList.add('dragover')"
         ondragleave="this.classList.remove('dragover')">
        <i class="fas fa-cloud-upload-alt"></i>
        <p style="margin-top:6px"><strong style="color:var(--text-primary)">Klik atau drag &amp; drop</strong> file di sini</p>
        <p style="margin-top:4px;font-size:9.5px;opacity:.4">Semua tipe · Max 500 MB/file · Multiple OK</p>
    </div>
    <div class="upload-list" id="upload-list"></div>
    <div style="display:flex;gap:7px;margin-top:12px">
        <button onclick="closeUploadModal()" style="flex:1;padding:7px;border-radius:5px;border:1px solid var(--border);background:transparent;color:var(--text-secondary);font-size:11px;font-weight:600;cursor:pointer">Batal</button>
        <button id="btn-do-upload" onclick="doUpload()" disabled style="flex:2;padding:7px;border-radius:5px;border:none;background:var(--accent);color:#000;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px"><i class="fas fa-upload"></i> Upload</button>
    </div>
</div>


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
        <input type="hidden" id="nf-parent">
        <label class="m-lbl">Di dalam</label>
        <div id="nf-parent-display" style="font-size:11px;color:var(--accent);font-family:'Space Mono',monospace;background:var(--bg-main);border:1px solid var(--border);padding:5px 9px;border-radius:5px;margin-bottom:9px">/meta</div>
        <label class="m-lbl">Nama Folder</label>
        <input type="text" class="m-input" id="nf-name" placeholder="contoh: isco" style="margin-bottom:3px">
        <small style="font-size:9.5px;color:var(--text-muted);display:block;margin-bottom:10px">Huruf, angka, - dan _ saja</small>
        <div class="modal-footer" style="margin-top:0">
            <button class="btn-cancel" onclick="closeNewFolderModal()">Batal</button>
            <button class="btn-submit" onclick="doCreateFolder()" style="background:#F59E0B;color:#000"><i class="fas fa-folder-plus"></i> Buat</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"><span id="toast-msg" style="color:var(--text-primary)"></span></div>

<script>
const el=id=>document.getElementById(id);
const qs=s=>document.querySelector(s);
const qsa=s=>document.querySelectorAll(s);

let curPath='{{ $activePath }}';
let curView='grid';
let pendingFiles=[];
let allFiles=@json($filesJson);

const TICON={video:'🎬',image:'🖼️',pdf:'📄',archive:'🗜️',text:'📝',file:'📁'};
const TCOLOR={video:'#a855f7',image:'#1877F2',pdf:'#EF4444',archive:'#F59E0B',text:'#10B981',file:'var(--text-muted)'};

// ── Init ────────────────────────────────────────────────────
renderBreadcrumb(curPath);
renderFiles(allFiles); // render ulang supaya tombol public muncul

// ── Tree: toggle expand/collapse ──────────────────────────────
function toggleNode(id){
    const ch=el('tc-'+id), tg=el('tg-'+id);
    if(!ch||!tg)return;
    const collapsed=ch.classList.toggle('collapsed');
    tg.classList.toggle('open',!collapsed);
}

// ── Switch path ───────────────────────────────────────────────
// ── Reload path aktif (tanpa switch, untuk refresh setelah upload) ─
function reloadPath(){
    fetch(`{{ route('client.files.list') }}?path=${encodeURIComponent(curPath)}`,{headers:{'Accept':'application/json'}})
    .then(r=>r.json())
    .then(d=>{
        allFiles=d.files;
        renderFiles(d.files);
        el('ib-count').innerHTML=`<i class="fas fa-file" style="margin-right:3px"></i>${d.total} file`;
        el('ib-size').textContent=d.total_size;
        const tn=el('tn-'+curPath.replace(/\//g,'-'));
        const cnt=tn?.querySelector('.ti-cnt');
        if(cnt)cnt.textContent=d.total;
        else if(d.total>0){const lbl=tn?.querySelector('.ti-label');if(lbl){const s=document.createElement('span');s.className='ti-cnt';s.textContent=d.total;lbl.appendChild(s);}}
    })
    .catch(()=>showToast('error','❌ Gagal memuat file.'));
}

function switchPath(path){
    if(path===curPath)return;
    curPath=path;
    el('search-inp').value='';

    // Update sidebar active
    qsa('.tree-item').forEach(i=>i.classList.remove('active'));
    const tn=el('tn-'+path.replace(/\//g,'-'));
    if(tn)tn.querySelector('.tree-item')?.classList.add('active');

    renderBreadcrumb(path);
    el('ib-path').textContent='/'+path;
    el('up-path-lbl').textContent='/'+path;

    // Loading
    const loadHtml=`<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted)"><i class="fas fa-circle-notch spin" style="font-size:20px;color:var(--accent);display:block;margin-bottom:7px"></i><span style="font-size:11px">Memuat...</span></div>`;
    if(curView==='grid')el('fm-grid').innerHTML=loadHtml;

    fetch(`{{ route('client.files.list') }}?path=${encodeURIComponent(path)}`,{headers:{'Accept':'application/json'}})
    .then(r=>r.json())
    .then(d=>{
        allFiles=d.files;
        renderFiles(d.files);
        el('ib-count').innerHTML=`<i class="fas fa-file" style="margin-right:3px"></i>${d.total} file`;
        el('ib-size').textContent=d.total_size;
        // Update count badge in sidebar
        const cnt=tn?.querySelector('.ti-cnt');
        if(cnt)cnt.textContent=d.total;
        else if(d.total>0){
            const lbl=tn?.querySelector('.ti-label');
            if(lbl){const s=document.createElement('span');s.className='ti-cnt';s.textContent=d.total;lbl.appendChild(s);}
        }
    })
    .catch(()=>showToast('error','❌ Gagal memuat file.'));
}

// ── Breadcrumb ────────────────────────────────────────────────
function renderBreadcrumb(path){
    const parts=path.split('/');
    let html=`<span class="bc-item" onclick="switchPath('meta')" style="font-size:11px"><i class="fas fa-hdd"></i></span>`;
    let built='';
    parts.forEach((p,i)=>{
        built=built?built+'/'+p:p;
        const isCur=i===parts.length-1;
        const snap=built;
        if(isCur){
            html+=`<span class="bc-sep"><i class="fas fa-chevron-right"></i></span><span class="bc-item cur">${p}</span>`;
        } else {
            html+=`<span class="bc-sep"><i class="fas fa-chevron-right"></i></span><span class="bc-item" onclick="switchPath('${snap}')">${p}</span>`;
        }
    });
    el('fm-breadcrumb').innerHTML=html;
}

// ── Render files ──────────────────────────────────────────────
function renderFiles(files){
    if(curView==='grid'){
        if(!files.length){
            el('fm-grid').innerHTML=`<div style="grid-column:1/-1"><div class="fm-empty"><i class="fas fa-folder-open"></i><p>Folder ini kosong.</p><button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button></div></div>`;
            return;
        }
        el('fm-grid').innerHTML=files.map(f=>`
        <div class="fc t-${f.type}" onclick="selectCard(this)">
            <div class="fc-thumb"><span>${TICON[f.type]||'📁'}</span><span class="fc-ext" style="background:${TCOLOR[f.type]||'#555'}">${f.extension||f.type}</span></div>
            <div class="fc-name" title="${e(f.original_name)}">${e(f.original_name)}</div>
            <div class="fc-size">${f.human_size}</div>
            <div class="fc-actions">
                <button class="fa-btn ${f.is_public?'pub-on':''}" title="${f.is_public?'Salin Link Publik':'Buat Link Publik'}" onclick="event.stopPropagation();togglePublic(${f.id},this)"><i class="fas fa-${f.is_public?'link':'lock'}"></i></button>
                <a href="${f.download_url}" class="fa-btn" title="Download" onclick="event.stopPropagation()"><i class="fas fa-download"></i></a>
                <button class="fa-btn" title="Edit" onclick="event.stopPropagation();openRename(${f.id},'${e(f.original_name)}','${e(f.description||'')}')"><i class="fas fa-pen"></i></button>
                <button class="fa-btn del" title="Hapus" onclick="event.stopPropagation();deleteFile(${f.id},this)"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>`).join('');
    } else {
        el('fm-list-body').innerHTML=files.length?files.map(f=>`
        <div class="fl-row">
            <div class="fl-ico t-${f.type}" style="font-size:13px;text-align:center">${TICON[f.type]||'📁'}</div>
            <div><div class="fl-name" title="${e(f.original_name)}">${e(f.original_name)}</div>${f.description?`<div class="fl-meta" style="margin-top:1px">${e(f.description)}</div>`:''}</div>
            <div class="fl-meta">${f.human_size}</div>
            <div><span style="background:rgba(255,255,255,.05);color:${TCOLOR[f.type]||'var(--text-muted)'};padding:1px 5px;border-radius:3px;font-size:9.5px;font-weight:600">${f.extension||f.type}</span></div>
            <div class="fl-meta">${f.created_at}</div>
            <div class="fl-actions">
                <button class="fa-btn ${f.is_public?'pub-on':''}" title="${f.is_public?'Salin Link Publik':'Buat Link Publik'}" onclick="togglePublic(${f.id},this)"><i class="fas fa-${f.is_public?'link':'lock'}"></i></button>
                <a href="${f.download_url}" class="fa-btn" title="Download"><i class="fas fa-download"></i></a>
                <button class="fa-btn" title="Edit" onclick="openRename(${f.id},'${e(f.original_name)}','${e(f.description||'')}')"><i class="fas fa-pen"></i></button>
                <button class="fa-btn del" title="Hapus" onclick="deleteFile(${f.id},this)"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>`).join(''):`<div class="fm-empty"><i class="fas fa-folder-open"></i><p>Folder ini kosong.</p><button class="btn-up" onclick="openUploadModal()"><i class="fas fa-upload"></i> Upload</button></div>`;
    }
}
function selectCard(c){qsa('.fc').forEach(x=>x.classList.remove('selected'));c.classList.add('selected');}

// ── View toggle ───────────────────────────────────────────────
function setView(v){
    curView=v;
    el('fm-grid').style.display=v==='grid'?'grid':'none';
    el('fm-list').style.display=v==='list'?'flex':'none';
    el('btn-grid').classList.toggle('active',v==='grid');
    el('btn-list').classList.toggle('active',v==='list');
    renderFiles(allFiles);
}

// ── Search ────────────────────────────────────────────────────
function searchFiles(q){
    const f=q.trim()?allFiles.filter(x=>x.original_name.toLowerCase().includes(q.toLowerCase())):allFiles;
    renderFiles(f);
    el('ib-count').innerHTML=`<i class="fas fa-file" style="margin-right:3px"></i>${f.length} file${q?' (filter)':''}`;
}

// ── Upload ────────────────────────────────────────────────────
function openUploadModal(){el('upload-panel').style.display='block';el('up-path-lbl').textContent='/'+curPath;pendingFiles=[];el('upload-list').innerHTML='';el('btn-do-upload').disabled=true;}
function closeUploadModal(){el('upload-panel').style.display='none';pendingFiles=[];el('upload-list').innerHTML='';}
function handleFileSelect(files){addFiles(Array.from(files));}
function handleDrop(ev){ev.preventDefault();el('dropzone').classList.remove('dragover');addFiles(Array.from(ev.dataTransfer.files));}
function addFiles(files){pendingFiles=[...pendingFiles,...files];renderUpList();el('btn-do-upload').disabled=pendingFiles.length===0;}
function renderUpList(){
    el('upload-list').innerHTML=pendingFiles.map((f,i)=>{
        const tp=f.type.startsWith('video')?'video':f.type.startsWith('image')?'image':f.type.includes('pdf')?'pdf':'file';
        const sz=f.size<1048576?(f.size/1024).toFixed(1)+' KB':(f.size/1048576).toFixed(1)+' MB';
        return `<div class="ui-item"><span class="ui-ico">${TICON[tp]}</span><div class="ui-info"><div class="ui-name">${e(f.name)}</div><div class="ui-sz">${sz}</div><div class="ui-prog"><div class="ui-prog-fill" id="up-${i}"></div></div></div><span class="ui-st" id="us-${i}" style="color:var(--text-muted)"><i class="fas fa-clock" style="font-size:10px"></i></span></div>`;
    }).join('');
}
function doUpload(){
    if(!pendingFiles.length)return;
    const btn=el('btn-do-upload');
    btn.innerHTML='<i class="fas fa-circle-notch spin"></i> Uploading...';btn.disabled=true;
    const fd=new FormData();fd.append('path',curPath);fd.append('_token','{{ csrf_token() }}');
    pendingFiles.forEach(f=>fd.append('files[]',f));
    const xhr=new XMLHttpRequest();
    xhr.upload.addEventListener('progress',ev=>{if(ev.lengthComputable){const p=Math.round(ev.loaded/ev.total*100);pendingFiles.forEach((_,i)=>{const pb=el('up-'+i);if(pb)pb.style.width=p+'%';});}});
    xhr.onload=function(){
        try{
            if(xhr.status===422){
                const err=JSON.parse(xhr.responseText);
                const msg=err.message||(err.errors?Object.values(err.errors).flat().join(', '):'Validasi gagal');
                showToast('error','❌ '+msg);
                btn.innerHTML='<i class="fas fa-upload"></i> Upload';btn.disabled=false;
                return;
            }
            if(xhr.status!==200&&xhr.status!==201){
                showToast('error','❌ Server error ('+xhr.status+')');
                btn.innerHTML='<i class="fas fa-upload"></i> Upload';btn.disabled=false;
                return;
            }
            console.log('STATUS:', xhr.status);
            console.log('RESPONSE:', xhr.responseText.substring(0,500));
            const d=JSON.parse(xhr.responseText);
            console.log('PARSED:', d);
            if(d.success){
                console.log('SUCCESS - closing modal');
                closeUploadModal();
                showToast('success','✅ '+d.message);
                reloadPath();
            } else {
                showToast('error','❌ '+(d.message||'Gagal'));
                btn.innerHTML='<i class="fas fa-upload"></i> Upload';btn.disabled=false;
            }
        } catch(ex){
            console.error('Upload parse error:',ex,xhr.responseText.substring(0,300));
            showToast('error','❌ Response error. Cek console.');
            btn.innerHTML='<i class="fas fa-upload"></i> Upload';btn.disabled=false;
        }
    };
    xhr.onerror=()=>{showToast('error','❌ Koneksi gagal.');btn.innerHTML='<i class="fas fa-upload"></i> Upload';btn.disabled=false;};
    xhr.open('POST','{{ route("client.files.upload") }}');xhr.send(fd);
}

// ── Delete ────────────────────────────────────────────────────
function deleteFile(id,btn){
    if(!confirm('Hapus file ini?'))return;
    const oh=btn.innerHTML;btn.innerHTML='<i class="fas fa-circle-notch spin"></i>';btn.disabled=true;
    fetch(`{{ url('client-area/files/delete') }}/${id}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
    .then(r=>r.json())
    .then(d=>{
        if(d.success){showToast('success','🗑️ Dihapus.');allFiles=allFiles.filter(f=>f.id!==id);renderFiles(allFiles);el('ib-count').innerHTML=`<i class="fas fa-file" style="margin-right:3px"></i>${allFiles.length} file`;}
        else{showToast('error','❌ Gagal.');btn.innerHTML=oh;btn.disabled=false;}
    }).catch(()=>{showToast('error','❌ Error.');btn.innerHTML=oh;btn.disabled=false;});
}

// ── Rename ────────────────────────────────────────────────────
function openRename(id,name,desc){el('rn-id').value=id;el('rn-name').value=name;el('rn-desc').value=desc||'';el('rename-modal').style.display='flex';setTimeout(()=>el('rn-name').select(),80);}
function closeRenameModal(){el('rename-modal').style.display='none';}
function doRename(){
    const id=el('rn-id').value,name=el('rn-name').value.trim(),desc=el('rn-desc').value.trim();
    if(!name)return;
    fetch(`{{ url('client-area/files/update') }}/${id}`,{method:'PATCH',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({original_name:name,description:desc})})
    .then(r=>r.json())
    .then(d=>{if(d.success){showToast('success','✅ Disimpan.');closeRenameModal();const idx=allFiles.findIndex(f=>f.id==id);if(idx>-1){allFiles[idx].original_name=name;allFiles[idx].description=desc;}renderFiles(allFiles);}else showToast('error','❌ Gagal.');}).catch(()=>showToast('error','❌ Error.'));
}

// ── New Folder ────────────────────────────────────────────────
function openNewFolderModal(parent){
    parent=parent||curPath;
    el('nf-parent').value=parent;
    el('nf-parent-display').textContent='/'+parent;
    el('nf-name').value='';
    el('nf-modal').style.display='flex';
    setTimeout(()=>el('nf-name').focus(),80);
}
function closeNewFolderModal(){el('nf-modal').style.display='none';}
function doCreateFolder(){
    const parent=el('nf-parent').value;
    const name=el('nf-name').value.trim().toLowerCase();
    if(!name)return;
    fetch('{{ route("client.files.folder.create") }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({parent,name})})
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            showToast('success','📁 '+d.message);
            closeNewFolderModal();
            // Inject node baru ke tree tanpa reload
            injectTreeNode(d.parent, d.name, d.new_path);
        } else showToast('error','❌ '+d.message);
    }).catch(()=>showToast('error','❌ Error.'));
}

// ── Inject tree node dinamis ──────────────────────────────────
function injectTreeNode(parentPath, name, newPath){
    const parentId = parentPath.replace(/\//g,'-');
    let childrenEl = el('tc-'+parentId);

    // Jika parent belum punya children container, buat
    if(!childrenEl){
        const parentNode = el('tn-'+parentId);
        if(!parentNode)return;
        // Update toggle dari leaf ke expandable
        const tg=el('tg-'+parentId);
        if(tg){tg.classList.remove('leaf');tg.classList.add('open');}
        childrenEl=document.createElement('div');
        childrenEl.className='tree-children';
        childrenEl.id='tc-'+parentId;
        childrenEl.style.maxHeight='999px';
        parentNode.appendChild(childrenEl);
    } else {
        // Auto-expand parent
        childrenEl.classList.remove('collapsed');
        const tg=el('tg-'+parentId);if(tg)tg.classList.add('open');
    }

    const newId=newPath.replace(/\//g,'-');
    const depth=(newPath.match(/\//g)||[]).length;
    const indent=depth*13;
    const div=document.createElement('div');
    div.className='tree-node';div.id='tn-'+newId;
    div.innerHTML=`<div class="tree-item" style="padding-left:${indent}px" onclick="switchPath('${newPath}')">
        <span class="ti-toggle leaf" id="tg-${newId}" onclick="event.stopPropagation();toggleNode('${newId}')"><i class="fas fa-chevron-right"></i></span>
        <i class="fas fa-folder ti-icon"></i>
        <div class="ti-label"><span class="ti-name">${name}</span></div>
        <button class="ti-add" onclick="event.stopPropagation();openNewFolderModal('${newPath}')" title="Tambah subfolder"><i class="fas fa-plus"></i></button>
    </div>`;
    childrenEl.appendChild(div);
    // Switch ke folder baru
    switchPath(newPath);
}

// ── Public URL ───────────────────────────────────────────────
function togglePublic(id, btn){
    const oh=btn.innerHTML;
    btn.innerHTML='<i class="fas fa-circle-notch spin"></i>';btn.disabled=true;
    fetch(`{{ url('client-area/files/toggle-public') }}/${id}`,{
        method:'POST',
        headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            // Update allFiles state
            const idx=allFiles.findIndex(f=>f.id==id);
            if(idx>-1){allFiles[idx].is_public=d.is_public;allFiles[idx].public_url=d.public_url;}
            renderFiles(allFiles);
            if(d.is_public && d.public_url){
                openPublicModal(d.public_url, allFiles.find(f=>f.id==id)?.original_name||'');
            } else {
                showToast('success','🔒 Akses publik dicabut.');
            }
        } else {showToast('error','❌ Gagal.');btn.innerHTML=oh;btn.disabled=false;}
    })
    .catch(()=>{showToast('error','❌ Error.');btn.innerHTML=oh;btn.disabled=false;});
}

function openPublicModal(url, name){
    el('pub-url-input').value=url;
    el('pub-filename').textContent=name;
    el('pub-modal').style.display='flex';
}
function closePublicModal(){el('pub-modal').style.display='none';}
function copyPublicUrl(){
    const inp=el('pub-url-input');
    inp.select();inp.setSelectionRange(0,99999);
    navigator.clipboard?.writeText(inp.value).then(()=>{
        showToast('success','✅ Link disalin!');
    }).catch(()=>{
        document.execCommand('copy');
        showToast('success','✅ Link disalin!');
    });
}

// ── Keys ──────────────────────────────────────────────────────
document.addEventListener('keydown',ev=>{
    if(ev.key==='Escape'){closeUploadModal();closeRenameModal();closeNewFolderModal();}
    if((ev.ctrlKey||ev.metaKey)&&ev.key==='u'){ev.preventDefault();openUploadModal();}
});

function e(s){const d=document.createElement('div');d.appendChild(document.createTextNode(String(s||'')));return d.innerHTML;}
let tT;
function showToast(type,msg){const t=el('toast');t.className='toast '+type+' show';el('toast-msg').textContent=msg;clearTimeout(tT);tT=setTimeout(()=>t.classList.remove('show'),2800);}
</script>
{{-- ── PUBLIC URL MODAL ── --}}
<div class="modal-ov" id="pub-modal" style="display:none" onclick="if(event.target===this)closePublicModal()">
    <div class="mini-modal" style="max-width:420px">
        <div class="modal-title">
            <span><i class="fas fa-link" style="color:var(--accent);margin-right:5px"></i>Link Publik Aktif</span>
            <button class="modal-close" onclick="closePublicModal()"><i class="fas fa-times"></i></button>
        </div>
        <div style="font-size:10.5px;color:var(--text-muted);margin-bottom:8px">
            File <strong id="pub-filename" style="color:var(--text-primary)"></strong> sekarang bisa diakses publik via link ini:
        </div>
        <div style="display:flex;gap:6px;align-items:center">
            <input type="text" id="pub-url-input" readonly
                style="flex:1;padding:7px 10px;border-radius:5px;border:1px solid var(--border);background:var(--bg-main);color:var(--accent);font-size:10px;font-family:'Space Mono',monospace;outline:none;cursor:text;min-width:0">
            <button onclick="copyPublicUrl()"
                style="padding:7px 12px;border-radius:5px;border:none;background:var(--accent);color:#000;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0">
                <i class="fas fa-copy"></i> Salin
            </button>
        </div>
        <div style="margin-top:10px;padding:8px 10px;background:rgba(255,170,0,.06);border:1px solid rgba(255,170,0,.15);border-radius:5px;font-size:10px;color:#F59E0B">
            <i class="fas fa-exclamation-triangle" style="margin-right:4px"></i>
            Link ini bisa diakses siapa saja tanpa login. Cabut akses kapan saja dengan klik tombol 🔗 di file.
        </div>
        <div class="modal-footer" style="margin-top:10px">
            <button class="btn-cancel" onclick="closePublicModal()">Tutup</button>
            <button class="btn-submit" onclick="copyPublicUrl();closePublicModal()"><i class="fas fa-copy"></i> Salin & Tutup</button>
        </div>
    </div>
</div>

@endsection
