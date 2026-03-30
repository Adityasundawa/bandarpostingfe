@extends('layouts.app')

@section('page-title', 'Meta Panel')
@section('breadcrumb', 'Auto Posting / Meta Panel')

@section('content')

<div id="meta-loader" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:var(--bg-main);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;transition:opacity 0.6s ease;">
    <div style="position:relative;margin-bottom:24px;">
        <div class="pulse-ring"></div>
        <i class="fab fa-facebook" style="font-size:64px;color:#1877F2;position:relative;z-index:2;"></i>
    </div>
    <h3 id="loader-title" style="color:var(--text-primary);font-size:20px;font-weight:700;margin-bottom:8px;">Menghubungkan ke Bot Meta...</h3>
    <p id="loader-desc" style="color:var(--text-muted);font-size:14px;max-width:400px;text-align:center;line-height:1.5;">Memverifikasi sesi dan token Anda.</p>
</div>

<style>
@keyframes pulse{0%{transform:scale(.8);opacity:.8}100%{transform:scale(1.8);opacity:0}}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
.pulse-ring{position:absolute;top:50%;left:50%;width:64px;height:64px;margin:-32px 0 0 -32px;background:#1877F2;border-radius:50%;animation:pulse 1.5s infinite cubic-bezier(.215,.61,.355,1);z-index:1}
.spin{animation:spin 1s linear infinite;display:inline-block}

/* Wizard */
.step-wizard{display:flex;align-items:center;background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:18px 28px;margin-bottom:24px;overflow-x:auto;gap:0}
.step-item{display:flex;align-items:center;gap:10px;flex-shrink:0;opacity:.3;transition:opacity .3s}
.step-item.active{opacity:1}.step-item.done{opacity:.65}
.step-num{width:30px;height:30px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--text-muted);background:var(--bg-main);transition:all .3s;flex-shrink:0}
.step-item.active .step-num{background:#1877F2;border-color:#1877F2;color:#fff;box-shadow:0 0 0 4px rgba(24,119,242,.2)}
.step-item.done .step-num{background:var(--accent);border-color:var(--accent);color:#000}
.step-label{font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap}
.step-sub{font-size:11px;color:var(--text-muted);white-space:nowrap}
.step-conn{flex:1;min-width:28px;height:2px;background:var(--border);margin:0 8px;flex-shrink:0;position:relative;overflow:hidden}
.step-conn.active::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,#1877F2,transparent);background-size:200% 100%;animation:shimmer 1.5s infinite}
.step-conn.done{background:#1877F2}

/* Panels */
.step-panel{display:none;animation:fadeUp .35s ease}.step-panel.active{display:block}

/* Session card */
.sesi-card{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:var(--bg-main);border:2px solid var(--border);border-radius:12px;transition:all .2s}
.sesi-card.available{cursor:pointer}.sesi-card.available:hover{border-color:#1877F2;background:rgba(24,119,242,.04)}

/* Asset card */
.asset-card-sel{display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--bg-main);border:2px solid var(--border);border-radius:12px;cursor:pointer;transition:all .2s}
.asset-card-sel:hover{border-color:#1877F2;background:rgba(24,119,242,.04)}
.asset-avatar{width:40px;height:40px;border-radius:50%;background:rgba(24,119,242,.12);border:2px solid rgba(24,119,242,.25);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#1877F2;flex-shrink:0;overflow:hidden}
.asset-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}

/* Trail */
.trail{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:10px 16px;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;margin-bottom:20px;font-size:13px}
.trail-chip{display:flex;align-items:center;gap:6px;background:rgba(24,119,242,.1);border:1px solid rgba(24,119,242,.25);color:#1877F2;padding:3px 12px;border-radius:20px;font-weight:600;font-size:12px;cursor:pointer;transition:background .2s}
.trail-chip:hover{background:rgba(24,119,242,.2)}
.trail-sep{color:var(--text-muted);font-size:11px}.trail-cur{color:var(--text-muted);font-size:12px}

/* Asset banner */
.asset-banner{display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(24,119,242,.06);border:1px solid rgba(24,119,242,.2);border-radius:10px;margin-bottom:18px}

/* Form */
.f-label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text-primary)}
.f-input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-size:13px;transition:border-color .2s;box-sizing:border-box}
.f-input:focus{outline:none;border-color:#1877F2;box-shadow:0 0 0 3px rgba(24,119,242,.12)}

/* Posts card */
.posts-card{background:var(--bg-card);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column}
.posts-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px}
.stat-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.stat-pill.scheduled{background:rgba(245,158,11,.1);color:#F59E0B;border:1px solid rgba(245,158,11,.25)}
.stat-pill.published{background:rgba(16,185,129,.1);color:#10B981;border:1px solid rgba(16,185,129,.25)}
.stat-pill.failed{background:rgba(239,68,68,.1);color:#EF4444;border:1px solid rgba(239,68,68,.25)}

/* Tabs */
.tab-filters{display:flex;gap:6px;padding:12px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap}
.tab-btn{padding:4px 12px;border-radius:6px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-muted);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.tab-btn.active{background:rgba(24,119,242,.12);border-color:rgba(24,119,242,.3);color:#1877F2}
.tab-btn:hover:not(.active){border-color:#1877F2;color:#1877F2}

/* Post row */
.post-row{display:flex;align-items:center;gap:12px;padding:13px 20px;border-bottom:1px solid var(--border);transition:background .15s}
.post-row:last-child{border-bottom:none}
.post-row:hover{background:rgba(255,255,255,.02)}
.post-info{flex:1;min-width:0}
.post-title{font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px}
.post-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.post-status-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600}
.post-status-badge.scheduled{background:rgba(245,158,11,.1);color:#F59E0B}
.post-status-badge.published{background:rgba(16,185,129,.1);color:#10B981}
.post-status-badge.failed{background:rgba(239,68,68,.1);color:#EF4444}

/* Metrics */
.post-metrics{display:flex;align-items:center;gap:12px;flex-shrink:0}
.metric-item{display:flex;align-items:center;gap:4px;font-size:12px;color:var(--text-muted);min-width:28px}
.metric-item i{font-size:11px}
.metric-item span{font-weight:600;color:var(--text-primary)}

/* Link btn */
.post-link-btn{width:30px;height:30px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);border:1px solid var(--border);border-radius:6px;font-size:12px;flex-shrink:0;transition:all .2s;text-decoration:none}
.post-link-btn:hover{color:#1877F2;border-color:#1877F2}

/* Sync btn */
.btn-sync-posts{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.35);color:#a855f7;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;white-space:nowrap}
.btn-sync-posts:hover{background:rgba(168,85,247,.2)}
.btn-sync-posts:disabled{opacity:.5;cursor:not-allowed}

/* Loading/empty */
.loading-state{text-align:center;padding:36px 20px;color:var(--text-muted)}
.loading-state i{display:block;margin-bottom:10px}
.empty-state{text-align:center;padding:40px 20px;color:var(--text-muted)}
.empty-state i{font-size:32px;opacity:.3;display:block;margin-bottom:10px}

/* Toast */
.toast{position:fixed;bottom:24px;right:24px;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:12px 18px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);z-index:99999;font-size:13px;max-width:360px;transform:translateY(80px);opacity:0;transition:all .3s ease}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{border-color:var(--accent)}.toast.error{border-color:#EF4444}.toast.warning{border-color:#F59E0B}

@media(max-width:600px){.post-metrics{display:none}}
</style>

{{-- WIZARD --}}
<div class="step-wizard">
    <div class="step-item active" id="wi-1"><div class="step-num">1</div><div><div class="step-label">Pilih Sesi</div><div class="step-sub">Akun Facebook</div></div></div>
    <div class="step-conn" id="wc-1"></div>
    <div class="step-item" id="wi-2"><div class="step-num">2</div><div><div class="step-label">Pilih Asset ID</div><div class="step-sub">Facebook Page</div></div></div>
    <div class="step-conn" id="wc-2"></div>
    <div class="step-item" id="wi-3"><div class="step-num">3</div><div><div class="step-label">Konten & Jadwal</div><div class="step-sub">Form & pantau</div></div></div>
</div>

{{-- ===== STEP 1: PILIH SESI ===== --}}
<div class="step-panel active" id="step-1">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:24px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
            <div>
                <div style="font-size:17px;font-weight:700;color:var(--text-primary);margin-bottom:4px"><i class="fas fa-user-circle" style="color:#1877F2;margin-right:8px"></i>Pilih Sesi Akun Facebook</div>
                <div style="font-size:13px;color:var(--text-muted)">Token: <code style="background:var(--bg-main);padding:2px 8px;border-radius:4px;border:1px solid var(--border);font-size:12px">{{ substr($tokenData->token??'',0,12) }}...</code></div>
            </div>
        </div>
        @if(isset($activeSessions) && count($activeSessions)>0)
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">
            @foreach($activeSessions as $session)
            @php $ok = isset($session['status']) && $session['status']==='available'; @endphp
            <div id="sesi-card-{{ $session['sessionName'] }}" class="sesi-card {{ $ok?'available':'' }}"
                 @if($ok) onclick="pilihSesi('{{ $session['sessionName'] }}')" @endif>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="width:42px;height:42px;border-radius:50%;background:{{ $ok?'rgba(24,119,242,.12)':'rgba(255,255,255,.04)' }};border:2px solid {{ $ok?'rgba(24,119,242,.3)':'var(--border)' }};display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-user-circle" style="font-size:22px;color:{{ $ok?'#1877F2':'var(--text-muted)' }}"></i>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--text-primary)">{{ $session['sessionName'] }}</div>
                        @if($ok)
                            <div style="font-size:12px;color:#10B981;font-weight:600;margin-top:3px"><i class="fas fa-check-circle"></i> Logged In</div>
                        @elseif(($session['status']??'')==='not_found')
                            <div style="font-size:12px;color:#EF4444;font-weight:600;margin-top:3px"><i class="fas fa-times-circle"></i> Butuh Login</div>
                        @else
                            <div style="font-size:12px;color:#F59E0B;font-weight:600;margin-top:3px"><i class="fas fa-exclamation-triangle"></i> {{ ucfirst($session['status']??'') }}</div>
                        @endif
                    </div>
                </div>
                @if($ok)
                    <i class="fas fa-chevron-right" style="color:#1877F2;opacity:.5"></i>
                @else
                    <button onclick="openCookieModal('{{ $session['sessionName'] }}')" style="background:rgba(239,68,68,.1);border:1px solid #EF4444;color:#EF4444;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600"><i class="fas fa-cookie-bite"></i> Login</button>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:36px;color:var(--text-muted);background:var(--bg-main);border-radius:8px">
            <i class="fas fa-folder-open" style="font-size:32px;opacity:.4;display:block;margin-bottom:12px"></i>Belum ada sesi terdaftar.
        </div>
        @endif
    </div>
</div>

{{-- ===== STEP 2: PILIH ASSET ===== --}}
<div class="step-panel" id="step-2">
    <div class="trail">
        <div class="trail-chip" onclick="backToStep(1)"><i class="fas fa-user-circle"></i><span id="t2-session">—</span></div>
        <span class="trail-sep"><i class="fas fa-chevron-right"></i></span>
        <span class="trail-cur"><i class="fas fa-id-card" style="margin-right:4px"></i>Pilih Asset ID</span>
    </div>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:24px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
            <div>
                <div style="font-size:17px;font-weight:700;color:var(--text-primary);margin-bottom:4px"><i class="fab fa-facebook" style="color:#1877F2;margin-right:8px"></i>Pilih Facebook Page</div>
                <div style="font-size:13px;color:var(--text-muted)">Klik page untuk lanjut ke form jadwal.</div>
            </div>
            <button id="btn-sync-asset" style="display:inline-flex;align-items:center;gap:7px;padding:7px 14px;background:rgba(245,158,11,.1);border:1px solid #F59E0B;color:#F59E0B;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap" onclick="syncAssets()">
                <i class="fas fa-sync-alt"></i> Sync Page
            </button>
        </div>
        <div id="asset-loading" class="loading-state" style="display:none"><i class="fas fa-circle-notch spin" style="font-size:26px;color:#1877F2"></i>Memuat daftar page...</div>
        <div id="asset-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:12px"></div>
        <div id="asset-empty" class="empty-state" style="display:none">
            <i class="fab fa-facebook"></i>
            <p style="margin:0 0 14px;font-size:14px">Belum ada page tersimpan.</p>
            <button style="display:inline-flex;align-items:center;gap:7px;padding:7px 14px;background:rgba(245,158,11,.1);border:1px solid #F59E0B;color:#F59E0B;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer" onclick="syncAssets()"><i class="fas fa-sync-alt"></i> Deteksi Sekarang</button>
        </div>
    </div>
</div>

{{-- ===== STEP 3: FORM + KONTEN ===== --}}
<div class="step-panel" id="step-3">
    <div class="trail">
        <div class="trail-chip" onclick="backToStep(1)"><i class="fas fa-user-circle"></i><span id="t3-session">—</span></div>
        <span class="trail-sep"><i class="fas fa-chevron-right"></i></span>
        <div class="trail-chip" onclick="backToStep(2)"><i class="fas fa-id-card"></i><span id="t3-asset">—</span></div>
        <span class="trail-sep"><i class="fas fa-chevron-right"></i></span>
        <span class="trail-cur"><i class="fas fa-calendar-alt" style="margin-right:4px"></i>Konten & Jadwal</span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:24px">

        {{-- FORM KIRI --}}
        <div>
            <div class="asset-banner">
                <div class="asset-avatar" id="b-avatar" style="width:38px;height:38px;font-size:14px">P</div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:700;color:var(--text-primary)" id="b-name">Page</div>
                    <div style="font-family:'Space Mono',monospace;font-size:11px;color:var(--accent)" id="b-assetid">000</div>
                </div>
                <button onclick="backToStep(2)" style="background:none;border:1px solid var(--border);color:var(--text-muted);padding:4px 10px;border-radius:6px;font-size:11px;cursor:pointer"><i class="fas fa-exchange-alt"></i> Ganti</button>
            </div>

            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:22px">
                <div style="font-size:15px;font-weight:700;color:var(--text-primary);margin-bottom:18px"><i class="fas fa-calendar-plus" style="color:#1877F2;margin-right:8px"></i>Form Jadwal Konten</div>
                <form id="form-schedule">
                    <input type="hidden" id="form-session-name" name="sessionName">
                    <input type="hidden" id="form-asset-id" name="assetId">
                    <div style="margin-bottom:14px">
                        <label class="f-label">Path File Server (Video/Gambar)</label>
                        <input type="text" name="filePath" placeholder="C:/bahan/video1.mp4" class="f-input" style="font-family:'Space Mono',monospace">
                        <small style="color:var(--text-muted);font-size:11px;margin-top:3px;display:block">File harus sudah berada di PC/Server bot.</small>
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:14px">
                        <div><label class="f-label">Tanggal (DD/MM/YYYY)</label><input type="text" name="date" placeholder="28/02/2026" class="f-input"></div>
                        <div><label class="f-label">Jam (0-23)</label><input type="number" name="hour" min="0" max="23" placeholder="10" class="f-input"></div>
                    </div>
                    <div style="margin-bottom:20px">
                        <label class="f-label">Caption & Hashtag</label>
                        <textarea name="caption" rows="4" placeholder="Tulis caption...&#10;#reels #viral" class="f-input" style="resize:vertical"></textarea>
                    </div>
                    <button type="button" style="width:100%;background:#1877F2;color:#fff;border:none;padding:13px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;justify-content:center;align-items:center;gap:8px" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-paper-plane"></i> Tambahkan ke Antrean
                    </button>
                </form>
            </div>
        </div>

        {{-- KONTEN TERJADWAL KANAN --}}
        <div class="posts-card">
            <div class="posts-header">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <div style="font-size:15px;font-weight:700;color:var(--text-primary)"><i class="fas fa-film" style="color:#a855f7;margin-right:8px"></i>Konten di Page Ini</div>
                    <span class="stat-pill scheduled"><i class="fas fa-clock"></i> <span id="cnt-scheduled">0</span> Scheduled</span>
                    <span class="stat-pill published"><i class="fas fa-check"></i> <span id="cnt-published">0</span> Published</span>
                    <span class="stat-pill failed" id="pill-failed" style="display:none"><i class="fas fa-times"></i> <span id="cnt-failed">0</span> Failed</span>
                </div>
                <button class="btn-sync-posts" id="btn-sync-posts" onclick="syncPosts()">
                    <i class="fas fa-sync-alt"></i> Sync dari FB
                </button>
            </div>

            <div class="tab-filters">
                <button class="tab-btn active" id="tab-all"       onclick="filterPosts('all')">Semua</button>
                <button class="tab-btn"         id="tab-scheduled" onclick="filterPosts('scheduled')"><i class="fas fa-clock" style="margin-right:3px"></i>Scheduled</button>
                <button class="tab-btn"         id="tab-published" onclick="filterPosts('published')"><i class="fas fa-check" style="margin-right:3px"></i>Published</button>
            </div>

            {{-- Legend metrics --}}
            <div style="display:flex;align-items:center;gap:16px;padding:8px 20px;border-bottom:1px solid var(--border);font-size:11px;color:var(--text-muted)">
                <span style="flex:1;font-weight:600">Judul Post</span>
                <span style="display:flex;gap:12px;flex-shrink:0">
                    <span title="Reach"><i class="fas fa-eye"></i></span>
                    <span title="Likes"><i class="fas fa-thumbs-up"></i></span>
                    <span title="Comments"><i class="fas fa-comment"></i></span>
                    <span title="Shares"><i class="fas fa-share"></i></span>
                </span>
                <span style="width:30px"></span>
            </div>

            <div id="posts-loading" class="loading-state" style="display:none"><i class="fas fa-circle-notch spin" style="font-size:24px;color:#a855f7"></i>Memuat konten...</div>
            <div id="posts-syncing" class="loading-state" style="display:none;color:#a855f7">
                <i class="fas fa-circle-notch spin" style="font-size:24px"></i>
                Scraping dari Facebook Business...<br>
                <small style="font-size:11px;margin-top:4px;display:block;opacity:.7">(estimasi 10–20 detik)</small>
            </div>
            <div id="posts-empty" class="empty-state" style="display:none">
                <i class="fas fa-photo-video"></i>
                <p style="margin:0 0 14px;font-size:14px">Belum ada konten tersimpan.</p>
                <button class="btn-sync-posts" onclick="syncPosts()"><i class="fas fa-sync-alt"></i> Tarik dari FB</button>
            </div>
            <div id="posts-list" style="overflow-y:auto;max-height:480px"></div>
        </div>
    </div>
</div>

{{-- Cookie Modal --}}
<div id="cookieModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:9998;align-items:center;justify-content:center;padding:20px">
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:28px;width:100%;max-width:480px;position:relative">
        <button onclick="closeCookieModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;color:var(--text-muted);font-size:18px;cursor:pointer"><i class="fas fa-times"></i></button>
        <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:20px" id="cookieModalTitle">Login Sesi</div>
        <input type="hidden" id="cookieSessionName">
        <div id="cookieStep1">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;line-height:1.6">Install ekstensi <strong>Cookie Editor</strong> di browser, buka Facebook, lalu export semua cookies sebagai JSON.</p>
            <button onclick="goToCookieStep2()" style="width:100%;background:#1877F2;color:#fff;border:none;padding:12px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer">Saya Sudah Punya Cookie <i class="fas fa-arrow-right" style="margin-left:6px"></i></button>
        </div>
        <div id="cookieStep2" style="display:none">
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:10px">Paste JSON array cookies dari Cookie Editor:</p>
            <textarea id="cookieInput" rows="7" placeholder='[{"domain":".facebook.com","name":"c_user","value":"..."}]' style="width:100%;padding:12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-main);color:var(--text-primary);font-family:'Space Mono',monospace;font-size:12px;margin-bottom:12px;resize:vertical;box-sizing:border-box"></textarea>
            <div id="cookieMessageArea" style="display:none;padding:10px;border-radius:8px;font-size:13px;margin-bottom:12px"></div>
            <div style="display:flex;gap:10px">
                <button onclick="backToCookieStep1()" style="width:44px;background:var(--bg-main);color:var(--text-primary);border:1px solid var(--border);border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fas fa-arrow-left"></i></button>
                <button id="btnSubmitCookie" onclick="submitCookies()" style="flex:1;background:#1877F2;color:#fff;border:none;padding:12px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer"><i class="fas fa-sign-in-alt" style="margin-right:6px"></i> Inject Cookies & Login</button>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast"><span id="toast-msg" style="color:var(--text-primary)"></span></div>

<script>
const el  = id  => document.getElementById(id);
const qs  = s   => document.querySelector(s);
const qsa = s   => document.querySelectorAll(s);

let curStep = 1, curSession = null, curAsset = null;
let allPosts = [], activeFilter = 'all';

// ── State Persistence (sessionStorage) ───────────────────────
const SS_KEY = 'meta_wizard_state';
function saveState() {
    sessionStorage.setItem(SS_KEY, JSON.stringify({
        step    : curStep,
        session : curSession,
        asset   : curAsset,
        filter  : activeFilter
    }));
}
function loadState() {
    try { return JSON.parse(sessionStorage.getItem(SS_KEY)) || null; } catch(e) { return null; }
}
function clearState() { sessionStorage.removeItem(SS_KEY); }

// ── Restore State setelah refresh ────────────────────────────
function restoreState() {
    const s = loadState();
    if (!s || !s.session) return; // tidak ada state → tetap di step 1

    curSession   = s.session;
    curAsset     = s.asset;
    activeFilter = s.filter || 'all';

    if (s.step >= 2) {
        ['t2-session', 't3-session'].forEach(id => { if (el(id)) el(id).textContent = curSession; });
        if (el('form-session-name')) el('form-session-name').value = curSession;
    }

    if (s.step === 3 && curAsset) {
        if (el('t3-asset'))     el('t3-asset').textContent  = curAsset.page_name || curAsset.asset_id;
        if (el('b-name'))       el('b-name').textContent    = curAsset.page_name || 'Unknown';
        if (el('b-assetid'))    el('b-assetid').textContent = curAsset.asset_id;
        if (el('form-asset-id')) el('form-asset-id').value  = curAsset.asset_id;
        const ini = (curAsset.page_name || 'P').charAt(0).toUpperCase();
        if (el('b-avatar')) el('b-avatar').innerHTML = curAsset.picture
            ? `<img src="${curAsset.picture}" style="width:100%;height:100%;object-fit:cover;border-radius:50%" onerror="this.parentElement.textContent='${ini}'">`
            : ini;
        // Restore filter tab
        qsa('.tab-btn').forEach(b => b.classList.remove('active'));
        if (el('tab-' + activeFilter)) el('tab-' + activeFilter).classList.add('active');
        goToStep(3);
        loadPosts();
    } else if (s.step === 2) {
        goToStep(2);
        loadAssets(curSession);
    }
}

// ── Loader ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetch('{{ route("client.meta.verify") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            el('loader-title').innerText = 'Terhubung!';
            el('loader-desc').innerText  = 'Membuka Dashboard...';
            setTimeout(() => {
                const l = el('meta-loader');
                l.style.opacity = '0';
                setTimeout(() => {
                    l.style.display = 'none';
                    restoreState(); // ← restore state setelah loader hilang
                }, 600);
            }, 700);
        } else {
            el('loader-title').innerText = 'Koneksi Ditolak'; el('loader-title').style.color = '#EF4444';
            qs('.fab.fa-facebook').style.color = '#EF4444'; qs('.pulse-ring').style.background = '#EF4444';
            el('loader-desc').innerText = d.message + ' Mengarahkan ulang...'; el('loader-desc').style.color = '#EF4444';
            setTimeout(() => location.reload(), 3500);
        }
    })
    .catch(() => { el('loader-title').innerText = 'Server API Down'; el('loader-title').style.color = '#F59E0B'; qs('.fab.fa-facebook').style.color = '#F59E0B'; qs('.pulse-ring').style.display = 'none'; });
});

// ── Wizard & Steps ────────────────────────────────────────────
function goToStep(n) {
    qsa('.step-panel').forEach(p => p.classList.remove('active'));
    el('step-' + n).classList.add('active');
    curStep = n;
    for (let i = 1; i <= 3; i++) {
        const item = el('wi-' + i), num = item.querySelector('.step-num');
        item.classList.remove('active', 'done');
        if (i < n)  { item.classList.add('done');   num.innerHTML = '<i class="fas fa-check" style="font-size:10px"></i>'; }
        if (i === n){ item.classList.add('active'); num.textContent = i; }
        if (i > n)  { num.textContent = i; }
    }
    for (let i = 1; i <= 2; i++) {
        const c = el('wc-' + i); c.classList.remove('active', 'done');
        if (i < n - 1) c.classList.add('done');
        if (i === n - 1) c.classList.add('active');
    }
    saveState(); // ← simpan setiap pindah step
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function backToStep(n) {
    if (n <= 1) { curSession = null; curAsset = null; allPosts = []; clearState(); }
    if (n <= 2) { curAsset = null; allPosts = []; saveState(); }
    goToStep(n);
}

// ── Step 1 ────────────────────────────────────────────────────
function pilihSesi(name) {
    curSession = name;
    curAsset = null;
    allPosts = [];
    ['t2-session', 't3-session'].forEach(id => el(id).textContent = name);
    el('form-session-name').value = name;
    saveState(); // ← simpan sesi yang dipilih
    goToStep(2);
    loadAssets(name);
}

// ── Step 2: Assets ────────────────────────────────────────────
function loadAssets(session) {
    el('asset-grid').style.display = 'none'; el('asset-empty').style.display = 'none';
    el('asset-loading').style.display = 'block'; el('asset-grid').innerHTML = '';
    fetch(`{{ url('client-area/meta/assets-by-session') }}?session=${encodeURIComponent(session)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => { el('asset-loading').style.display = 'none'; renderAssets(d.assets || []); })
    .catch(() => { el('asset-loading').style.display = 'none'; renderAssets([]); });
}
function renderAssets(assets) {
    const grid = el('asset-grid');
    if (!assets.length) { el('asset-empty').style.display = 'block'; return; }
    grid.style.display = 'grid'; grid.innerHTML = '';
    assets.forEach(a => {
        const ini = (a.page_name || 'P').charAt(0).toUpperCase();
        const card = document.createElement('div');
        card.className = 'asset-card-sel'; card.id = 'acard-' + a.id;
        card.onclick = () => pilihAsset(a);
        card.innerHTML = `
            <div class="asset-avatar">${a.picture ? `<img src="${e(a.picture)}" onerror="this.parentElement.textContent='${ini}'">` : ini}</div>
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${e(a.page_name||'')}">${e(a.page_name||'Unknown')}</div>
                <div style="font-family:'Space Mono',monospace;font-size:11px;color:var(--accent);margin-top:2px">${e(a.asset_id)}</div>
                ${a.category ? `<div style="font-size:11px;color:var(--text-muted)">${e(a.category)}</div>` : ''}
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                <i class="fas fa-chevron-right" style="color:var(--text-muted);font-size:12px"></i>
                <button onclick="event.stopPropagation();deleteAsset(${a.id},this)" style="background:none;border:none;color:#EF4444;cursor:pointer;font-size:11px;padding:2px" title="Hapus"><i class="fas fa-trash-alt"></i></button>
            </div>`;
        grid.appendChild(card);
    });
}
function syncAssets() {
    if (!curSession) return;
    const btn = el('btn-sync-asset');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Menyinkronkan...';
    fetch('{{ route("client.meta.assets.sync") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ session_name: curSession }) })
    .then(r => r.json())
    .then(d => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Page'; if (d.success) { showToast('success', '✅ ' + d.message); loadAssets(curSession); } else showToast('error', '❌ ' + (d.message || 'Sync gagal')); })
    .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync Page'; showToast('error', '❌ Koneksi gagal'); });
}
function deleteAsset(id, btn) {
    if (!confirm('Hapus page ini dari database?')) return;
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-circle-notch spin"></i>';
    fetch(`{{ url('client-area/meta/assets') }}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(r => r.json())
    .then(d => { if (d.success) { const c = el('acard-' + id); c.style.opacity = '0'; c.style.transition = '.3s'; setTimeout(() => { c.remove(); loadAssets(curSession); }, 300); showToast('success', '🗑️ Page dihapus'); } else { showToast('error', '❌ Gagal'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash-alt"></i>'; } })
    .catch(() => { showToast('error', '❌ Koneksi gagal'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash-alt"></i>'; });
}

// ── Step 3: Pilih Asset → Posts ───────────────────────────────
function pilihAsset(asset) {
    curAsset = asset;
    el('t3-asset').textContent  = asset.page_name || asset.asset_id;
    el('b-name').textContent    = asset.page_name || 'Unknown';
    el('b-assetid').textContent = asset.asset_id;
    el('form-asset-id').value   = asset.asset_id;
    const ini = (asset.page_name || 'P').charAt(0).toUpperCase();
    el('b-avatar').innerHTML = asset.picture
        ? `<img src="${asset.picture}" style="width:100%;height:100%;object-fit:cover;border-radius:50%" onerror="this.parentElement.textContent='${ini}'">`
        : ini;
    saveState(); // ← simpan asset yang dipilih
    goToStep(3);
    loadPosts();
}

// ── Posts: Load dari DB ───────────────────────────────────────
function loadPosts() {
    ['posts-empty', 'posts-syncing'].forEach(id => el(id).style.display = 'none');
    el('posts-list').innerHTML = '';
    el('posts-loading').style.display = 'block';
    fetch(`{{ url('client-area/meta/posts-by-asset') }}?session=${encodeURIComponent(curSession)}&asset_id=${encodeURIComponent(curAsset.asset_id)}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => { el('posts-loading').style.display = 'none'; allPosts = d.posts || []; updateStats(d.stats || {}); renderPosts(); })
    .catch(() => { el('posts-loading').style.display = 'none'; renderPosts(); });
}

// ── Posts: Sync dari API ──────────────────────────────────────
function syncPosts() {
    if (!curSession || !curAsset) return;
    const btn = el('btn-sync-posts');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-circle-notch spin"></i> Scraping...';
    ['posts-list', 'posts-empty', 'posts-loading'].forEach(id => { if (id === 'posts-list') el(id).innerHTML = ''; else el(id).style.display = 'none'; });
    el('posts-syncing').style.display = 'block';
    fetch('{{ route("client.meta.posts.sync") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ session_name: curSession, asset_id: curAsset.asset_id }) })
    .then(r => r.json())
    .then(d => {
        el('posts-syncing').style.display = 'none';
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync dari FB';
        if (d.success) { showToast('success', '✅ ' + d.message); loadPosts(); }
        else showToast('error', '❌ ' + (d.message || 'Sync gagal'));
    })
    .catch(() => { el('posts-syncing').style.display = 'none'; btn.disabled = false; btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync dari FB'; showToast('error', '❌ Koneksi gagal'); });
}

// ── Posts: Render ─────────────────────────────────────────────
function renderPosts() {
    const list = el('posts-list');
    list.innerHTML = '';
    const filtered = activeFilter === 'all' ? allPosts : allPosts.filter(p => p.status === activeFilter);
    if (!filtered.length) { el('posts-empty').style.display = 'block'; return; }
    el('posts-empty').style.display = 'none';
    filtered.forEach(p => {
        const sc = p.status === 'scheduled' ? 'scheduled' : p.status === 'published' ? 'published' : 'failed';
        const si = p.status === 'scheduled' ? 'fa-clock' : p.status === 'published' ? 'fa-check-circle' : 'fa-times-circle';
        const reach = parseInt(p.reach || 0), likes = parseInt(p.likes_reactions || 0), cmts = parseInt(p.comments || 0), shr = parseInt(p.shares || 0);
        const row = document.createElement('div');
        row.className = 'post-row';
        row.innerHTML = `
            <div class="post-info">
                <div class="post-title" title="${e(p.title||'')}">${e(p.title || '(Tanpa judul)')}</div>
                <div class="post-meta">
                    <span class="post-status-badge ${sc}"><i class="fas ${si}"></i> ${sc}</span>
                    <span style="font-size:11px;color:var(--text-muted);display:flex;align-items:center;gap:3px"><i class="fas fa-calendar-alt"></i> ${e(p.post_date||'—')}</span>
                </div>
            </div>
            <div class="post-metrics">
                <div class="metric-item" title="Reach"><i class="fas fa-eye"></i><span ${reach===0?'style="color:var(--text-muted);font-weight:400"':''}>${reach}</span></div>
                <div class="metric-item" title="Likes"><i class="fas fa-thumbs-up"></i><span ${likes===0?'style="color:var(--text-muted);font-weight:400"':''}>${likes}</span></div>
                <div class="metric-item" title="Comments"><i class="fas fa-comment"></i><span ${cmts===0?'style="color:var(--text-muted);font-weight:400"':''}>${cmts}</span></div>
                <div class="metric-item" title="Shares"><i class="fas fa-share"></i><span ${shr===0?'style="color:var(--text-muted);font-weight:400"':''}>${shr}</span></div>
            </div>
            ${p.post_url ? `<a href="${e(p.post_url)}" target="_blank" class="post-link-btn" title="Buka di Facebook"><i class="fas fa-external-link-alt"></i></a>` : '<div style="width:30px"></div>'}
        `;
        list.appendChild(row);
    });
}

// ── Stats & Filter ────────────────────────────────────────────
function updateStats(stats) {
    el('cnt-scheduled').textContent = stats.scheduled || 0;
    el('cnt-published').textContent = stats.published || 0;
    el('cnt-failed').textContent    = stats.failed    || 0;
    el('pill-failed').style.display = (stats.failed > 0) ? 'inline-flex' : 'none';
}
function filterPosts(f) {
    activeFilter = f;
    qsa('.tab-btn').forEach(b => b.classList.remove('active'));
    el('tab-' + f).classList.add('active');
    saveState(); // ← simpan filter aktif
    renderPosts();
}

// ── Cookie Modal ──────────────────────────────────────────────
function openCookieModal(n) { event.stopPropagation(); el('cookieSessionName').value=n; el('cookieModalTitle').innerText=`Login Sesi: ${n}`; el('cookieInput').value=''; el('cookieMessageArea').style.display='none'; el('cookieStep1').style.display='block'; el('cookieStep2').style.display='none'; el('cookieModal').style.display='flex'; }
function goToCookieStep2() { el('cookieStep1').style.display='none'; el('cookieStep2').style.display='block'; }
function backToCookieStep1() { el('cookieStep2').style.display='none'; el('cookieStep1').style.display='block'; el('cookieMessageArea').style.display='none'; }
function closeCookieModal() { el('cookieModal').style.display='none'; }
function submitCookies() {
    const sn=el('cookieSessionName').value, raw=el('cookieInput').value, btn=el('btnSubmitCookie'), msg=el('cookieMessageArea');
    if(!raw.trim()){msg.style.cssText='display:block;background:rgba(239,68,68,.1);color:#EF4444;padding:10px;border-radius:8px;font-size:13px;margin-bottom:12px';msg.innerHTML='JSON tidak boleh kosong.';return;}
    btn.innerHTML='<i class="fas fa-circle-notch fa-spin"></i> Memproses...';btn.disabled=true;msg.style.display='none';
    fetch('{{ route("client.meta.login-cookies") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({sessionName:sn,cookies:raw})})
    .then(r=>r.json())
    .then(d=>{msg.style.cssText='display:block;padding:10px;border-radius:8px;font-size:13px;margin-bottom:12px';if(d.status==='Success'){msg.style.background='rgba(16,185,129,.1)';msg.style.color='#10B981';msg.innerHTML=`<b>Berhasil!</b> ${d.message}`;setTimeout(()=>location.reload(),2000);}else{msg.style.background='rgba(239,68,68,.1)';msg.style.color='#EF4444';msg.innerHTML=`<b>Gagal:</b> ${d.message||'Error.'}`;btn.innerHTML='<i class="fas fa-sign-in-alt" style="margin-right:6px"></i> Inject Cookies & Login';btn.disabled=false;}})
    .catch(()=>{msg.style.cssText='display:block;background:rgba(239,68,68,.1);color:#EF4444;padding:10px;border-radius:8px;font-size:13px;margin-bottom:12px';msg.innerHTML='Koneksi gagal.';btn.innerHTML='<i class="fas fa-sign-in-alt" style="margin-right:6px"></i> Inject Cookies & Login';btn.disabled=false;});
}

// ── Toast ─────────────────────────────────────────────────────
let toastTimer;
function showToast(type, msg) {
    const t = el('toast'); t.className = 'toast ' + type;
    el('toast-msg').textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Escape helper ─────────────────────────────────────────────
function e(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

@endsection
