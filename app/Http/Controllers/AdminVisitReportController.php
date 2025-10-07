<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        // Date range filter
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Basic statistics
        $totalVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->count();
        $pendingVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'belum_dikunjungi')->count();
        $completedVisits = Visit::whereBetween('visit_date', [$startDate, $endDate])->where('status', 'selesai')->count();
        
        // Visits by author
        $visitsByAuthor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->select('author_name', DB::raw('count(*) as total'))
            ->groupBy('author_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        // Visits by auditor
        $visitsByAuditor = Visit::whereBetween('visit_date', [$startDate, $endDate])
            ->select('auditor_name', DB::raw('count(*) as total'))
            ->groupBy('auditor_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
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
        
        // Recent activities
        $recentVisits = Visit::orderBy('updated_at', 'desc')->limit(10)->get();
        
        return view('admin.visits.reports', compact(
            'totalVisits',
            'pendingVisits', 
            'completedVisits',
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
        }
        
        // Default to JSON if format not supported
        return response()->json($visits);
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