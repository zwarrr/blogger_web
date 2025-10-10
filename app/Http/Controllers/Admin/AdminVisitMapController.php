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
        // Build query with comprehensive relations and data
        $query = Visit::with([
            'author:id,name,email,phone', 
            'auditor:id,name,email,phone'
        ]);
        
        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get all visits first for debugging
        $allVisitsForDebug = $query->get();
        
        // Reset query for coordinates filtering
        $query = Visit::with([
            'author:id,name,email,phone', 
            'auditor:id,name,email,phone'
        ]);
        
        // Apply status filter again if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get visits with valid coordinates (either main coordinates or selfie coordinates)
        $visits = $query->where(function($q) {
                           $q->where(function($subQuery) {
                               // Main coordinates
                               $subQuery->whereNotNull('latitude')
                                       ->whereNotNull('longitude')
                                       ->where('latitude', '!=', 0)
                                       ->where('longitude', '!=', 0);
                           })->orWhere(function($subQuery) {
                               // Selfie coordinates as fallback
                               $subQuery->whereNotNull('selfie_latitude')
                                       ->whereNotNull('selfie_longitude')
                                       ->where('selfie_latitude', '!=', 0)
                                       ->where('selfie_longitude', '!=', 0);
                           });
                       })
                       ->orderBy('visit_date', 'desc')
                       ->get();
                       
        // Debug information
        \Log::info('Total visits in database: ' . $allVisitsForDebug->count());
        \Log::info('Visits with coordinates: ' . $visits->count());
        if ($visits->count() > 0) {
            \Log::info('Sample visit with coordinates: ', [
                'id' => $visits->first()->id,
                'latitude' => $visits->first()->latitude,
                'longitude' => $visits->first()->longitude,
                'status' => $visits->first()->status
            ]);
        }
        
        // If no visits with coordinates found, try to get all visits for debugging
        if ($visits->count() === 0) {
            \Log::info('No visits with coordinates found. Checking all visits in database...');
            
            // Get all visits without coordinate filter for debugging
            $allVisitsForMap = Visit::with(['author:id,name,email', 'auditor:id,name,email'])->get();
            \Log::info('Total visits without coordinate filter: ' . $allVisitsForMap->count());
            
            if ($allVisitsForMap->count() > 0) {
                // Log first few visits for debugging
                foreach ($allVisitsForMap->take(3) as $visit) {
                    \Log::info('Visit debug: ', [
                        'id' => $visit->id,
                        'latitude' => $visit->latitude,
                        'longitude' => $visit->longitude,
                        'status' => $visit->status,
                        'location_address' => $visit->location_address
                    ]);
                }
            }
            
            // Show message that no visits with coordinates found
            \Log::warning('No visits with coordinates found in database');
            $mapData = collect([]);
        } else {
            // Prepare comprehensive data for map from database
            $mapData = $visits->map(function ($visit) {
                // Get status labels that match database values
                $statusLabels = [
                    'belum_dikunjungi' => 'Belum Dikunjungi',
                    'dikonfirmasi' => 'Dikonfirmasi',
                    'dalam_perjalanan' => 'Dalam Perjalanan', 
                    'selesai' => 'Selesai',
                    'menunggu_acc' => 'Menunggu ACC'
                ];
                
                // Use visit_id from database or generate formatted visit ID (VST0001, VST0002, etc.)
                $visitId = $visit->visit_id ?: 'VST' . str_pad($visit->id, 4, '0', STR_PAD_LEFT);
                
                return [
                    'id' => $visit->id,
                    'visit_id' => $visitId,
                    'purpose' => $visit->visit_purpose ?? $visit->purpose ?? 'Tujuan kunjungan tidak disebutkan',
                    'author' => $visit->author ? [
                        'name' => $visit->author->name,
                        'email' => $visit->author->email,
                        'phone' => $visit->author->phone
                    ] : null,
                    'author_name' => $visit->author ? $visit->author->name : ($visit->author_name ?? 'Data author tidak tersedia'),
                    'author_email' => $visit->author ? $visit->author->email : null,
                    'author_phone' => $visit->author ? $visit->author->phone : null,
                    'auditor' => $visit->auditor ? [
                        'name' => $visit->auditor->name,
                        'email' => $visit->auditor->email,
                        'phone' => $visit->auditor->phone
                    ] : null,
                    'auditor_name' => $visit->auditor ? $visit->auditor->name : ($visit->auditor_name ?? 'Data auditor tidak tersedia'),
                    'auditor_email' => $visit->auditor ? $visit->auditor->email : null,
                    'auditor_phone' => $visit->auditor ? $visit->auditor->phone : null,
                    'location_address' => $visit->location_address ?? 'Alamat tidak tersedia',
                    // Use main coordinates if available, otherwise use selfie coordinates
                    'latitude' => (float) ($visit->latitude && $visit->latitude != 0 ? $visit->latitude : $visit->selfie_latitude),
                    'longitude' => (float) ($visit->longitude && $visit->longitude != 0 ? $visit->longitude : $visit->selfie_longitude),
                    'using_selfie_location' => ($visit->latitude == null || $visit->latitude == 0) && $visit->selfie_latitude,
                    'status' => $visit->status,
                    'status_label' => $statusLabels[$visit->status] ?? ucfirst($visit->status),
                    'visit_date' => $visit->visit_date,
                    'notes' => $visit->notes ?? '',
                    'duration' => $visit->duration ?? 'Belum ditentukan',
                    // Report data from Visit model directly (no separate report table yet)
                    'report_notes' => $visit->report_notes ?? '',
                    'auditor_notes' => $visit->auditor_notes ?? '',
                    'selfie_photo' => $visit->selfie_photo ?? null,
                    'selfie_latitude' => $visit->selfie_latitude ?? null,
                    'selfie_longitude' => $visit->selfie_longitude ?? null,
                    'photos' => $visit->photos ? (is_string($visit->photos) ? json_decode($visit->photos, true) : $visit->photos) : [],
                    'created_at' => $visit->created_at->format('d M Y, H:i'),
                    'updated_at' => $visit->updated_at->format('d M Y, H:i'),
                    'author_id' => $visit->author_id,
                    'auditor_id' => $visit->auditor_id,
                ];
            });
        }
        
        // Calculate comprehensive statistics from database
        $allVisits = Visit::all(); // Get all visits for overall stats
        $visitsWithCoords = Visit::where(function($q) {
            $q->where(function($subQuery) {
                // Main coordinates
                $subQuery->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->where('latitude', '!=', 0)
                        ->where('longitude', '!=', 0);
            })->orWhere(function($subQuery) {
                // Selfie coordinates as fallback
                $subQuery->whereNotNull('selfie_latitude')
                        ->whereNotNull('selfie_longitude')
                        ->where('selfie_latitude', '!=', 0)
                        ->where('selfie_longitude', '!=', 0);
            });
        })->get();
        
        // Get status counts from database
        $statusCounts = [
            'belum_dikunjungi' => Visit::where('status', 'belum_dikunjungi')->count(),
            'dikonfirmasi' => Visit::where('status', 'dikonfirmasi')->count(), 
            'dalam_perjalanan' => Visit::where('status', 'dalam_perjalanan')->count(),
            'selesai' => Visit::where('status', 'selesai')->count(),
            'menunggu_acc' => Visit::where('status', 'menunggu_acc')->count(),
        ];
        
        // Use real data from database only
        $totalVisits = $allVisits->count();
        $coordsCount = $visitsWithCoords->count();
        
        $stats = [
            'total_visits' => $totalVisits,
            'total_with_coordinates' => $coordsCount,
            'belum_dikunjungi' => $statusCounts['belum_dikunjungi'],
            'dikonfirmasi' => $statusCounts['dikonfirmasi'],
            'dalam_perjalanan' => $statusCounts['dalam_perjalanan'],
            'selesai' => $statusCounts['selesai'],
            'menunggu_acc' => $statusCounts['menunggu_acc'],
            'percentage_with_coords' => $totalVisits > 0 ? round(($coordsCount / $totalVisits) * 100, 1) : 0,
        ];
        
        \Log::info('Database statistics calculated:', $stats);
        
        return view('admin.visits.map', compact('mapData', 'stats'));
    }
}