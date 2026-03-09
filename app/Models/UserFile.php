<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserFile extends Model
{
    protected $fillable = [
        'user_id', 'folder_id', 'original_name', 'stored_name',
        'mime_type', 'size', 'extension', 'description',
        'public_token', 'is_public',
    ];

    protected $casts = ['is_public' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function folder(): BelongsTo { return $this->belongsTo(UserFolder::class, 'folder_id'); }

    public function getStoragePathAttribute(): string {
        $folder = $this->folder ? $this->folder->full_path : 'root';
        return "users/{$this->user_id}/{$folder}/{$this->stored_name}";
    }

    public function getDownloadUrlAttribute(): string {
        return route('client.files.download', $this->id);
    }

    public function getPublicUrlAttribute(): ?string {
        if (!$this->is_public || !$this->public_token) return null;
        return route('files.public', ['token' => $this->public_token, 'filename' => $this->original_name]);
    }

    public function getHumanSizeAttribute(): string {
        $bytes = $this->size;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function getTypeAttribute(): string {
        $ext = strtolower($this->extension ?? '');
        if (in_array($ext, ['jpg','jpeg','png','gif','webp','svg','bmp'])) return 'image';
        if (in_array($ext, ['mp4','mov','avi','mkv','webm'])) return 'video';
        if ($ext === 'pdf') return 'pdf';
        if (in_array($ext, ['zip','rar','7z','tar','gz'])) return 'archive';
        if (in_array($ext, ['txt','md','csv','log'])) return 'text';
        return 'file';
    }

    public function makePublic(): void {
        $this->public_token = hash('sha256', $this->id . $this->user_id . Str::random(32));
        $this->is_public = true;
        $this->save();
    }

    public function revokePublic(): void {
        $this->public_token = null;
        $this->is_public = false;
        $this->save();
    }
}
