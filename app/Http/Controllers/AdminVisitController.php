<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\VisitReport;
use App\Models\User;
use App\Models\Author;
use App\Models\Auditor;
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
        $query = Visit::with(['author', 'auditor', 'visitReport']);
        
        // Apply filters
        if ($request->filled('auditor_filter')) {
            $query->where('auditor_id', $request->auditor_filter);
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
                  ->orWhereHas('author', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $visits = $query->orderBy('visit_date', 'desc')->paginate(15);
        
        // For filter dropdowns
        $authors = User::where('role', 'author')->get();
        $auditors = User::where('role', 'auditor')->get();
        $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai'];
        
        // Statistics
        $stats = [
            'total' => Visit::count(),
            'belum_dikunjungi' => Visit::where('status', 'belum_dikunjungi')->count(),
            'dalam_perjalanan' => Visit::where('status', 'dalam_perjalanan')->count(),
            'sedang_dikunjungi' => Visit::where('status', 'sedang_dikunjungi')->count(),
            'menunggu_acc' => Visit::where('status', 'menunggu_acc')->count(),
            'selesai' => Visit::where('status', 'selesai')->count(),
        ];

        // Check if this is an AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json([
                'html' => view('visits.table-rows', compact('visits'))->render(),
                'pagination' => $visits->appends($request->all())->render()
            ]);
        }
        
        return view('admin.visits.index', compact('visits', 'stats', 'authors', 'auditors', 'statuses'));
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
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|string',
            'auditor_id' => 'required|string|exists:users,id',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
            'tujuan' => 'required|string|max:1000',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get author data - prioritize authors table due to foreign key constraint
        $author = null;
        $authorId = $request->author_id;
        
        // First try authors table (required by foreign key)
        if (Schema::hasTable('authors')) {
            $author = Author::find($authorId);
        }
        
        // If not found, try users table but we'll need different handling
        if (!$author) {
            $userAuthor = User::where('role', 'author')->find($authorId);
            if ($userAuthor) {
                // Look for corresponding author in authors table by name or create mapping
                $author = Author::where('name', $userAuthor->name)->first();
                if (!$author) {
                    return redirect()->back()
                        ->withErrors(['author_id' => 'Author tidak ditemukan di tabel authors. Silakan pilih author yang valid.'])
                        ->withInput();
                }
                $authorId = $author->id; // Use authors table ID
            }
        }
        
        if (!$author) {
            return redirect()->back()
                ->withErrors(['author_id' => 'Author tidak ditemukan'])
                ->withInput();
        }

        // Get auditor data - prioritize auditors table due to foreign key constraint
        $auditor = null;
        $auditorId = $request->auditor_id;
        
        // First try auditors table (required by foreign key)
        if (Schema::hasTable('auditors')) {
            $auditor = Auditor::find($auditorId);
        }
        
        // If not found, try users table but we'll need different handling
        if (!$auditor) {
            $userAuditor = User::where('role', 'auditor')->find($auditorId);
            if ($userAuditor) {
                // Look for corresponding auditor in auditors table by name or create mapping
                $auditor = Auditor::where('name', $userAuditor->name)->first();
                if (!$auditor) {
                    return redirect()->back()
                        ->withErrors(['auditor_id' => 'Auditor tidak ditemukan di tabel auditors. Silakan pilih auditor yang valid.'])
                        ->withInput();
                }
                $auditorId = $auditor->id; // Use auditors table ID
            }
        }
        
        if (!$auditor) {
            return redirect()->back()
                ->withErrors(['auditor_id' => 'Auditor tidak valid'])
                ->withInput();
        }

        // Find corresponding user ID for assigned_to (foreign key to users table)
        $assignedToUserId = null;
        if ($auditor) {
            // Look for user with same name as auditor
            $correspondingUser = User::where('role', 'auditor')
                ->where('name', $auditor->name)
                ->first();
            $assignedToUserId = $correspondingUser ? $correspondingUser->id : '1'; // Fallback to admin
        }
        
        // Create visit assignment using the correct IDs for foreign keys
        $visit = Visit::create([
            'visit_id' => Visit::generateVisitId(),
            'author_name' => $author->name,
            'auditor_name' => $auditor->name,
            'author_id' => $authorId, // Use correct authors table ID
            'auditor_id' => $auditorId, // Use correct auditors table ID
            'assigned_to' => $assignedToUserId, // Use users table ID for this foreign key
            'location_address' => $author->address ?? 'Alamat akan diverifikasi oleh auditor',
            'visit_date' => $request->tanggal_kunjungan,
            'visit_purpose' => $request->tujuan,
            'notes' => $request->catatan_admin,
            'status' => 'belum_dikunjungi',
            'created_by' => auth()->id() ?? '1',
        ]);

        return redirect()->route('admin.visits.index')
            ->with('success', 'Penugasan kunjungan berhasil dibuat dengan ID: ' . $visit->visit_id . ' dan ditugaskan kepada ' . $auditor->name);
    }

    /**
     * Display the specified visit
     */
    public function show(Visit $visit)
    {
        $visit->load(['author', 'auditor', 'visitReport']);
        
        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Show the form for editing the specified visit
     */
    public function edit(Visit $visit)
    {
        return view('admin.visits.edit', compact('visit'));
    }

    /**
     * Update the specified visit
     */
    public function update(Request $request, Visit $visit)
    {
        $validator = Validator::make($request->all(), [
            'author_name' => 'required|string|max:255',
            'auditor_name' => 'required|string|max:255',
            'location_address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'visit_date' => 'required|date',
            'status' => 'required|in:pending,konfirmasi,selesai',
            'notes' => 'nullable|string',
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

        $visit->update([
            'author_name' => $request->author_name,
            'auditor_name' => $request->auditor_name,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'visit_date' => $request->visit_date,
            'status' => $request->status,
            'notes' => $request->notes,
            'photos' => $photosPaths,
        ]);

        return redirect()->route('admin.visits.index')
            ->with('success', 'Kunjungan berhasil diperbarui');
    }

    /**
     * Update visit status (for admin approval)
     */
    public function updateStatus(Request $request, Visit $visit)
    {
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
    }

    /**
     * Approve visit report (ACC function)
     */
    public function approve(Visit $visit)
    {
        if ($visit->status !== 'menunggu_acc') {
            return redirect()->back()
                ->with('error', 'Kunjungan tidak dalam status menunggu ACC');
        }

        $visit->update([
            'status' => 'selesai',
            'completed_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Laporan kunjungan telah di-ACC dan diselesaikan');
    }

    /**
     * Reject visit report
     */
    public function reject(Request $request, Visit $visit)
    {
        $request->validate([
            'rejection_notes' => 'required|string'
        ]);

        if ($visit->status !== 'menunggu_acc') {
            return redirect()->back()
                ->with('error', 'Kunjungan tidak dalam status menunggu ACC');
        }

        $visit->update([
            'status' => 'belum_dikunjungi',
            'notes' => $visit->notes . "\n\nDITOLAK: " . $request->rejection_notes,
            'report_notes' => null,
            'selfie_photo' => null,
            'selfie_latitude' => null,
            'selfie_longitude' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Laporan kunjungan ditolak, auditor perlu melakukan kunjungan ulang');
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
        if (!$visit->visitReport) {
            return back()->with('error', 'Laporan tidak ditemukan.');
        }

        $visit->visitReport->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        $visit->update(['status' => 'completed']);

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

        if (!$visit->visitReport) {
            return back()->with('error', 'Laporan tidak ditemukan.');
        }

        $visit->visitReport->update([
            'status' => 'revision_required',
            'admin_notes' => $request->revision_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
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
}