@extends('layouts.app')

@section('page-title', 'Edit User')
@section('breadcrumb', 'Users / Edit')

@section('content')

{{-- Reuse the same styles as create --}}
<style>
    .form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

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
        flex-shrink: 0;
    }

    .form-card-title { font-size: 15px; font-weight: 700; color: var(--text-primary); }
    .form-card-sub   { font-size: 11px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 2px; }
    .form-card-body  { padding: 28px; }

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

    .form-label .required { color: #ff4466; margin-left: 3px; }

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

    .input-icon-wrap { position: relative; }
    .input-icon-wrap .form-control { padding-right: 42px; }
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

    .radio-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .radio-card { position: relative; }
    .radio-card input[type="radio"] { position: absolute; opacity: 0; width: 0; }

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
    .radio-card input:checked + label { border-color: var(--accent-border); background: var(--accent-dim); color: var(--accent); }

    /* Profile card sidebar */
    .profile-sidebar-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .profile-banner {
        height: 80px;
        background: linear-gradient(135deg, rgba(0,255,136,0.15) 0%, rgba(0,170,255,0.15) 100%);
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .profile-avatar-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0 24px 24px;
        margin-top: -30px;
    }

    .profile-avatar {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: var(--accent-dim);
        border: 3px solid var(--bg-card);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        font-family: 'Space Mono', monospace;
        color: var(--accent);
        margin-bottom: 14px;
    }

    .profile-name { font-size: 16px; font-weight: 800; color: var(--text-primary); text-align: center; }
    .profile-email { font-size: 11px; font-family: 'Space Mono', monospace; color: var(--text-muted); margin-top: 4px; text-align: center; }

    .profile-meta {
        width: 100%;
        border-top: 1px solid var(--border);
        margin-top: 16px;
        padding-top: 16px;
    }

    .profile-meta-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        padding: 6px 0;
    }

    .profile-meta-row .key { color: var(--text-muted); font-family: 'Space Mono', monospace; }
    .profile-meta-row .val { color: var(--text-primary); font-weight: 600; }

    /* Danger zone */
    .danger-card {
        background: var(--bg-card);
        border: 1px solid rgba(255,68,102,0.2);
        border-radius: 14px;
        overflow: hidden;
        margin-top: 16px;
    }

    .danger-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255,68,102,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ff4466;
        font-size: 13px;
        font-weight: 700;
    }

    .danger-body { padding: 16px 20px; }
    .danger-body p { font-size: 12px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.6; }

    .btn-danger-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: rgba(255,68,102,0.08);
        border: 1px solid rgba(255,68,102,0.3);
        border-radius: 8px;
        color: #ff4466;
        font-size: 12px;
        font-weight: 700;
        font-family: 'Syne', sans-serif;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        justify-content: center;
    }

    .btn-danger-outline:hover { background: rgba(255,68,102,0.15); }

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

    .btn-primary:hover { background: #00cc6e; transform: translateY(-1px); box-shadow: 0 4px 20px rgba(0,255,136,0.25); }

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

    /* Password section toggle */
    .change-pass-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: var(--bg-hover);
        border: 1px solid var(--border);
        border-radius: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.2s;
        user-select: none;
    }

    .change-pass-toggle:hover { border-color: var(--accent-border); color: var(--accent); }

    .pass-section { display: none; margin-top: 16px; }
    .pass-section.open { display: block; }

    /* Flash */
    .flash-msg {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 600;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }

    .flash-msg.success { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.25); color: #00ff88; }

    @media (max-width: 900px) {
        .form-layout { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .form-row { grid-template-columns: 1fr; }
        .radio-group { grid-template-columns: 1fr 1fr; }
    }
</style>


@if(session('success'))
    <div class="flash-msg success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Back --}}
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="display:inline-flex;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editForm">
    @csrf
    @method('PUT')

    <div class="form-layout">

        {{-- ─── MAIN FORM ─────────────────────────────── --}}
        <div>
            {{-- Basic Info --}}
            <div class="form-card" style="margin-bottom: 16px;">
                <div class="form-card-header">
                    <div class="form-card-icon"><i class="fas fa-user-edit"></i></div>
                    <div>
                        <div class="form-card-title">Edit Informasi Dasar</div>
                        <div class="form-card-sub">Update data identitas user</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="name" id="nameInput" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control"
                                value="{{ old('username', $user->username) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" id="emailInput" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>
            </div>

            {{-- Role & Status --}}
            <div class="form-card" style="margin-bottom: 16px;">
                <div class="form-card-header">
                    <div class="form-card-icon" style="background:rgba(0,170,255,0.1); border-color:rgba(0,170,255,0.25); color:#00aaff;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="form-card-title">Role & Akses</div>
                        <div class="form-card-sub">Update level akses user</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
    <label class="form-label">Role <span class="required">*</span></label>
    <div class="radio-group">
        @foreach([
            1 => ['fas fa-shield-alt', '#b464ff', 'Admin'],
            2 => ['fas fa-code',       '#00aaff', 'Client'],
        ] as $roleValue => [$icon, $color, $label])
        <div class="radio-card">
            <input type="radio" name="role" id="role_{{ $roleValue }}" value="{{ $roleValue }}"
                {{ old('role', $user->role ?? 2) == $roleValue ? 'checked' : '' }}>
            <label for="role_{{ $roleValue }}">
                <i class="{{ $icon }}" style="color:{{ $color }};"></i>
                {{ $label }}
            </label>
        </div>
        @endforeach
    </div>
</div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @foreach(['active','pending','inactive'] as $s)
                            <option value="{{ $s }}" {{ old('status', $user->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
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
                        <div class="form-card-title">Ubah Password</div>
                        <div class="form-card-sub">Kosongkan jika tidak ingin mengubah password</div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="change-pass-toggle" onclick="togglePassSection()">
                        <i class="fas fa-key"></i>
                        <span id="passToggleText">Klik untuk ubah password</span>
                        <i class="fas fa-chevron-down" style="margin-left:auto;" id="passChevron"></i>
                    </div>
                    <div class="pass-section" id="passSection">
                        <div class="form-row">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Password Baru</label>
                                <div class="input-icon-wrap">
                                    <input type="password" name="password" id="passInput" class="form-control" placeholder="Min. 8 karakter">
                                    <button type="button" class="input-icon-btn" onclick="togglePass('passInput', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="input-icon-wrap">
                                    <input type="password" name="password_confirmation" id="confirmInput" class="form-control" placeholder="Ulangi password baru">
                                    <button type="button" class="input-icon-btn" onclick="togglePass('confirmInput', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </div>
        </div>

        {{-- ─── SIDEBAR ─────────────────────────────────── --}}
        <div>
            {{-- Profile Card --}}
            <div class="profile-sidebar-card">
                <div class="profile-banner"></div>
                <div class="profile-avatar-wrap">
                    <div class="profile-avatar" id="profileAvatarPreview">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="profile-name" id="profileNamePreview">{{ $user->name }}</div>
                    <div class="profile-email" id="profileEmailPreview">{{ $user->email }}</div>
                    <div class="profile-meta">
                        <div class="profile-meta-row">
                            <span class="key">ID</span>
                            <span class="val">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="key">Role</span>
                            <span class="val" style="color: var(--accent);">{{ ucfirst($user->role) }}</span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="key">Status</span>
                            <span class="val" style="color: {{ $user->status === 'active' ? '#00ff88' : ($user->status === 'pending' ? '#ffaa00' : '#ff4466') }};">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="profile-meta-row">
                            <span class="key">Bergabung</span>
                            <span class="val">{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="danger-card">
                <div class="danger-header">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </div>
                <div class="danger-body">
                    <p>Hapus user ini secara permanen beserta semua datanya. Tindakan ini tidak bisa dibatalkan.</p>
                    {{-- Tombol ini trigger form delete yang ada DI LUAR form utama --}}
                    <button type="button" class="btn-danger-outline" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt"></i> Hapus User Ini
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>

{{-- ⚠️ Form DELETE harus di LUAR form PUT, supaya tidak nested --}}
<form id="deleteUserForm"
      action="{{ route('admin.users.destroy', $user->id) }}"
      method="POST"
      style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Live Preview
    document.getElementById('nameInput').addEventListener('input', function() {
        const name = this.value.trim();
        const initials = name ? name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() : 'XX';
        document.getElementById('profileAvatarPreview').textContent = initials;
        document.getElementById('profileNamePreview').textContent = name || 'Nama User';
    });

    document.getElementById('emailInput').addEventListener('input', function() {
        document.getElementById('profileEmailPreview').textContent = this.value || 'email@domain.com';
    });

    // Delete confirmation — submit form yang terpisah di luar form PUT
    function confirmDelete() {
        if (confirm('Yakin ingin menghapus user ini? Tindakan ini tidak bisa dibatalkan.')) {
            document.getElementById('deleteUserForm').submit();
        }
    }

    // Password toggle section
    function togglePassSection() {
        const section  = document.getElementById('passSection');
        const chevron  = document.getElementById('passChevron');
        const text     = document.getElementById('passToggleText');
        const isOpen   = section.classList.toggle('open');
        chevron.style.transform = isOpen ? 'rotate(180deg)' : '';
        text.textContent = isOpen ? 'Sembunyikan form password' : 'Klik untuk ubah password';
    }

    // Password visibility toggle
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
</script>
@endpush

@endsection
