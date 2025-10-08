<?php

namespace App\Services;

use App\Models\Visit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitManagementService
{
    /**
     * Handle visit completion workflow
     */
    public function completeVisit(Visit $visit, Request $request, User $auditor)
    {
        // Validate permissions
        if (!$this->canUserCompleteVisit($visit, $auditor)) {
            throw new \Exception('Unauthorized to complete this visit');
        }

        // Validate visit status
        if (!$this->canVisitBeCompleted($visit)) {
            throw new \Exception('Visit cannot be completed in current status: ' . $visit->status);
        }

        DB::beginTransaction();
        
        try {
            // Process uploaded files
            $fileData = $this->processUploadedFiles($visit, $request);
            
            // Update visit with completion data
            $completionData = [
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'completion_notes' => $request->notes,
                'selfie_photo_path' => $fileData['selfie_path'],
                'selfie_latitude' => $request->selfie_latitude,
                'selfie_longitude' => $request->selfie_longitude,
                'additional_photos' => $fileData['additional_photos'],
            ];

            $visit->update($completionData);

            // Log completion
            $this->logVisitCompletion($visit, $auditor);

            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Kunjungan berhasil diselesaikan!',
                'visit' => $visit->fresh()
            ];
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Visit completion failed', [
                'visit_id' => $visit->id,
                'auditor_id' => $auditor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle visit rescheduling workflow
     */
    public function rescheduleVisit(Visit $visit, Request $request, User $author)
    {
        // Validate permissions
        if (!$this->canUserRescheduleVisit($visit, $author)) {
            throw new \Exception('Unauthorized to reschedule this visit');
        }

        // Validate reschedule eligibility
        if (!$this->canVisitBeRescheduled($visit)) {
            throw new \Exception('Visit cannot be rescheduled');
        }

        DB::beginTransaction();
        
        try {
            // Update visit with new schedule
            $rescheduleData = [
                'visit_date' => Carbon::parse($request->new_visit_date),
                'reschedule_reason' => $request->reschedule_reason,
                'reschedule_count' => $visit->reschedule_count + 1,
                'rescheduled_at' => Carbon::now(),
                'status' => 'pending', // Reset to pending after reschedule
            ];

            $visit->update($rescheduleData);

            // Log reschedule
            $this->logVisitReschedule($visit, $author, $request->reschedule_reason);

            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Jadwal kunjungan berhasil diundur!',
                'visit' => $visit->fresh()
            ];
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Visit reschedule failed', [
                'visit_id' => $visit->id,
                'author_id' => $author->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Process uploaded files for visit completion
     */
    private function processUploadedFiles(Visit $visit, Request $request)
    {
        $selfiePath = null;
        $additionalPhotos = [];

        // Store selfie photo
        if ($request->hasFile('selfie_photo')) {
            $selfieFile = $request->file('selfie_photo');
            $selfiePath = $selfieFile->store('visits/selfies/' . $visit->id, 'public');
            
            Log::info('Selfie photo stored', [
                'visit_id' => $visit->id,
                'path' => $selfiePath,
                'size' => $selfieFile->getSize()
            ]);
        }

        // Store additional photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                if ($index >= 5) break; // Limit to 5 additional photos
                
                $photoPath = $photo->store('visits/additional/' . $visit->id, 'public');
                $additionalPhotos[] = $photoPath;
            }
            
            Log::info('Additional photos stored', [
                'visit_id' => $visit->id,
                'count' => count($additionalPhotos)
            ]);
        }

        return [
            'selfie_path' => $selfiePath,
            'additional_photos' => !empty($additionalPhotos) ? json_encode($additionalPhotos) : null,
        ];
    }

    /**
     * Check if user can complete the visit
     */
    private function canUserCompleteVisit(Visit $visit, User $user)
    {
        return $user->role === 'auditor' && $visit->auditor_id === $user->id;
    }

    /**
     * Check if user can reschedule the visit
     */
    private function canUserRescheduleVisit(Visit $visit, User $user)
    {
        return $user->role === 'author' && $visit->author_id === $user->id;
    }

    /**
     * Check if visit can be completed
     */
    private function canVisitBeCompleted(Visit $visit)
    {
        return in_array($visit->status, ['confirmed', 'in_progress']);
    }

    /**
     * Check if visit can be rescheduled
     */
    private function canVisitBeRescheduled(Visit $visit)
    {
        return $visit->status === 'pending' && $visit->reschedule_count < 3;
    }

    /**
     * Log visit completion
     */
    private function logVisitCompletion(Visit $visit, User $auditor)
    {
        Log::info('Visit completed successfully', [
            'visit_id' => $visit->id,
            'auditor_id' => $auditor->id,
            'auditor_name' => $auditor->name,
            'completed_at' => $visit->completed_at,
            'has_selfie' => !empty($visit->selfie_photo_path),
            'has_gps' => !empty($visit->selfie_latitude) && !empty($visit->selfie_longitude),
            'notes_length' => strlen($visit->completion_notes ?? ''),
        ]);
    }

    /**
     * Log visit reschedule
     */
    private function logVisitReschedule(Visit $visit, User $author, string $reason)
    {
        Log::info('Visit rescheduled', [
            'visit_id' => $visit->id,
            'author_id' => $author->id,
            'author_name' => $author->name,
            'reschedule_count' => $visit->reschedule_count,
            'new_date' => $visit->visit_date,
            'reason' => $reason,
            'rescheduled_at' => $visit->rescheduled_at,
        ]);
    }

    /**
     * Get visit statistics for user
     */
    public function getVisitStatistics(User $user)
    {
        $query = Visit::query();

        // Filter by user role
        if ($user->role === 'auditor') {
            $query->where('auditor_id', $user->id);
        } elseif ($user->role === 'author') {
            $query->where('author_id', $user->id);
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'this_month_completed' => (clone $query)
                ->where('status', 'completed')
                ->whereMonth('completed_at', Carbon::now()->month)
                ->whereYear('completed_at', Carbon::now()->year)
                ->count(),
        ];
    }

    /**
     * Clean up old visit files
     */
    public function cleanupOldFiles($daysOld = 90)
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        
        $oldVisits = Visit::where('completed_at', '<', $cutoffDate)
            ->whereNotNull('selfie_photo_path')
            ->get();

        $deletedCount = 0;
        
        foreach ($oldVisits as $visit) {
            try {
                // Delete selfie photo
                if ($visit->selfie_photo_path && Storage::disk('public')->exists($visit->selfie_photo_path)) {
                    Storage::disk('public')->delete($visit->selfie_photo_path);
                }

                // Delete additional photos
                if ($visit->additional_photos) {
                    $photos = json_decode($visit->additional_photos, true);
                    foreach ($photos as $photo) {
                        if (Storage::disk('public')->exists($photo)) {
                            Storage::disk('public')->delete($photo);
                        }
                    }
                }

                $deletedCount++;
                
            } catch (\Exception $e) {
                Log::warning('Failed to cleanup visit files', [
                    'visit_id' => $visit->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Visit files cleanup completed', [
            'days_old' => $daysOld,
            'visits_processed' => $oldVisits->count(),
            'files_deleted' => $deletedCount
        ]);

        return $deletedCount;
    }
}