<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class AuditorDashboardController extends Controller
{
    public function index()
    {
        try {
            $auditor = auth()->user();
            
            if (!$auditor || $auditor->role !== 'auditor') {
                return redirect()->route('auth.login')->with('error', 'Please login as auditor');
            }
            
            // Get statistics for the auditor using auditor_id
            $totalVisits = Visit::where('auditor_id', $auditor->id)->count();
            $belumDikunjungiVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'belum_dikunjungi')->count();
            $dalamPerjalananVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'dalam_perjalanan')->count();
            $sedangDikunjungiVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'sedang_dikunjungi')->count();
            $menungguAccVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'menunggu_acc')->count();
            $completedVisits = Visit::where('auditor_id', $auditor->id)->where('status', 'selesai')->count();
        
            // Recent visits for the auditor
            $recentVisits = Visit::where('auditor_id', $auditor->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Visits by status for charts
            $visitsByStatus = [
                'belum_dikunjungi' => $belumDikunjungiVisits,
                'dalam_perjalanan' => $dalamPerjalananVisits,
                'sedang_dikunjungi' => $sedangDikunjungiVisits,
                'menunggu_acc' => $menungguAccVisits,
                'selesai' => $completedVisits
            ];
            
            // Today's visits
            $todayVisits = Visit::where('auditor_name', $auditor->name)
                ->whereDate('visit_date', Carbon::today())
                ->orderBy('visit_date', 'asc')
                ->get();
        
            return view('auditor.dashboard', compact(
                'totalVisits',
                'belumDikunjungiVisits', 
                'dalamPerjalananVisits',
                'sedangDikunjungiVisits',
                'menungguAccVisits',
                'completedVisits',
                'recentVisits',
                'visitsByStatus',
                'todayVisits'
            ));
        } catch (\Exception $e) {
            \Log::error('Auditor Dashboard Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return view('auditor.dashboard', [
                'totalVisits' => 0,
                'belumDikunjungiVisits' => 0,
                'dalamPerjalananVisits' => 0,
                'sedangDikunjungiVisits' => 0,
                'menungguAccVisits' => 0,
                'completedVisits' => 0,
                'recentVisits' => collect(),
                'visitsByStatus' => ['belum_dikunjungi' => 0, 'dalam_perjalanan' => 0, 'sedang_dikunjungi' => 0, 'menunggu_acc' => 0, 'selesai' => 0],
                'todayVisits' => collect()
            ])->with('error', 'Dashboard data could not be loaded. Please try again.');
        }
    }
}
