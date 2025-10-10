<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminVisitReportController extends Controller
{
    /**
     * Display visit statistics and reports
     */
    public function index(Request $request)
    {
        // Date range filter - Extended to show more data by default
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Basic statistics from database
        $totalVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->count();
        $pendingVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'belum_dikunjungi')->count();
        $completedVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'selesai')->count();
        
        // Additional status counts for better analytics
        $confirmedVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'dikonfirmasi')->count();
        $inProgressVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'dalam_perjalanan')->count();
        $awaitingAccVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'menunggu_acc')->count();
        
        // Visits by author with enhanced data - using relationships
        $visitsByAuthor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->with('author')
            ->whereHas('author')
            ->select('author_id', DB::raw('count(*) as total'))
            ->groupBy('author_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->author_name = $item->author ? $item->author->name : ($item->author_name ?? 'Unknown Author');
                return $item;
            });
        
        // Visits by auditor with enhanced data - using relationships
        $visitsByAuditor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->with('auditor')
            ->whereHas('auditor')
            ->select('auditor_id', DB::raw('count(*) as total'))
            ->groupBy('auditor_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->auditor_name = $item->auditor ? $item->auditor->name : ($item->auditor_name ?? 'Unknown Auditor');
                return $item;
            });
        
        // Visits by status over time (last 30 days)
        $visitsTimeline = Visit::whereBetween('visit_date', [Carbon::now()->subDays(30), Carbon::now()])
            ->select(
                DB::raw('DATE(visit_date) as date'),
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');
        
        // Prepare timeline data for chart
        $timelineData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $visitsTimeline->get($date, collect());
            
            $timelineData[] = [
                'date' => Carbon::parse($date)->format('d M'),
                'belum_dikunjungi' => $dayData->where('status', 'belum_dikunjungi')->sum('total'),
                'dalam_perjalanan' => $dayData->where('status', 'dalam_perjalanan')->sum('total'),
                'sedang_dikunjungi' => $dayData->where('status', 'sedang_dikunjungi')->sum('total'),
                'menunggu_acc' => $dayData->where('status', 'menunggu_acc')->sum('total'),
                'selesai' => $dayData->where('status', 'selesai')->sum('total'),
            ];
        }
        
        // Recent activities with relationships - ensure proper data loading
        $recentVisits = Visit::with(['author', 'auditor'])
            ->orderBy('updated_at', 'desc')
            ->limit(12)
            ->get()
            ->map(function($visit) {
                // Ensure proper name display from relationships
                $visit->author_display_name = $visit->author ? $visit->author->name : ($visit->author_name ?? 'Author tidak tersedia');
                $visit->auditor_display_name = $visit->auditor ? $visit->auditor->name : ($visit->auditor_name ?? null);
                
                // Ensure all fields are properly available
                $visit->visit_id_display = $visit->visit_id ?? ('VIS-' . str_pad($visit->id, 4, '0', STR_PAD_LEFT));
                $visit->status_display = $visit->status ?? 'unknown';
                $visit->location_display = $visit->location_address ?? 'Lokasi tidak tersedia';
                $visit->purpose_display = $visit->purpose ?? 'Tujuan tidak dicantumkan';
                $visit->notes_display = $visit->notes ?? null;
                
                return $visit;
            });
        
        // Debug information for data verification
        \Log::info('AdminVisitReportController: Data loaded', [
            'total_visits' => $totalVisits,
            'visits_by_author_count' => $visitsByAuthor->count(),
            'visits_by_auditor_count' => $visitsByAuditor->count(),
            'recent_visits_count' => $recentVisits->count(),
            'date_range' => "{$startDate} to {$endDate}",
            'sample_author' => $visitsByAuthor->first() ? [
                'name' => $visitsByAuthor->first()->author_name,
                'total' => $visitsByAuthor->first()->total
            ] : null,
            'sample_recent_visit' => $recentVisits->first() ? [
                'id' => $recentVisits->first()->id,
                'author' => $recentVisits->first()->author_display_name,
                'auditor' => $recentVisits->first()->auditor_display_name,
                'status' => $recentVisits->first()->status
            ] : null
        ]);
        
        // No fallback data - always use real database data
        // If empty, it will show proper empty states in the view
        
        return view('admin.visits.reports', compact(
            'totalVisits',
            'pendingVisits', 
            'completedVisits',
            'confirmedVisits',
            'inProgressVisits', 
            'awaitingAccVisits',
            'visitsByAuthor',
            'visitsByAuditor',
            'timelineData',
            'recentVisits',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Export visits data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $visits = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->orderBy('visit_date', 'desc')
            ->get();
        
        if ($format === 'csv') {
            return $this->exportToCsv($visits, $startDate, $endDate);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($request, $startDate, $endDate);
        }
        
        // Default to JSON if format not supported
        return response()->json($visits);
    }
    
    /**
     * Export reports to PDF (server-side alternative)
     */
    public function exportToPdf(Request $request, $startDate = null, $endDate = null)
    {
        // Get the same data as the index method
        $startDate = $startDate ?? $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $endDate ?? $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Get all the statistics
        $totalVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->count();
        $pendingVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'belum_dikunjungi')->count();
        $completedVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'selesai')->count();
        
        // Get top authors and auditors
        $visitsByAuthor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->with('author')
            ->whereHas('author')
            ->select('author_id', DB::raw('count(*) as total'))
            ->groupBy('author_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->author_name = $item->author ? $item->author->name : ($item->author_name ?? 'Unknown Author');
                return $item;
            });
            
        $visitsByAuditor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->with('auditor')
            ->whereHas('auditor')
            ->select('auditor_id', DB::raw('count(*) as total'))
            ->groupBy('auditor_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->auditor_name = $item->auditor ? $item->auditor->name : ($item->auditor_name ?? 'Unknown Auditor');
                return $item;
            });
            
        // Recent visits
        $recentVisits = Visit::with(['author', 'auditor'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        // Return JSON data for client-side PDF generation
        return response()->json([
            'totalVisits' => $totalVisits,
            'pendingVisits' => $pendingVisits,
            'completedVisits' => $completedVisits,
            'successRate' => $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0,
            'visitsByAuthor' => $visitsByAuthor,
            'visitsByAuditor' => $visitsByAuditor,
            'recentVisits' => $recentVisits,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => Carbon::now()->format('d M Y, H:i')
        ]);
    }
    
    /**
     * Export to CSV
     */
    private function exportToCsv($visits, $startDate, $endDate)
    {
        $filename = "visits_export_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($visits) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID Kunjungan',
                'Nama Author',
                'Nama Auditor', 
                'Alamat Lokasi',
                'Latitude',
                'Longitude',
                'Status',
                'Tanggal Kunjungan',
                'Catatan',
                'Dibuat',
                'Diperbarui'
            ]);
            
            // CSV data
            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->visit_id,
                    $visit->author_name,
                    $visit->auditor_name,
                    $visit->location_address,
                    $visit->latitude,
                    $visit->longitude,
                    $visit->status_label['text'],
                    $visit->formatted_visit_date,
                    $visit->notes,
                    $visit->created_at->format('d M Y H:i'),
                    $visit->updated_at->format('d M Y H:i'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}