@extends('layouts.app')

@section('page-title', 'Meta Panel')
@section('breadcrumb', 'Auto Posting / Meta Panel')

@section('content')

<div id="meta-loader" style="
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: var(--bg-main);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: opacity 0.6s ease;
">
    <div style="position: relative; margin-bottom: 24px;">
        <div class="pulse-ring"></div>
        <i class="fab fa-facebook" style="font-size: 64px; color: #1877F2; position: relative; z-index: 2;"></i>
    </div>
    <h3 id="loader-title" style="color: var(--text-primary); font-size: 20px; font-weight: 700; margin-bottom: 8px;">
        Menghubungkan ke Bot Meta...
    </h3>
    <p id="loader-desc" style="color: var(--text-muted); font-size: 14px; max-width: 400px; text-align: center; line-height: 1.5;">
        Sedang memverifikasi keabsahan sesi dan token Anda.
    </p>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(0.8); opacity: 0.8; }
        100% { transform: scale(1.8); opacity: 0; }
    }
    .pulse-ring {
        position: absolute;
        top: 50%; left: 50%;
        width: 64px; height: 64px;
        margin-top: -32px; margin-left: -32px;
        background: #1877F2;
        border-radius: 50%;
        animation: pulse 1.5s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
        z-index: 1;
    }
</style>

<div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0;">Dashboard Meta API</h2>
        <div style="font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 12px;">
            <span><i class="fas fa-key" style="margin-right: 4px;"></i> Token: <code style="background: var(--bg-main); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">{{ substr($tokenData->token ?? '', 0, 10) }}...</code></span>
            <span><i class="fas fa-clock" style="margin-right: 4px;"></i> Expired: {{ $tokenData->expired_at ? $tokenData->expired_at->format('d M Y') : '-' }}</span>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        <button style="background: var(--bg-main); color: var(--text-primary); border: 1px solid var(--border); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-sync-alt" style="margin-right: 4px;"></i> Refresh Status
        </button>
        <button style="background: #1877F2; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-plus-circle" style="margin-right: 4px;"></i> Tambah Sesi Baru
        </button>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px; margin-bottom: 24px;">
    <div style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">
        1. Pilih Sesi Akun Facebook
    </div>

    @if(isset($activeSessions) && count($activeSessions) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            @foreach($activeSessions as $session)
                @php
                    $isAvailable = isset($session['status']) && $session['status'] === 'available';
                    $cardCursor = $isAvailable ? 'cursor: pointer;' : 'cursor: not-allowed; opacity: 0.8;';
                    $onClick = $isAvailable ? "onclick=\"pilihSesi('{$session['sessionName']}')\"" : '';
                @endphp

                <div id="sesi-card-{{ $session['sessionName'] }}" class="sesi-card" {!! $onClick !!}
                     style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--bg-main); border: 2px solid var(--border); border-radius: 12px; transition: all 0.2s ease; {{ $cardCursor }}">

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <i class="fas fa-user-circle" style="font-size: 36px; color: {{ $isAvailable ? '#1877F2' : 'var(--text-muted)' }};"></i>
                        <div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--text-primary);">
                                {{ $session['sessionName'] ?? 'Unknown' }}
                            </div>

                            @if($isAvailable)
                                <div style="font-size: 12px; color: #10B981; font-weight: 600; margin-top: 4px;"><i class="fas fa-check-circle"></i> Logged In (Siap Pakai)</div>
                            @elseif(isset($session['status']) && $session['status'] === 'not_found')
                                <div style="font-size: 12px; color: #EF4444; font-weight: 600; margin-top: 4px;"><i class="fas fa-times-circle"></i> Membutuhkan Login</div>
                            @else
                                <div style="font-size: 12px; color: #F59E0B; font-weight: 600; margin-top: 4px;"><i class="fas fa-exclamation-triangle"></i> {{ ucfirst($session['status']) }}</div>
                            @endif
                        </div>
                    </div>

                    @if(!$isAvailable)
                        <button onclick="openCookieModal('{{ $session['sessionName'] }}')" style="background: rgba(239, 68, 68, 0.1); border: 1px solid #EF4444; color: #EF4444; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                            <i class="fas fa-cookie-bite"></i> Login Cookie
                        </button>
                    @else
                        <div class="sesi-indicator" style="display: none; color: #1877F2; font-size: 20px;">
                            <i class="fas fa-chevron-circle-right"></i>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 30px; color: var(--text-muted); font-size: 14px; background: var(--bg-main); border-radius: 8px;">
            <i class="fas fa-folder-open" style="font-size: 32px; margin-bottom: 12px; opacity: 0.5;"></i><br>
            Belum ada sesi yang terdaftar pada token ini.
        </div>
    @endif
</div>

<div id="panel-detail" style="display: none; animation: fadeIn 0.4s ease;">

    <div style="margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px dashed var(--border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="color: var(--text-primary); margin: 0; font-size: 18px;">
            <i class="fas fa-cogs" style="color: var(--accent); margin-right: 8px;"></i>
            Mengelola Sesi: <span id="label-sesi-terpilih" style="color: #1877F2; font-weight: 800; background: rgba(24, 119, 242, 0.1); padding: 4px 12px; border-radius: 20px;">-</span>
        </h3>
        <button style="background: none; border: 1px solid var(--border); color: var(--text-primary); padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
            <i class="fas fa-search" style="margin-right: 4px;"></i> Deteksi Asset ID
        </button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">

        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
            <div style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 20px;"><i class="fas fa-calendar-plus" style="margin-right: 8px;"></i> Form Jadwal Konten</div>

            <form action="#" method="POST" id="form-schedule">
                <input type="hidden" id="form-session-name" name="sessionName" value="">

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Asset ID (Target Facebook Page)</label>
                    <input type="text" placeholder="Cth: 123456789" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-size: 13px;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Path File Server Absolute (Video/Gambar)</label>
                    <input type="text" placeholder="C:/bahan/video1.mp4" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-size: 13px; font-family: 'Space Mono', monospace;">
                    <small style="color: var(--text-muted); font-size: 11px; margin-top: 4px; display: block;">File harus sudah berada di PC/Server bot.</small>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Tanggal Posting (DD/MM/YYYY)</label>
                        <input type="text" placeholder="28/02/2026" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Jam (0-23)</label>
                        <input type="number" min="0" max="23" placeholder="10" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-size: 13px;">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Caption & Hashtag</label>
                    <textarea rows="4" placeholder="Tulis caption di sini...\n\n#reels #viral" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-size: 13px; resize: vertical;"></textarea>
                </div>

                <button type="button" style="width: 100%; background: var(--accent); color: white; border: none; padding: 14px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-paper-plane"></i> Tambahkan ke Antrean
                </button>
            </form>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">

            <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="font-size: 16px; font-weight: 600; color: var(--text-primary);"><i class="fas fa-satellite-dish" style="margin-right: 8px;"></i>Status Bot</div>
                    <span style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                        <span style="width: 6px; height: 6px; background: #10B981; border-radius: 50%; display: inline-block;"></span> IDLE
                    </span>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                    <div style="flex: 1; text-align: center; padding: 16px; background: var(--bg-main); border: 1px solid var(--border); border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--text-primary);">0</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Dalam Antrean</div>
                    </div>
                    <div style="flex: 1; text-align: center; padding: 16px; background: var(--bg-main); border: 1px solid var(--border); border-radius: 8px;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--text-primary);">12</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Selesai Hari Ini</div>
                    </div>
                </div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px; flex-grow: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div style="font-size: 16px; font-weight: 600; color: var(--text-primary);"><i class="fas fa-history" style="margin-right: 8px;"></i>Riwayat Sesi Ini</div>
                </div>

                <div style="overflow-x: auto; max-height: 250px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border); color: var(--text-muted); position: sticky; top: 0; background: var(--bg-card);">
                                <th style="padding: 12px 8px; font-weight: 600;">File</th>
                                <th style="padding: 12px 8px; font-weight: 600;">Jadwal</th>
                                <th style="padding: 12px 8px; font-weight: 600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px 8px; color: var(--text-primary);">video1.mp4</td>
                                <td style="padding: 12px 8px; color: var(--text-muted);">28 Feb, 10:00</td>
                                <td style="padding: 12px 8px;"><span style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Queued</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 12px 8px; color: var(--text-primary);">gambar_promo.jpg</td>
                                <td style="padding: 12px 8px; color: var(--text-muted);">27 Feb, 15:00</td>
                                <td style="padding: 12px 8px;"><span style="background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Success</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div id="cookieModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 id="cookieModalTitle" style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Login Sesi</h3>
            <button onclick="closeCookieModal()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <input type="hidden" id="cookieSessionName">

        <div id="cookieStep1">
            <div style="background: rgba(24, 119, 242, 0.05); border: 1px dashed #1877F2; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="margin: 0 0 12px 0; color: #1877F2; font-size: 14px;"><i class="fas fa-info-circle"></i> Cara Mendapatkan Cookies:</h4>
                <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: var(--text-primary); line-height: 1.6;">
                    <li>Install ekstensi <b>Cookie Editor</b> dari <a href="https://chromewebstore.google.com/detail/cookie-editor/ookdjilphngeeeghgngjabigmpepanpl" target="_blank" style="color: #1877F2; font-weight: 700; text-decoration: none;">Chrome Web Store</a>.</li>
                    <li>Buka halaman web <a href="https://business.facebook.com/" target="_blank" style="color: #1877F2; text-decoration: none;"><b>https://business.facebook.com/</b></a>.</li>
                    <li>Pastikan Anda sudah <b>Login</b> ke akun yang ingin digunakan.</li>
                    <li>Klik icon Cookie Editor (<i class="fas fa-cookie-bite" style="color:#F59E0B"></i>) di pojok kanan atas browser Anda.</li>
                    <li>Klik tombol <b>Export</b> (biasanya berbentuk icon panah kanan).</li>
                    <li>Pilih <b>Export as JSON</b> (Cookie otomatis tersalin).</li>
                </ol>
            </div>
            <button onclick="goToCookieStep2()" style="width: 100%; background: #1877F2; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s;">
                Saya Sudah Punya Cookie <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
            </button>
        </div>

        <div id="cookieStep2" style="display: none;">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">
                Paste JSON array yang sudah Anda <b>Export</b> dari Cookie Editor ke dalam kolom di bawah ini.
            </p>

            <textarea id="cookieInput" rows="8" placeholder='[\n  {\n    "domain": ".facebook.com",\n    "name": "c_user",\n    "value": "1000123456789"\n  }\n]' style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-main); color: var(--text-primary); font-family: 'Space Mono', monospace; font-size: 12px; margin-bottom: 16px; resize: vertical;"></textarea>

            <div id="cookieMessageArea" style="display: none; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;"></div>

            <div style="display: flex; gap: 12px;">
                <button onclick="backToCookieStep1()" style="width: 48px; background: var(--bg-main); color: var(--text-primary); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <button id="btnSubmitCookie" onclick="submitCookies()" style="flex: 1; background: #1877F2; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;">
                    <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i> Inject Cookies & Login
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Verifikasi Token Otomatis Saat Halaman Dimuat ---
        fetch('{{ route("client.meta.verify") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('loader-title').innerText = "Terhubung!";
                document.getElementById('loader-desc').innerText = "Membuka Dashboard...";

                setTimeout(() => {
                    const loader = document.getElementById('meta-loader');
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 600);
                }, 800);

            } else {
                document.getElementById('loader-title').innerText = "Koneksi Ditolak";
                document.getElementById('loader-title').style.color = "#EF4444";
                document.querySelector('.fab.fa-facebook').style.color = "#EF4444";
                document.querySelector('.pulse-ring').style.background = "#EF4444";

                document.getElementById('loader-desc').innerText = data.message + " Mengarahkan ulang ke halaman Setup...";
                document.getElementById('loader-desc').style.color = "#EF4444";

                setTimeout(() => {
                    window.location.reload();
                }, 3500);
            }
        })
        .catch(error => {
            document.getElementById('loader-title').innerText = "Server API Down";
            document.getElementById('loader-title').style.color = "#F59E0B";
            document.querySelector('.fab.fa-facebook').style.color = "#F59E0B";
            document.querySelector('.pulse-ring').style.display = "none";
            document.getElementById('loader-desc').innerText = "Tidak dapat terhubung ke server Bot Meta utama. Pastikan server sedang menyala.";
            console.error("Fetch error:", error);
        });
    });

    // --- 2. LOGIKA PILIH SESI (DOM MANIPULATION) ---
    function pilihSesi(sessionName) {
        // Reset tampilan semua card sesi ke kondisi awal (unselected)
        document.querySelectorAll('.sesi-card').forEach(card => {
            card.style.borderColor = 'var(--border)';
            card.style.background = 'var(--bg-main)';

            // Sembunyikan icon panah kalau ada
            let indicator = card.querySelector('.sesi-indicator');
            if(indicator) indicator.style.display = 'none';
        });

        // Highlight card yang diklik
        const selectedCard = document.getElementById('sesi-card-' + sessionName);
        if (selectedCard) {
            selectedCard.style.borderColor = '#1877F2';
            selectedCard.style.background = 'rgba(24, 119, 242, 0.04)'; // Latar sedikit biru

            // Munculkan icon panah
            let indicator = selectedCard.querySelector('.sesi-indicator');
            if(indicator) indicator.style.display = 'block';
        }

        // Tampilkan Area Panel Detail
        const panelDetail = document.getElementById('panel-detail');
        panelDetail.style.display = 'block';

        // Update Text dan Input Hidden untuk Form Schedule
        document.getElementById('label-sesi-terpilih').innerText = sessionName;
        document.getElementById('form-session-name').value = sessionName;

        // Scroll halus ke arah panel agar user sadar ada konten baru di bawah
        setTimeout(() => {
            panelDetail.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }

    // --- 3. LOGIKA MODAL LOGIN COOKIES ---
    function openCookieModal(sessionName) {
        // Jangan biarkan klik diteruskan ke card (hindari fungsi pilihSesi berjalan tanpa sengaja)
        event.stopPropagation();

        document.getElementById('cookieSessionName').value = sessionName;
        document.getElementById('cookieModalTitle').innerText = `Login Sesi: ${sessionName}`;
        document.getElementById('cookieInput').value = '';
        document.getElementById('cookieMessageArea').style.display = 'none';

        document.getElementById('cookieStep1').style.display = 'block';
        document.getElementById('cookieStep2').style.display = 'none';

        document.getElementById('cookieModal').style.display = 'flex';
    }

    function goToCookieStep2() {
        document.getElementById('cookieStep1').style.display = 'none';
        document.getElementById('cookieStep2').style.display = 'block';
    }

    function backToCookieStep1() {
        document.getElementById('cookieStep2').style.display = 'none';
        document.getElementById('cookieStep1').style.display = 'block';
        document.getElementById('cookieMessageArea').style.display = 'none';
    }

    function closeCookieModal() {
        document.getElementById('cookieModal').style.display = 'none';
    }

    function submitCookies() {
        const sessionName = document.getElementById('cookieSessionName').value;
        const cookiesRawVal = document.getElementById('cookieInput').value;
        const btnSubmit = document.getElementById('btnSubmitCookie');
        const msgArea = document.getElementById('cookieMessageArea');

        if (!cookiesRawVal.trim()) {
            msgArea.style.display = 'block';
            msgArea.style.background = 'rgba(239, 68, 68, 0.1)';
            msgArea.style.color = '#EF4444';
            msgArea.innerHTML = '<i class="fas fa-info-circle"></i> JSON Cookies tidak boleh kosong.';
            return;
        }

        btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
        btnSubmit.disabled = true;
        btnSubmit.style.opacity = '0.7';
        msgArea.style.display = 'none';

        fetch('{{ route("client.meta.login-cookies") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sessionName: sessionName,
                cookies: cookiesRawVal
            })
        })
        .then(response => response.json())
        .then(data => {
            msgArea.style.display = 'block';

            if (data.status === 'Success') {
                msgArea.style.background = 'rgba(16, 185, 129, 0.1)';
                msgArea.style.color = '#10B981';
                msgArea.innerHTML = `<b>Berhasil!</b> ${data.message}<br><small>${data.next_step || ''}</small>`;
                setTimeout(() => { window.location.reload(); }, 2000);

            } else if (data.status === 'Failed') {
                msgArea.style.background = 'rgba(245, 158, 11, 0.1)';
                msgArea.style.color = '#F59E0B';
                msgArea.innerHTML = `<b>Gagal Login:</b> ${data.message}<br><small><i class="fas fa-lightbulb"></i> ${data.tip || ''}</small>`;
                resetButton(btnSubmit);

            } else if (data.status === 'Forbidden') {
                msgArea.style.background = 'rgba(239, 68, 68, 0.1)';
                msgArea.style.color = '#EF4444';
                msgArea.innerHTML = `<b>Akses Ditolak:</b> ${data.message}<br><small><i class="fas fa-info-circle"></i> ${data.hint || ''}</small>`;
                resetButton(btnSubmit);

            } else {
                msgArea.style.background = 'rgba(239, 68, 68, 0.1)';
                msgArea.style.color = '#EF4444';
                msgArea.innerHTML = `<b>Error:</b> ${data.message || 'Terjadi kesalahan sistem.'}`;
                resetButton(btnSubmit);
            }
        })
        .catch(error => {
            msgArea.style.display = 'block';
            msgArea.style.background = 'rgba(239, 68, 68, 0.1)';
            msgArea.style.color = '#EF4444';
            msgArea.innerHTML = `<b>Koneksi Terputus:</b> Gagal menghubungi server.`;
            resetButton(btnSubmit);
        });
    }

    function resetButton(btnElement) {
        btnElement.innerHTML = '<i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i> Inject Cookies & Login';
        btnElement.disabled = false;
        btnElement.style.opacity = '1';
    }
</script>

@endsection
