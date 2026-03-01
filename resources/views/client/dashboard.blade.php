@extends('layouts.app')

@section('page-title', 'Client Area')
@section('breadcrumb', 'Dashboard BandarPosting')

@section('content')

<div style="margin-bottom: 24px;">
    <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin: 0;">Halo, User! 👋</h2>
    <p style="color: var(--text-muted); margin-top: 4px;">Selamat datang di Dashboard BandarPosting.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="font-size: 16px; font-weight: 600; color: var(--text-primary);">Paket Saat Ini</div>
            <span style="background: var(--accent); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">PRO BULANAN</span>
        </div>
        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">
            15 <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">Hari Tersisa</span>
        </div>
        <div style="font-size: 13px; color: var(--text-muted); font-family: 'Space Mono', monospace;">
            <i class="fas fa-sync-alt" style="margin-right: 6px;"></i> Diperpanjang otomatis tgl 12 Mar 2026
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
        <div style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">Sisa Kuota Posting</div>
        <div style="font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">
            1,240 <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">/ 2000 Post</span>
        </div>
        <div style="width: 100%; background: var(--border); height: 8px; border-radius: 4px; margin-top: 16px; overflow: hidden;">
            <div style="width: 62%; background: var(--accent); height: 100%; border-radius: 4px;"></div>
        </div>
    </div>

</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
        <div style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 20px;">Koneksi Platform</div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fab fa-x-twitter" style="font-size: 24px; color: var(--text-primary);"></i>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 14px;">X (Twitter)</div>
                    <div style="font-size: 12px; color: var(--text-muted);">@akun_dummy</div>
                </div>
            </div>
            <span style="color: #10B981; font-size: 12px; font-weight: 600;"><i class="fas fa-check-circle"></i> Terhubung</span>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fab fa-facebook" style="font-size: 24px; color: #1877F2;"></i>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 14px;">Meta (Facebook/IG)</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Fanspage Utama</div>
                </div>
            </div>
            <span style="color: #10B981; font-size: 12px; font-weight: 600;"><i class="fas fa-check-circle"></i> Terhubung</span>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fab fa-telegram" style="font-size: 24px; color: #2AABEE;"></i>
                <div>
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 14px;">Telegram</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Bot Belum Disetting</div>
                </div>
            </div>
            <a href="#" style="color: var(--accent); font-size: 12px; font-weight: 600; text-decoration: none;">Hubungkan</a>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="font-size: 16px; font-weight: 600; color: var(--text-primary);">Jadwal Posting Terdekat</div>
            <a href="#" style="color: var(--accent); font-size: 13px; text-decoration: none;">Lihat Semua</a>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B; margin-top: 6px;"></div>
            <div>
                <div style="font-size: 14px; color: var(--text-primary); font-weight: 500;">Promo Akhir Pekan</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                    <i class="far fa-clock"></i> Hari ini, 19:00 WIB • <i class="fab fa-telegram"></i> <i class="fab fa-x-twitter"></i>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; margin-top: 6px;"></div>
            <div>
                <div style="font-size: 14px; color: var(--text-primary); font-weight: 500;">Update Konten Harian</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                    <i class="far fa-clock"></i> Besok, 08:00 WIB • <i class="fab fa-facebook"></i>
                </div>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button style="width: 100%; background: var(--accent); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-plus"></i> Buat Jadwal Baru
            </button>
        </div>
    </div>

</div>

@endsection
