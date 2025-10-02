<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'title', 'cover_image', 'description', 'location', 'published_at',
        'allow_comments', 'is_pinned', 'is_featured', 'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_pinned' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];
}
