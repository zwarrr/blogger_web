<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorVisitController extends Controller
{
    /**
     * Display list of visits for the author
     */
    public function index(Request $request)
    {
        $author = Auth::user();
        
        if (!$author) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        // Debug: Log user info
        \Log::info('Author visits index - User info:', [
            'user_id' => $author->id,
            'user_name' => $author->name,
            'user_role' => $author->role
        ]);
        
        // Check if there are any visits in database first
        $totalVisitsInDb = Visit::count();
        \Log::info('Total visits in database: ' . $totalVisitsInDb);
        
        // Check visits for this author_id
        $visitsForAuthor = Visit::where('author_id', $author->id)->count();
        \Log::info('Visits for author ID ' . $author->id . ': ' . $visitsForAuthor);
        
        // Also check by author_name for backward compatibility
        $visitsByName = Visit::where('author_name', $author->name)->count();
        \Log::info('Visits by author name "' . $author->name . '": ' . $visitsByName);
        
        $query = Visit::query()
                      ->with(['admin', 'auditor', 'author'])
                      ->where(function($q) use ($author) {
                          $q->where('author_id', $author->id)
                            ->orWhere('author_name', $author->name);
                      });

        // Apply filters
        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('visit_date', $request->date_filter);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('auditor_name', 'like', '%' . $request->search . '%')
                  ->orWhere('location_address', 'like', '%' . $request->search . '%')
                  ->orWhereHas('auditor', function($auditorQuery) use ($request) {
                      $auditorQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $visits = $query->orderBy('visit_date', 'desc')->paginate(15);

        $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai'];
        
        // Calculate statistics - use fresh query for each count to avoid where conflicts
        $authorQuery = function() use ($author) {
            return Visit::where(function($q) use ($author) {
                $q->where('author_id', $author->id)
                  ->orWhere('author_name', $author->name);
            });
        };
        
        $totalVisits = $authorQuery()->count();
        $belumDikunjungi = $authorQuery()->where('status', 'belum_dikunjungi')->count();
        $dalamPerjalanan = $authorQuery()->where('status', 'dalam_perjalanan')->count();
        $sedangDikunjungi = $authorQuery()->where('status', 'sedang_dikunjungi')->count();
        $menungguAcc = $authorQuery()->where('status', 'menunggu_acc')->count();
        $selesai = $authorQuery()->where('status', 'selesai')->count();
        
        // Debug log
        \Log::info('Stats calculated:', [
            'total' => $totalVisits,
            'belum_dikunjungi' => $belumDikunjungi,
            'dalam_perjalanan' => $dalamPerjalanan,
            'selesai' => $selesai
        ]);

        // Check if this is an AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json([
                'html' => view('author.visits.table-rows', compact('visits'))->render(),
                'pagination' => $visits->appends($request->all())->render()
            ]);
        }

        return view('author.visits.index', compact(
            'visits', 
            'statuses',
            'totalVisits',
            'belumDikunjungi', 
            'dalamPerjalanan',
            'sedangDikunjungi',
            'menungguAcc',
            'selesai'
        ));
    }

    /**
     * Show visit details for author
     */
    public function show(Visit $visit)
    {
        // Check if the visit belongs to current author
        if ($visit->author_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Load relationships
        $visit->load(['admin', 'auditor']);

        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Show visit details via AJAX for modal
     */
    public function detail(Visit $visit)
    {
        try {
            $author = Auth::user();
            
            // Check if the visit belongs to current author (by ID or name for backward compatibility)
            if ($visit->author_id !== $author->id && $visit->author_name !== $author->name) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kunjungan ini.'
                ], 403);
            }

            // Load relationships
            $visit->load(['admin', 'auditor', 'author']);

            $visitData = [
                'id' => $visit->id,
                'visit_id' => $visit->visit_id,
                'visit_date' => $visit->visit_date ? $visit->visit_date->format('Y-m-d H:i:s') : null,
                'visit_time' => $visit->visit_time,
                'duration' => $visit->duration,
                'author' => [
                    'name' => $visit->author->name ?? $visit->author_name,
                    'email' => $visit->author->email ?? null,
                    'phone' => $visit->author->phone ?? null,
                ],
                'auditor' => $visit->auditor ? [
                    'name' => $visit->auditor->name,
                    'email' => $visit->auditor->email,
                    'phone' => $visit->auditor->phone,
                ] : null,
                'location_address' => $visit->location_address,
                'latitude' => $visit->latitude,
                'longitude' => $visit->longitude,
                'purpose' => $visit->visit_purpose,
                'status' => $visit->status,
                'status_label' => $visit->status_label,
                'notes' => $visit->notes,
                'reschedule_count' => $visit->reschedule_count ?? 0,
                'remaining_reschedule_attempts' => 3 - ($visit->reschedule_count ?? 0),
                'can_be_confirmed' => $visit->canBeConfirmed(),
                'can_be_rescheduled' => $visit->canBeRescheduled(),
                'created_at' => $visit->created_at ? $visit->created_at->format('d M Y H:i') : null,
                'updated_at' => $visit->updated_at ? $visit->updated_at->format('d M Y H:i') : null,
            ];

            // Add report data if visit is completed - Same as Auditor
            if ($visit->status === 'selesai' && ($visit->report_notes || $visit->auditor_notes || $visit->selfie_photo || $visit->photos)) {
                // Process photos to use correct storage paths
                $photos = [];
                if ($visit->photos) {
                    $photosData = $visit->photos;
                    if (is_string($photosData)) {
                        $photosData = json_decode($photosData, true);
                    }
                    
                    if (is_array($photosData)) {
                        foreach ($photosData as $photo) {
                            if ($photo) {
                                // Check if path already includes storage/visits
                                if (strpos($photo, 'storage/visits/') !== false) {
                                    $photos[] = asset($photo);
                                } else {
                                    // Build path: storage/visits/photos/{visit_id}/filename
                                    $filename = basename($photo);
                                    $photos[] = asset("storage/visits/photos/{$visit->id}/{$filename}");
                                }
                            }
                        }
                    }
                }

                // Handle selfie photo path
                $selfiePhoto = null;
                if ($visit->selfie_photo) {
                    // Check if path already includes storage/visits
                    if (strpos($visit->selfie_photo, 'storage/visits/') !== false) {
                        $selfiePhoto = asset($visit->selfie_photo);
                    } else {
                        // Build path: storage/visits/selfies/{visit_id}/filename
                        $filename = basename($visit->selfie_photo);
                        $selfiePhoto = asset("storage/visits/selfies/{$visit->id}/{$filename}");
                    }
                }

                $visitData['report'] = [
                    'report_notes' => $visit->report_notes,
                    'auditor_notes' => $visit->auditor_notes,
                    'photos' => $photos,
                    'selfie_photo' => $selfiePhoto,
                    'selfie_latitude' => $visit->selfie_latitude,
                    'selfie_longitude' => $visit->selfie_longitude,
                    'visit_start_time' => $visit->started_at,
                    'visit_end_time' => $visit->completed_at,
                    'created_at' => $visit->updated_at
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $visitData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in detail method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail kunjungan.'
            ], 500);
        }
    }

    /**
     * Confirm visit by author
     */
    public function confirm(Visit $visit)
    {
        try {
            $author = Auth::user();
            
            \Log::info('AuthorVisitController::confirm - Starting', [
                'visit_id' => $visit->id,
                'user_id' => $author->id,
                'user_name' => $author->name ?? 'Unknown'
            ]);

            // Check if the visit belongs to current author (by ID or name for backward compatibility)
            if ($visit->author_id !== $author->id && $visit->author_name !== $author->name) {
                \Log::warning('AuthorVisitController::confirm - Access denied', [
                    'visit_author_id' => $visit->author_id,
                    'visit_author_name' => $visit->author_name,
                    'current_user_id' => $author->id,
                    'current_user_name' => $author->name
                ]);
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke kunjungan ini.'], 403);
            }

            // Validate that visit can be confirmed
            if ($visit->status !== 'belum_dikunjungi') {
                \Log::warning('AuthorVisitController::confirm - Cannot be confirmed', [
                    'visit_status' => $visit->status
                ]);
                return response()->json(['success' => false, 'message' => 'Kunjungan hanya dapat dikonfirmasi jika status masih "Belum Dikunjungi". Status saat ini: ' . $visit->status], 400);
            }

            $visit->update([
                'status' => 'dalam_perjalanan',
                'confirmed_at' => now(),
                'confirmed_by' => $author->id
            ]);

            \Log::info('AuthorVisitController::confirm - Success', [
                'visit_id' => $visit->id,
                'new_status' => $visit->fresh()->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dikonfirmasi! Auditor dapat memulai kunjungan.',
                'data' => [
                    'visit_id' => $visit->id,
                    'status' => $visit->status,
                    'confirmed_at' => $visit->confirmed_at->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AuthorVisitController::confirm - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reschedule visit by author
     */
    public function reschedule(Request $request, Visit $visit)
    {
        try {
            $author = Auth::user();
            
            // Check if the visit belongs to current author (by ID or name for backward compatibility)
            if ($visit->author_id !== $author->id && $visit->author_name !== $author->name) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke kunjungan ini.'], 403);
            }

            // Check if reschedule count should be reset (after 1 month)
            $currentRescheduleCount = $visit->reschedule_count ?? 0;
            $lastRescheduleDate = $visit->rescheduled_at;
            
            if ($lastRescheduleDate && $lastRescheduleDate->lt(now()->subMonth())) {
                // Reset reschedule count if more than 1 month has passed
                $currentRescheduleCount = 0;
            }

            // Validate that visit can be rescheduled (max 3 times per month)
            if ($currentRescheduleCount >= 3) {
                $resetDate = $lastRescheduleDate ? $lastRescheduleDate->addMonth()->format('d M Y') : 'N/A';
                return response()->json([
                    'success' => false, 
                    'message' => "Batas pengaturan ulang jadwal sudah tercapai (3x). Anda dapat mengatur ulang kembali setelah tanggal: {$resetDate}"
                ], 400);
            }

            // Validate that visit status allows rescheduling
            if (!in_array($visit->status, ['belum_dikunjungi', 'dalam_perjalanan'])) {
                return response()->json(['success' => false, 'message' => 'Kunjungan tidak dapat diatur ulang jadwalnya dengan status saat ini: ' . $visit->status], 400);
            }

            $request->validate([
                'visit_date' => 'required|date|after:today',
                'reschedule_reason' => 'required|string|max:500'
            ]);

            $newRescheduleCount = $currentRescheduleCount + 1;
            $visit->update([
                'status' => 'belum_dikunjungi', // Reset to pending
                'visit_date' => $request->visit_date,
                'reschedule_reason' => $request->reschedule_reason,
                'reschedule_count' => $newRescheduleCount,
                'rescheduled_at' => now(),
                'rescheduled_by' => $author->id
            ]);

            $remainingAttempts = 3 - $newRescheduleCount;
            $message = 'Jadwal kunjungan berhasil diundur.';
            if ($remainingAttempts > 0) {
                $message .= " Sisa kesempatan mengundur jadwal: {$remainingAttempts}x";
            } else {
                $message .= " Tidak ada lagi kesempatan mengundur jadwal.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'visit_id' => $visit->id,
                    'status' => $visit->status,
                    'new_date' => $visit->scheduled_date,
                    'reschedule_count' => $visit->reschedule_count,
                    'remaining_attempts' => $remainingAttempts
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengatur ulang jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get visit details for modal (JSON response)
     */
    public function getModalDetail(Visit $visit)
    {
        // Check if the visit belongs to current author
        if ($visit->author_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke kunjungan ini.'
            ], 403);
        }

        try {
            $visitData = [
                'id' => $visit->id,
                'visit_date' => $visit->visit_date ? $visit->visit_date->format('d M Y H:i') : null,
                'author_name' => $visit->author_name,
                'auditor_name' => $visit->auditor_name ?? 'Belum ditentukan',
                'status' => $visit->status,
                'created_at' => $visit->created_at ? $visit->created_at->format('d M Y H:i') : null,
                'updated_at' => $visit->updated_at ? $visit->updated_at->format('d M Y H:i') : null,
                'notes' => $visit->notes ?? 'Tidak ada catatan.',
                'reschedule_count' => $visit->reschedule_count ?? 0,
                'remaining_reschedule_attempts' => $visit->remaining_reschedule_attempts ?? 3,
                'can_be_rescheduled' => $visit->canBeRescheduled(),
                'location' => $visit->location ?? 'Tidak ditentukan'
            ];

            return response()->json([
                'success' => true,
                'visit' => $visitData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kunjungan.'
            ], 500);
        }
    }
}
