<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFolder extends Model
{
    /** @use HasFactory<\Database\Factories\UserFolderFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'parent_id', 'name'];
}
