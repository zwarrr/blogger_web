<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Visit;

class AdminVisitMapController extends Controller
{
    /**
     * Display visits map
     */
    public function index(Request $request)
    {
        $query = Visit::query();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Only get visits with coordinates
        $visits = $query->whereNotNull('latitude')
                       ->whereNotNull('longitude')
                       ->orderBy('visit_date', 'desc')
                       ->get();
        
        // Prepare data for map
        $mapData = $visits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'visit_id' => $visit->visit_id,
                'author_name' => $visit->author_name,
                'auditor_name' => $visit->auditor_name,
                'location_address' => $visit->location_address,
                'latitude' => (float) $visit->latitude,
                'longitude' => (float) $visit->longitude,
                'status' => $visit->status,
                'status_label' => $visit->status_label,
                'visit_date' => $visit->formatted_visit_date,
                'notes' => $visit->notes,
            ];
        });
        
        // Statistics for map
        $stats = [
            'total_with_coordinates' => $visits->count(),
            'pending' => $visits->where('status', 'pending')->count(),
            'konfirmasi' => $visits->where('status', 'konfirmasi')->count(),
            'selesai' => $visits->where('status', 'selesai')->count(),
        ];
        
        return view('admin.visits.map', compact('mapData', 'stats'));
    }
}