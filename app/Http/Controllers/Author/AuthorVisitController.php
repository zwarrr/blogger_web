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
        
        $query = Visit::query()
                      ->where('author_name', $author->name);

        // Apply filters
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        if ($request->filled('date_filter')) {
            $query->whereDate('visit_date', $request->date_filter);
        }

        if ($request->filled('search')) {
            $query->where('auditor_name', 'like', '%' . $request->search . '%');
        }
        
        $visits = $query->orderBy('visit_date', 'desc')->paginate(15);

        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

        // Check if this is an AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json([
                'html' => view('author.visits.table-rows', compact('visits'))->render(),
                'pagination' => $visits->appends($request->all())->render()
            ]);
        }

        return view('author.visits.index', compact('visits', 'statuses'));
    }

    /**
     * Show visit details for author
     */
    public function show(Visit $visit)
    {
        // Check if the visit belongs to current author
        if ($visit->author_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Load removed - using name-based fields

        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Show visit details via AJAX for modal
     */
    public function detail(Visit $visit)
    {
        // Check if the visit belongs to current author
        if ($visit->author_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        // Load removed - using name-based fields

        return view('visits.detail-modal', compact('visit'));
    }

    /**
     * Confirm visit by author
     */
    public function confirm(Visit $visit)
    {
        try {
            \Log::info('AuthorVisitController::confirm - Starting', [
                'visit_id' => $visit->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown'
            ]);

            // Check if the visit belongs to current author
            if ($visit->author_name !== Auth::user()->name) {
                \Log::warning('AuthorVisitController::confirm - Access denied', [
                    'visit_author' => $visit->author_name,
                    'current_user' => Auth::user()->name
                ]);
                abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
            }

            if (!$visit->canBeConfirmed()) {
                \Log::warning('AuthorVisitController::confirm - Cannot be confirmed', [
                    'visit_status' => $visit->status
                ]);
                return back()->with('error', 'Kunjungan ini tidak dapat dikonfirmasi.');
            }

            $visit->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => Auth::id()
            ]);

            \Log::info('AuthorVisitController::confirm - Success', [
                'visit_id' => $visit->id,
                'new_status' => $visit->fresh()->status
            ]);

            return back()->with('success', 'Kunjungan berhasil dikonfirmasi. Auditor akan segera memproses kunjungan.');
            
        } catch (\Exception $e) {
            \Log::error('AuthorVisitController::confirm - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat konfirmasi kunjungan.');
        }
    }

    /**
     * Reschedule visit by author
     */
    public function reschedule(Request $request, Visit $visit)
    {
        // Check if the visit belongs to current author
        if ($visit->author_name !== Auth::user()->name) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        if (!$visit->canBeRescheduled()) {
            return back()->with('error', 'Kunjungan ini tidak dapat diundur lagi. Batas pengunduran jadwal sudah tercapai (3x).');
        }

        $request->validate([
            'new_visit_date' => 'required|date|after:today',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        $newRescheduleCount = $visit->reschedule_count + 1;
        $visit->update([
            'visit_date' => $request->new_visit_date,
            'reschedule_count' => $newRescheduleCount,
            'notes' => ($visit->notes ? $visit->notes . "\n\n" : '') . 
                      "PENGUNDURAN JADWAL #{$newRescheduleCount} pada " . now()->format('d/m/Y H:i') . 
                      " - Alasan: " . $request->reschedule_reason
        ]);

        $remainingAttempts = $visit->remaining_reschedule_attempts;
        $message = 'Jadwal kunjungan berhasil diundur.';
        if ($remainingAttempts > 0) {
            $message .= " Sisa kesempatan mengundur jadwal: {$remainingAttempts}x";
        } else {
            $message .= " Tidak ada lagi kesempatan mengundur jadwal.";
        }

        return back()->with('success', $message);
    }
}
