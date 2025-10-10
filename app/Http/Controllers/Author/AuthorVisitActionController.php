<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuthorVisitActionController extends Controller
{
    /**
     * Confirm a visit (author accepts the scheduled visit)
     */
    public function confirm(Request $request, Visit $visit)
    {
        try {
            // Check if the authenticated user is the author of this visit by name
            $currentUser = Auth::user();
            if ($currentUser->name !== $visit->author_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengkonfirmasi kunjungan ini'
                ], 403);
            }

            // Check if visit can be confirmed
            if (!in_array($visit->status, ['belum_dikunjungi', 'dalam_perjalanan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kunjungan tidak dapat dikonfirmasi dengan status saat ini'
                ], 400);
            }

            // Update visit status
            $visit->update([
                'status' => 'sedang_dikunjungi',
                'confirmed_at' => now(),
                'confirmed_by' => $currentUser->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dikonfirmasi',
                'visit' => $visit->fresh()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error confirming visit: ' . $e->getMessage(), [
                'visit_id' => $visit->id,
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi kunjungan'
            ], 500);
        }
    }

    /**
     * Reschedule a visit (author requests to change the visit date)
     */
    public function reschedule(Request $request, Visit $visit)
    {
        try {
            // Check if the authenticated user is the author of this visit
            // Handle both string and numeric author_id formats
            $user = Auth::user();
            $userId = $user->id;
            $userCode = $user->user_code ?? null;
            
            $isAuthorized = ($visit->author_id == $userId) || 
                           ($visit->author_id == $userCode) ||
                           ($visit->author_name == $user->name);
            
            if (!$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this visit. User: ' . $userId . ', Visit author: ' . $visit->author_id
                ], 403);
            }

            // Validate request
            $validated = $request->validate([
                'visit_date' => 'required|date|after:now',
                'reschedule_reason' => 'required|string|max:1000'
            ]);
            
            \Log::info('Reschedule validation passed', $validated);

            // Check if visit can be rescheduled
            if (!$this->canBeRescheduled($visit)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit cannot be rescheduled. Maximum reschedule limit reached or invalid status.'
                ], 400);
            }

            // Update visit
            $updateData = [
                'visit_date' => $validated['visit_date'],
                'reschedule_count' => $visit->reschedule_count + 1,
                'status' => 'belum_dikunjungi', // Reset status to pending
            ];
            
            // Only add fields that exist in the table
            if (Schema::hasColumn('visits', 'reschedule_reason')) {
                $updateData['reschedule_reason'] = $validated['reschedule_reason'];
            }
            if (Schema::hasColumn('visits', 'rescheduled_at')) {
                $updateData['rescheduled_at'] = now();
            }
            if (Schema::hasColumn('visits', 'rescheduled_by')) {
                // Handle both string and numeric user IDs
                $currentUserId = Auth::id();
                // If user ID is string (like USER001), extract numeric part or use a default
                if (is_string($currentUserId)) {
                    // Try to extract numeric part from string ID like USER001
                    preg_match('/\d+/', $currentUserId, $matches);
                    $numericId = !empty($matches) ? (int)$matches[0] : 1; // Default to 1 if no number found
                    $updateData['rescheduled_by'] = $numericId;
                } else {
                    $updateData['rescheduled_by'] = $currentUserId;
                }
            }
            
            \Log::info('Updating visit with data', $updateData);
            $visit->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Visit rescheduled successfully',
                'visit' => $visit->fresh(),
                'remaining_attempts' => 3 - $visit->reschedule_count
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error rescheduling visit: ' . $e->getMessage(), [
                'visit_id' => $visit->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rescheduling the visit: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Check if a visit can be rescheduled
     */
    private function canBeRescheduled(Visit $visit)
    {
        // Check reschedule limit (max 3 times)
        if ($visit->reschedule_count >= 3) {
            return false;
        }

        // Check if visit status allows rescheduling
        // Handle both old and new status formats
        $allowedStatuses = ['belum_dikunjungi', 'dalam_perjalanan', 'pending', 'confirmed'];
        if (!in_array($visit->status, $allowedStatuses)) {
            return false;
        }

        return true;
    }
}
