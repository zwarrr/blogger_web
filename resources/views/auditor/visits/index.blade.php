<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kunjungan - Auditor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef7ee',
                            100: '#fdead6', 
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ auth()->user()->role }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        /* Page transition and basic page styles */
        .page-transition { position: fixed; top:0; left:0; width:100vw; height:100vh; z-index:99999; display:flex; align-items:center; justify-content:center; opacity:0; visibility:hidden; transition:opacity .3s, visibility .3s; pointer-events:none; }
        .page-transition.active { opacity:1; visibility:visible; pointer-events:all; }
        @keyframes rotate { 100% { transform: rotate(360deg); } }
        @keyframes dash { 0% { stroke-dasharray:1,150; stroke-dashoffset:0 } 50% { stroke-dasharray:90,150; stroke-dashoffset:-35 } 100% { stroke-dasharray:90,150; stroke-dashoffset:-124 } }
        .spinner-circle { animation: dash 1.5s ease-in-out infinite, rotate 2s linear infinite; transform-origin:center }
        .loading-text { color:#FFCCBC; font-size:1.125rem; font-weight:600; margin-top:1.25rem }
        body { font-family: 'Inter', sans-serif }
        
        /* Enhanced Table Styling */
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
        
        /* Sidebar profile dropdown - khusus untuk dropdown profil di sidebar */
        aside [x-show="open"] {
            position: absolute !important;
            left: 100% !important;
            margin-left: 0.5rem !important;
            bottom: 0 !important;
            z-index: 9999 !important;
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
        
        /* Sidebar dropdown styling - pastikan tidak terpengaruh */
        aside .relative > div[x-show="open"] {
            position: absolute !important;
            left: 100% !important;
            margin-left: 0.5rem !important;
            bottom: 0 !important;
            width: 15rem !important;
            min-width: 15rem !important;
            background: white !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
            transform: none !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        
        /* Force parent containers to not clip dropdowns - Same as Author */
        .bg-white.rounded-lg.border.border-gray-200 {
            overflow: visible !important;
        }
        
        /* Ensure table cells don't clip dropdowns - Same as Author */
        tbody td {
            position: relative;
            overflow: visible !important;
        }
        
        /* Action cell specific styling - Same as Author */
        tbody td:last-child {
            overflow: visible !important;
        }
        
        /* Enhanced Table Styling - Same as Author */
        .table-enhanced tbody tr:hover {
            background: linear-gradient(90deg, rgba(239, 246, 255, 0.5), rgba(219, 234, 254, 0.3));
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
        
        /* Camera preview - mirror for user experience */
        #cameraVideo {
            transform: scaleX(-1);
        }
        
        /* Captured photo result - show as captured (mirrored like preview) */
        #photoResult {
            /* No transform needed - result will match preview since we flip in canvas */
        }
        
        /* Leaflet map container */
        #leafletMap {
            height: 256px !important;
            width: 100% !important;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Ensure Leaflet tiles load properly */
        .leaflet-container {
            height: 256px !important;
            width: 100% !important;
        }
        
        .leaflet-tile {
            max-width: none !important;
        }
        
        /* Custom marker styling */
        .leaflet-marker-icon {
            filter: hue-rotate(0deg) saturate(1.5) brightness(1.1);
        }
        
        /* Sidebar Profile Dropdown - Override untuk memastikan tidak terpengaruh table dropdown */
        aside[x-data] .relative {
            position: relative !important;
        }
        
        aside[x-data] .relative > div {
            position: absolute !important;
            left: calc(100% + 0.5rem) !important;
            bottom: 0 !important;
            z-index: 50000 !important;
            width: 15rem !important;
            min-width: 15rem !important;
            background: white !important;
            color: rgb(31 41 55) !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            transform: none !important;
            margin: 0 !important;
        }
        
        /* Force sidebar dropdown to not use fixed positioning */
        aside[x-data] .relative > div[x-show] {
            position: absolute !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('auditor.sidebar')

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <!-- Page Transition Overlay -->
        <div class="page-transition" id="pageTransition">
            <svg class="w-24 h-24 text-primary-600" viewBox="0 0 50 50" fill="none">
                <circle class="spinner-circle" cx="25" cy="25" r="20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <p class="loading-text">Loading...</p>
        </div>

        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Daftar Kunjungan
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola dan pantau jadwal kunjungan yang ditugaskan kepada Anda</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8" x-data="visitTable()">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @isset($stats)
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
                        <p class="text-2xl font-bold mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
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
                        <p class="text-2xl font-bold mt-2">{{ number_format($stats['belum_dikunjungi'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['belum_dikunjungi'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
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
                        <p class="text-2xl font-bold mt-2">{{ number_format($stats['dalam_perjalanan'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['dalam_perjalanan'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
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
                        <p class="text-2xl font-bold mt-2">{{ number_format($stats['selesai'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['selesai'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
                    </div>
                    @endisset
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-900">Filter & Pencarian</h3>
                        <button onclick="clearFilters()" class="text-xs text-gray-500 hover:text-gray-700">Clear All</button>
                    </div>
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Status Filter -->
                            <select x-model="filters.status_filter" @change="applyFilters()" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white min-w-[140px]">
                                <option value="">Semua Status</option>
                                <option value="belum_dikunjungi">Belum Dikunjungi</option>
                                <option value="dalam_perjalanan">Dalam Perjalanan</option>
                                <option value="selesai">Selesai</option>
                            </select>
                            
                            <!-- Date Filter -->
                            <input type="date" x-model="filters.date_filter" @change="applyFilters()" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 min-w-[140px]">
                        </div>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()" placeholder="Cari nama author..." class="text-sm border border-gray-300 rounded-md px-3 py-2 w-full sm:w-60 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>                <!-- Visits Table -->
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
                                <col style="width: 20%;">       <!-- Author -->
                                <col style="width: 20%;">       <!-- Tujuan -->
                                <col style="width: 15%;">       <!-- Status -->
                                <col style="width: 90px;">      <!-- Actions -->
                            </colgroup>
                            
                            <!-- Table Header -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">No</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">ID Kunjungan</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Tanggal & Waktu</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Author</th>
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
                                                <div class="text-gray-500 mt-1">{{ \Carbon\Carbon::parse($visit->visit_time)->format('H:i') }}</div>
                                            </div>
                                        </td>

                                        <!-- Author -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center">
                                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-2">
                                                    <span class="text-xs font-semibold text-orange-600">
                                                        {{ strtoupper(substr($visit->author->name ?? 'N/A', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="text-left">
                                                    <div class="text-xs font-medium text-gray-900 truncate" style="max-width: 120px;">{{ $visit->author->name ?? 'N/A' }}</div>
                                                    <div class="text-xs text-gray-500 truncate" style="max-width: 120px;">{{ $visit->author->email ?? '' }}</div>
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
                                                        
                                                        @if(!in_array($visit->status, ['selesai', 'menunggu_acc']))
                                                            <button onclick="showCompleteModal({{ $visit->id }})" 
                                                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 transition-colors duration-150">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-green-600">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                Selesaikan
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

                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-orange-500 transition ease-in-out duration-150">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sedang memuat...
                        </div>
                    </div>
                </div>
                </div>

                <!-- Pagination -->
                @if($visits->hasPages())
                    <div class="mt-8 flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-700">
                            <span class="font-medium">
                                Menampilkan {{ $visits->firstItem() ?? 0 }} - {{ $visits->lastItem() ?? 0 }} 
                                dari {{ number_format($visits->total()) }} hasil
                            </span>
                        </div>
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-1">
                            {{ $visits->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </main>

        <!-- Detail Modal -->
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

        <!-- Error Modal -->
        <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="bg-red-500 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Perhatian!
                    </h3>
                    <button onclick="closeErrorModal()" class="text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <svg class="w-16 h-16 text-red-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p id="errorMessage" class="text-gray-700 text-center mb-6 leading-relaxed">
                        Terjadi kesalahan saat memproses permintaan Anda.
                    </p>
                    <div class="flex justify-center">
                        <button onclick="closeErrorModal()" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complete Modal (Selesaikan) -->
        <div id="completeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[95vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Selesaikan Kunjungan</h3>
                    <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[85vh]">
                    <form id="completeForm" onsubmit="submitComplete(event)">
                        <input type="hidden" id="completeVisitId" name="visit_id">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <!-- Catatan/Keterangan Audit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan Audit <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="auditor_notes" id="reportDescription" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"
                                            rows="6" placeholder="Masukkan detail hasil kunjungan, temuan audit, dan catatan penting... (minimal 10 karakter)"
                                            minlength="10" maxlength="2000"></textarea>
                                    <div class="text-xs text-gray-500 mt-1" id="charCount"></div>
                                </div>

                                <!-- Foto Selfie -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Selfie <span class="text-red-500">*</span></label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-md p-4">
                                        <div id="cameraSection" class="text-center">
                                            <div id="cameraPreview" class="hidden mb-4">
                                                <video id="cameraVideo" class="w-full max-w-sm mx-auto rounded-lg" autoplay playsinline></video>
                                                <canvas id="cameraCanvas" class="hidden"></canvas>
                                            </div>
                                            
                                            <div id="capturedPhoto" class="hidden mb-4">
                                                <img id="photoResult" class="w-full max-w-sm mx-auto rounded-lg" alt="Captured photo">
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2 justify-center">
                                                <button type="button" id="openCameraBtn" onclick="openCamera()" 
                                                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Buka Kamera
                                                </button>
                                                <button type="button" id="captureBtn" onclick="capturePhoto()" 
                                                        class="hidden inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Ambil Foto
                                                </button>
                                                <button type="button" id="retakeBtn" onclick="retakePhoto()" 
                                                        class="hidden inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    Ambil Ulang
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">Ambil foto selfie sebagai bukti kehadiran di lokasi. Pastikan wajah Anda terlihat jelas.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="selfie_photo_data" id="selfiePhotoData">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-4">
                                <!-- Foto Tambahan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Foto Tambahan <span class="text-gray-500">(Opsional)</span>
                                    </label>
                                    <div class="text-sm text-gray-600 mb-2">Maksimal 5 foto tambahan (bukti kondisi lokasi, dokumen, dll.)</div>
                                    <input type="file" name="additional_photos[]" multiple accept="image/*" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                           onchange="previewAdditionalPhotos(this)" max="5">
                                    <div id="additionalPhotoPreview" class="mt-3 grid grid-cols-2 gap-2 hidden"></div>
                                </div>

                                <!-- Lokasi pada Peta -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Lokasi pada Peta <span class="text-red-500">*</span></label>
                                    
                                    <!-- Status Loading GPS -->
                                    <div id="locationStatus" class="mb-3 p-3 bg-orange-50 rounded-lg border border-orange-200 text-center">
                                        <div class="flex items-center justify-center gap-2 text-orange-700">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-700"></div>
                                            <span class="text-sm font-medium">Mengambil lokasi GPS otomatis...</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Leaflet Map Display -->
                                    <div id="mapContainer" class="hidden">
                                        <div id="leafletMap" class="h-64 w-full rounded-lg border border-gray-300 shadow-sm"></div>
                                        <div class="mt-3 p-2 bg-gray-50 rounded-md">
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span>Koordinat:</span>
                                                </div>
                                                <span id="coordText" class="font-mono font-semibold text-gray-800">-</span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="selfie_latitude" id="latitude">
                                    <input type="hidden" name="selfie_longitude" id="longitude">
                                    <input type="hidden" name="location_accuracy" id="locationAccuracy">
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                            <button type="button" onclick="closeCompleteModal()" 
                                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                Selesaikan Kunjungan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function visitTable() {
        return {
            loading: false,
            showModal: false,
            filters: {
                status_filter: '',
                date_filter: '',
                search: ''
            },

            resetFilters() {
                this.filters = {
                    status_filter: '',
                    date_filter: '',
                    search: ''
                };
                this.applyFilters();
            },

            applyFilters() {
                this.loading = true;
                
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.set(key, this.filters[key]);
                    }
                });
                
                const url = `${window.location.pathname}?${params.toString()}`;
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('visits-table-body').innerHTML = data.html;
                    const paginationEl = document.getElementById('pagination');
                    if (paginationEl && data.pagination) {
                        paginationEl.innerHTML = data.pagination;
                    }
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
            },

            refreshTable() {
                this.applyFilters();
            }
        }
    }

    // Global function to refresh table (called from modal)
    window.refreshTable = function() {
        const tableComponent = document.querySelector('[x-data="visitTable()"]');
        if (tableComponent && tableComponent._x_dataStack) {
            const data = tableComponent._x_dataStack[0];
            if (data && typeof data.refreshTable === 'function') {
                data.refreshTable();
            }
        }
    };

    // Clear filters function
    function clearFilters() {
        const tableComponent = document.querySelector('[x-data]');
        if (tableComponent && tableComponent._x_dataStack) {
            const data = tableComponent._x_dataStack[0];
            if (data && data.filters) {
                data.filters.status_filter = '';
                data.filters.date_filter = '';
                data.filters.search = '';
                if (typeof data.applyFilters === 'function') {
                    data.applyFilters();
                }
            }
        }
    }

    // Dropdown functionality - Fixed positioning
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
            const dropdownWidth = 180;
            const dropdownHeight = 140;
            
            // Reset positioning
            dropdown.style.position = 'fixed';
            dropdown.style.zIndex = '99999';
            
            // Calculate positions
            let top = buttonRect.bottom + 5;
            let left = buttonRect.left;
            
            // Adjust if dropdown goes off right edge
            if (left + dropdownWidth > viewportWidth) {
                left = buttonRect.right - dropdownWidth;
            }
            
            // Adjust if dropdown goes off bottom edge
            if (top + dropdownHeight > viewportHeight) {
                top = buttonRect.top - dropdownHeight - 5;
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

    // Detail Modal Functions
    function showDetailModal(id) {
        // Close any open dropdowns
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        // Show loading in modal
        const modal = document.getElementById('detailModal');
        const modalBody = document.getElementById('detailModalBody');
        modalBody.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div></div>';
        modal.classList.remove('hidden');
        
        // Fetch visit details (same endpoint as admin)
        fetch(`/auditor/visits/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                modalBody.innerHTML = generateDetailContent(data);
                // Initialize any pending maps
                setTimeout(initializePendingMaps, 100);
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="text-red-600 text-center py-4">Gagal memuat detail kunjungan</div>';
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // Error modal functions
    function showErrorModal(message) {
        const modal = document.getElementById('errorModal');
        const messageElement = document.getElementById('errorMessage');
        
        messageElement.textContent = message;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function generateDetailContent(visit) {
        var statusConfig = {
            'selesai': { color: 'orange', text: 'Selesai' },
            'menunggu_acc': { color: 'orange', text: 'Menunggu ACC' },
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
        content += '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-' + status.color + '-50 text-' + status.color + '-700 border border-' + status.color + '-200">';
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
        content += '<div class="bg-blue-50 p-3 rounded-lg border border-blue-200">';
        content += '<label class="flex items-center text-sm font-medium text-blue-800 mb-2">';
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
        
        // Report Information (for completed visits)
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

    // Complete Modal Functions
    function showCompleteModal(id) {
        // Close any open dropdowns
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        // Set visit ID in form
        document.getElementById('completeVisitId').value = id;
        
        // Show modal FIRST
        document.getElementById('completeModal').classList.remove('hidden');
        
        // Coba GPS terlebih dahulu untuk otomatis rekam koordinat
        console.log('Modal opened, attempting GPS location...');
        getCurrentLocationForModal();
        
        // Fetch visit data in background (optional)
        fetch(`/auditor/visits/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                // Pre-fill form with visit data
                document.getElementById('reportDescription').placeholder = `Laporkan hasil kunjungan ke ${data.author.name} di ${data.location_address}`;
            })
            .catch(error => {
                console.error('Error fetching visit data:', error);
            });
    }

    function closeCompleteModal() {
        document.getElementById('completeModal').classList.add('hidden');
        
        // Stop camera if active
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        
        // Remove map if exists
        if (currentMap) {
            currentMap.remove();
            currentMap = null;
            currentMarker = null;
        }
        
        // Reset form and UI elements
        document.getElementById('completeForm').reset();
        document.getElementById('selfiePhotoData').value = '';
        document.getElementById('additionalPhotoPreview').classList.add('hidden');
        document.getElementById('mapContainer').classList.add('hidden');
        document.getElementById('locationStatus').classList.remove('hidden');
        document.getElementById('locationStatus').className = 'mb-3 p-3 bg-orange-50 rounded-lg border border-orange-200 text-center';
        document.getElementById('locationStatus').innerHTML = `
            <div class="flex items-center justify-center gap-2 text-orange-700">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-700"></div>
                <span class="text-sm font-medium">Mengambil lokasi GPS otomatis...</span>
            </div>
        `;
        document.getElementById('coordText').textContent = '-';
        
        // Reset camera UI
        document.getElementById('cameraPreview').classList.add('hidden');
        document.getElementById('capturedPhoto').classList.add('hidden');
        document.getElementById('openCameraBtn').classList.remove('hidden');
        document.getElementById('captureBtn').classList.add('hidden');
        document.getElementById('retakeBtn').classList.add('hidden');
    }

    // Camera and Photo Functions
    let cameraStream = null;
    let cameraVideo = null;
    let cameraCanvas = null;

    function openCamera() {
        cameraVideo = document.getElementById('cameraVideo');
        cameraCanvas = document.getElementById('cameraCanvas');
        
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'user',
                width: { ideal: 640 },
                height: { ideal: 480 }
            } 
        })
        .then(function(stream) {
            cameraStream = stream;
            cameraVideo.srcObject = stream;
            
            // Show camera preview
            document.getElementById('cameraPreview').classList.remove('hidden');
            document.getElementById('openCameraBtn').classList.add('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
        })
        .catch(function(error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera.');
        });
    }

    function capturePhoto() {
        if (cameraVideo && cameraCanvas) {
            const context = cameraCanvas.getContext('2d');
            cameraCanvas.width = cameraVideo.videoWidth;
            cameraCanvas.height = cameraVideo.videoHeight;
            
            // Flip horizontally to match preview (mirror effect)
            context.scale(-1, 1);
            context.drawImage(cameraVideo, -cameraCanvas.width, 0);
            
            // Get base64 data with higher quality
            const photoData = cameraCanvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('selfiePhotoData').value = photoData;
            
            // Debug log
            console.log('Photo captured:', {
                dataLength: photoData.length,
                startsCorrect: photoData.startsWith('data:image/jpeg;base64,')
            });
            
            // Show captured photo (mirrored result same as preview)
            document.getElementById('photoResult').src = photoData;
            document.getElementById('capturedPhoto').classList.remove('hidden');
            
            // Hide camera preview and show retake button
            document.getElementById('cameraPreview').classList.add('hidden');
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            
            // Stop camera stream
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }
    }

    function retakePhoto() {
        // Reset photo
        document.getElementById('selfiePhotoData').value = '';
        document.getElementById('capturedPhoto').classList.add('hidden');
        document.getElementById('retakeBtn').classList.add('hidden');
        document.getElementById('openCameraBtn').classList.remove('hidden');
    }

    // Additional photos preview function
    function previewAdditionalPhotos(input) {
        const preview = document.getElementById('additionalPhotoPreview');
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            if (input.files.length > 5) {
                alert('Maksimal 5 foto tambahan');
                input.value = '';
                return;
            }
            
            preview.classList.remove('hidden');
            
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-20 object-cover rounded-md">
                        <button type="button" onclick="removeAdditionalPhoto(${index}, this)" 
                                class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">×</button>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            preview.classList.add('hidden');
        }
    }

    function removeAdditionalPhoto(index, button) {
        button.parentElement.remove();
        // Reset file input to trigger change
        const input = document.querySelector('input[name="additional_photos[]"]');
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        if (files.length === 0) {
            document.getElementById('additionalPhotoPreview').classList.add('hidden');
        }
    }

    // GPS and Maps Functions
    function getCurrentLocationForModal() {
        const statusEl = document.getElementById('locationStatus');
        
        // Show GPS loading dengan warna orange
        statusEl.innerHTML = `
            <div class="flex flex-col items-center gap-3 text-orange-700">
                <div class="flex items-center gap-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-700"></div>
                    <span class="text-sm font-medium">Mengambil lokasi GPS...</span>
                </div>
                <button onclick="showDefaultMap()" class="bg-orange-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-700 transition-colors">
                    Lewati & Pilih Manual
                </button>
            </div>
        `;
        statusEl.className = 'mb-3 p-3 bg-orange-50 rounded-lg border border-orange-200 text-center';
        statusEl.classList.remove('hidden');
        
        // Auto fallback setelah 3 detik jika GPS tidak berhasil
        const autoFallback = setTimeout(() => {
            console.log('GPS timeout - fallback to manual mode');
            showDefaultMap();
        }, 3000);
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                clearTimeout(autoFallback);
                
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                
                console.log('GPS Success:', lat, lng, 'Accuracy:', accuracy);
                
                // Store coordinates
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('locationAccuracy').value = accuracy;
                
                // Update coordinates display
                document.getElementById('coordText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Hide status and show map directly
                statusEl.classList.add('hidden');
                
                // Initialize and show map with GPS location
                showMap(lat, lng);
                
            }, function(error) {
                clearTimeout(autoFallback);
                console.log('GPS Error:', error.message);
                
                // Show error dan fallback ke manual
                statusEl.innerHTML = `
                    <div class="flex flex-col items-center gap-2 text-orange-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm">GPS tidak tersedia</span>
                        </div>
                        <button onclick="showDefaultMap()" class="bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700">
                            Pilih Lokasi Manual
                        </button>
                    </div>
                `;
                
            }, {
                enableHighAccuracy: true,
                timeout: 3000,
                maximumAge: 60000
            });
        } else {
            clearTimeout(autoFallback);
            showDefaultMap();
        }
    }
    
    // Function to show default map when GPS fails
    function showDefaultMap() {
        console.log('showDefaultMap called - forcing immediate map display');
        
        // Default coordinates (Jakarta center)
        const defaultLat = -6.2088;
        const defaultLng = 106.8456;
        
        // Clear coordinates first
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('locationAccuracy').value = '';
        document.getElementById('coordText').textContent = 'Klik peta untuk pilih lokasi';
        
        // FORCE HIDE loading status - semua cara
        const statusEl = document.getElementById('locationStatus');
        if (statusEl) {
            statusEl.style.display = 'none';
            statusEl.classList.add('hidden');
            statusEl.hidden = true;
        }
        
        // FORCE SHOW map container - semua cara
        const mapContainer = document.getElementById('mapContainer');
        if (mapContainer) {
            mapContainer.style.display = 'block';
            mapContainer.classList.remove('hidden');
            mapContainer.hidden = false;
        }
        
        // Show map immediately with timeout to ensure DOM ready
        setTimeout(() => {
            console.log('Initializing map now...');
            showMap(defaultLat, defaultLng, true); // true = manual mode
        }, 50);
    }

    let currentMap = null;
    let currentMarker = null;

    function showMap(lat, lng, isManualMode = false) {
        const mapContainer = document.getElementById('mapContainer');
        const mapDiv = document.getElementById('leafletMap');
        
        console.log(`Showing map at: ${lat}, ${lng}, Manual mode: ${isManualMode}`);
        
        // Show map container immediately
        mapContainer.classList.remove('hidden');
        
        // Clear any existing content
        mapDiv.innerHTML = '';
        
        // Remove existing map if any
        if (currentMap) {
            currentMap.remove();
            currentMap = null;
            currentMarker = null;
        }
        
        try {
            // Initialize Leaflet map immediately
            currentMap = L.map('leafletMap', {
                zoomControl: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                touchZoom: true,
                preferCanvas: false
            }).setView([lat, lng], isManualMode ? 11 : 16);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
                subdomains: ['a', 'b', 'c']
            }).addTo(currentMap);
            
            // Create professional orange marker icon using SVG
            const orangeIcon = L.divIcon({
                html: `<svg width="32" height="45" viewBox="0 0 32 45" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
                            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.3)"/>
                        </filter>
                    </defs>
                    <!-- Outer marker shape with gradient -->
                    <path d="M16 0C7.163 0 0 7.163 0 16c0 8.837 16 29 16 29s16-20.163 16-29C32 7.163 24.837 0 16 0z" 
                          fill="url(#orangeGradient)" 
                          stroke="#dc2626" 
                          stroke-width="1" 
                          filter="url(#shadow)"/>
                    <!-- Inner white circle -->
                    <circle cx="16" cy="16" r="6" fill="white" stroke="#ea580c" stroke-width="1.5"/>
                    <!-- Inner orange dot -->
                    <circle cx="16" cy="16" r="3" fill="#ea580c"/>
                    
                    <defs>
                        <linearGradient id="orangeGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#f97316;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#ea580c;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                </svg>`,
                className: 'professional-orange-marker',
                iconSize: [32, 45],
                iconAnchor: [16, 45],
                popupAnchor: [0, -45]
            });

            // Add marker (only if not manual mode or if coordinates are set)
            if (!isManualMode || (document.getElementById('latitude').value && document.getElementById('longitude').value)) {
                currentMarker = L.marker([lat, lng], {
                    icon: orangeIcon,
                    draggable: true,
                    riseOnHover: true
                }).addTo(currentMap);
                
                const popupContent = isManualMode 
                    ? `<div class="text-center p-3">
                        <strong class="text-orange-600">📍 Pilih Lokasi</strong><br>
                        <div class="text-xs text-gray-600 mt-1">Seret marker atau klik peta</div>
                       </div>`
                    : `<div class="text-center p-3">
                        <strong class="text-orange-600">📍 Lokasi GPS</strong><br>
                        <div class="font-mono text-xs text-gray-600">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                       </div>`;
                
                currentMarker.bindPopup(popupContent);
                
                // Open popup immediately for manual mode
                if (isManualMode) {
                    setTimeout(() => currentMarker.openPopup(), 100);
                }
            }
            
            // Handle marker drag
            if (currentMarker) {
                currentMarker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });
            }
            
            // Handle map click to place/move marker
            currentMap.on('click', function(e) {
                const clickLat = e.latlng.lat;
                const clickLng = e.latlng.lng;
                
                if (!currentMarker) {
                    // Create marker if doesn't exist with orange icon
                    currentMarker = L.marker([clickLat, clickLng], {
                        icon: orangeIcon,
                        draggable: true,
                        riseOnHover: true
                    }).addTo(currentMap);
                    
                    currentMarker.on('dragend', function(e) {
                        const position = e.target.getLatLng();
                        updateCoordinates(position.lat, position.lng);
                    });
                } else {
                    // Move existing marker
                    currentMarker.setLatLng([clickLat, clickLng]);
                }
                
                updateCoordinates(clickLat, clickLng);
            });
            
            // Force map to resize properly
            setTimeout(() => {
                if (currentMap) {
                    currentMap.invalidateSize();
                    
                    // Style zoom controls
                    const zoomControls = document.querySelector('#leafletMap .leaflet-control-zoom');
                    if (zoomControls) {
                        zoomControls.style.border = '1px solid #d1d5db';
                        zoomControls.style.borderRadius = '0.375rem';
                        zoomControls.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
                    }
                }
            }, 100);
            
            console.log('Map initialized successfully');
            
        } catch (error) {
            console.error('Error initializing map:', error);
            mapDiv.innerHTML = `
                <div class="flex items-center justify-center h-64 bg-red-50 rounded-lg border border-red-200">
                    <div class="text-center p-4">
                        <svg class="w-8 h-8 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-600 font-medium">Gagal memuat peta</p>
                        <p class="text-xs text-red-500 mt-1">Silakan refresh halaman</p>
                        <button onclick="showDefaultMap()" class="mt-2 text-xs bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700">
                            Coba Lagi
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    // Helper function to update coordinates
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('coordText').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        
        if (currentMarker) {
            currentMarker.setPopupContent(`
                <div class="text-center p-3">
                    <strong class="text-orange-600">📍 Lokasi Dipilih</strong><br>
                    <div class="font-mono text-xs text-gray-600">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                    <div class="text-xs text-orange-600 mt-1">✅ Koordinat tersimpan</div>
                </div>
            `);
        }
    }

    // Submit complete form
    async function submitComplete(event) {
        event.preventDefault();
        
        // Get visit ID first and validate it
        const visitId = document.getElementById('completeVisitId').value;
        if (!visitId) {
            showErrorModal('ID kunjungan tidak ditemukan. Silakan refresh halaman.');
            return;
        }
        
        // Validation
        const reportDescription = document.getElementById('reportDescription').value.trim();
        const selfiePhoto = document.getElementById('selfiePhotoData').value;
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        
        // Validate audit notes
        if (!reportDescription) {
            showErrorModal('Mohon isi keterangan audit');
            document.getElementById('reportDescription').focus();
            return;
        }
        
        if (reportDescription.length < 10) {
            showErrorModal('Catatan audit harus minimal 10 karakter. Saat ini: ' + reportDescription.length + ' karakter');
            document.getElementById('reportDescription').focus();
            return;
        }
        
        if (reportDescription.length > 2000) {
            showErrorModal('Catatan audit maksimal 2000 karakter. Saat ini: ' + reportDescription.length + ' karakter');
            document.getElementById('reportDescription').focus();
            return;
        }
        
        if (!selfiePhoto) {
            showErrorModal('Mohon ambil foto selfie sebagai bukti kehadiran');
            return;
        }
        
        if (!latitude || !longitude) {
            showErrorModal('Mohon klik di peta untuk menentukan lokasi');
            return;
        }
        
        const formData = new FormData();
        
        // Add form fields explicitly
        formData.append('auditor_notes', reportDescription);
        formData.append('selfie_photo_data', selfiePhoto);
        formData.append('selfie_latitude', latitude);
        formData.append('selfie_longitude', longitude);
        
        // Add additional photos if any
        const additionalPhotosInput = document.querySelector('input[name="additional_photos[]"]');
        if (additionalPhotosInput && additionalPhotosInput.files) {
            for (let i = 0; i < additionalPhotosInput.files.length; i++) {
                formData.append('additional_photos[]', additionalPhotosInput.files[i]);
            }
        }
        
        // Show loading
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Menyimpan...';
        submitBtn.disabled = true;
        
        // Debug log all FormData entries
        console.log('Submitting form data:', {
            visitId: visitId,
            auditorNotes: reportDescription,
            auditorNotesLength: reportDescription.length,
            selfiePhoto: selfiePhoto ? 'Present' : 'Missing',
            latitude: latitude,
            longitude: longitude,
            submitUrl: `/auditor/visits/${visitId}/complete`
        });
        
        // Log all FormData entries
        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (typeof pair[1] === 'string' ? (pair[1].length > 100 ? 'Long string (' + pair[1].length + ' chars)' : pair[1]) : pair[1]));
        }
        
        // Add method spoofing for PATCH request
        formData.append('_method', 'PATCH');
        
        // Final validation before send
        if (!formData.get('auditor_notes') || !formData.get('selfie_photo_data') || 
            !formData.get('selfie_latitude') || !formData.get('selfie_longitude')) {
            alert('Data tidak lengkap. Mohon pastikan semua field telah diisi.');
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // Submit via AJAX
        fetch(`/auditor/visits/${visitId}/complete`, {
            method: 'POST', // Use POST with _method spoofing
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(errorText => {
                    console.log('Error response text:', errorText);
                    try {
                        const errorData = JSON.parse(errorText);
                        if (errorData.errors && typeof errorData.errors === 'object') {
                            // Laravel validation errors
                            const errorMessages = Object.values(errorData.errors).flat();
                            throw new Error(errorMessages.join('\n'));
                        } else if (errorData.message) {
                            throw new Error(errorData.message);
                        } else {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                    } catch (parseError) {
                        if (parseError instanceof SyntaxError) {
                            // Not JSON, use text as error
                            throw new Error(errorText || `HTTP ${response.status}: ${response.statusText}`);
                        } else {
                            throw parseError;
                        }
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Success message with green styling
                const successModal = document.createElement('div');
                successModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                successModal.innerHTML = `
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="bg-green-500 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                            <h3 class="text-lg font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Berhasil!
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-4">
                                <svg class="w-16 h-16 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 text-center mb-6">
                                Kunjungan berhasil diselesaikan dan menunggu konfirmasi admin!
                            </p>
                            <div class="flex justify-center">
                                <button onclick="this.closest('.fixed').remove(); closeCompleteModal(); window.location.reload();" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(successModal);
            } else {
                showErrorModal(data.message || 'Terjadi kesalahan saat memproses kunjungan');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Complete visit error:', error);
            
            // Use the error message from the Error object
            let errorMessage = error.message || 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
            
            // Show detailed error
            showErrorModal(errorMessage);
            
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

    // Helper functions
    function getStatusClass(status) {
        const classes = {
            'belum_dikunjungi': 'bg-yellow-100 text-yellow-800',
            'dalam_perjalanan': 'bg-blue-100 text-blue-800', 
            'sedang_dikunjungi': 'bg-orange-100 text-orange-800',
            'menunggu_acc': 'bg-purple-100 text-purple-800',
            'selesai': 'bg-green-100 text-green-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    function getStatusText(status) {
        const texts = {
            'belum_dikunjungi': 'Belum Dikunjungi',
            'dalam_perjalanan': 'Dalam Perjalanan',
            'sedang_dikunjungi': 'Sedang Dikunjungi',
            'menunggu_acc': 'Menunggu Konfirmasi Admin',
            'selesai': 'Selesai'
        };
        return texts[status] || status;
    }

    function getStatusConfig(status) {
        var statusConfigs = {
            'belum_dikunjungi': { color: 'yellow', text: 'Belum Dikunjungi' },
            'dalam_perjalanan': { color: 'blue', text: 'Dalam Perjalanan' },
            'sedang_dikunjungi': { color: 'orange', text: 'Sedang Dikunjungi' },
            'menunggu_acc': { color: 'purple', text: 'Menunggu Konfirmasi Admin' },
            'selesai': { color: 'green', text: 'Selesai' }
        };
        return statusConfigs[status] || { color: 'gray', text: status };
    }

    // Download report function
    function downloadReport(id) {
        // Close any open dropdowns
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        // Show not available message
        alert('Fitur download akan segera tersedia');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[id^="dropdown-"]') && !event.target.closest('button[onclick*="toggleDropdown"]')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });

    // Initialize visit system when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Character count for audit notes
        const reportDescription = document.getElementById('reportDescription');
        const charCount = document.getElementById('charCount');
        
        if (reportDescription && charCount) {
            function updateCharCount() {
                const length = reportDescription.value.length;
                charCount.textContent = `${length} / 2000 karakter (minimal 10 karakter)`;
                
                if (length < 10) {
                    charCount.className = 'text-xs text-red-500 mt-1';
                } else if (length > 1800) {
                    charCount.className = 'text-xs text-yellow-600 mt-1';
                } else {
                    charCount.className = 'text-xs text-green-600 mt-1';
                }
            }
            
            reportDescription.addEventListener('input', updateCharCount);
            reportDescription.addEventListener('paste', function() {
                setTimeout(updateCharCount, 10);
            });
            
            // Initial count
            updateCharCount();
        }
        
        // Ensure all containers allow overflow
        const containers = document.querySelectorAll('main, .p-8, .bg-white, .table-container, .overflow-x-auto, table, tbody');
        containers.forEach(container => {
            container.style.overflow = 'visible';
            container.style.position = 'relative';
        });
        
        console.log('Auditor visit management system loaded successfully');
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
</script>

</body>
</html>