<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditorVisitActionController extends Controller
{
    /**
     * Complete a visit with selfie and audit notes
     */
    public function complete(Request $request, Visit $visit)
    {
        // Ensure the auditor can only complete their own visits by name
        $currentUser = Auth::user();
        if ($currentUser->name !== $visit->auditor_name) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk menyelesaikan kunjungan ini'], 403);
        }

        // Validate that visit can be completed
        if (!in_array($visit->status, ['sedang_dikunjungi', 'dalam_perjalanan', 'confirmed', 'in_progress'])) {
            return response()->json(['error' => 'Kunjungan tidak dapat diselesaikan dengan status saat ini'], 400);
        }

        // Validate required fields
        $request->validate([
            'auditor_notes' => 'required|string|min:10|max:2000',
            'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'selfie_latitude' => 'nullable|numeric|between:-90,90',
            'selfie_longitude' => 'nullable|numeric|between:-180,180',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Additional photos
        ], [
            'auditor_notes.required' => 'Catatan/keterangan audit harus diisi',
            'auditor_notes.min' => 'Catatan minimal 10 karakter',
            'auditor_notes.max' => 'Catatan maksimal 2000 karakter',
            'selfie_photo.required' => 'Foto selfie harus diambil',
            'selfie_photo.image' => 'File harus berupa gambar',
            'selfie_photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG',
            'selfie_photo.max' => 'Ukuran foto maksimal 5MB',
        ]);

        DB::beginTransaction();
        
        try {
            // Store selfie photo
            $selfiePath = null;
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
            $additionalPhotos = [];
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

            // Update visit record
            $visit->update([
                'status' => 'selesai',
                'completed_at' => Carbon::now(),
                'auditor_notes' => $request->auditor_notes,
                'selfie_photo' => $selfiePath,
                'selfie_latitude' => $request->selfie_latitude,
                'selfie_longitude' => $request->selfie_longitude,
                'photos' => !empty($additionalPhotos) ? json_encode($additionalPhotos) : null,
            ]);

            DB::commit();
            
            Log::info('Visit completed successfully', [
                'visit_id' => $visit->id,
                'auditor_id' => Auth::id(),
                'completed_at' => $visit->completed_at
            ]);

            return redirect()->back()->with('success', 'Kunjungan berhasil diselesaikan!');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to complete visit', [
                'visit_id' => $visit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyelesaikan kunjungan: ' . $e->getMessage());
        }
    }

    /**
     * Start a visit (change status from confirmed to in_progress)
     */
    public function start(Request $request, Visit $visit)
    {
        // Ensure the auditor can only start their own visits
        if ($visit->auditor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate that visit can be started
        if ($visit->status !== 'confirmed') {
            return response()->json(['error' => 'Visit cannot be started in current status'], 400);
        }

        try {
            $visit->update([
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
            ]);

            Log::info('Visit started', [
                'visit_id' => $visit->id,
                'auditor_id' => Auth::id(),
                'started_at' => $visit->started_at
            ]);

            return redirect()->back()->with('success', 'Kunjungan telah dimulai!');
            
        } catch (\Exception $e) {
            Log::error('Failed to start visit', [
                'visit_id' => $visit->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memulai kunjungan: ' . $e->getMessage());
        }
    }

    /**
     * Get visit details for modal display
     */
    public function getDetails(Visit $visit)
    {
        // Ensure the auditor can only view their own visits or admin can view all
        if ($visit->auditor_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $visitData = [
            'id' => $visit->id,
            'visit_date' => $visit->visit_date,
            'visit_purpose' => $visit->visit_purpose,
            'location_address' => $visit->location_address,
            'status' => $visit->status,
            'author' => [
                'name' => $visit->author->name ?? $visit->author_name,
                'email' => $visit->author->email ?? null,
            ],
            'completion_notes' => $visit->completion_notes,
            'selfie_photo_url' => $visit->selfie_photo_path ? Storage::url($visit->selfie_photo_path) : null,
            'additional_photos' => $visit->additional_photos ? 
                array_map(fn($path) => Storage::url($path), json_decode($visit->additional_photos, true)) : [],
            'completed_at' => $visit->completed_at,
            'created_at' => $visit->created_at,
        ];

        return response()->json($visitData);
    }

    /**
     * Get auditor's visit statistics
     */
    public function getStatistics()
    {
        $auditorId = Auth::id();
        
        $stats = [
            'total_visits' => Visit::where('auditor_id', $auditorId)->count(),
            'pending_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'pending')->count(),
            'confirmed_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'confirmed')->count(),
            'in_progress_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'in_progress')->count(),
            'completed_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'completed')->count(),
            'this_month_completed' => Visit::where('auditor_id', $auditorId)
                ->where('status', 'completed')
                ->whereMonth('completed_at', Carbon::now()->month)
                ->whereYear('completed_at', Carbon::now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }
}