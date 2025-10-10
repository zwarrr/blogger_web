<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ auth()->user()->role }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Leaflet CSS dan JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
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
        
        /* Dropdown Animation */
        .dropdown-menu {
            transform-origin: top right;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Dropdown positioning */
        .relative {
            position: relative !important;
        }
        
        [id^="dropdown-"] {
            position: absolute !important;
            z-index: 99999 !important;
            min-width: 180px;
            max-width: 220px;
            white-space: nowrap;
        }
        
        /* Prevent dropdown overflow */
        .table-container {
            position: relative;
        }
        
        /* Remove all outlines and borders from dropdown elements */
        [id^="dropdown-"] button,
        [id^="dropdown-"] a {
            outline: none !important;
            border: none !important;
        }
        
        [id^="dropdown-"] button:focus,
        [id^="dropdown-"] a:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Prevent table overflow from clipping dropdowns */
        .table-container {
            position: relative;
            overflow: visible !important;
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
        
        /* Leaflet Map Styles */
        .leaflet-container {
            background: #f8f9fa !important;
        }
        
        .leaflet-control-zoom {
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
        }
        
        .leaflet-control-zoom a {
            background: white !important;
            border: none !important;
            color: #666 !important;
        }
        
        .leaflet-control-zoom a:hover {
            background: #f5f5f5 !important;
        }
        
        /* Make maps responsive */
        [id^="main-map-"], [id^="selfie-map-"] {
            z-index: 1 !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('admin.sidebar')

    <div class="ml-64 min-h-screen">
        <!-- Page Transition Overlay -->
        <div class="page-transition" id="pageTransition">
            <svg class="w-24 h-24 text-orange-600" viewBox="0 0 50 50" fill="none">
                <circle class="spinner-circle" cx="25" cy="25" r="20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <p class="loading-text">Loading...</p>
        </div>

        <main class="flex-1">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i data-feather="clipboard-list" class="w-6 h-6 mr-3 text-orange-600"></i>
                            Kelola Kunjungan
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Manajemen kunjungan auditor ke author</p>
                    </div>
                    <a href="{{ route('admin.visits.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-150 shadow-sm">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Kunjungan
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-6 mt-6 mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-6 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="alert-circle" class="w-5 h-5 mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="p-6" x-data="visitTable()">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @isset($stats)
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="users" class="w-5 h-5 text-orange-600"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Kunjungan</h3>
                                </div>
                                <span class="text-xs text-green-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Seluruh periode</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="clock" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Menunggu</h3>
                                </div>
                                <span class="text-xs text-green-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2">{{ number_format($stats['pending'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['pending'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="check-circle" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Dikonfirmasi</h3>
                                </div>
                                <span class="text-xs text-green-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2">{{ number_format($stats['confirmed'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['confirmed'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center animate-pulse">
                                        <i data-feather="alert-circle" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Menunggu ACC</h3>
                                </div>
                            </div>
                            <p class="text-2xl font-bold mt-2 text-black-600">{{ number_format($stats['menunggu_acc'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round((($stats['menunggu_acc'] ?? 0) / $stats['total']) * 100, 1) : 0 }}% dari total</p>
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
                            <select x-model="filters.auditor_filter" @change="applyFilters()" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white min-w-[140px]">
                                <option value="">Semua Auditor</option>
                                @isset($auditors)
                                    @foreach($auditors as $auditor)
                                        <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <select x-model="filters.status_filter" @change="applyFilters()" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white min-w-[140px]">
                                <option value="">Semua Status</option>
                                @isset($statuses)
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <input type="date" x-model="filters.date_filter" @change="applyFilters()" class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 min-w-[140px]">
                        </div>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()" placeholder="Cari nama author..." class="text-sm border border-gray-300 rounded-md px-3 py-2 w-full sm:w-60 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <!-- Data Table -->
                <div class="bg-white rounded-lg border border-gray-200" style="overflow: visible;">
                    <!-- Table Header -->
                    <div class="flex items-center justify-between p-5">
                        <h3 class="text-lg font-semibold text-gray-900">Data Kunjungan</h3>
                        <a href="{{ route('admin.visits.create') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white text-sm rounded-md hover:bg-orange-600 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
                            </svg>
                            Tambah Kunjungan
                        </a>
                    </div>
                    <div class="mt-4 relative table-container">

                    <!-- Table Container -->
                    <div class="overflow-x-auto overflow-y-visible table-container">
                        <table class="w-full text-sm text-left table-fixed">
                            <colgroup>
                                <col style="width: 60px;">      <!-- No -->
                                <col style="width: 12%;">       <!-- ID -->
                                <col style="width: 16%;">       <!-- Date -->
                                <col style="width: 20%;">       <!-- Author -->
                                <col style="width: 20%;">       <!-- Auditor -->
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
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Auditor</th>
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
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100 shadow-sm">
                                                    <span>#{{ $visit->id }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Date & Time -->
                                        <td class="px-4 py-3 text-center">
                                            <div class="text-xs text-gray-900 font-medium whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($visit->visit_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                {{ \Carbon\Carbon::parse($visit->visit_date)->format('H:i') }} WIB
                                            </div>
                                        </td>
                                        
                                        <!-- Author -->
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100 shadow-sm max-w-full">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                    </svg>
                                                    <span class="truncate min-w-0" title="{{ $visit->author->name ?? ($visit->author_name ?? '') }}">{{ $visit->author->name ?? ($visit->author_name ?? '') }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Auditor -->
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center">
                                                @if($visit->auditor && $visit->auditor->name)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-100 shadow-sm max-w-full">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                                        </svg>
                                                        <span class="truncate min-w-0" title="{{ $visit->auditor->name }}">{{ $visit->auditor->name }}</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                                        </svg>
                                                        <span>Belum Assigned</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                        <!-- Status -->
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center items-center">
                                @if($visit->status === 'selesai')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif($visit->status === 'dalam_perjalanan' || $visit->status === 'sedang_dikunjungi')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $visit->status === 'dalam_perjalanan' ? 'Dalam Perjalanan' : 'Sedang Dikunjungi' }}
                                    </span>
                                @elseif($visit->status === 'menunggu_acc')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Menunggu ACC Admin
                                    </span>
                                @elseif($visit->status === 'belum_dikunjungi')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Belum Dikunjungi
                                    </span>
                                @elseif($visit->status === 'completed')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Selesai
                                    </span>
                                @elseif($visit->status === 'in_progress')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Berlangsung
                                    </span>
                                @elseif($visit->status === 'pending')
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200 whitespace-nowrap animate-pulse">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                    </span>
                                @endif
                            </div>
                        </td>                                        <!-- Actions -->
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
                                                     class="rounded-lg shadow-xl bg-white border border-gray-200 hidden overflow-hidden"
                                                     style="z-index: 99999;">
                                                    <div class="py-1">
                                                        <button onclick="showDetailModal({{ $visit->id }})" 
                                                                class="group flex items-center w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-500">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            Lihat Detail
                                                        </button>
                                                        
                                        @if($visit->status === 'menunggu_acc')
                                            @if(auth()->user()->role === 'admin')
                                                <!-- ACC Actions for Admin only -->
                                                <button onclick="showApproveModal({{ $visit->id }})" 
                                                        class="group flex items-center w-full px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-green-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    ACC / Setujui
                                                </button>
                                                
                                                <button onclick="showRejectModal({{ $visit->id }})" 
                                                        class="group flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-red-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Tolak / Reject
                                                </button>
                                                
                                                <div class="border-t border-gray-100 my-1"></div>
                                            @endif
                                            
                                            @if(auth()->user()->role === 'auditor' && auth()->user()->id == $visit->auditor_id)
                                                <!-- Complete Action for Auditor -->
                                                <button onclick="completeVisit({{ $visit->id }})" 
                                                        class="group flex items-center w-full px-4 py-2.5 text-sm text-blue-700 hover:bg-blue-50 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-blue-600">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                                    </svg>
                                                    Selesaikan Kunjungan
                                                </button>
                                                
                                                <div class="border-t border-gray-100 my-1"></div>
                                            @endif
                                        @endif
                                        
                                        @if($visit->status !== 'selesai')
                                            <a href="{{ route('admin.visits.edit', $visit->id) }}" 
                                               class="group flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Edit Kunjungan
                                            </a>
                                            
                                            <div class="border-t border-gray-100 my-1"></div>
                                        @endif
                                        
                                        @if($visit->status !== 'selesai')
                                            <button onclick="deleteVisit({{ $visit->id }})" 
                                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244 2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Hapus Kunjungan
                                            </button>
                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i data-feather="inbox" class="w-6 h-6 text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Tidak ada data kunjungan</h3>
                                                    <p class="text-xs text-gray-500">Belum ada kunjungan yang tercatat dalam sistem</p>
                                                </div>
                                                <a href="{{ route('admin.visits.create') }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-xs font-medium rounded-md hover:bg-orange-700 transition-colors">
                                                    <i data-feather="plus" class="w-3 h-3 mr-1.5"></i>
                                                    Tambah Kunjungan
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    </div>

    <!-- Inline scripts -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        function visitTable() {
            return {
                loading: false,
                filters: { auditor_filter:'', status_filter:'', date_filter:'', search:'' },
                applyFilters() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    Object.keys(this.filters).forEach(k=>{ if (this.filters[k]) params.set(k,this.filters[k]) });
                    const url = `${window.location.pathname}?${params.toString()}`;
                    fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} })
                        .then(r=>r.json()).then(data=>{ document.getElementById('visits-table-body').innerHTML = data.html; this.loading=false; feather.replace() }).catch(e=>{ console.error(e); this.loading=false });
                }
            }
        }


        function toggleDropdown(id) {
            const dropdown = document.getElementById(`dropdown-${id}`);
            const button = document.querySelector(`[onclick="toggleDropdown(${id})"]`);
            const isHidden = dropdown.classList.contains('hidden');
            
            // Close all dropdowns first
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                d.classList.add('hidden');
                d.style.position = '';
                d.style.top = '';
                d.style.bottom = '';
                d.style.left = '';
                d.style.right = '';
                d.style.transform = '';
                d.style.marginTop = '';
                d.style.marginBottom = '';
            });
            
            if (isHidden && button && dropdown) {
                // Get button position and viewport info
                const buttonRect = button.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const viewportWidth = window.innerWidth;
                const dropdownHeight = 120; // Estimated dropdown height
                const dropdownWidth = 180;
                
                // Check if there's enough space below
                const spaceBelow = viewportHeight - buttonRect.bottom;
                const spaceAbove = buttonRect.top;
                
                dropdown.style.position = 'absolute';
                dropdown.style.zIndex = '99999';
                
                // Vertical positioning - show above if not enough space below
                if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                    dropdown.style.bottom = '100%';
                    dropdown.style.top = 'auto';
                    dropdown.style.marginBottom = '4px';
                } else {
                    dropdown.style.top = '100%';
                    dropdown.style.bottom = 'auto';
                    dropdown.style.marginTop = '4px';
                }
                
                // Horizontal positioning
                const buttonCenter = buttonRect.left + (buttonRect.width / 2);
                const dropdownLeft = buttonCenter - (dropdownWidth / 2);
                
                if (dropdownLeft < 10) {
                    // Too far left
                    dropdown.style.left = '0';
                    dropdown.style.right = 'auto';
                    dropdown.style.transform = 'translateX(0)';
                } else if (dropdownLeft + dropdownWidth > viewportWidth - 10) {
                    // Too far right
                    dropdown.style.right = '0';
                    dropdown.style.left = 'auto';
                    dropdown.style.transform = 'translateX(0)';
                } else {
                    // Center under button
                    dropdown.style.left = '50%';
                    dropdown.style.right = 'auto';
                    dropdown.style.transform = 'translateX(-50%)';
                }
                
                // Show dropdown
                dropdown.classList.remove('hidden');
            }
        }

        function showDetailModal(id) {
            // Close any open dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d=>d.classList.add('hidden'));
            
            // Show loading in modal
            const modal = document.getElementById('detailModal');
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '<div class="flex justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div></div>';
            modal.classList.remove('hidden');
            
            // Fetch visit details
            fetch(`/admin/visits/${id}/json`)
                .then(response => response.json())
                .then(data => {
                    modalBody.innerHTML = generateModalContent(data);
                    feather.replace();
                    
                    // Initialize maps after modal content is loaded
                    setTimeout(initializePendingMaps, 100);
                })
                .catch(error => {
                    modalBody.innerHTML = '<div class="text-red-600 text-center py-4">Gagal memuat detail kunjungan</div>';
                });
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function deleteVisit(id) {
            // Close any open dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
            
            // Show delete confirmation modal
            showDeleteModal(id);
        }

        function completeVisit(id) {
            // Close any open dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
            
            if (confirm('Apakah Anda yakin ingin menyelesaikan kunjungan ini?')) {
                // Update status to selesai
                fetch('/admin/visits/' + id + '/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Kunjungan berhasil diselesaikan', 'success');
                        location.reload();
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan sistem', 'error');
                });
            }
        }

        function showDeleteModal(visitId) {
            const modal = document.getElementById('deleteModal');
            const confirmButton = document.getElementById('confirmDeleteBtn');
            
            // Set the visit ID for confirmation
            confirmButton.onclick = function() {
                confirmDelete(visitId);
            };
            
            // Show modal
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmDelete(id) {
            // Close modal
            closeDeleteModal();
            
            // Create form for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/visits/${id}`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add DELETE method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }

        function generateModalContent(visit) {
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

        function clearFilters() {
            // Reset all filter values
            const filterElements = document.querySelectorAll('[x-model^="filters."]');
            filterElements.forEach(element => {
                if (element.type === 'select-one') {
                    element.selectedIndex = 0;
                } else {
                    element.value = '';
                }
            });
            
            // Trigger filter update
            if (window.visitTable && typeof window.visitTable === 'function') {
                window.visitTable();
            } else {
                location.reload();
            }
        }

        document.addEventListener('click', function(e) { 
            if (!e.target.closest('[onclick*="toggleDropdown"]') && !e.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                    d.style.position = '';
                    d.style.top = '';
                    d.style.bottom = '';
                    d.style.left = '';
                    d.style.right = '';
                    d.style.transform = '';
                    d.style.marginTop = '';
                    d.style.marginBottom = '';
                });
            }
        });

        // Close dropdowns on scroll to prevent positioning issues
        window.addEventListener('scroll', function() {
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                d.classList.add('hidden');
                d.style.position = '';
                d.style.top = '';
                d.style.bottom = '';
                d.style.left = '';
                d.style.right = '';
                d.style.transform = '';
                d.style.marginTop = '';
                d.style.marginBottom = '';
            });
        });
        
        // Global variables for ACC functionality
        var currentVisitId = null;

        // Approve Modal Functions
        function showApproveModal(visitId) {
            // Close any open dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(function(d) {
                d.classList.add('hidden');
            });
            
            currentVisitId = visitId;
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentVisitId = null;
        }

        function confirmApprove() {
            if (!currentVisitId) return;

            // Show loading state
            var button = document.getElementById('confirmApproveBtn');
            var originalText = button.textContent;
            button.textContent = 'Memproses...';
            button.disabled = true;

            // Send approve request
            fetch('/admin/visits/' + currentVisitId + '/approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { response: response, data: data };
                });
            })
            .then(function(result) {
                if (result.response.ok && result.data.success !== false) {
                    // Show success message
                    showNotification(result.data.message || 'Laporan kunjungan telah di-ACC dan diselesaikan', 'success');
                    
                    // Close modal
                    closeApproveModal();
                    
                    // Refresh page to show updated status
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.data.message || 'Gagal menyetujui laporan');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showNotification(error.message || 'Terjadi kesalahan saat menyetujui laporan', 'error');
                
                // Reset button
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        // Reject Modal Functions
        function showRejectModal(visitId) {
            // Close any open dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(function(d) {
                d.classList.add('hidden');
            });
            
            currentVisitId = visitId;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Reset form
            document.getElementById('rejectForm').reset();
            updateRejectCharCount();
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentVisitId = null;
        }

        function confirmReject(event) {
            event.preventDefault();
            
            if (!currentVisitId) return;

            var formData = new FormData(event.target);
            var rejectionNotes = formData.get('rejection_notes');

            if (!rejectionNotes || rejectionNotes.trim().length < 10) {
                showNotification('Alasan penolakan minimal 10 karakter', 'error');
                return;
            }

            // Show loading state
            var button = event.target.querySelector('button[type="submit"]');
            var originalText = button.textContent;
            button.textContent = 'Memproses...';
            button.disabled = true;

            // Send reject request
            fetch('/admin/visits/' + currentVisitId + '/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    rejection_notes: rejectionNotes
                })
            })
            .then(function(response) {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(function(data) {
                        console.log('Response data:', data);
                        return { response: response, data: data };
                    });
                } else {
                    // Handle non-JSON responses (like HTML error pages)
                    return response.text().then(function(text) {
                        console.log('Non-JSON response text:', text.substring(0, 200));
                        throw new Error(`Server error: Expected JSON but received ${response.status} ${response.statusText}`);
                    });
                }
            })
            .then(function(result) {
                if (result.response.ok && result.data.success !== false) {
                    // Show success message
                    showNotification(result.data.message || 'Laporan kunjungan ditolak, auditor perlu melakukan kunjungan ulang', 'warning');
                    
                    // Close modal
                    closeRejectModal();
                    
                    // Refresh page to show updated status
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.data.message || 'Gagal menolak laporan');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                
                // More specific error messages
                let errorMessage = 'Terjadi kesalahan saat menolak laporan';
                if (error.message.includes('Expected JSON')) {
                    errorMessage = 'Server mengembalikan response yang tidak valid. Silakan periksa koneksi atau hubungi administrator.';
                } else if (error.message.includes('401')) {
                    errorMessage = 'Session telah berakhir. Silakan login ulang.';
                } else if (error.message.includes('403')) {
                    errorMessage = 'Anda tidak memiliki akses untuk melakukan aksi ini.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                }
                
                showNotification(errorMessage, 'error');
                
                // Reset button
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        // Character counter for rejection notes
        function updateRejectCharCount() {
            var textarea = document.getElementById('rejectionNotes');
            var counter = document.getElementById('rejectCharCount');
            
            if (textarea && counter) {
                var count = textarea.value.length;
                counter.textContent = count;
                
                // Color coding
                if (count < 10) {
                    counter.className = 'text-red-500 font-medium';
                } else if (count > 450) {
                    counter.className = 'text-orange-500 font-medium';
                } else {
                    counter.className = 'text-green-500 font-medium';
                }
            }
        }

        // Initialize ACC functionality
        function initializeAccFunctionality() {
            var rejectionNotes = document.getElementById('rejectionNotes');
            if (rejectionNotes) {
                rejectionNotes.addEventListener('input', updateRejectCharCount);
            }
            
            // ESC key to close modals
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeApproveModal();
                    closeRejectModal();
                }
            });
            
            // Click outside modal to close
            var approveModal = document.getElementById('approveModal');
            if (approveModal) {
                approveModal.addEventListener('click', function(e) {
                    if (e.target === this) closeApproveModal();
                });
            }
            
            var rejectModal = document.getElementById('rejectModal');
            if (rejectModal) {
                rejectModal.addEventListener('click', function(e) {
                    if (e.target === this) closeRejectModal();
                });
            }
        }

        // Notification function
        function showNotification(message, type) {
            if (!type) type = 'info';
            
            // Create notification element
            var notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-[99999] max-w-sm p-4 rounded-lg shadow-lg border ' + getNotificationClasses(type);
            notification.innerHTML = 
                '<div class="flex items-center">' +
                    '<div class="flex-shrink-0">' +
                        getNotificationIcon(type) +
                    '</div>' +
                    '<div class="ml-3">' +
                        '<p class="text-sm font-medium">' + message + '</p>' +
                    '</div>' +
                    '<button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">' +
                        '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">' +
                            '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>' +
                        '</svg>' +
                    '</button>' +
                '</div>';
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        function getNotificationClasses(type) {
            const classes = {
                'success': 'bg-green-50 border-green-200 text-green-800',
                'error': 'bg-red-50 border-red-200 text-red-800',
                'warning': 'bg-orange-50 border-orange-200 text-orange-800',
                'info': 'bg-blue-50 border-blue-200 text-blue-800'
            };
            return classes[type] || classes.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                'success': '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                'error': '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
                'warning': '<svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
                'info': '<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>'
            };
            return icons[type] || icons.info;
        }

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

        document.addEventListener('DOMContentLoaded', function() { 
            feather.replace();
            initializeAccFunctionality();
        });
    </script>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-black/70"></div>
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-2xl mx-4">
                <!-- Modal Header -->
                <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Kunjungan</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">✕</button>
                </div>
                
                <!-- Modal Body -->
                <div id="modalBody" class="p-5 text-sm">
                    <!-- Content will be loaded here -->
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end px-5 py-3 border-t border-gray-200">
                    <button onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-black/70"></div>
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md mx-4">
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 mx-auto flex items-center justify-center rounded-full bg-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="ml-4 text-left">
                            <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                            <p class="text-sm text-gray-600 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="px-5 py-4">
                    <p class="text-sm text-gray-700">
                        Apakah Anda yakin ingin menghapus kunjungan ini? Semua data yang terkait dengan kunjungan ini akan dihapus secara permanen.
                    </p>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-150">
                        Batal
                    </button>
                    <button id="confirmDeleteBtn" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-150">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Visit Modal -->
    <div id="approveModal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-black/70"></div>
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md mx-4">
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 mx-auto flex items-center justify-center rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 text-left">
                            <h3 class="text-lg font-medium text-gray-900">Konfirmasi ACC</h3>
                            <p class="text-sm text-gray-600 mt-1">Setujui laporan kunjungan auditor</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="px-5 py-4">
                    <p class="text-sm text-gray-700">
                        Apakah Anda yakin ingin menyetujui (ACC) laporan kunjungan ini? Status akan berubah menjadi <strong>"Selesai"</strong>.
                    </p>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200">
                    <button onclick="closeApproveModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-150">
                        Batal
                    </button>
                    <button id="confirmApproveBtn" onclick="confirmApprove()"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-150">
                        Ya, ACC Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Visit Modal -->
    <div id="rejectModal" class="fixed inset-0 z-[9999] hidden">
        <div class="fixed inset-0 bg-black/70"></div>
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md mx-4">
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 mx-auto flex items-center justify-center rounded-full bg-red-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4 text-left">
                            <h3 class="text-lg font-medium text-gray-900">Tolak Laporan</h3>
                            <p class="text-sm text-gray-600 mt-1">Berikan alasan penolakan</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <form id="rejectForm" onsubmit="confirmReject(event)">
                    <div class="px-5 py-4">
                        <div class="mb-4">
                            <label for="rejectionNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="rejectionNotes" 
                                name="rejection_notes" 
                                rows="4" 
                                required
                                minlength="10"
                                maxlength="500"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                                placeholder="Jelaskan alasan penolakan laporan kunjungan ini... (minimal 10 karakter)"></textarea>
                            <div class="text-xs text-gray-500 mt-1">
                                <span id="rejectCharCount">0</span>/500 karakter (minimal 10)
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-sm text-yellow-800">
                                <strong>Perhatian:</strong> Setelah ditolak, status akan kembali ke "Belum Dikunjungi" dan auditor harus melakukan kunjungan ulang.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-150">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-150">
                            Tolak Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('visits.workflow-modals')
</body>
</html>