<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'author_name',
        'author_id',
        'auditor_name',
        'auditor_id',
        'assigned_to',
        'location_address',
        'latitude',
        'longitude',
        'status',
        'reschedule_count',
        'reschedule_reason',
        'rescheduled_at',
        'rescheduled_by',
        'notes',
        'report_notes',
        'auditor_notes',
        'photos',
        'selfie_photo',
        'selfie_latitude',
        'selfie_longitude',
        'visit_date',
        'confirmed_at',
        'confirmed_by',
        'started_at',
        'visit_purpose',
        'created_by',
        'completed_at'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'photos' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($visit) {
            if (empty($visit->visit_id)) {
                $visit->visit_id = self::generateVisitId();
            }
        });
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id', 'id')->withDefault([
            'name' => $this->auditor_name ?? 'Unknown Auditor',
            'email' => 'No email'
        ]);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id')->withDefault([
            'name' => $this->author_name ?? 'Unknown Author',
            'email' => 'No email'
        ]);
    }

    public function visitReport()
    {
        return $this->hasOne(VisitReport::class, 'visit_id', 'id');
    }

    public static function generateVisitId()
    {
        // Get the last visit ID number
        $lastVisit = self::whereNotNull('visit_id')
                        ->where('visit_id', 'LIKE', 'VIST%')
                        ->orderBy('visit_id', 'desc')
                        ->first();
        
        if ($lastVisit && preg_match('/VIST(\d+)/', $lastVisit->visit_id, $matches)) {
            $lastNumber = (int) $matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'VIST' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'belum_dikunjungi' => ['text' => 'Belum Dikunjungi', 'class' => 'bg-gray-100 text-gray-800'],
            'dalam_perjalanan' => ['text' => 'Dalam Perjalanan', 'class' => 'bg-blue-100 text-blue-800'],
            'sedang_dikunjungi' => ['text' => 'Sedang Dikunjungi', 'class' => 'bg-yellow-100 text-yellow-800'],
            'menunggu_acc' => ['text' => 'Menunggu ACC', 'class' => 'bg-orange-100 text-orange-800'],
            'selesai' => ['text' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
        ];
        
        return $labels[$this->status] ?? ['text' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'bg-gray-100 text-gray-800'];
    }

    public function getFormattedVisitDateAttribute()
    {
        return $this->visit_date ? $this->visit_date->format('d M Y H:i') : '-';
    }

    public function canBeReported()
    {
        return in_array($this->status, ['in_progress', 'completed']);
    }

    /**
     * Check if author can confirm this visit
     */
    public function canBeConfirmed()
    {
        return $this->status === 'belum_dikunjungi';
    }

    /**
     * Check if author can reschedule this visit
     */
    public function canBeRescheduled()
    {
        return in_array($this->status, ['belum_dikunjungi', 'dalam_perjalanan']) && $this->reschedule_count < 3;
    }

    /**
     * Check if auditor can start process
     */
    public function canBeStarted()
    {
        return $this->status === 'belum_dikunjungi';
    }

    /**
     * Check if auditor can complete this visit
     */
    public function canBeCompleted()
    {
        return in_array($this->status, ['sedang_dikunjungi', 'dalam_perjalanan']);
    }

    /**
     * Check if visit can be edited by admin
     */
    public function canBeEditedByAdmin()
    {
        return $this->status !== 'completed';
    }

    /**
     * Get remaining reschedule attempts
     */
    public function getRemainingRescheduleAttemptsAttribute()
    {
        return max(0, 3 - $this->reschedule_count);
    }

    /**
     * Relationship with confirming author
     */
    public function confirmingAuthor()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
