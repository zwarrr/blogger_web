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
        
        $query = Visit::where('author_name', $author->name);

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

        $statuses = ['belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai'];

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
        if ($visit->author_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kunjungan ini.');
        }

        $visit->load(['author', 'auditor', 'visitReport']);

        return view('visits.detail-modal', compact('visit'));
    }
}