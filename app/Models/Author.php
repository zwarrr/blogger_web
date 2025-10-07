<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Author extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'authors';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'specialization',
        'total_posts',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Generate unique author ID
     */
    public static function generateAuthorId(): string
    {
        do {
            $lastAuthor = self::orderBy('id', 'desc')->first();
            $number = 1;
            
            if ($lastAuthor && preg_match('/AUTHOR(\d+)/', $lastAuthor->id, $matches)) {
                $number = intval($matches[1]) + 1;
            }
            
            $id = 'AUTHOR' . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (self::where('id', $id)->exists());
        
        return $id;
    }

    /**
     * Get posts created by this author
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Get visits where author is the subject
     */
    public function visits()
    {
        return $this->hasMany(Visit::class, 'author_id');
    }

    /**
     * Check if author is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'email';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Increment total posts count
     */
    public function incrementPostCount()
    {
        $this->increment('total_posts');
    }

    /**
     * Decrement total posts count
     */
    public function decrementPostCount()
    {
        $this->decrement('total_posts');
    }
}
