<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\VisitReport;
use App\Models\User;
use App\Models\Author;
use App\Models\Auditor;
use App\Services\Admin\AdminVisitManagementService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminVisitController extends Controller
{
    /**
     * Display a listing of visits with dynamic filtering
     */
    public function index(Request $request)
    {
        try {
            $query = Visit::query();
        
        // Apply filters
        if ($request->filled('auditor_filter')) {
            $query->where('auditor_name', $request->auditor_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('visit_date', $request->date_filter);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('author_name', 'like', '%' . $request->search . '%')
                  ->orWhere('auditor_name', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }
        
        // Load visits with relationships (exclude visitReport temporarily until table exists)
        $visits = $query->with(['author', 'auditor'])
                       ->orderBy('visit_date', 'desc')
                       ->paginate(15);
        
        // For filter dropdowns
        $authors = User::where('role', 'author')->get();
        $auditors = User::where('role', 'auditor')->get();
        $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai', 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        // Statistics
        $stats = [
            'total' => Visit::count(),
            'belum_dikunjungi' => Visit::where('status', 'belum_dikunjungi')->count(),
            'dalam_perjalanan' => Visit::where('status', 'dalam_perjalanan')->count(),
            'sedang_dikunjungi' => Visit::where('status', 'sedang_dikunjungi')->count(),
            'menunggu_acc' => Visit::where('status', 'menunggu_acc')->count(),
            'selesai' => Visit::where('status', 'selesai')->count(),
            'pending' => Visit::where('status', 'pending')->count(),
            'confirmed' => Visit::where('status', 'confirmed')->count(),
            'in_progress' => Visit::where('status', 'in_progress')->count(),
            'completed' => Visit::where('status', 'completed')->count(),
            'cancelled' => Visit::where('status', 'cancelled')->count(),
        ];

        // Check if this is an AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json([
                'html' => view('visits.table-rows', compact('visits'))->render(),
                'pagination' => $visits->appends($request->all())->render()
            ]);
        }
        
        return view('admin.visits.index', compact('visits', 'stats', 'authors', 'auditors', 'statuses'));
        
        } catch (\Exception $e) {
            \Log::error('Error in admin visits index: ' . $e->getMessage(), [
                'error' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memuat halaman: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new visit assignment
     */
    public function create()
    {
        // For form display, prioritize authors table since foreign key requires it
        $allAuthors = collect();
        
        // Get authors from authors table if exists (primary source due to foreign key)
        if (Schema::hasTable('authors')) {
            $authorsList = Author::select('id', 'name', 'email', 'address', 'phone', 'status')
                ->get();
            
            $allAuthors = $authorsList->map(function($author) {
                return (object) [
                    'id' => $author->id,
                    'name' => $author->name,
                    'email' => $author->email,
                    'address' => $author->address ?? '',
                    'phone' => $author->phone ?? '',
                    'table' => 'authors'
                ];
            });
        }
        
        // If no authors in authors table, show warning that authors need to be in authors table
        if ($allAuthors->isEmpty()) {
            $usersAuthors = User::where('role', 'author')->select('id', 'name', 'email')->get();
            if ($usersAuthors->count() > 0) {
                // Show users but warn that they need to be migrated to authors table
                $allAuthors = $usersAuthors->map(function($user) {
                    return (object) [
                        'id' => $user->id,
                        'name' => $user->name . ' (Perlu migrasi ke tabel authors)',
                        'email' => $user->email,
                        'address' => '',
                        'phone' => '',
                        'table' => 'users'
                    ];
                });
            }
        }
        
        // For form display, prioritize auditors table since foreign key requires it
        $auditors = collect();
        
        // Get auditors from auditors table if exists (primary source due to foreign key)
        if (Schema::hasTable('auditors')) {
            $auditorsList = Auditor::select('id', 'name', 'email', 'phone', 'status')
                ->get();
            
            $auditors = $auditorsList->map(function($auditor) {
                return (object) [
                    'id' => $auditor->id,
                    'name' => $auditor->name,
                    'email' => $auditor->email,
                    'phone' => $auditor->phone ?? '',
                    'table' => 'auditors'
                ];
            });
        }
        
        // If no auditors in auditors table, show warning that auditors need to be in auditors table
        if ($auditors->isEmpty()) {
            $usersAuditors = User::where('role', 'auditor')->select('id', 'name', 'email')->get();
            if ($usersAuditors->count() > 0) {
                // Show users but warn that they need to be migrated to auditors table
                $auditors = $usersAuditors->map(function($user) {
                    return (object) [
                        'id' => $user->id,
                        'name' => $user->name . ' (Perlu migrasi ke tabel auditors)',
                        'email' => $user->email,
                        'phone' => '',
                        'table' => 'users'
                    ];
                });
            }
        }
        
        // Check if we have authors
        if ($allAuthors->isEmpty()) {
            return redirect()->route('admin.visits.index')
                ->with('error', 'Tidak ada author yang tersedia. Silakan buat author terlebih dahulu.');
        }

        // Check if we have auditors
        if ($auditors->isEmpty()) {
            return redirect()->route('admin.visits.index')
                ->with('error', 'Tidak ada auditor yang tersedia. Silakan buat auditor terlebih dahulu.');
        }

        return view('admin.visits.create', compact('allAuthors', 'auditors'));
    }

    /**
     * Store a newly created visit assignment
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'author_id' => 'required|string',
                'auditor_id' => 'required|string', // Remove exists check since we handle both tables
                'visit_date' => 'required|date|after_or_equal:today',
                'tujuan' => 'required|string|max:1000',
                'catatan_admin' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed for visit creation', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

        // Get author data - prioritize authors table since form sends author IDs from there
        $author = null;
        $authorId = $request->author_id;
        
        \Log::info('Looking for author with ID: ' . $authorId);
        
        // Try authors table first (primary source since form uses these IDs)
        if (Schema::hasTable('authors')) {
            $authorModel = Author::find($authorId);
            if ($authorModel) {
                $author = $authorModel;
                \Log::info('Found author in authors table: ' . $authorModel->name);
            }
        }
        
        // Fallback to users table if not found in authors
        if (!$author) {
            $userAuthor = User::where('role', 'author')->find($authorId);
            if ($userAuthor) {
                $author = $userAuthor;
                \Log::info('Found author in users table: ' . $userAuthor->name);
            }
        }
        
        if (!$author) {
            \Log::error('Author not found with ID: ' . $authorId);
            return redirect()->back()
                ->withErrors(['author_id' => 'Author tidak ditemukan'])
                ->withInput();
        }

        // Get auditor data - prioritize auditors table since form sends auditor IDs from there
        $auditor = null;
        $auditorId = $request->auditor_id;
        
        \Log::info('Looking for auditor with ID: ' . $auditorId);
        
        // Try auditors table first (primary source since form uses these IDs)
        if (Schema::hasTable('auditors')) {
            $auditorModel = Auditor::find($auditorId);
            if ($auditorModel) {
                $auditor = $auditorModel;
                \Log::info('Found auditor in auditors table: ' . $auditorModel->name);
            }
        }
        
        // Fallback to users table if not found in auditors
        if (!$auditor) {
            $userAuditor = User::where('role', 'auditor')->find($auditorId);
            if ($userAuditor) {
                $auditor = $userAuditor;
                \Log::info('Found auditor in users table: ' . $userAuditor->name);
            }
        }
        
        if (!$auditor) {
            \Log::error('Auditor not found with ID: ' . $auditorId);
            return redirect()->back()
                ->withErrors(['auditor_id' => 'Auditor tidak valid'])
                ->withInput();
        }

        // Find corresponding user ID for assigned_to foreign key
        $assignedToUserId = null;
        
        // First try to find user by matching email with auditor
        if ($auditor->email) {
            $correspondingUser = User::where('email', $auditor->email)->first();
            if ($correspondingUser) {
                $assignedToUserId = $correspondingUser->id;
                \Log::info('Found corresponding user by email: ' . $correspondingUser->id);
            }
        }
        
        // If no user found by email, try to find any auditor role user
        if (!$assignedToUserId) {
            $anyAuditorUser = User::where('role', 'auditor')->first();
            if ($anyAuditorUser) {
                $assignedToUserId = $anyAuditorUser->id;
                \Log::info('Using fallback auditor user: ' . $anyAuditorUser->id);
            }
        }
        
        // If still no user found, set to null (field is nullable)
        if (!$assignedToUserId) {
            \Log::warning('No corresponding user found for auditor assignment, setting assigned_to to null');
        }

        // Prepare visit data
        $visitData = [
            'author_name' => $author->name,
            'auditor_name' => $auditor->name,
            'assigned_to' => $assignedToUserId, // Use users table ID for foreign key
            'location_address' => $author->address ?? ($author->alamat ?? 'Alamat akan diverifikasi oleh auditor'),
            'visit_date' => $request->visit_date,
            'visit_purpose' => $request->tujuan,
            'notes' => $request->catatan_admin,
            'status' => 'belum_dikunjungi',
            'reschedule_count' => 0,
            'created_by' => Auth::id(),
            'author_id' => $author->id, // Use the actual ID from the selected table
            'auditor_id' => $auditor->id, // Use the actual ID from the selected table
        ];
        
        \Log::info('Creating visit with data: ', $visitData);
        
        // Create visit assignment
        $visit = Visit::create($visitData);

        return redirect()->route('admin.visits.index')
            ->with('success', 'Penugasan kunjungan berhasil dibuat dengan ID: ' . $visit->visit_id . ' dan ditugaskan kepada ' . $auditor->name);

        } catch (\Exception $e) {
            \Log::error('Error creating visit: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat kunjungan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified visit
     */
    public function show(Visit $visit)
    {
        // Load removed - using name-based fields
        
        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Return visit details as JSON for modal display
     */
    public function showJson(Visit $visit)
    {
        // Load relationships if not already loaded (exclude visitReport temporarily until table exists)
        $visit->load(['author', 'auditor']);
        
        $data = [
            'id' => $visit->id,
            'visit_id' => $visit->visit_id,
            'visit_date' => $visit->visit_date,
            'status' => $visit->status,
            'duration' => $visit->duration,
            'purpose' => $visit->visit_purpose,
            'notes' => $visit->notes,
            'location_address' => $visit->location_address,
            'latitude' => $visit->latitude,
            'longitude' => $visit->longitude,
            'author' => [
                'name' => $visit->author?->name ?? $visit->author_name ?? '',
                'email' => $visit->author?->email ?? '',
                'phone' => $visit->author?->phone ?? '',
                'address' => $visit->author?->address ?? ''
            ],
            'auditor' => [
                'name' => $visit->auditor?->name ?? $visit->auditor_name ?? '',
                'email' => $visit->auditor?->email ?? '',
                'phone' => $visit->auditor?->phone ?? ''
            ]
        ];

        // Process photos to use correct storage paths
        $photos = [];
        if ($visit->photos) {
            // Handle JSON field for photos with proper type checking
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

        // Handle selfie photo path - match storage structure (selfies/1/filename.jpg)
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

        // Use visit table data directly since visitReport table may not exist
        $data['report'] = [
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

        // Add reschedule information if available
        if ($visit->reschedule_count > 0) {
            $data['reschedule_count'] = $visit->reschedule_count;
            $data['reschedule_reason'] = $visit->reschedule_reason;
            $data['rescheduled_at'] = $visit->rescheduled_at;
            
            // Get rescheduled_by user name if available
            if ($visit->rescheduled_by) {
                // Try to get user name from users table
                $rescheduledBy = \App\Models\User::find($visit->rescheduled_by);
                $data['rescheduled_by_name'] = $rescheduledBy ? $rescheduledBy->name : 'User ID: ' . $visit->rescheduled_by;
            } else {
                $data['rescheduled_by_name'] = $visit->author?->name ?? $visit->author_name ?? 'Author';
            }
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified visit
     */
    public function edit(Visit $visit)
    {
        // Load visit with available relationships
        $visit->load(['author', 'auditor']);
        
        // Get authors and auditors for dropdowns
        $authors = User::where('role', 'author')->get();
        $auditors = User::where('role', 'auditor')->get();
        
        return view('admin.visits.edit', compact('visit', 'authors', 'auditors'));
    }

    /**
     * Update the specified visit
     */
    public function update(Request $request, Visit $visit)
    {
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|exists:users,id',
            'auditor_id' => 'required|exists:users,id',
            'location_address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'visit_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'purpose' => 'nullable|string',
            'duration' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle new photo uploads
        $photosPaths = $visit->photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('visits', 'public');
                $photosPaths[] = $path;
            }
        }

        // Get author and auditor names for backward compatibility
        $author = User::find($request->author_id);
        $auditor = User::find($request->auditor_id);

        $visit->update([
            'author_id' => $request->author_id,
            'auditor_id' => $request->auditor_id,
            'author_name' => $author ? $author->name : null,
            'auditor_name' => $auditor ? $auditor->name : null,
            'author_email' => $author ? $author->email : null,
            'auditor_email' => $auditor ? $auditor->email : null,
            'author_phone' => $author ? $author->phone : null,
            'auditor_phone' => $auditor ? $auditor->phone : null,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'visit_date' => $request->visit_date,
            'status' => $request->status,
            'notes' => $request->notes,
            'purpose' => $request->purpose,
            'duration' => $request->duration,
            'photos' => $photosPaths,
        ]);

        return redirect()->route('admin.visits.index')
            ->with('success', 'Kunjungan berhasil diperbarui');
    }

    /**
     * Update visit status (DISABLED - Status is now managed automatically by workflow)
     * Admin should not manually change status - it's controlled by Author and Auditor actions
     */
    public function updateStatus(Request $request, Visit $visit)
    {
        return response()->json([
            'error' => 'Status tidak dapat diubah secara manual. Status dikelola otomatis berdasarkan aksi Author dan Auditor.'
        ], 403);

        // OLD CODE DISABLED - Status should be automatic based on workflow
        /*
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:belum_dikunjungi,selesai',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Admin can only approve visits that are waiting for ACC
        if ($visit->status !== 'menunggu_acc' && $request->status === 'selesai') {
            return response()->json(['error' => 'Kunjungan belum dalam status menunggu ACC'], 422);
        }

        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes ?? $visit->notes
        ];

        // Mark as completed when admin approves
        if ($request->status === 'selesai') {
            $updateData['completed_at'] = now();
        }

        $visit->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status kunjungan berhasil diperbarui',
            'status_label' => $visit->status_label
        ]);
        */
    }

    /**
     * Approve visit report (ACC function)
     */
    public function approve(Request $request, Visit $visit)
    {
        if ($visit->status !== 'menunggu_acc') {
            $message = 'Kunjungan tidak dalam status menunggu ACC';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        $visit->update([
            'status' => 'selesai',
            'completed_at' => now()
        ]);

        $message = 'Laporan kunjungan telah di-ACC dan diselesaikan';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Reject visit report
     */
    public function reject(Request $request, Visit $visit)
    {
        $request->validate([
            'rejection_notes' => 'required|string|min:10'
        ]);

        if ($visit->status !== 'menunggu_acc') {
            $message = 'Kunjungan tidak dalam status menunggu ACC';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return redirect()->back()->with('error', $message);
        }

        $visit->update([
            'status' => 'belum_dikunjungi',
            'notes' => $visit->notes . "\n\nDITOLAK: " . $request->rejection_notes,
            'report_notes' => null,
            'selfie_photo' => null,
            'selfie_latitude' => null,
            'selfie_longitude' => null,
        ]);

        $message = 'Laporan kunjungan ditolak, auditor perlu melakukan kunjungan ulang';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified visit
     */
    public function destroy(Visit $visit)
    {
        // Delete associated photos
        if ($visit->photos) {
            foreach ($visit->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $visit->delete();

        return redirect()->route('admin.visits.index')
            ->with('success', 'Kunjungan berhasil dihapus');
    }

    /**
     * Approve visit report
     */
    public function approveReport(Request $request, Visit $visit)
    {
        // Update visit status directly since visitReport table doesn't exist
        $visit->update([
            'status' => 'completed',
            'auditor_notes' => $request->admin_notes ?? $visit->auditor_notes,
            'completed_at' => now()
        ]);

        return back()->with('success', 'Laporan berhasil disetujui.');
    }

    /**
     * Reject visit report for revision
     */
    public function rejectReport(Request $request, Visit $visit)
    {
        $request->validate([
            'revision_notes' => 'required|string|max:1000'
        ]);

        // Update visit status directly since visitReport table doesn't exist
        $visit->update([
            'status' => 'revision_required',
            'auditor_notes' => $request->revision_notes,
            'notes' => $visit->notes . "\n\nRevisi: " . $request->revision_notes
        ]);

        return back()->with('success', 'Laporan dikembalikan untuk revisi.');
    }

    /**
     * Remove photo from visit
     */
    public function removePhoto(Visit $visit, $photoIndex)
    {
        $photos = $visit->photos ?? [];
        
        if (isset($photos[$photoIndex])) {
            // Delete file from storage
            Storage::disk('public')->delete($photos[$photoIndex]);
            
            // Remove from array
            unset($photos[$photoIndex]);
            $photos = array_values($photos); // Reindex array
            
            $visit->update(['photos' => $photos]);
            
            return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus']);
        }
        
        return response()->json(['error' => 'Foto tidak ditemukan'], 404);
    }

    /**
     * Cancel a visit (Admin only)
     */
    public function cancel(Visit $visit)
    {
        if ($visit->status === 'completed') {
            return back()->with('error', 'Kunjungan yang sudah selesai tidak dapat dibatalkan.');
        }

        $visit->update([
            'status' => 'cancelled'
        ]);

        return back()->with('success', 'Kunjungan berhasil dibatalkan.');
    }
}
