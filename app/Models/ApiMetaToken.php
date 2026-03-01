<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiMetaToken extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = [
        'user_id',
        'token',
        'client_name',
        'role',
        'is_active',
        'expired_at',
        'sessions',
    ];

    // Cast kolom agar format datanya otomatis disesuaikan oleh Laravel
    protected $casts = [
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
        'sessions' => 'array',
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
