<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_name',
        'asset_id',
        'page_name',
        'category',
        'picture',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
