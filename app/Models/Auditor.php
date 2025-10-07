<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Auditor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'auditors';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'employee_id',
        'department',
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
     * Generate unique auditor ID
     */
    public static function generateAuditorId(): string
    {
        do {
            $lastAuditor = self::orderBy('id', 'desc')->first();
            $number = 1;
            
            if ($lastAuditor && preg_match('/AUDITOR(\d+)/', $lastAuditor->id, $matches)) {
                $number = intval($matches[1]) + 1;
            }
            
            $id = 'AUDITOR' . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (self::where('id', $id)->exists());
        
        return $id;
    }

    /**
     * Get visits where auditor is assigned
     */
    public function visits()
    {
        return $this->hasMany(Visit::class, 'auditor_id');
    }

    /**
     * Check if auditor is active
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
}
