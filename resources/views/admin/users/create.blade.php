@extends('layouts.app')

@section('page-title', 'Tambah User')
@section('breadcrumb', 'Users / Create')

@section('content')

<style>
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    /* ─── CARD ─────────────────────────────────────── */
    .form-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .form-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .form-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: var(--accent-dim);
        border: 1px solid var(--accent-border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 16px;
    }

    .form-card-title { font-size: 15px; font-weight: 700; color: var(--text-primary); }
    .form-card-sub   { font-size: 11px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 2px; }

    .form-card-body { padding: 28px; }

    /* ─── FORM ELEMENTS ────────────────────────────── */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group { margin-bottom: 20px; }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        font-family: 'Space Mono', monospace;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #ff4466;
        margin-left: 3px;
    }

    .form-control {
        width: 100%;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 11px 16px;
        color: var(--text-primary);
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: var(--accent-border);
        box-shadow: 0 0 0 3px rgba(0,255,136,0.08);
    }

    .form-control::placeholder { color: var(--text-muted); }

    .form-control.is-invalid {
        border-color: rgba(255,68,102,0.5);
        box-shadow: 0 0 0 3px rgba(255,68,102,0.08);
    }

    .invalid-feedback {
        font-size: 11px;
        color: #ff4466;
        font-family: 'Space Mono', monospace;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-hint {
        font-size: 11px;
        color: var(--text-muted);
        font-family: 'Space Mono', monospace;
        margin-top: 6px;
    }

    /* Select */
    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7a96' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-color: var(--bg-secondary);
        padding-right: 38px;
        cursor: pointer;
    }

    select.form-control option { background: var(--bg-card); }

    /* Password toggle */
    .input-icon-wrap {
        position: relative;
    }

    .input-icon-wrap .form-control {
        padding-right: 42px;
    }

    .input-icon-btn {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 14px;
        transition: color 0.2s;
    }

    .input-icon-btn:hover { color: var(--accent); }

    /* Radio group */
    .radio-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .radio-card {
        position: relative;
    }

    .radio-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
    }

    .radio-card label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .radio-card label i { font-size: 14px; }
    .radio-card label:hover { border-color: var(--accent-border); color: var(--text-primary); }

    .radio-card input:checked + label {
        border-color: var(--accent-border);
        background: var(--accent-dim);
        color: var(--accent);
    }

    /* Avatar Preview */
    .avatar-preview-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 28px;
        text-align: center;
    }

    .avatar-preview {
        width: 90px;
        height: 90px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 700;
        font-family: 'Space Mono', monospace;
        background: var(--accent-dim);
        border: 2px solid var(--accent-border);
        color: var(--accent);
        margin-bottom: 16px;
        transition: all 0.3s;
    }

    .avatar-preview-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .avatar-preview-email {
        font-size: 11px;
        font-family: 'Space Mono', monospace;
        color: var(--text-muted);
    }

    /* Info card */
    .info-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        margin-top: 16px;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
    }

    .info-item:last-child { border-bottom: none; }

    .info-item i {
        font-size: 14px;
        margin-top: 1px;
    }

    .info-item .info-text {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* Form actions */
    .form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 20px 28px;
        border-top: 1px solid var(--border);
        background: var(--bg-secondary);
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 24px;
        background: var(--accent);
        color: #000;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        background: #00cc6e;
        transform: translateY(-1px);
        box-shadow: 0 4px 20px rgba(0,255,136,0.25);
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        background: var(--bg-hover);
        color: var(--text-secondary);
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        border: 1px solid var(--border);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-secondary:hover { background: var(--bg-secondary); color: var(--text-primary); }

    /* Strength indicator */
    .strength-bar {
        display: flex;
        gap: 4px;
        margin-top: 8px;
    }

    .strength-seg {
        height: 3px;
        flex: 1;
        border-radius: 2px;
        background: var(--border);
        transition: background 0.3s;
    }

    .strength-label {
        font-size: 10px;
        font-family: 'Space Mono', monospace;
        margin-top: 4px;
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .form-layout { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
        .radio-group { grid-template-columns: 1fr 1fr; }
    }
</style>

{{-- Back button --}}
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="display:inline-flex;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" id="createForm">
    @csrf

    <div class="form-layout">

        {{-- ─── MAIN FORM ─────────────────────────────── --}}
        <div>
            {{-- Basic Info --}}
            <div class="form-card" style="margin-bottom: 16px;">
                <div class="form-card-header">
                    <div class="form-card-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <div class="form-card-title">Informasi Dasar</div>
                        <div class="form-card-sub">Data identitas user baru</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="name" id="nameInput" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                placeholder="username_unik" value="{{ old('username') }}">
                            <div class="form-hint">Opsional · hanya huruf, angka, underscore</div>
                            @error('username')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" id="emailInput" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="email@domain.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control"
                            placeholder="08xx-xxxx-xxxx" value="{{ old('phone') }}">
                    </div>
                </div>
            </div>

            {{-- Access & Role --}}
            <div class="form-card" style="margin-bottom: 16px;">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background:rgba(0,170,255,0.1); border-color:rgba(0,170,255,0.25); color:#00aaff;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="form-card-title">Role & Akses</div>
                        <div class="form-card-sub">Tentukan level akses user</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <div class="radio-group">
    <div class="radio-card">
        <input type="radio" name="role" id="role_admin" value="1" {{ old('role', '2') === '1' ? 'checked' : '' }}>
        <label for="role_admin"><i class="fas fa-shield-alt" style="color:#b464ff;"></i> Admin</label>
    </div>
    <div class="radio-card">
        <input type="radio" name="role" id="role_client" value="2" {{ old('role', '2') === '2' ? 'checked' : '' }}>
        <label for="role_client"><i class="fas fa-code" style="color:#00aaff;"></i> Client</label>
    </div>
</div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-control">
                            <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="pending"  {{ old('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background:rgba(255,170,0,0.1); border-color:rgba(255,170,0,0.25); color:#ffaa00;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <div class="form-card-title">Keamanan</div>
                        <div class="form-card-sub">Set password untuk user baru</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password" id="passwordInput" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    placeholder="Min. 8 karakter" required>
                                <button type="button" class="input-icon-btn" onclick="togglePass('passwordInput', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="strength-bar">
                                <div class="strength-seg" id="s1"></div>
                                <div class="strength-seg" id="s2"></div>
                                <div class="strength-seg" id="s3"></div>
                                <div class="strength-seg" id="s4"></div>
                            </div>
                            <div class="strength-label" id="strengthLabel">Masukkan password</div>
                            @error('password')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password_confirmation" id="confirmInput" class="form-control"
                                    placeholder="Ulangi password" required>
                                <button type="button" class="input-icon-btn" onclick="togglePass('confirmInput', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Tambah User
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- ─── SIDEBAR ─────────────────────────────────── --}}
        <div>
            {{-- Live Preview --}}
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background:rgba(180,100,255,0.1); border-color:rgba(180,100,255,0.25); color:#b464ff;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <div class="form-card-title">Preview</div>
                        <div class="form-card-sub">Live preview user card</div>
                    </div>
                </div>
                <div class="avatar-preview-wrap">
                    <div class="avatar-preview" id="avatarPreview">?</div>
                    <div class="avatar-preview-name" id="previewName">Nama User</div>
                    <div class="avatar-preview-email" id="previewEmail">email@domain.com</div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="info-card">
                <div class="info-item">
                    <i class="fas fa-info-circle" style="color: var(--accent);"></i>
                    <div class="info-text">Password harus minimal <strong style="color:var(--text-primary);">8 karakter</strong> dan mengandung huruf besar, angka, atau simbol.</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope" style="color: #00aaff;"></i>
                    <div class="info-text">Email konfirmasi akan dikirim ke user setelah akun berhasil dibuat.</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-alt" style="color: #b464ff;"></i>
                    <div class="info-text">Role <strong style="color:#b464ff;">Admin</strong> memiliki akses penuh ke semua fitur sistem.</div>
                </div>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
    // Live Preview
    const nameInput  = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput');

    function updatePreview() {
        const name  = nameInput.value.trim();
        const email = emailInput.value.trim();
        const initials = name ? name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() : '?';
        document.getElementById('avatarPreview').textContent = initials;
        document.getElementById('previewName').textContent  = name  || 'Nama User';
        document.getElementById('previewEmail').textContent = email || 'email@domain.com';
    }

    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);

    // Password Toggle
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye-slash';
        }
    }

    // Password Strength
    document.getElementById('passwordInput').addEventListener('input', function() {
        const val    = this.value;
        let score    = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const segs   = ['s1','s2','s3','s4'];
        const colors = ['#ff4466','#ffaa00','#00aaff','#00ff88'];
        const labels = ['Sangat Lemah','Lemah','Cukup','Kuat'];

        segs.forEach((id, i) => {
            document.getElementById(id).style.background = i < score ? colors[score - 1] : 'var(--border)';
        });

        document.getElementById('strengthLabel').textContent = val ? labels[score - 1] || 'Sangat Lemah' : 'Masukkan password';
        document.getElementById('strengthLabel').style.color = val ? colors[score - 1] : 'var(--text-muted)';
    });
</script>
@endpush

@endsection
