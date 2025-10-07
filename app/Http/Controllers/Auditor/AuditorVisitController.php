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
        
        $query = Visit::where('auditor_id', $auditor->id);

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

        $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai'];
        
        // Statistics for auditor
        $stats = [
            'total' => Visit::where('auditor_id', $auditor->id)->count(),
            'belum_dikunjungi' => Visit::where('auditor_id', $auditor->id)->where('status', 'belum_dikunjungi')->count(),
            'dalam_perjalanan' => Visit::where('auditor_id', $auditor->id)->where('status', 'dalam_perjalanan')->count(),
            'sedang_dikunjungi' => Visit::where('auditor_id', $auditor->id)->where('status', 'sedang_dikunjungi')->count(),
            'menunggu_acc' => Visit::where('auditor_id', $auditor->id)->where('status', 'menunggu_acc')->count(),
            'selesai' => Visit::where('auditor_id', $auditor->id)->where('status', 'selesai')->count(),
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
        if ($visit->auditor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        $visit->load(['author', 'visitReport']);

        return view('auditor.visits.show', compact('visit'));
    }

    /**
     * Show form for creating visit report
     */
    public function createReport(Visit $visit)
    {
        // Check if the visit is assigned to current auditor
        if ($visit->auditor_id !== Auth::id()) {
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

        $visit->load('author');

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

        $visit->load(['author', 'visitReport']);

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
}