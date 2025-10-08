<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\VisitReport;
use App\Models\User;
use App\Services\VisitManagementService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AuditorVisitController extends Controller
{
    /**
     * Display list of auditor's assigned visits
     */
    public function index(Request $request)
    {
        $auditor = auth('auditor')->user() ?? auth()->user();
        
        // Only show visits assigned to this auditor
        $query = Visit::assignedTo($auditor->id)
                     ->with(['author', 'creator']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('visit_id', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%")
                  ->orWhere('visit_purpose', 'like', "%{$search}%")
                  ->orWhere('location_address', 'like', "%{$search}%");
            });
        }
        
        $visits = $query->orderBy('visit_date', 'desc')->paginate(10);
        
        // Statistics for this auditor
        $stats = [
            'belum_dikunjungi' => Visit::assignedTo($auditor->id)->where('status', 'belum_dikunjungi')->count(),
            'dalam_perjalanan' => Visit::assignedTo($auditor->id)->where('status', 'dalam_perjalanan')->count(),
            'sedang_dikunjungi' => Visit::assignedTo($auditor->id)->where('status', 'sedang_dikunjungi')->count(),
            'menunggu_acc' => Visit::assignedTo($auditor->id)->where('status', 'menunggu_acc')->count(),
            'selesai' => Visit::assignedTo($auditor->id)->where('status', 'selesai')->count(),
        ];
        
        return view('auditor.visits.index', compact('visits', 'stats'));
    }
    
    /**
     * Start visit (update status to dalam_perjalanan)
     */
    public function startVisit(Visit $visit)
    {
        $auditor = auth('auditor')->user() ?? auth()->user();
        
        // Check if this visit is assigned to current auditor
        if ($visit->assigned_to !== $auditor->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses ke kunjungan ini');
        }
        
        if ($visit->status !== 'belum_dikunjungi') {
            return redirect()->back()
                ->with('error', 'Status kunjungan tidak dapat diubah');
        }
        
        $visit->update(['status' => 'dalam_perjalanan']);
        
        return redirect()->back()
            ->with('success', 'Status kunjungan diubah menjadi "Dalam Perjalanan"');
    }
    
    /**
     * Arrive at location (update status to sedang_dikunjungi)
     */
    public function arriveAtLocation(Visit $visit)
    {
        $auditor = auth('auditor')->user() ?? auth()->user();
        
        // Check if this visit is assigned to current auditor
        if ($visit->assigned_to !== $auditor->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses ke kunjungan ini');
        }
        
        if ($visit->status !== 'dalam_perjalanan') {
            return redirect()->back()
                ->with('error', 'Status kunjungan tidak dapat diubah');
        }
        
        $visit->update(['status' => 'sedang_dikunjungi']);
        
        return redirect()->back()
            ->with('success', 'Status kunjungan diubah menjadi "Sedang Dikunjungi"');
    }
    
    /**
     * Display the specified visit.
     */
    public function show(Visit $visit)
    {
        $visit->load(['author', 'auditor', 'visitReport']);
        
        return view('visits.detail-modal', compact('visit'));
    }
    
    /**
     * Show reporting form
     */
    public function report($id)
    {
        $auditor = auth('auditor')->user() ?? auth()->user();
        $visit = Visit::assignedTo($auditor->id)->with(['author'])->findOrFail($id);
        
        // Check if visit can be reported
        if (!$visit->canBeEditedByAuditor()) {
            return redirect()->back()
                ->with('error', 'Kunjungan ini tidak dapat dilaporkan');
        }
        
        return view('auditor.visits.report', compact('visit'));
    }
    
    /**
     * Submit visit report
     */
    public function submitReport(Request $request, $id)
    {
        $auditor = auth('auditor')->user() ?? auth()->user();
        $visit = Visit::assignedTo($auditor->id)->findOrFail($id);
        
        // Check if visit can be reported
        if (!$visit->canBeEditedByAuditor()) {
            return redirect()->back()
                ->with('error', 'Kunjungan ini tidak dapat dilaporkan');
        }
        
        $validated = $request->validate([
            'report_notes' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Handle selfie upload
        $selfiePath = null;
        if ($request->hasFile('selfie_photo')) {
            $selfiePath = $request->file('selfie_photo')->store('visits/selfies', 'public');
        }
        
        // Handle additional photos
        $photosPaths = $visit->photos ?? [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('visits', 'public');
                $photosPaths[] = $path;
            }
        }
        
        // Update visit with report
        $visit->update([
            'report_notes' => $validated['report_notes'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'selfie_photo' => $selfiePath,
            'selfie_latitude' => $validated['latitude'],
            'selfie_longitude' => $validated['longitude'],
            'photos' => $photosPaths,
            'status' => 'menunggu_acc'
        ]);
        
        return redirect()->route('auditor.visits.index')
            ->with('success', 'Laporan kunjungan berhasil dikirim dan menunggu ACC dari admin');
    }
    
    /**
     * Show map view
     */
    public function map()
    {
        $auditor = auth('auditor')->user();
        $visits = Visit::where('auditor_id', $auditor->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        
        return view('auditor.visits.map', compact('visits'));
    }
    
    /**
     * Show statistics
     */
    public function statistics()
    {
        $auditor = auth('auditor')->user();
        
        // Basic stats
        $totalVisits = Visit::where('auditor_id', $auditor->id)->count();
        $pendingVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'pending')->count();
        $confirmedVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'konfirmasi')->count();
        $completedVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'selesai')->count();
        
        // Monthly stats
        $monthlyStats = Visit::where('auditor_id', $auditor->id)
            ->whereYear('visit_date', Carbon::now()->year)
            ->selectRaw('MONTH(visit_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyStats[$i])) {
                $monthlyStats[$i] = 0;
            }
        }
        ksort($monthlyStats);
        
        return view('auditor.visits.statistics', compact(
            'totalVisits',
            'pendingVisits',
            'confirmedVisits', 
            'completedVisits',
            'monthlyStats'
        ));
    }

    /**
     * Complete a visit with selfie and audit notes
     */
    public function complete(Request $request, Visit $visit)
    {
        // Validate required fields
        $request->validate([
            'notes' => 'required|string|min:10|max:2000',
            'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'selfie_latitude' => 'nullable|numeric|between:-90,90',
            'selfie_longitude' => 'nullable|numeric|between:-180,180',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Additional photos
        ], [
            'notes.required' => 'Catatan/keterangan audit harus diisi',
            'notes.min' => 'Catatan minimal 10 karakter',
            'notes.max' => 'Catatan maksimal 2000 karakter',
            'selfie_photo.required' => 'Foto selfie harus diambil',
            'selfie_photo.image' => 'File harus berupa gambar',
            'selfie_photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG',
            'selfie_photo.max' => 'Ukuran foto maksimal 5MB',
        ]);

        try {
            $visitManagementService = new VisitManagementService();
            $result = $visitManagementService->completeVisit($visit, $request, Auth::user());
            
            return redirect()->back()->with('success', $result['message']);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
