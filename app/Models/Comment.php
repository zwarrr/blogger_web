<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'name',
        'email',
        'body',
        'code',
        'is_visible',
        'parent_id',
        'likes',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'likes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (Comment $comment) {
            if (!$comment->code) {
                $comment->code = 'CMT' . str_pad((string) $comment->id, 4, '0', STR_PAD_LEFT);
                $comment->saveQuietly();
            }
        });
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
