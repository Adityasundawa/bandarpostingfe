<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_name',
        'asset_id',
        'title',
        'caption',
        'post_date',
        'status',
        'post_url',
        'reach',
        'likes_reactions',
        'comments',
        'shares',
        'post_hash',
        'raw_data',
    ];

    protected $casts = [
        'raw_data'        => 'array',
        'reach'           => 'integer',
        'likes_reactions' => 'integer',
        'comments'        => 'integer',
        'shares'          => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => '#F59E0B',
            'published' => '#10B981',
            'failed'    => '#EF4444',
            default     => '#6B7280',
        };
    }
}
