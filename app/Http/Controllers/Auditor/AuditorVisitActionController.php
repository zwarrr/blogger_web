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
        // Debug log
        Log::info('Complete visit request received', [
            'visit_id' => $visit->id,
            'visit_status' => $visit->status,
            'visit_auditor_id' => $visit->auditor_id,
            'request_method' => $request->method(),
            'request_data' => $request->except(['selfie_photo_data']), // Exclude large base64 data
            'has_files' => $request->hasFile('additional_photos'),
            'has_selfie_data' => $request->filled('selfie_photo_data'),
            'selfie_data_length' => $request->filled('selfie_photo_data') ? strlen($request->selfie_photo_data) : 0,
            'user' => Auth::user()->name ?? 'Not authenticated',
            'user_id' => Auth::id()
        ]);
        
        // Ensure user is authenticated
        $currentUser = Auth::user();
        if (!$currentUser) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu'], 401);
        }

        try {
            // Validate that visit can be completed - auditor can complete when visit is confirmed by author or waiting for ACC
            if (!in_array($visit->status, ['dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc'])) {
                Log::warning('Visit cannot be completed', [
                    'visit_id' => $visit->id,
                    'current_status' => $visit->status,
                    'allowed_statuses' => ['dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc']
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Kunjungan hanya dapat diselesaikan jika sudah dalam perjalanan, sedang dikunjungi, atau menunggu ACC. Status saat ini: ' . $visit->status
                ], 400);
            }
            
            // Check if current auditor is assigned to this visit
            if ($visit->auditor_id !== Auth::user()->id && $visit->assigned_to !== Auth::user()->id) {
                Log::warning('Unauthorized visit completion attempt', [
                    'visit_id' => $visit->id,
                    'visit_auditor_id' => $visit->auditor_id,
                    'visit_assigned_to' => $visit->assigned_to,
                    'current_user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyelesaikan kunjungan ini'
                ], 403);
            }
            
            // Additional debug logging
            Log::info('Visit validation passed', [
                'visit_id' => $visit->id,
                'current_status' => $visit->status,
                'auditor_id' => $visit->auditor_id,
                'current_user_id' => Auth::id()
            ]);

            // Validate required fields
            $request->validate([
                'auditor_notes' => 'required|string|min:10|max:2000',
                'selfie_latitude' => 'nullable|numeric|between:-90,90',
                'selfie_longitude' => 'nullable|numeric|between:-180,180',
                'additional_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Additional photos
            ], [
                'auditor_notes.required' => 'Catatan/keterangan audit harus diisi',
                'auditor_notes.min' => 'Catatan minimal 10 karakter',
                'auditor_notes.max' => 'Catatan maksimal 2000 karakter',
            ]);
            
            Log::info('Validation passed', [
                'visit_id' => $visit->id,
                'auditor_notes' => $request->auditor_notes,
                'has_selfie_data' => $request->filled('selfie_photo_data'),
                'has_coordinates' => $request->filled('selfie_latitude') && $request->filled('selfie_longitude')
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'visit_id' => $visit->id,
                'errors' => $e->validator->errors()->all(),
                'request_keys' => array_keys($request->all())
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->validator->errors()
            ], 422);
        }
        
        // Validate selfie photo (can be file or base64)
        if (!$request->hasFile('selfie_photo') && !$request->filled('selfie_photo_data')) {
            Log::error('Selfie photo validation failed', [
                'visit_id' => $visit->id,
                'has_selfie_file' => $request->hasFile('selfie_photo'),
                'has_selfie_data' => $request->filled('selfie_photo_data'),
                'request_keys' => array_keys($request->all())
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Foto selfie harus diambil'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Store selfie photo
            $selfiePath = null;
            
            if ($request->hasFile('selfie_photo')) {
                // Handle uploaded file
                $selfieFile = $request->file('selfie_photo');
                $selfiePath = $selfieFile->store('visits/selfies/' . $visit->id, 'public');
                
                Log::info('Selfie photo stored from file', [
                    'visit_id' => $visit->id,
                    'path' => $selfiePath,
                    'size' => $selfieFile->getSize()
                ]);
            } elseif ($request->filled('selfie_photo_data')) {
                // Handle base64 data
                $base64Data = $request->input('selfie_photo_data');
                
                Log::info('Processing base64 selfie', [
                    'visit_id' => $visit->id,
                    'data_length' => strlen($base64Data),
                    'starts_with' => substr($base64Data, 0, 30)
                ]);
                
                // Remove data:image/jpeg;base64, prefix if exists
                if (strpos($base64Data, 'data:image') === 0) {
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                }
                
                // Decode base64
                $imageData = base64_decode($base64Data);
                
                if ($imageData !== false && strlen($imageData) > 0) {
                    $fileName = 'selfie_' . $visit->id . '_' . time() . '.jpg';
                    $directory = 'visits/selfies/' . $visit->id;
                    
                    // Ensure directory exists
                    Storage::disk('public')->makeDirectory($directory);
                    
                    // Store file
                    $selfiePath = $directory . '/' . $fileName;
                    $success = Storage::disk('public')->put($selfiePath, $imageData);
                    
                    if ($success) {
                        Log::info('Selfie photo stored from base64', [
                            'visit_id' => $visit->id,
                            'path' => $selfiePath,
                            'size' => strlen($imageData)
                        ]);
                    } else {
                        Log::error('Failed to store selfie photo', [
                            'visit_id' => $visit->id,
                            'path' => $selfiePath
                        ]);
                    }
                } else {
                    Log::error('Failed to decode base64 selfie data', [
                        'visit_id' => $visit->id,
                        'original_length' => strlen($request->input('selfie_photo_data'))
                    ]);
                }
            }

            // Store additional photos
            $additionalPhotos = [];
            if ($request->hasFile('additional_photos')) {
                foreach ($request->file('additional_photos') as $index => $photo) {
                    if ($index >= 5) break; // Limit to 5 additional photos
                    
                    $photoPath = $photo->store('visits/additional/' . $visit->id, 'public');
                    $additionalPhotos[] = $photoPath;
                }
                
                Log::info('Additional photos stored', [
                    'visit_id' => $visit->id,
                    'count' => count($additionalPhotos)
                ]);
            }

            // Update visit record - Set status based on current state
            $newStatus = ($visit->status === 'menunggu_acc') ? 'menunggu_acc' : 'menunggu_acc';
            
            $updateData = [
                'status' => $newStatus, // Status menunggu konfirmasi admin
                'completed_at' => Carbon::now(),
                'auditor_notes' => $request->auditor_notes,
                'selfie_latitude' => $request->selfie_latitude,
                'selfie_longitude' => $request->selfie_longitude,
            ];
            
            // Add selfie path if available
            if ($selfiePath) {
                $updateData['selfie_photo'] = $selfiePath;
            }
            
            // Add additional photos if available
            if (!empty($additionalPhotos)) {
                $updateData['photos'] = json_encode($additionalPhotos);
            }
            
            Log::info('Updating visit with data:', [
                'visit_id' => $visit->id,
                'update_data' => $updateData
            ]);
            
            $visit->update($updateData);

            DB::commit();
            
            Log::info('Visit completed successfully', [
                'visit_id' => $visit->id,
                'auditor_id' => Auth::id(),
                'completed_at' => $visit->completed_at
            ]);

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil diselesaikan dan menunggu konfirmasi admin!',
                'data' => [
                    'visit_id' => $visit->id,
                    'status' => $visit->status,
                    'completed_at' => $visit->completed_at->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to complete visit', [
                'visit_id' => $visit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            
            // Return more specific error message for debugging
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => app()->isLocal() ? $e->getTraceAsString() : null
            ], 500);
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
            'pending_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'belum_dikunjungi')->count(),
            'confirmed_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'dalam_perjalanan')->count(),
            'in_progress_visits' => Visit::where('auditor_id', $auditorId)->where('status', 'sedang_dikunjungi')->count(),
            'completed_visits' => Visit::where('auditor_id', $auditorId)->whereIn('status', ['menunggu_acc', 'selesai'])->count(),
            'this_month_completed' => Visit::where('auditor_id', $auditorId)
                ->whereIn('status', ['menunggu_acc', 'selesai'])
                ->whereMonth('completed_at', Carbon::now()->month)
                ->whereYear('completed_at', Carbon::now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }
}