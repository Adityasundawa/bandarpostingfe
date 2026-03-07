<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserFile extends Model
{
    protected $fillable = [
        'user_id', 'path', 'original_name', 'stored_name',
        'mime_type', 'size', 'extension', 'description',
        'public_token', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public const ROOT_FOLDERS = ['meta', 'x'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Path helpers ──────────────────────────────────────────

    public function getRootFolderAttribute(): string
    {
        return explode('/', $this->path)[0];
    }

    public function getStoragePathAttribute(): string
    {
        return "users/{$this->user_id}/{$this->path}/{$this->stored_name}";
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('client.files.download', $this->id);
    }

    /** Public URL: /files/{token}/{original_name} */
    public function getPublicUrlAttribute(): ?string
    {
        if (!$this->is_public || !$this->public_token) return null;
        return url('files/' . $this->public_token . '/' . rawurlencode($this->original_name));
    }

    // ── Display helpers ───────────────────────────────────────

    public function getHumanSizeAttribute(): string
    {
        $b = $this->size;
        if ($b < 1024)       return $b . ' B';
        if ($b < 1048576)    return round($b / 1024, 1) . ' KB';
        if ($b < 1073741824) return round($b / 1048576, 1) . ' MB';
        return round($b / 1073741824, 2) . ' GB';
    }

    public function getTypeAttribute(): string
    {
        $ext = strtolower($this->extension ?? '');
        if (in_array($ext, ['mp4','mov','avi','mkv','webm','m4v']))        return 'video';
        if (in_array($ext, ['jpg','jpeg','png','gif','webp','bmp','svg'])) return 'image';
        if ($ext === 'pdf')                                                  return 'pdf';
        if (in_array($ext, ['zip','rar','7z','tar','gz']))                  return 'archive';
        if (in_array($ext, ['txt','md','csv','log']))                       return 'text';
        return 'file';
    }

    // ── Token helpers ─────────────────────────────────────────

    /** Generate token baru dan jadikan public */
    public function makePublic(): string
    {
        $token = hash('sha256', $this->user_id . $this->path . $this->stored_name . Str::random(16));
        $this->update(['public_token' => $token, 'is_public' => true]);
        return $token;
    }

    /** Cabut akses publik */
    public function revokePublic(): void
    {
        $this->update(['public_token' => null, 'is_public' => false]);
    }
}
