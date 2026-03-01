@extends('layouts.app')
@section('page-title', 'Buat Token')
@section('breadcrumb', 'Tokens / Create')

@section('content')
<style>
    .form-layout { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:16px; }
    .form-card:last-child { margin-bottom:0; }
    .form-card-header { padding:20px 28px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:14px; }
    .form-card-icon { width:42px; height:42px; border-radius:10px; background:var(--accent-dim); border:1px solid var(--accent-border); display:flex; align-items:center; justify-content:center; color:var(--accent); font-size:16px; flex-shrink:0; }
    .form-card-title { font-size:15px; font-weight:700; color:var(--text-primary); }
    .form-card-sub   { font-size:11px; font-family:'Space Mono',monospace; color:var(--text-muted); margin-top:2px; }
    .form-card-body  { padding:28px; }
    .form-group { margin-bottom:20px; }
    .form-group:last-child { margin-bottom:0; }
    .form-label { display:block; font-size:12px; font-weight:600; font-family:'Space Mono',monospace; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
    .form-label .required { color:#ff4466; margin-left:3px; }
    .form-control { width:100%; background:var(--bg-secondary); border:1px solid var(--border); border-radius:10px; padding:11px 16px; color:var(--text-primary); font-family:'Syne',sans-serif; font-size:14px; outline:none; transition:border-color .2s,box-shadow .2s; }
    .form-control:focus { border-color:var(--accent-border); box-shadow:0 0 0 3px rgba(0,255,136,.08); }
    .form-control::placeholder { color:var(--text-muted); }
    .form-hint { font-size:11px; color:var(--text-muted); font-family:'Space Mono',monospace; margin-top:6px; }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7a96' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; background-color:var(--bg-secondary); padding-right:38px; cursor:pointer; }
    select.form-control option { background:var(--bg-card); }
    .invalid-feedback { font-size:11px; color:#ff4466; font-family:'Space Mono',monospace; margin-top:6px; display:flex; align-items:center; gap:4px; }
    .radio-group { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
    .radio-card { position:relative; }
    .radio-card input[type="radio"] { position:absolute; opacity:0; width:0; }
    .radio-card label { display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--bg-secondary); border:1px solid var(--border); border-radius:10px; cursor:pointer; transition:all .2s; font-size:13px; font-weight:600; color:var(--text-secondary); }
    .radio-card input:checked + label { border-color:var(--accent-border); background:var(--accent-dim); color:var(--accent); }
    .radio-card label:hover { border-color:var(--accent-border); color:var(--text-primary); }
    .form-actions { display:flex; align-items:center; gap:10px; padding:20px 28px; border-top:1px solid var(--border); background:var(--bg-secondary); }
    .btn-primary { display:inline-flex; align-items:center; gap:8px; padding:11px 24px; background:var(--accent); color:#000; font-family:'Syne',sans-serif; font-size:13px; font-weight:700; border-radius:10px; border:none; cursor:pointer; transition:all .2s; }
    .btn-primary:hover { background:#00cc6e; transform:translateY(-1px); box-shadow:0 4px 20px rgba(0,255,136,.25); }
    .btn-secondary { display:inline-flex; align-items:center; gap:8px; padding:11px 20px; background:var(--bg-hover); color:var(--text-secondary); font-family:'Syne',sans-serif; font-size:13px; font-weight:600; border-radius:10px; border:1px solid var(--border); text-decoration:none; transition:all .2s; }
    .btn-secondary:hover { background:var(--bg-secondary); color:var(--text-primary); }
    .info-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .info-item { display:flex; align-items:flex-start; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border); }
    .info-item:last-child { border-bottom:none; }
    .info-item .info-text { font-size:12px; color:var(--text-secondary); line-height:1.5; }
    /* Preview */
    .preview-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:16px; }
    .preview-body  { padding:20px; }
    .preview-row   { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px; }
    .preview-row:last-child { border-bottom:none; }
    .preview-row .pkey { color:var(--text-muted); font-family:'Space Mono',monospace; font-size:11px; }
    .preview-row .pval { font-weight:600; color:var(--text-primary); }

    @media(max-width:900px) { .form-layout{grid-template-columns:1fr;} }
    @media(max-width:600px) { .radio-group{grid-template-columns:1fr 1fr;} }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.meta.tokens.index') }}" class="btn-secondary" style="display:inline-flex;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
<div style="background:rgba(255,68,102,.1);border:1px solid rgba(255,68,102,.25);border-radius:10px;padding:14px 18px;margin-bottom:20px;color:#ff4466;font-size:13px;">
    <i class="fas fa-exclamation-circle"></i> &nbsp;{{ $errors->first() }}
</div>
@endif

<form action="{{ route('admin.meta.tokens.store') }}" method="POST" id="createTokenForm">
    @csrf
    <div class="form-layout">
        <div>
            {{-- Info Token --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon"><i class="fas fa-key"></i></div>
                    <div>
                        <div class="form-card-title">Informasi Token</div>
                        <div class="form-card-sub">Data klien & konfigurasi token</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Nama Klien <span class="required">*</span></label>
                        <input type="text" name="client_name" id="clientName" class="form-control"
                            placeholder="cth: Reseller Jakarta" value="{{ old('client_name') }}" required>
                        <div class="form-hint">Nama yang akan muncul di log dan daftar token.</div>
                        @error('client_name')<div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-card">
                                <input type="radio" name="role" id="role_client" value="client" {{ old('role','client')==='client'?'checked':'' }}>
                                <label for="role_client"><i class="fas fa-user" style="color:#00aaff;"></i> Client</label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" name="role" id="role_admin" value="admin" {{ old('role')==='admin'?'checked':'' }}>
                                <label for="role_admin"><i class="fas fa-shield-alt" style="color:#b464ff;"></i> Admin</label>
                            </div>
                        </div>
                        <div class="form-hint">Admin punya akses penuh ke semua sesi.</div>
                        @error('role')<div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Expired At</label>
                        <input type="text" name="expired_at" id="expiredAt" class="form-control"
                            placeholder="2026-12-31 23:59:59" value="{{ old('expired_at') }}">
                        <div class="form-hint">Format: YYYY-MM-DD HH:MM:SS · Kosongkan = tidak pernah expired.</div>
                        @error('expired_at')<div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Buat Token
                    </button>
                    <a href="{{ route('admin.meta.tokens.index') }}" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            {{-- Live Preview --}}
            <div class="preview-card">
                <div class="form-card-header" style="border-bottom:1px solid var(--border);">
                    <div class="form-card-icon" style="background:rgba(0,170,255,.1);border-color:rgba(0,170,255,.25);color:#00aaff;"><i class="fas fa-id-badge"></i></div>
                    <div>
                        <div class="form-card-title">Preview Token</div>
                        <div class="form-card-sub">Live preview sebelum dibuat</div>
                    </div>
                </div>
                <div class="preview-body">
                    <div class="preview-row">
                        <span class="pkey">Nama Klien</span>
                        <span class="pval" id="previewName" style="color:var(--accent);">—</span>
                    </div>
                    <div class="preview-row">
                        <span class="pkey">Role</span>
                        <span class="pval" id="previewRole">Client</span>
                    </div>
                    <div class="preview-row">
                        <span class="pkey">Expired</span>
                        <span class="pval" id="previewExpired" style="color:var(--text-muted);">Tidak Expired</span>
                    </div>
                    <div class="preview-row">
                        <span class="pkey">Status</span>
                        <span class="pval" style="color:#00ff88;"><i class="fas fa-circle" style="font-size:8px;"></i> Aktif</span>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="info-card">
                <div class="info-item">
                    <i class="fas fa-exclamation-triangle" style="color:#ffaa00;margin-top:1px;font-size:14px;"></i>
                    <div class="info-text">Token hanya tampil <strong style="color:var(--text-primary);">sekali</strong> setelah dibuat. Simpan segera!</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-alt" style="color:#b464ff;margin-top:1px;font-size:14px;"></i>
                    <div class="info-text">Token <strong style="color:#b464ff;">Admin</strong> bisa akses semua sesi tanpa perlu assign.</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar" style="color:#00aaff;margin-top:1px;font-size:14px;"></i>
                    <div class="info-text">Expired bisa diperpanjang kapanpun dari halaman list token.</div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    const clientNameEl = document.getElementById('clientName');
    const expiredAtEl  = document.getElementById('expiredAt');
    const roleEls      = document.querySelectorAll('input[name="role"]');

    function updatePreview() {
        document.getElementById('previewName').textContent = clientNameEl.value || '—';
        const role = document.querySelector('input[name="role"]:checked')?.value || 'client';
        document.getElementById('previewRole').textContent = role === 'admin' ? 'Admin' : 'Client';
        document.getElementById('previewRole').style.color = role === 'admin' ? '#b464ff' : '#00aaff';
        const exp = expiredAtEl.value.trim();
        document.getElementById('previewExpired').textContent = exp || 'Tidak Expired';
        document.getElementById('previewExpired').style.color = exp ? '#ffaa00' : 'var(--text-muted)';
    }

    clientNameEl.addEventListener('input', updatePreview);
    expiredAtEl.addEventListener('input', updatePreview);
    roleEls.forEach(r => r.addEventListener('change', updatePreview));
</script>
@endpush
@endsection
