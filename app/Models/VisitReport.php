<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'auditor_id',
        'tanggal_kunjungan_aktual',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi_kunjungan',
        'latitude',
        'longitude',
        'hasil_kunjungan',
        'temuan',
        'rekomendasi',
        'status_kunjungan',
        'kendala',
        'foto_kunjungan',
        'dokumen_pendukung',
        'catatan_auditor',
        'status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'admin_notes'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tanggal_kunjungan_aktual' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the visit that owns the report
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the admin who reviewed this report
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the auditor who created this report
     */
    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /**
     * Get decoded photo paths
     */
    public function getPhotoPathsAttribute()
    {
        return $this->foto_kunjungan ? json_decode($this->foto_kunjungan, true) : [];
    }

    /**
     * Get decoded document paths
     */
    public function getDocumentPathsAttribute()
    {
        return $this->dokumen_pendukung ? json_decode($this->dokumen_pendukung, true) : [];
    }

    /**
     * Check if report can be approved
     */
    public function canBeApproved()
    {
        return $this->status === 'submitted';
    }

    /**
     * Check if report requires revision
     */
    public function requiresRevision()
    {
        return $this->status === 'revision_required';
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for approved reports
     */
    public function scopeApproved($query)
    {
        return $query->where('status_laporan', 'approved');
    }

    /**
     * Check if report can be edited
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status_laporan, ['pending', 'revision']);
    }
}