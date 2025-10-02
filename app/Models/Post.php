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
        'id', 'title', 'content', 'cover_image', 'thumbnail', 'description', 'location', 'published_at',
        'allow_comments', 'is_pinned', 'is_featured', 'is_published', 'author', 'category_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_pinned' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
