<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VisitHistoryController extends Controller
{
    /**
     * Display a listing of visits received by the author
     */
    public function index(Request $request)
    {
        $author = auth('web')->user() ?? auth()->user();
        
        // Only show completed visits received by this author
        $query = Visit::where('author_id', $author->id)
            ->where('status', 'selesai')
            ->with(['assignedAuditor', 'creator'])
            ->orderBy('completed_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        // Search by auditor name or notes
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('auditor_name', 'like', "%{$search}%")
                  ->orWhere('visit_purpose', 'like', "%{$search}%")
                  ->orWhere('report_notes', 'like', "%{$search}%");
            });
        }

        $visits = $query->paginate(10);

        // Statistics for dashboard cards
        $stats = [
            'total' => Visit::where('author_id', $author->id)->count(),
            'pending' => Visit::where('author_id', $author->id)->where('status', 'pending')->count(),
            'konfirmasi' => Visit::where('author_id', $author->id)->where('status', 'konfirmasi')->count(),
            'selesai' => Visit::where('author_id', $author->id)->where('status', 'selesai')->count(),
        ];

        // Recent visits for chart (last 7 days)
        $recentVisits = Visit::where('author_id', $author->id)
            ->where('visit_date', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(visit_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        return view('author.visit-history.index', compact('visits', 'stats', 'recentVisits'));
    }

    /**
     * Display the specified visit detail
     */
    public function show($id)
    {
        $author = auth('web')->user();
        
        $visit = Visit::where('author_id', $author->id)
            ->where('id', $id)
            ->with(['auditor'])
            ->firstOrFail();

        return view('author.visit-history.show', compact('visit'));
    }
}