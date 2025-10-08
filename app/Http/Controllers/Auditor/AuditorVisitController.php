<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuditorVisitController extends Controller
{
    /**
     * Display list of visits assigned to the auditor with filtering
     */
    public function index(Request $request)
    {
        $auditor = Auth::user();
        
        if (!$auditor) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        $query = Visit::query()
                      ->where('auditor_name', $auditor->name);

        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('visit_date', $request->date_filter);
        }

        if ($request->filled('search')) {
            $query->where('author_name', 'like', '%' . $request->search . '%');
        }
        
        $visits = $query->orderBy('visit_date', 'desc')->paginate(15);

        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        // Statistics for auditor
        $stats = [
            'total' => Visit::where('auditor_id', $auditor->id)->count(),
            'pending' => Visit::where('auditor_id', $auditor->id)->where('status', 'pending')->count(),
            'confirmed' => Visit::where('auditor_id', $auditor->id)->where('status', 'confirmed')->count(),
            'in_progress' => Visit::where('auditor_id', $auditor->id)->where('status', 'in_progress')->count(),
            'completed' => Visit::where('auditor_id', $auditor->id)->where('status', 'completed')->count(),
            'cancelled' => Visit::where('auditor_id', $auditor->id)->where('status', 'cancelled')->count(),
        ];

        // Check if this is an AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json([
                'html' => view('visits.table-rows', compact('visits'))->render(),
                'pagination' => $visits->appends($request->all())->render()
            ]);
        }

        return view('auditor.visits.index', compact('visits', 'statuses', 'stats'));
    }

    /**
     * Show visit details for reporting
     */
    public function show(Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Load removed - using name-based fields

        return view('auditor.visits.show', compact('visit'));
    }

    /**
     * Show visit details via AJAX for modal
     */
    public function detail(Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Load removed - using name-based fields

        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Show form for creating visit report
     */
    public function createReport(Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Check if visit can be reported
        if (!$visit->canBeReported()) {
            return redirect()->route('auditor.visits.show', $visit)
                ->with('error', 'Kunjungan ini tidak dapat dilaporkan pada status saat ini.');
        }

        // Check if report already exists
        if ($visit->visitReport) {
            return redirect()->route('auditor.visits.show', $visit)
                ->with('info', 'Laporan untuk kunjungan ini sudah ada.');
        }

        // Load removed - using name-based fields

        return view('auditor.visits.create-report', compact('visit'));
    }

    /**
     * Store visit report
     */
    public function storeReport(Request $request, Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Check if visit can be reported
        if (!$visit->canBeReported()) {
            throw ValidationException::withMessages([
                'status' => 'Kunjungan ini tidak dapat dilaporkan pada status saat ini.'
            ]);
        }

        // Check if report already exists
        if ($visit->visitReport) {
            throw ValidationException::withMessages([
                'duplicate' => 'Laporan untuk kunjungan ini sudah ada.'
            ]);
        }

        $validated = $request->validate([
            'tanggal_kunjungan_aktual' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi_kunjungan' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'hasil_kunjungan' => 'required|string|max:2000',
            'temuan' => 'nullable|string|max:1500',
            'rekomendasi' => 'nullable|string|max:1500',
            'status_kunjungan' => 'required|in:berhasil,tidak_berhasil,tertunda',
            'kendala' => 'nullable|string|max:1000',
            'foto_kunjungan.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120', // 5MB max per file
            'dokumen_pendukung.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB max per file
            'catatan_auditor' => 'nullable|string|max:1000',
        ], [
            'tanggal_kunjungan_aktual.required' => 'Tanggal kunjungan aktual wajib diisi.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'lokasi_kunjungan.required' => 'Lokasi kunjungan wajib diisi.',
            'hasil_kunjungan.required' => 'Hasil kunjungan wajib diisi.',
            'status_kunjungan.required' => 'Status kunjungan wajib dipilih.',
            'foto_kunjungan.*.image' => 'File foto harus berupa gambar (JPEG, JPG, PNG).',
            'foto_kunjungan.*.max' => 'Ukuran foto maksimal 5MB.',
            'dokumen_pendukung.*.max' => 'Ukuran dokumen maksimal 10MB.',
        ]);

        try {
            // Handle file uploads
            $fotoFiles = [];
            $dokumenFiles = [];

            // Process photo uploads
            if ($request->hasFile('foto_kunjungan')) {
                foreach ($request->file('foto_kunjungan') as $index => $file) {
                    if ($file->isValid()) {
                        $filename = 'visit_' . $visit->id . '_photo_' . ($index + 1) . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('visit-reports/photos', $filename, 'public');
                        $fotoFiles[] = $path;
                    }
                }
            }

            // Process document uploads
            if ($request->hasFile('dokumen_pendukung')) {
                foreach ($request->file('dokumen_pendukung') as $index => $file) {
                    if ($file->isValid()) {
                        $filename = 'visit_' . $visit->id . '_doc_' . ($index + 1) . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('visit-reports/documents', $filename, 'public');
                        $dokumenFiles[] = $path;
                    }
                }
            }

            // Create visit report
            $visitReport = VisitReport::create([
                'visit_id' => $visit->id,
                'auditor_id' => Auth::id(),
                'tanggal_kunjungan_aktual' => $validated['tanggal_kunjungan_aktual'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'lokasi_kunjungan' => $validated['lokasi_kunjungan'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'hasil_kunjungan' => $validated['hasil_kunjungan'],
                'temuan' => $validated['temuan'] ?? null,
                'rekomendasi' => $validated['rekomendasi'] ?? null,
                'status_kunjungan' => $validated['status_kunjungan'],
                'kendala' => $validated['kendala'] ?? null,
                'foto_kunjungan' => !empty($fotoFiles) ? json_encode($fotoFiles) : null,
                'dokumen_pendukung' => !empty($dokumenFiles) ? json_encode($dokumenFiles) : null,
                'catatan_auditor' => $validated['catatan_auditor'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Update visit status
            $visit->update([
                'status' => 'completed'
            ]);

            return redirect()->route('auditor.visits.show', $visit)
                ->with('success', 'Laporan kunjungan berhasil dibuat dan disubmit untuk review.');

        } catch (\Exception $e) {
            // Clean up uploaded files if database transaction fails
            foreach (array_merge($fotoFiles, $dokumenFiles) as $filePath) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Show visit report details
     */
    public function showReport(Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        if (!$visit->visitReport) {
            return redirect()->route('auditor.visits.show', $visit)
                ->with('error', 'Laporan untuk kunjungan ini belum dibuat.');
        }

        // Load removed - using name-based fields

        return view('auditor.visits.show-report', compact('visit'));
    }

    /**
     * Update visit status (for accepting/declining assignments)
     */
    public function updateStatus(Request $request, Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,declined',
            'alasan_penolakan' => 'required_if:status,declined|nullable|string|max:500'
        ]);

        if ($validated['status'] === 'declined') {
            $visit->update([
                'status' => 'declined',
                'alasan_penolakan' => $validated['alasan_penolakan']
            ]);

            return redirect()->route('auditor.visits.index')
                ->with('success', 'Penugasan kunjungan berhasil ditolak.');
        } else {
            $visit->update([
                'status' => 'accepted'
            ]);

            return redirect()->route('auditor.visits.show', $visit)
                ->with('success', 'Penugasan kunjungan berhasil diterima.');
        }
    }

    /**
     * Show map view of visits
     */
    public function map()
    {
        $auditor = Auth::user();
        
        if (!$auditor) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }
        
        $visits = Visit::where('auditor_id', $auditor->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('auditor.visits.map', compact('visits'));
    }

    /**
     * Start visit process
     */
    public function startProcess(Visit $visit)
    {
        try {
            \Log::info('AuditorVisitController::startProcess - Starting', [
                'visit_id' => $visit->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown'
            ]);

            // Check if the visit is assigned to current auditor
            if ($visit->auditor_name !== Auth::user()->name) {
                \Log::warning('AuditorVisitController::startProcess - Access denied', [
                    'visit_auditor' => $visit->auditor_name,
                    'current_user' => Auth::user()->name
                ]);
                abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
            }

            if (!$visit->canBeStarted()) {
                \Log::warning('AuditorVisitController::startProcess - Cannot be started', [
                    'visit_status' => $visit->status
                ]);
                return back()->with('error', 'Kunjungan ini tidak dapat diproses. Pastikan sudah dikonfirmasi oleh Author terlebih dahulu.');
            }

            $visit->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);

            \Log::info('AuditorVisitController::startProcess - Success', [
                'visit_id' => $visit->id,
                'new_status' => $visit->fresh()->status
            ]);

            return back()->with('success', 'Proses kunjungan telah dimulai.');
            
        } catch (\Exception $e) {
            \Log::error('AuditorVisitController::startProcess - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat memulai proses kunjungan.');
        }
    }

    /**
     * Complete visit with required information
     */
    public function complete(Request $request, Visit $visit)
    {
        try {
            \Log::info('AuditorVisitController::complete - Starting', [
                'visit_id' => $visit->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown'
            ]);

            // Check if the visit is assigned to current auditor
            if ($visit->auditor_name !== Auth::user()->name) {
                \Log::warning('AuditorVisitController::complete - Access denied', [
                    'visit_auditor' => $visit->auditor_name,
                    'current_user' => Auth::user()->name
                ]);
                abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
            }

            // Allow completion from both confirmed and in_progress status
            if (!in_array($visit->status, ['confirmed', 'in_progress'])) {
                \Log::warning('AuditorVisitController::complete - Cannot be completed', [
                    'visit_status' => $visit->status
                ]);
                return back()->with('error', 'Kunjungan ini tidak dapat diselesaikan. Pastikan sudah dikonfirmasi oleh Author terlebih dahulu.');
            }

            $request->validate([
                'auditor_notes' => 'required|string|max:1000',
                'selfie_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'photos' => 'nullable|array|max:5',
                'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);

            // Store selfie photo with GPS coordinates if available
            $selfiePath = null;
            if ($request->hasFile('selfie_photo')) {
                $selfiePath = $request->file('selfie_photo')->store('visits/selfies', 'public');  
                \Log::info('AuditorVisitController::complete - Selfie uploaded', [
                    'path' => $selfiePath
                ]);
            }

            // Store additional photos
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $photoPaths[] = $photo->store('visits/photos', 'public');
                }
                \Log::info('AuditorVisitController::complete - Additional photos uploaded', [
                    'count' => count($photoPaths)
                ]);
            }

            // Get GPS coordinates from request if available
            $selfieLatitude = $request->input('selfie_latitude');
            $selfieLongitude = $request->input('selfie_longitude');

            // Prepare update data
            $updateData = [
                'status' => 'completed',
                'completed_at' => now(),
                'auditor_notes' => $request->auditor_notes,
                'selfie_photo' => $selfiePath,
                'selfie_latitude' => $selfieLatitude,
                'selfie_longitude' => $selfieLongitude,
                'photos' => array_merge($visit->photos ?? [], $photoPaths)
            ];

            // If coming from confirmed status, also set started_at
            if ($visit->status === 'confirmed') {
                $updateData['started_at'] = now();
            }

            $visit->update($updateData);

            \Log::info('AuditorVisitController::complete - Visit completed successfully', [
                'visit_id' => $visit->id,
                'selfie_coordinates' => $selfieLatitude && $selfieLongitude ? "{$selfieLatitude},{$selfieLongitude}" : 'Not provided'
            ]);

            return back()->with('success', 'Kunjungan berhasil diselesaikan.');
            
        } catch (\Exception $e) {
            \Log::error('AuditorVisitController::complete - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan kunjungan.');
        }
    }

    /**
     * Display visit statistics for auditor
     */
    public function statistics()
    {
        try {
            $auditor = Auth::user();
            
            if (!$auditor) {
                return redirect()->route('login')->with('error', 'Please login to access this page.');
            }

            // Get statistics for the auditor
            $stats = [
                'total' => Visit::where('auditor_name', $auditor->name)->count(),
                'pending' => Visit::where('auditor_name', $auditor->name)->where('status', 'pending')->count(),
                'confirmed' => Visit::where('auditor_name', $auditor->name)->where('status', 'confirmed')->count(),
                'in_progress' => Visit::where('auditor_name', $auditor->name)->where('status', 'in_progress')->count(),
                'completed' => Visit::where('auditor_name', $auditor->name)->where('status', 'completed')->count(),
                'cancelled' => Visit::where('auditor_name', $auditor->name)->where('status', 'cancelled')->count(),
            ];

            // Monthly statistics
            $monthlyStats = Visit::where('auditor_name', $auditor->name)
                ->selectRaw('MONTH(visit_date) as month, COUNT(*) as count')
                ->whereYear('visit_date', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return view('auditor.visits.statistics', compact('stats', 'monthlyStats'));
            
        } catch (\Exception $e) {
            \Log::error('AuditorVisitController::statistics - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat memuat statistik.');
        }
    }

}
