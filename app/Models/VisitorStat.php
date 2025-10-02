<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    /**
     * Get active visitors count (active in last 5 minutes)
     */
    public static function getActiveCount(): int
    {
        return self::where('last_activity', '>=', Carbon::now()->subMinutes(5))->count();
    }

    /**
     * Track or update visitor
     */
    public static function trackVisitor(string $sessionId, ?string $ip = null, ?string $userAgent = null): void
    {
        self::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'last_activity' => Carbon::now(),
            ]
        );
    }

    /**
     * Clean old visitors (older than 1 day)
     */
    public static function cleanOldVisitors(): void
    {
        self::where('last_activity', '<', Carbon::now()->subDay())->delete();
    }
}
