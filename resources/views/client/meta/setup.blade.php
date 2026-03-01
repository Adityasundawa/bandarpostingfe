@extends('layouts.app')

@section('page-title', 'Setup Meta API')
@section('breadcrumb', 'Meta Panel / Setup')

@section('content')

<div style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">

    <div style="
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 40px;
        width: 100%;
        max-width: 500px;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    ">

        <i class="fab fa-facebook" style="font-size: 56px; color: #1877F2; margin-bottom: 20px;"></i>

        <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">
            Koneksi Meta Panel
        </h3>

        <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 30px; line-height: 1.5;">
            Sistem mendeteksi Anda belum menghubungkan API Unofficial Meta. Silakan masukkan Token Bearer Meta Anda untuk melanjutkan.
        </p>

        <form action="{{ route('client.meta.setup.store') }}" method="POST">
            @csrf

            <div style="text-align: left; margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">
                    Bearer Token <span style="color: #EF4444;">*</span>
                </label>
                <input type="text" name="token" required placeholder="Contoh: bm_3e8f09751a7c416eb8cd..."
                    style="
                        width: 100%;
                        padding: 12px 16px;
                        border-radius: 8px;
                        border: 1px solid var(--border);
                        background: var(--bg-main);
                        color: var(--text-primary);
                        font-family: 'Space Mono', monospace;
                        font-size: 13px;
                    ">
                @error('token')
                    <div style="color: #EF4444; font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" style="
                width: 100%;
                background: var(--accent);
                color: white;
                border: none;
                padding: 12px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.2s;
            ">
                <i class="fas fa-plug" style="margin-right: 6px;"></i> Simpan & Hubungkan
            </button>
        </form>

        <div style="margin-top: 24px; font-size: 12px; color: var(--text-muted);">
            <i class="fas fa-info-circle"></i> Token akan diverifikasi dan diamankan dalam database.
        </div>

    </div>

</div>

@endsection
