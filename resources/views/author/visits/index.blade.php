@extends('author.layout')

@section('title', 'Riwayat Kunjungan - Author Panel')
@section('page-title', 'Riwayat Kunjungan')
@section('page-description', 'Kelola dan pantau jadwal kunjungan auditor')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    [x-cloak] { display: none !important; }
    
    /* Enhanced Table Styling - Sama persis dengan Auditor */
    .table-enhanced tbody tr:hover {
        background: linear-gradient(90deg, rgba(239, 246, 255, 0.5), rgba(219, 234, 254, 0.3));
    }
    
    /* Professional Orange Marker Styling */
    .professional-orange-marker {
        border: none !important;
        background: transparent !important;
    }
    
    .professional-orange-marker svg {
        display: block;
        margin: 0 auto;
    }
    
    /* Dropdown Positioning Fix */
    .visits-table-container {
        overflow: visible !important;
    }
    
    .table-overflow-wrapper {
        overflow: visible !important;
    }
    
    /* Force all containers to allow dropdown visibility */
    main, 
    .p-8,
    .bg-white.rounded-lg.border.border-gray-200,
    .table-container,
    .overflow-x-auto,
    table,
    tbody,
    tr,
    td {
        overflow: visible !important;
        position: relative !important;
    }
    
    /* Dropdown positioning */
    .relative {
        position: relative !important;
    }
    
    /* Table dropdown styling - untuk dropdown di dalam tabel */
    tbody [id^="dropdown-"] {
        position: fixed !important;
        z-index: 99999 !important;
        min-width: 180px;
        max-width: 220px;
        white-space: nowrap;
        background: white !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Remove all outlines and borders from table dropdown elements */
    tbody [id^="dropdown-"] button,
    tbody [id^="dropdown-"] a {
        outline: none !important;
        border: none !important;
    }
    
    tbody [id^="dropdown-"] button:focus,
    tbody [id^="dropdown-"] a:focus {
        outline: none !important;
        box-shadow: none !important;
    }
    
    /* Force parent containers to not clip dropdowns */
    .bg-white.rounded-lg.border.border-gray-200 {
        overflow: visible !important;
    }
    
    /* Ensure table cells don't clip dropdowns */
    tbody td {
        position: relative;
        overflow: visible !important;
    }
    
    /* Action cell specific styling */
    tbody td:last-child {
        overflow: visible !important;
    }
    
    /* Status Badge Enhancement */
    .status-pulse {
        animation: statusPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes statusPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    /* Modal Animation */
    #detailModal {
        animation: fadeIn 0.2s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Kunjungan -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i data-feather="map-pin" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-500">Total Kunjungan</h3>
                </div>
                <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ number_format($totalVisits ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">Seluruh periode</p>
        </div>

        <!-- Belum Dikunjungi -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i data-feather="clock" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-500">Belum Dikunjungi</h3>
                </div>
                <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ number_format($belumDikunjungi ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ ($totalVisits ?? 0) > 0 ? round((($belumDikunjungi ?? 0) / ($totalVisits ?? 1)) * 100, 1) : 0 }}% dari total</p>
        </div>

        <!-- Dalam Perjalanan -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i data-feather="activity" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-500">Dalam Perjalanan</h3>
                </div>
                <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ number_format($dalamPerjalanan ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ ($totalVisits ?? 0) > 0 ? round((($dalamPerjalanan ?? 0) / ($totalVisits ?? 1)) * 100, 1) : 0 }}% dari total</p>
        </div>

        <!-- Selesai -->
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-500">Selesai</h3>
                </div>
                <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ number_format($selesai ?? 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ ($totalVisits ?? 0) > 0 ? round((($selesai ?? 0) / ($totalVisits ?? 1)) * 100, 1) : 0 }}% dari total</p>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <!-- Status Filter -->
                <div class="relative">
                    <select name="status_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-3 min-w-[180px]" onchange="window.location.href = updateUrlParameter(window.location.href, 'status_filter', this.value)">
                        <option value="">Semua Status</option>
                        <option value="belum_dikunjungi" {{ request('status_filter') == 'belum_dikunjungi' ? 'selected' : '' }}>Belum Dikunjungi</option>
                        <option value="dalam_perjalanan" {{ request('status_filter') == 'dalam_perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                        <option value="sedang_dikunjungi" {{ request('status_filter') == 'sedang_dikunjungi' ? 'selected' : '' }}>Sedang Dikunjungi</option>
                        <option value="menunggu_acc" {{ request('status_filter') == 'menunggu_acc' ? 'selected' : '' }}>Menunggu ACC</option>
                        <option value="selesai" {{ request('status_filter') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="relative">
                    <input type="date" name="date_filter" value="{{ request('date_filter') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-3 min-w-[180px]" onchange="window.location.href = updateUrlParameter(window.location.href, 'date_filter', this.value)">
                </div>
            </div>

            <!-- Search -->
            <div class="flex gap-3">
                <form method="GET" action="{{ route('author.visits.index') }}" class="flex gap-3">
                    <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                    <input type="hidden" name="date_filter" value="{{ request('date_filter') }}">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari auditor, lokasi..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full pl-10 p-3 min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:ring-4 focus:ring-orange-300 transition-colors font-medium">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Debug Info (remove in production) -->
    {{-- Debug: {{ 'Total: ' . ($totalVisits ?? 'undefined') . ', Belum: ' . ($belumDikunjungi ?? 'undefined') . ', Dalam Perjalanan: ' . ($dalamPerjalanan ?? 'undefined') . ', Selesai: ' . ($selesai ?? 'undefined') }} --}}



    <!-- Visits Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 visits-table-container">
        <!-- Table Header -->
        <div class="flex items-center justify-between p-5">
            <h3 class="text-lg font-semibold text-gray-900">Data Kunjungan</h3>
        </div>
        <div class="mt-4 relative table-container">

        <!-- Table Container -->
        <div class="table-overflow-wrapper">
            <table class="min-w-full divide-y divide-gray-200 table-enhanced">
                <colgroup>
                    <col style="width: 60px;">      <!-- No -->
                    <col style="width: 12%;">       <!-- ID -->
                    <col style="width: 16%;">       <!-- Date -->
                    <col style="width: 20%;">       <!-- Auditor -->
                    <col style="width: 20%;">       <!-- Lokasi -->
                    <col style="width: 15%;">       <!-- Status -->
                    <col style="width: 90px;">      <!-- Actions -->
                </colgroup>
                
                <!-- Table Header -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">No</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">ID Kunjungan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Auditor</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Lokasi</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Aksi</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody id="visits-table-body">
                    @forelse($visits as $index => $visit)
                        <tr class="border-t hover:bg-gray-50 transition-colors">
                            <!-- Row Number -->
                            <td class="px-4 py-3 text-center">
                                <div class="text-xs text-gray-600 font-medium">
                                    {{ ($visits->currentPage()-1)*$visits->perPage()+$index+1 }}
                                </div>
                            </td>
                            
                            <!-- Visit ID -->
                            <td class="px-4 py-3">
                                <div class="flex justify-center">
                                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">
                                        #{{ str_pad($visit->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Date & Time -->
                            <td class="px-4 py-3">
                                <div class="text-center text-xs">
                                    <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}</div>
                                    <div class="text-gray-500 mt-1">{{ \Carbon\Carbon::parse($visit->visit_time ?? $visit->visit_date)->format('H:i') }}</div>
                                </div>
                            </td>

                            <!-- Auditor -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-2">
                                        <span class="text-xs font-semibold text-orange-600">
                                            {{ strtoupper(substr($visit->auditor->name ?? 'N/A', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-medium text-gray-900 truncate" style="max-width: 120px;">{{ $visit->auditor->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 truncate" style="max-width: 120px;">{{ $visit->auditor->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Lokasi -->
                            <td class="px-4 py-3">
                                <div class="text-center text-xs">
                                    <div class="font-medium text-gray-900 truncate" style="max-width: 150px;" title="{{ $visit->location_address }}">
                                        {{ $visit->location_address }}
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3">
                                <div class="flex justify-center">
                                    @php
                                        $statusConfig = [
                                            'belum_dikunjungi' => 'Belum Dikunjungi',
                                            'dalam_perjalanan' => 'Dalam Perjalanan', 
                                            'selesai' => 'Selesai',
                                            'menunggu_acc' => 'Menunggu ACC Admin'
                                        ];
                                        $statusLabel = $statusConfig[$visit->status] ?? ucfirst(str_replace('_', ' ', $visit->status));
                                    @endphp
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </td>
                            <!-- Actions -->
                            <td class="px-4 py-3 text-center">
                                <div class="relative inline-block text-left">
                                    <button type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-all duration-150 focus:outline-none"
                                            onclick="toggleDropdown({{ $visit->id }})"
                                            title="Menu Aksi">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                            <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div id="dropdown-{{ $visit->id }}" 
                                         class="hidden"
                                         style="z-index: 99999;">
                                        <div class="py-1">
                                            <button onclick="showDetailModal({{ $visit->id }})" 
                                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Detail
                                            </button>
                                            
                                            @if($visit->status === 'belum_dikunjungi')
                                                <button onclick="confirmVisit({{ $visit->id }})" 
                                                        class="group flex items-center w-full px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-green-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Konfirmasi
                                                </button>
                                                
                                                <button onclick="showRescheduleModal({{ $visit->id }})" 
                                                        class="group flex items-center w-full px-4 py-2.5 text-sm text-orange-700 hover:bg-orange-50 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-orange-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Undur
                                                </button>
                                            @endif
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                            
                                            <button onclick="downloadReport({{ $visit->id }})" 
                                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-gray-400 cursor-not-allowed transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-400">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                Download
                                                <span class="ml-auto text-xs text-gray-400 italic">Soon</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-feather="inbox" class="w-12 h-12 text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada data kunjungan</p>
                                    <p class="text-sm">Belum ada kunjungan yang ditugaskan kepada Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>

        <!-- Pagination -->
        @if(isset($visits) && $visits->hasPages())
        <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($visits->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <a href="{{ $visits->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                @endif

                @if($visits->hasMorePages())
                    <a href="{{ $visits->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing {{ $visits->firstItem() ?? 0 }} to {{ $visits->lastItem() ?? 0 }} of {{ $visits->total() }} results
                    </p>
                </div>
                <div>
                    {{ $visits->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>



<!-- Detail Modal - Same as Auditor -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Kunjungan</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="detailModalBody" class="p-6 overflow-y-auto max-h-[70vh]">
            <!-- Modal content akan dimuat di sini -->
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-32 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-semibold">Konfirmasi Jadwal</h3>
            <button onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="confirmModalBody">
            <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin mengkonfirmasi jadwal kunjungan ini? Setelah dikonfirmasi, auditor dapat memulai kunjungan.</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button id="confirmButton" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-semibold">Atur Ulang Jadwal</h3>
            <button onclick="closeRescheduleModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="rescheduleModalBody">
            <form id="rescheduleForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Tanggal Baru
                    </label>
                    <input type="date" id="newDate" name="new_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Alasan
                    </label>
                    <textarea id="rescheduleReason" name="reschedule_reason" required rows="3" maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Masukkan alasan pengaturan ulang jadwal..."></textarea>
                </div>
                <div id="rescheduleInfo" class="mb-4 text-sm text-gray-600"></div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        Atur Ulang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Modal - Same as Auditor -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div id="notificationHeader" class="px-6 py-4 rounded-t-lg flex items-center justify-between">
            <h3 id="notificationTitle" class="text-lg font-semibold flex items-center">
                <span id="notificationIcon" class="mr-2">
                    <!-- Icon will be inserted here -->
                </span>
                <span id="notificationTitleText"></span>
            </h3>
            <button onclick="closeNotificationModal()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center mb-4">
                <div id="notificationBodyIcon" class="w-16 h-16 mx-auto mb-4">
                    <!-- Large icon will be inserted here -->
                </div>
            </div>
            <p id="notificationMessage" class="text-gray-700 text-center mb-6 leading-relaxed"></p>
            <div class="flex justify-center">
                <button id="notificationOkButton" 
                        class="px-6 py-2 rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                        onclick="closeNotificationModal()">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal - Same as Auditor -->
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
            <h3 id="confirmationTitle" class="text-lg font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" clip-rule="evenodd"/>
                </svg>
                Konfirmasi
            </h3>
            <button onclick="closeConfirmationModal()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center mb-4">
                <svg class="w-16 h-16 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p id="confirmationMessage" class="text-gray-700 text-center mb-6 leading-relaxed">
                Apakah Anda yakin ingin melakukan aksi ini?
            </p>
            <div class="flex justify-center space-x-3">
                <button class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        onclick="closeConfirmationModal()">
                    Batal
                </button>
                <button id="confirmationYesButton" 
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Ya, Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
// URL Parameter Utility Function
function updateUrlParameter(url, param, paramVal) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    if (paramVal != "") {
        var rows_txt = temp + "" + param + "=" + paramVal;
        return baseURL + "?" + newAdditionalURL + rows_txt;
    } else {
        return baseURL + "?" + newAdditionalURL;
    }
}

// Modal Functions - Same styling as Auditor
function showNotificationModal(title, message, type = 'info') {
    const modal = document.getElementById('notificationModal');
    const header = document.getElementById('notificationHeader');
    const icon = document.getElementById('notificationIcon');
    const titleEl = document.getElementById('notificationTitleText');
    const bodyIcon = document.getElementById('notificationBodyIcon');
    const messageEl = document.getElementById('notificationMessage');
    const okButton = document.getElementById('notificationOkButton');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Set colors and icons based on type - Same as Auditor
    if (type === 'success') {
        header.className = 'bg-green-500 text-white px-6 py-4 rounded-t-lg flex items-center justify-between';
        icon.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
        bodyIcon.innerHTML = '<svg class="w-16 h-16 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
        okButton.className = 'px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2';
    } else if (type === 'error') {
        header.className = 'bg-red-500 text-white px-6 py-4 rounded-t-lg flex items-center justify-between';
        icon.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
        bodyIcon.innerHTML = '<svg class="w-16 h-16 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
        okButton.className = 'px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2';
    } else if (type === 'warning') {
        header.className = 'bg-yellow-500 text-white px-6 py-4 rounded-t-lg flex items-center justify-between';
        icon.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
        bodyIcon.innerHTML = '<svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
        okButton.className = 'px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2';
    } else {
        header.className = 'bg-gray-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between';
        icon.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>';
        bodyIcon.innerHTML = '<svg class="w-16 h-16 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>';
        okButton.className = 'px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2';
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNotificationModal() {
    document.getElementById('notificationModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function showConfirmationModal(title, message, onConfirm) {
    const modal = document.getElementById('confirmationModal');
    const titleEl = document.getElementById('confirmationTitle');
    const messageEl = document.getElementById('confirmationMessage');
    const yesButton = document.getElementById('confirmationYesButton');
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    yesButton.onclick = function() {
        closeConfirmationModal();
        onConfirm();
    };
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function closeRescheduleModal() {
    const modal = document.getElementById('rescheduleModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Enhanced dropdown functionality with proper positioning - sama dengan Auditor
function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    const button = document.querySelector(`[onclick="toggleDropdown(${id})"]`);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
        d.classList.add('hidden');
    });
    
    if (isHidden && button && dropdown) {
        // Get button position
        const buttonRect = button.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const dropdownWidth = 192; // w-48 = 12rem = 192px
        const dropdownHeight = 120; // estimated height
        
        // Reset positioning
        dropdown.style.position = 'fixed';
        dropdown.style.zIndex = '99999';
        
        // Calculate positions
        let top = buttonRect.bottom + 8;
        let left = buttonRect.left;
        
        // Adjust if dropdown goes off right edge
        if (left + dropdownWidth > viewportWidth) {
            left = buttonRect.right - dropdownWidth;
        }
        
        // Adjust if dropdown goes off bottom edge
        if (top + dropdownHeight > viewportHeight) {
            top = buttonRect.top - dropdownHeight - 8;
        }
        
        // Ensure dropdown doesn't go off left edge
        if (left < 10) {
            left = 10;
        }
        
        // Apply positions
        dropdown.style.top = `${top}px`;
        dropdown.style.left = `${left}px`;
        
        // Show dropdown
        dropdown.classList.remove('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const isDropdownButton = event.target.closest('[onclick^="toggleDropdown"]');
    const isDropdownContent = event.target.closest('[id^="dropdown-"]');
    
    if (!isDropdownButton && !isDropdownContent) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Modal functions - Same as Auditor
function showDetailModal(id) {
    // Close any open dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    
    // Show loading in modal
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('detailModalBody');
    modalBody.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div></div>';
    modal.classList.remove('hidden');
    
    // Fetch visit details (same endpoint as auditor)
    fetch(`/author/visits/${id}/detail`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalBody.innerHTML = generateDetailContent(data.data);
            // Initialize any pending maps
            setTimeout(initializePendingMaps, 100);
        } else {
            modalBody.innerHTML = '<div class="text-red-600 text-center py-4">Gagal memuat detail kunjungan</div>';
        }
    })
    .catch(error => {
        console.error('Error fetching visit details:', error);
        modalBody.innerHTML = '<div class="text-red-600 text-center py-4">Terjadi kesalahan saat memuat detail kunjungan</div>';
    });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

// Function removed - using newer version with confirmation modal below

function rescheduleVisit(visitId) {
    const newDate = document.getElementById('newDate').value;
    const reason = document.getElementById('rescheduleReason').value;
    
    if (!newDate) {
        showNotificationModal('Validation Error', 'Tanggal baru harus dipilih.', 'warning');
        return;
    }
    
    if (!reason.trim()) {
        showNotificationModal('Validation Error', 'Alasan reschedule harus diisi.', 'warning');
        return;
    }
    
    fetch(`/author/visits/${visitId}/reschedule`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            visit_date: newDate,
            reschedule_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRescheduleModal();
            showNotificationModal('Berhasil!', data.message || 'Jadwal berhasil diatur ulang.', 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotificationModal('Error', data.message || 'Gagal melakukan reschedule.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotificationModal('Error', 'Terjadi kesalahan sistem.', 'error');
    });
}

// Generate Detail Content - Same as Auditor
function generateDetailContent(visit) {
    var statusConfig = {
        'selesai': { color: 'orange', text: 'Selesai' },
        'menunggu_acc': { color: 'purple', text: 'Menunggu ACC' },
        'sedang_dikunjungi': { color: 'orange', text: 'Sedang Dikunjungi' },
        'dalam_perjalanan': { color: 'orange', text: 'Dalam Perjalanan' },
        'belum_dikunjungi': { color: 'orange', text: 'Belum Dikunjungi' },
        'completed': { color: 'orange', text: 'Selesai' },
        'in_progress': { color: 'orange', text: 'Berlangsung' },
        'pending': { color: 'orange', text: 'Menunggu' }
    };
    
    var status = statusConfig[visit.status] || { color: 'orange', text: visit.status };
    var visitDate = new Date(visit.visit_date);
    
    var content = '<div class="space-y-6 max-h-96 overflow-y-auto">';
    
    // Basic Information
    content += '<div class="grid grid-cols-2 gap-4">';
    content += '<div>';
    content += '<label class="block text-xs font-medium text-gray-700 mb-1">ID Kunjungan</label>';
    content += '<div class="text-sm font-semibold text-gray-900">' + (visit.visit_id || '#' + visit.id) + '</div>';
    content += '</div>';
    content += '<div>';
    content += '<label class="block text-xs font-medium text-gray-700 mb-1">Status</label>';
    content += '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-' + status.color + '-50 text-' + status.color + '-700 border border-' + status.color + '-200 whitespace-nowrap animate-pulse">';
    content += '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
    content += status.text;
    content += '</span>';
    content += '</div>';
    content += '</div>';
    
    // Date and Duration
    content += '<div class="grid grid-cols-2 gap-4">';
    content += '<div>';
    content += '<label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>';
    content += '<div class="text-sm font-medium text-gray-900">' + visitDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + '</div>';
    content += '<div class="text-xs text-gray-600">' + visitDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB</div>';
    content += '</div>';
    if (visit.report && (visit.report.visit_start_time || visit.report.visit_end_time)) {
        content += '<div>';
        content += '<label class="block text-xs font-medium text-gray-700 mb-1">Waktu Kunjungan</label>';
        if (visit.report.visit_start_time) {
            content += '<div class="text-xs text-gray-600">Mulai: ' + new Date(visit.report.visit_start_time).toLocaleTimeString('id-ID') + '</div>';
        }
        if (visit.report.visit_end_time) {
            content += '<div class="text-xs text-gray-600">Selesai: ' + new Date(visit.report.visit_end_time).toLocaleTimeString('id-ID') + '</div>';
        }
        content += '</div>';
    } else {
        content += '<div>';
        content += '<label class="block text-xs font-medium text-gray-700 mb-1">Durasi</label>';
        content += '<div class="text-sm font-medium text-gray-900">' + (visit.duration || 'Belum ditentukan') + '</div>';
        content += '</div>';
    }
    content += '</div>';
    
    // Author and Auditor Information
    content += '<div class="grid grid-cols-2 gap-4 mb-4">';
    
    // Author Info
    content += '<div class="bg-gray-50 p-3 rounded-lg border border-gray-200">';
    content += '<label class="flex items-center text-sm font-medium text-gray-700 mb-2">';
    content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
    content += '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>';
    content += '</svg>Author</label>';
    if (visit.author && visit.author.name) {
        content += '<div class="text-sm font-medium text-gray-900 mb-1">' + visit.author.name + '</div>';
        if (visit.author.email) {
            content += '<div class="flex items-center text-xs text-gray-600 mb-1">';
            content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>';
            content += '<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>';
            content += '</svg>' + visit.author.email + '</div>';
        }
        if (visit.author.phone) {
            content += '<div class="flex items-center text-xs text-gray-600">';
            content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>';
            content += '</svg>' + visit.author.phone + '</div>';
        }
    } else {
        content += '<div class="text-sm text-gray-500 italic">Data author tidak tersedia</div>';
    }
    content += '</div>';
    
    // Auditor Info
    content += '<div class="bg-green-50 p-3 rounded-lg border border-green-200">';
    content += '<label class="flex items-center text-sm font-medium text-green-800 mb-2">';
    content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
    content += '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
    content += '</svg>Auditor</label>';
    if (visit.auditor && visit.auditor.name) {
        content += '<div class="text-sm font-medium text-gray-900 mb-1">' + visit.auditor.name + '</div>';
        if (visit.auditor.email) {
            content += '<div class="flex items-center text-xs text-gray-600 mb-1">';
            content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>';
            content += '<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>';
            content += '</svg>' + visit.auditor.email + '</div>';
        }
        if (visit.auditor.phone) {
            content += '<div class="flex items-center text-xs text-gray-600">';
            content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>';
            content += '</svg>' + visit.auditor.phone + '</div>';
        }
    } else {
        content += '<div class="text-sm text-gray-500 italic">Data auditor tidak tersedia</div>';
    }
    content += '</div>';
    
    content += '</div>';
    
    // Location Information
    if (visit.location_address || (visit.latitude && visit.longitude)) {
        content += '<div class="bg-gray-50 p-3 rounded-lg">';
        content += '<label class="flex items-center text-xs font-medium text-gray-700 mb-2">';
        content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
        content += '<path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>';
        content += '</svg>Lokasi Kunjungan</label>';
        
        if (visit.location_address) {
            content += '<div class="text-sm text-gray-900 mb-2">';
            content += '<svg class="w-4 h-4 inline mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>';
            content += '</svg>';
            content += visit.location_address;
            content += '</div>';
        }
        
        if (visit.latitude && visit.longitude) {
            // Peta inline untuk lokasi kunjungan
            var mainMapId = 'main-map-' + visit.id;
            content += '<div class="mb-3">';
            content += '<h4 class="text-sm font-medium text-gray-700 mb-2">Peta Lokasi</h4>';
            content += '<div id="' + mainMapId + '" class="w-full h-48 rounded-lg border border-gray-300 shadow-sm"></div>';
            content += '</div>';
            
            content += '<div class="text-xs text-gray-600 mb-3 p-2 bg-white rounded border">';
            content += '<strong>Koordinat:</strong> ' + parseFloat(visit.latitude).toFixed(6) + ', ' + parseFloat(visit.longitude).toFixed(6);
            content += '</div>';
            
            // Simpan data untuk inisialisasi peta
            window.pendingMaps = window.pendingMaps || [];
            window.pendingMaps.push({
                id: mainMapId,
                lat: parseFloat(visit.latitude),
                lng: parseFloat(visit.longitude),
                title: 'Lokasi Kunjungan'
            });
        }
        content += '</div>';
    }
    
    // Purpose and Notes
    content += '<div class="grid grid-cols-1 gap-3 mb-4">';
    
    // Purpose
    content += '<div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">';
    content += '<label class="flex items-center text-sm font-medium text-yellow-800 mb-2">';
    content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
    content += '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>';
    content += '</svg>Tujuan Kunjungan</label>';
    if (visit.purpose) {
        content += '<div class="text-sm text-gray-900">' + visit.purpose + '</div>';
    } else {
        content += '<div class="text-sm text-gray-500 italic">Tujuan kunjungan tidak dicantumkan</div>';
    }
    content += '</div>';
    
    // Notes  
    if (visit.notes) {
        content += '<div class="bg-gray-50 p-3 rounded-lg border border-gray-200">';
        content += '<label class="flex items-center text-sm font-medium text-gray-700 mb-2">';
        content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
        content += '<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>';
        content += '</svg>Catatan</label>';
        content += '<div class="text-sm text-gray-900">' + visit.notes + '</div>';
        content += '</div>';
    }
    
    content += '</div>';
    
    // Report Information (for completed visits) - Same as Auditor
    if (visit.report) {
        content += '<div class="border-t pt-4">';
        content += '<h4 class="text-sm font-semibold text-gray-900 mb-3">Laporan Kunjungan</h4>';
        
        if (visit.report.report_notes) {
            content += '<div class="mb-3">';
            content += '<label class="block text-xs font-medium text-gray-700 mb-1">Laporan</label>';
            content += '<div class="text-sm text-gray-900 bg-gray-50 p-2 rounded">' + visit.report.report_notes + '</div>';
            content += '</div>';
        }
        
        if (visit.report.auditor_notes) {
            content += '<div class="mb-3">';
            content += '<label class="block text-xs font-medium text-gray-700 mb-1">Catatan Auditor</label>';
            content += '<div class="text-sm text-gray-900 bg-gray-50 p-2 rounded">' + visit.report.auditor_notes + '</div>';
            content += '</div>';
        }
        
        // Selfie Photo and Location
        if (visit.report.selfie_photo || (visit.report.selfie_latitude && visit.report.selfie_longitude)) {
            content += '<div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">';
            content += '<label class="flex items-center text-sm font-medium text-gray-800 mb-3">';
            content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>';
            content += '</svg>Foto Selfie & Lokasi</label>';
            
            // Container untuk foto dan peta side by side
            if (visit.report.selfie_photo && visit.report.selfie_latitude && visit.report.selfie_longitude) {
                content += '<div class="grid grid-cols-2 gap-4">';
                
                // Foto Selfie - Kolom Kiri
                var selfieUrl = visit.report.selfie_photo;
                console.log('Original selfie URL:', selfieUrl);
                
                content += '<div>';
                content += '<h4 class="text-sm font-medium text-gray-700 mb-2">Foto Selfie</h4>';
                content += '<img src="' + selfieUrl + '" alt="Foto Selfie" class="w-full h-40 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-all shadow-sm" onclick="window.open(this.src)" onerror="console.log(\'Image error:\', this.src); this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" title="Klik untuk memperbesar">';
                content += '<div class="w-full h-40 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-500 text-sm" style="display: none;">Foto tidak tersedia<br><small>' + selfieUrl + '</small></div>';
                content += '</div>';
                
                // Peta - Kolom Kanan
                var mapId = 'selfie-map-' + visit.id;
                content += '<div>';
                content += '<h4 class="text-sm font-medium text-gray-700 mb-2">Lokasi Selfie</h4>';
                content += '<div id="' + mapId + '" class="w-full h-40 rounded-lg border border-gray-300 shadow-sm"></div>';
                content += '</div>';
                
                content += '</div>';
                
                // Koordinat info
                content += '<div class="mt-3 p-2 bg-gray-100 rounded text-sm text-gray-600">';
                content += '<strong>Koordinat:</strong> ' + parseFloat(visit.report.selfie_latitude).toFixed(6) + ', ' + parseFloat(visit.report.selfie_longitude).toFixed(6);
                content += '</div>';
                
                // Simpan data untuk inisialisasi peta nanti
                window.pendingMaps = window.pendingMaps || [];
                window.pendingMaps.push({
                    id: mapId,
                    lat: parseFloat(visit.report.selfie_latitude),
                    lng: parseFloat(visit.report.selfie_longitude),
                    title: 'Lokasi Selfie'
                });
            } else {
                // Jika hanya foto atau hanya koordinat
                if (visit.report.selfie_photo) {
                    var selfieUrl = visit.report.selfie_photo;
                    content += '<div class="mb-3">';
                    content += '<h4 class="text-sm font-medium text-gray-700 mb-2">Foto Selfie</h4>';
                    content += '<img src="' + selfieUrl + '" alt="Foto Selfie" class="w-40 h-40 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-all shadow-sm" onclick="window.open(this.src)" onerror="console.log(\'Image error:\', this.src); this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" title="Klik untuk memperbesar">';
                    content += '<div class="w-40 h-40 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-500 text-sm" style="display: none;">Foto tidak tersedia<br><small>' + selfieUrl + '</small></div>';
                    content += '</div>';
                }
                
                if (visit.report.selfie_latitude && visit.report.selfie_longitude) {
                    var mapId = 'selfie-map-' + visit.id;
                    content += '<div class="mb-3">';
                    content += '<h4 class="text-sm font-medium text-gray-700 mb-2">Lokasi Selfie</h4>';
                    content += '<div id="' + mapId + '" class="w-full h-40 rounded-lg border border-gray-300 shadow-sm"></div>';
                    content += '<div class="mt-2 text-sm text-gray-600">';
                    content += '<strong>Koordinat:</strong> ' + parseFloat(visit.report.selfie_latitude).toFixed(6) + ', ' + parseFloat(visit.report.selfie_longitude).toFixed(6);
                    content += '</div>';
                    content += '</div>';
                    
                    window.pendingMaps = window.pendingMaps || [];
                    window.pendingMaps.push({
                        id: mapId,
                        lat: parseFloat(visit.report.selfie_latitude),
                        lng: parseFloat(visit.report.selfie_longitude),
                        title: 'Lokasi Selfie'
                    });
                }
            }
            
            content += '</div>';
        }
        
        // Documentation Photos
        if (visit.report.photos && visit.report.photos.length > 0) {
            content += '<div class="mb-4 bg-green-50 p-3 rounded-lg border border-green-200">';
            content += '<label class="flex items-center text-sm font-medium text-green-800 mb-2">';
            content += '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">';
            content += '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>';
            content += '</svg>Foto Dokumentasi (' + visit.report.photos.length + ' foto)</label>';
            content += '<div class="grid grid-cols-2 gap-3">';
            
            for (var i = 0; i < visit.report.photos.length; i++) {
                var photoUrl = visit.report.photos[i];
                console.log('Photo ' + (i + 1) + ' URL:', photoUrl);
                
                content += '<div class="relative">';
                content += '<img src="' + photoUrl + '" alt="Dokumentasi ' + (i + 1) + '" class="w-full h-24 object-cover rounded-lg border-2 border-green-300 cursor-pointer hover:opacity-90 transition-all shadow-md" onclick="window.open(this.src)" onerror="console.log(\'Photo error:\', this.src); this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" title="Klik untuk memperbesar">';
                content += '<div class="w-full h-24 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-500 text-xs" style="display: none;">Foto ' + (i + 1) + ' tidak tersedia<br><small>' + photoUrl + '</small></div>';
                content += '<div class="absolute top-1 right-1 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">' + (i + 1) + '</div>';
                content += '</div>';
            }
            
            content += '</div>';
            content += '</div>';
        }
        
        content += '</div>';
    }
    
    content += '</div>';
    
    return content;
}

// Note: toggleDropdown function sudah didefinisikan di atas dengan styling yang baru

function showVisitDetail(visitId) {
    // Close any open dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    
    // Use the proper detail modal - Same as Auditor
    showDetailModal(visitId);
}

function confirmVisit(visitId) {
    // Close any open dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    
    showConfirmationModal(
        'Konfirmasi Jadwal Kunjungan',
        'Apakah Anda yakin ingin mengkonfirmasi jadwal kunjungan ini? Setelah dikonfirmasi, auditor dapat memulai kunjungan.',
        function() {
            // Perform the confirmation
            fetch(`/author/visits/${visitId}/confirm`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotificationModal('Berhasil!', 'Jadwal kunjungan telah dikonfirmasi successfully.', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotificationModal('Error', data.message || 'Gagal mengkonfirmasi jadwal kunjungan.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotificationModal('Error', 'Terjadi kesalahan saat konfirmasi kunjungan.', 'error');
            });
        }
    );
}

function showRescheduleModal(visitId) {
    // Close any open dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    
    // Check if reschedule modal exists
    const rescheduleModal = document.getElementById('rescheduleModal');
    if (rescheduleModal) {
        document.getElementById('rescheduleVisitId').value = visitId;
        rescheduleModal.classList.remove('hidden');
    } else {
        // Show notification if modal doesn't exist
        showNotificationModal(
            'Atur Ulang Jadwal', 
            `Fitur pengaturan ulang jadwal untuk kunjungan ID: ${visitId} akan segera tersedia.`, 
            'info'
        );
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-container')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

function showConfirmModal(visitId) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    const modal = document.getElementById('confirmModal');
    const button = document.getElementById('confirmButton');
    
    button.onclick = function() {
        confirmVisit(visitId);
    };
    
    modal.classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

function showRescheduleModal(visitId) {
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    const modal = document.getElementById('rescheduleModal');
    const form = document.getElementById('rescheduleForm');
    const infoDiv = document.getElementById('rescheduleInfo');
    
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('newDate').min = tomorrow.toISOString().split('T')[0];
    
    // Get current visit info for reschedule attempts
    fetch(`/author/visits/${visitId}/detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.remaining_reschedule_attempts !== undefined) {
                infoDiv.innerHTML = `<div class="p-2 bg-gray-50 border border-gray-200 rounded">
                    <p class="text-gray-700 text-xs">
                        <strong>Sisa kesempatan:</strong> ${data.data.remaining_reschedule_attempts} kali
                    </p>
                </div>`;
            }
        });
    
    form.onsubmit = function(e) {
        e.preventDefault();
        rescheduleVisit(visitId);
    };
    
    modal.classList.remove('hidden');
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.add('hidden');
    document.getElementById('rescheduleForm').reset();
}

function generateModalContent(visit) {
    const statusConfig = {
        'belum_dikunjungi': { class: 'bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse', text: 'Menunggu Konfirmasi' },
        'dalam_perjalanan': { class: 'bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse', text: 'Dalam Perjalanan' },
        'sedang_dikunjungi': { class: 'bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse', text: 'Sedang Dikunjungi' },
        'menunggu_acc': { class: 'bg-purple-100 text-purple-800', text: 'Menunggu Konfirmasi Admin' },
        'selesai': { class: 'bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse', text: 'Selesai' }
    };
    
    const status = statusConfig[visit.status] || { class: 'bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse', text: visit.status };
    
    return `
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">ID Kunjungan</label>
                        <div class="mt-1 text-sm font-semibold text-gray-900">#${visit.id.toString().padStart(4, '0')}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal & Waktu</label>
                        <div class="mt-1 text-sm text-gray-900">${visit.visit_date || 'Belum dijadwalkan'}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Status</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${status.class}">
                                ${status.text}
                            </span>
                        </div>
                    </div>
                    ${visit.location ? `
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Lokasi</label>
                            <div class="mt-1 text-sm text-gray-900">${visit.location}</div>
                        </div>
                    ` : ''}
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Author</label>
                        <div class="mt-1 text-sm text-gray-900">${visit.author_name || 'N/A'}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Auditor</label>
                        <div class="mt-1 text-sm text-gray-900">${visit.auditor_name || 'Belum ditentukan'}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Dibuat</label>
                        <div class="mt-1 text-sm text-gray-900">${visit.created_at || 'N/A'}</div>
                    </div>
                    ${visit.updated_at && visit.updated_at !== visit.created_at ? `
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Diperbarui</label>
                            <div class="mt-1 text-sm text-gray-900">${visit.updated_at}</div>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            ${(visit.reschedule_count && visit.reschedule_count > 0) ? `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="text-sm font-medium text-yellow-800">
                            Kunjungan ini telah diundur ${visit.reschedule_count} kali. 
                            Sisa kesempatan: ${visit.remaining_reschedule_attempts} kali
                        </span>
                    </div>
                </div>
            ` : ''}
            
            ${visit.notes ? `
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Catatan</label>
                    <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3 whitespace-pre-wrap">${visit.notes}</div>
                </div>
            ` : ''}
        </div>
    `;
}

function downloadReport(id) {
    showNotification('Fitur download laporan akan segera tersedia', 'info');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[99999] p-4 rounded-lg shadow-xl text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-gray-600'
    }`;
    notification.innerHTML = `
        <div class='flex items-center'>
            <span>${message}</span>
            <button onclick='this.parentElement.parentElement.remove()' class='ml-3 text-white hover:text-gray-200'>
                <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 4000);
}



document.addEventListener('DOMContentLoaded', function() {
    console.log('Author visits page loaded with auditor-style interface');
    
    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('detailModal').classList.contains('hidden')) {
            closeDetailModal();
        }
    });
});

// Function to initialize Leaflet maps
function initializePendingMaps() {
    if (!window.pendingMaps) return;
    
    window.pendingMaps.forEach(function(mapConfig) {
        var mapElement = document.getElementById(mapConfig.id);
        if (mapElement && !mapElement._leaflet_id) { // Check if map not already initialized
            try {
                // Initialize map with neutral colors (no blue)
                var map = L.map(mapConfig.id, {
                    zoomControl: true,
                    attributionControl: false
                }).setView([mapConfig.lat, mapConfig.lng], 15);
                
                // Use OpenStreetMap tiles with neutral colors
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
                
                // Add marker with custom icon (no blue)
                var redIcon = L.icon({
                    iconUrl: 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#dc2626" width="32" height="32"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'),
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                
                L.marker([mapConfig.lat, mapConfig.lng], {icon: redIcon})
                    .addTo(map)
                    .bindPopup('<b>' + mapConfig.title + '</b><br>Lat: ' + mapConfig.lat.toFixed(6) + '<br>Lng: ' + mapConfig.lng.toFixed(6))
                    .openPopup();
                
                // Fit map to marker with some padding
                setTimeout(function() {
                    map.invalidateSize();
                }, 50);
                
            } catch (error) {
                console.error('Error initializing map:', error);
                mapElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 text-sm">Gagal memuat peta</div>';
            }
        }
    });
    
    // Clear pending maps after initialization
    window.pendingMaps = [];
}

// Close dropdowns when clicking outside - Same as Auditor
document.addEventListener('click', function(event) {
    const isDropdownButton = event.target.closest('[onclick^="toggleDropdown"]');
    const isDropdownContent = event.target.closest('[id^="dropdown-"]');
    
    if (!isDropdownButton && !isDropdownContent) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Feather Icons -->
<script src="https://unpkg.com/feather-icons"></script>
<script>
    // Initialize Feather icons
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush