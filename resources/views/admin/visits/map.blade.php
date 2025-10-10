<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Peta Kunjungan - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <script src="https://unpkg.com/feather-icons"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        
        /* Remove black hover effects */
        .hover-no-black:hover {
            background-color: #f9fafb !important;
            border-color: #e5e7eb !important;
        }
        
        /* Custom select styling */
        select:focus {
            outline: none;
        }
        
        /* Input styling enhancements */
        input:focus,
        select:focus {
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
        }
        
        /* Smooth transitions for all interactive elements */
        .transition-smooth {
            transition: all 0.2s ease-in-out;
        }
        
        /* Legend item hover effects */
        .legend-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('admin.sidebar')
    
    <div class="ml-64 min-h-screen">
        <main class="flex-1">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i data-feather="map-pin" class="w-6 h-6 mr-3 text-orange-600"></i>
                            Peta Kunjungan
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Visualisasi lokasi kunjungan auditor ke author</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.visits.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-150 shadow-sm">
                            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Kunjungan
                        </a>
                        <a href="{{ route('admin.visits.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-150 shadow-sm">
                            <i data-feather="list" class="w-4 h-4 mr-2"></i>
                            Daftar Kunjungan
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="p-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <!-- Total Kunjungan -->
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
                        <p class="text-2xl font-bold mt-2">{{ $stats['total_visits'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Semua data kunjungan</p>
                    </div>
                    
                    <!-- Dalam Perjalanan -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <i data-feather="navigation" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-500">Dalam Perjalanan</h3>
                            </div>
                            <span class="text-xs text-green-600">&nbsp;</span>
                        </div>
                        <p class="text-2xl font-bold mt-2">{{ $stats['dalam_perjalanan'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Sedang dalam perjalanan</p>
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
                        <p class="text-2xl font-bold mt-2">{{ $stats['belum_dikunjungi'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Menunggu kunjungan</p>
                    </div>
                    
                    <!-- Dikonfirmasi -->
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
                        <p class="text-2xl font-bold mt-2">{{ $stats['dikonfirmasi'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Siap dikunjungi</p>
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
                        <p class="text-2xl font-bold mt-2">{{ $stats['selesai'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Kunjungan berhasil</p>
                    </div>
                </div>

                <!-- Map Controls & Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-900">Kontrol Peta & Filter</h3>
                    </div>
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                <!-- Auditor Filter -->
                                <form method="GET" class="flex items-center gap-3">
                                    <label class="text-sm font-semibold text-gray-700 whitespace-nowrap flex items-center gap-2">
                                        <i data-feather="user-check" class="w-4 h-4 text-blue-500"></i>
                                        Auditor:
                                    </label>
                                    <div class="relative">
                                        <select name="auditor" id="auditorFilter" 
                                                class="appearance-none border-2 border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm bg-white text-gray-700 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 min-w-[170px] font-medium shadow-sm">
                                            <option value="">Semua Auditor</option>
                                            @php
                                                $auditors = \App\Models\Auditor::select('id', 'name')->orderBy('name')->get();
                                            @endphp
                                            @foreach($auditors as $auditor)
                                                <option value="{{ $auditor->id }}" {{ request('auditor') == $auditor->id ? 'selected' : '' }}>
                                                    {{ $auditor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i data-feather="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                    </div>
                                </form>
                                
                                <!-- Status Info -->
                                <div class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                                    <i data-feather="map-pin" class="w-4 h-4 text-blue-600"></i>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-700">Menampilkan:</span> 
                                        <span class="text-blue-600 font-bold">{{ count($mapData) }}</span>
                                        <span class="text-gray-500">lokasi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
                                    <i data-feather="map" class="w-5 h-5 text-orange-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Peta Lokasi Kunjungan</h3>
                                    <p class="text-sm text-gray-600">Klik marker untuk melihat detail lengkap kunjungan</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full border border-green-200">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-green-700 font-medium">Live Data</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative bg-gray-50">
                        <div id="map" class="w-full h-[450px] rounded-b-lg overflow-hidden">
                            <div id="mapLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[1000]">
                                <div class="text-center">
                                    <div class="inline-flex items-center px-6 py-3 bg-white rounded-xl shadow-lg border border-gray-200">
                                        <svg class="animate-spin h-5 w-5 text-orange-500 mr-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-800">Memuat peta kunjungan...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Visit Details Modal -->
            <div id="visitModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[9999] hidden transition-all duration-300">
                <div class="bg-white rounded-xl border border-gray-200 shadow-2xl max-w-5xl w-full mx-6 max-h-[85vh] overflow-hidden transform transition-all duration-300 scale-95">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-orange-500 flex items-center justify-center">
                                    <i data-feather="file-text" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Detail Kunjungan</h3>
                                    <p class="text-sm text-gray-600">Informasi lengkap kunjungan auditor</p>
                                </div>
                            </div>
                            <button onclick="closeModal()" class="h-8 w-8 rounded-lg bg-white hover:bg-gray-100 border border-gray-200 text-gray-500 hover:text-gray-700 transition-all duration-200 flex items-center justify-center">
                                <i data-feather="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Modal Content -->
                    <div id="modalContent" class="p-6 overflow-y-auto max-h-[calc(85vh-140px)] bg-gray-50">
                        <!-- Loading State -->
                        <div class="flex items-center justify-center py-12">
                            <div class="text-center">
                                <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-600 text-sm">Memuat detail kunjungan...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-b-xl">
                        <div class="flex justify-end gap-3">
                            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                Tutup
                            </button>
                            <button id="modalActionBtn" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 hidden">
                                Detail Lengkap
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>


<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loading indicator when map is ready
        setTimeout(() => {
            document.getElementById('mapLoading').style.display = 'none';
        }, 1000);

        // Initialize map
        const map = L.map('map').setView([-6.2088, 106.8456], 10);
        
        // Add tile layer with better attribution
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const visitsData = @json($mapData);
        console.log('Loaded visits data from database:', visitsData.length, 'locations');
        
        if (visitsData.length > 0) {
            console.log('First visit data from database:', visitsData[0]);
            
            // Debug: Log all visit statuses from database
            const statusCounts = {};
            visitsData.forEach(visit => {
                statusCounts[visit.status] = (statusCounts[visit.status] || 0) + 1;
            });
            console.log('Status distribution from database:', statusCounts);
            
            // Check if any visits have valid coordinates
            const validCoords = visitsData.filter(v => v.latitude && v.longitude && v.latitude !== 0 && v.longitude !== 0);
            console.log('Visits with valid coordinates:', validCoords.length);
        } else {
            console.warn('No visits data loaded from database');
            console.log('Check if Visit table has data with valid coordinates (latitude, longitude)');
        }
        
        // Status colors matching the legend
        const statusColors = {
            'di_peta': '#f97316',            // Orange-500 (for map markers)
            'belum_dikunjungi': '#eab308',   // Yellow-500
            'dikonfirmasi': '#8b5cf6',       // Violet-500
            'dalam_perjalanan': '#a855f7',   // Purple-500
            'selesai': '#22c55e',            // Green-500
            'menunggu_acc': '#f97316'        // Orange-500
        };
        
        // Status labels for display
        const statusLabels = {
            'belum_dikunjungi': 'Belum Dikunjungi',
            'dikonfirmasi': 'Dikonfirmasi',
            'dalam_perjalanan': 'Dalam Perjalanan',
            'selesai': 'Selesai',
            'menunggu_acc': 'Menunggu ACC'
        };

        // Function to get auditor initials
        function getAuditorInitials(auditorName) {
            if (!auditorName) return 'A';
            return auditorName.split(' ')
                .map(name => name.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 2);
        }

        // Function to create numbered marker icon with auditor initials
        function createNumberedIcon(color, status, visitNumber, auditorName) {
            const initials = getAuditorInitials(auditorName);
            
            return L.divIcon({
                className: 'custom-numbered-icon',
                html: `
                    <div class="relative">
                        <!-- Main marker circle with initials -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg border-3 border-white relative" 
                             style="background-color: ${color};">
                            ${initials}
                        </div>
                        <!-- Number badge -->
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white shadow-md">
                            ${visitNumber}
                        </div>
                        <!-- Pointer tail -->
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-[7px] border-r-[7px] border-t-[10px] border-transparent" 
                             style="border-top-color: ${color};"></div>
                    </div>
                `,
                iconSize: [40, 50],
                iconAnchor: [20, 50],
                popupAnchor: [0, -50]
            });
        }

        const markers = [];
        const routeLines = [];
        let visitCounter = 1;
        
        // Sort visits by visit_date for proper route sequencing
        const sortedVisits = visitsData.slice().sort((a, b) => new Date(a.visit_date) - new Date(b.visit_date));
        
        sortedVisits.forEach((visit, index) => {
            // Determine color based on status - matching legend colors
            let color;
            switch(visit.status) {
                case 'belum_dikunjungi':
                    color = '#eab308'; // Yellow-500 - matching legend
                    break;
                case 'dikonfirmasi':
                    color = '#8b5cf6'; // Violet-500 - matching legend
                    break;
                case 'dalam_perjalanan':
                    color = '#a855f7'; // Purple-500 - matching legend
                    break;
                case 'selesai':
                    color = '#22c55e'; // Green-500 - matching legend
                    break;
                default:
                    color = '#f97316'; // Orange-500 - default "Di Peta"
            }
            
            // Create numbered marker with auditor initials
            const numberedIcon = createNumberedIcon(color, visit.status, visitCounter, visit.auditor_name);
            const marker = L.marker([visit.latitude, visit.longitude], {
                icon: numberedIcon
            }).addTo(map);
            
            visitCounter++;
            
            // Professional popup with complete information
            const popupContent = `
                <div class="min-w-[280px] max-w-[320px]">
                    <!-- Header -->
                    <div class="bg-gray-50 -m-3 mb-3 p-3 border-b">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-800 text-base">${visit.visit_id}</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full" 
                                  style="background-color: ${color}20; color: ${color}; border: 1px solid ${color}40;">
                                ${statusLabels[visit.status] || visit.status_label}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="space-y-3 text-sm">
                        <!-- Participants -->
                        <div class="grid grid-cols-1 gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">${visit.author_name}</p>
                                    <p class="text-xs text-gray-500">Author</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center text-xs font-bold text-orange-600">
                                    ${getAuditorInitials(visit.auditor_name)}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">${visit.auditor_name}</p>
                                    <p class="text-xs text-gray-500">Auditor (#${index + 1})</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details -->
                        <div class="space-y-2 pt-2 border-t border-gray-100">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs">Jadwal Kunjungan</p>
                                    <p class="font-medium text-gray-800">${visit.visit_date}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-xs">Alamat Lokasi</p>
                                    <p class="font-medium text-gray-800 leading-tight">${visit.location_address || 'Alamat tidak tersedia'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <button onclick="showVisitDetail(${visit.id})" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail Lengkap
                        </button>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            markers.push(marker);
            
            // Create route connections (connect to previous marker)
            if (index > 0) {
                const prevVisit = sortedVisits[index - 1];
                const routeLine = L.polyline([
                    [prevVisit.latitude, prevVisit.longitude],
                    [visit.latitude, visit.longitude]
                ], {
                    color: '#6366f1', // Indigo color for routes
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '8, 8', // Dashed line
                    className: 'route-line'
                }).addTo(map);
                
                // Add route popup with travel info
                const routePopup = `
                    <div class="text-center p-2">
                        <div class="font-semibold text-sm text-indigo-700 mb-1">Rute Kunjungan</div>
                        <div class="text-xs text-gray-600">
                            Dari: <span class="font-medium">${prevVisit.visit_id}</span><br>
                            Ke: <span class="font-medium">${visit.visit_id}</span>
                        </div>
                        <div class="text-xs text-indigo-600 mt-1 font-medium">Urutan ${index} → ${index + 1}</div>
                    </div>
                `;
                
                routeLine.bindPopup(routePopup, {
                    className: 'route-popup'
                });
                
                routeLines.push(routeLine);
            }
        });

        // Hide loading and fit map to markers
        setTimeout(() => {
            const loading = document.getElementById('mapLoading');
            if (loading) {
                loading.style.opacity = '0';
                setTimeout(() => loading.remove(), 300);
            }
            
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
                console.log('Map fitted to', markers.length, 'markers');
            } else {
                console.warn('❌ No valid markers to display from database');
                
                // Show helpful message overlay on map
                const noDataOverlay = L.popup({
                    closeButton: false,
                    autoClose: false,
                    closeOnClick: false,
                    className: 'no-data-popup'
                })
                .setLatLng([-6.2088, 106.8456])
                .setContent(`
                    <div class="text-center p-4">
                        <div class="text-6xl mb-2">
                            <i data-feather="map" class="w-16 h-16 text-orange-600 mx-auto"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Tidak Ada Data Kunjungan</h3>
                        <p class="text-gray-600 text-sm mb-3">
                            Belum ada kunjungan dengan koordinat lokasi yang tersedia.
                        </p>
                        <div class="text-xs text-gray-500">
                            <p>Koordinat akan muncul setelah:</p>
                            <p>• Data kunjungan ditambahkan ke database</p>
                            <p>• Lokasi kunjungan sudah diisi dengan benar</p>
                        </div>
                    </div>
                `)
                .openOn(map);
                
                // Set default view to Indonesia
                map.setView([-6.2088, 106.8456], 6);
            }
        }, 1000);
        
        // Add map controls with custom styling
        L.control.scale({
            metric: true,
            imperial: false,
            position: 'bottomright'
        }).addTo(map);

        // Filter functionality for auditor only
        const auditorFilter = document.getElementById('auditorFilter');
        let originalMarkers = [...markers]; // Store original markers from database
        let originalRoutes = [...routeLines]; // Store original route lines
        
        // Combined filter function
        function applyFilters() {
            const selectedAuditor = auditorFilter ? auditorFilter.value : '';
            
            // Clear all markers and routes
            markers.forEach(marker => map.removeLayer(marker));
            routeLines.forEach(route => map.removeLayer(route));
            markers.length = 0;
            routeLines.length = 0;
            
            let filteredVisits = sortedVisits.slice();
            
            // Apply auditor filter
            if (selectedAuditor) {
                filteredVisits = filteredVisits.filter(visit => 
                    visit.auditor_id && visit.auditor_id.toString() === selectedAuditor
                );
            }
            
            // Recreate markers and routes for filtered visits
            let visitCounter = 1;
            filteredVisits.forEach((visit, index) => {
                const originalIndex = sortedVisits.findIndex(v => v.id === visit.id);
                if (originalIndex !== -1) {
                    const marker = originalMarkers[originalIndex];
                    if (marker) {
                        map.addLayer(marker);
                        markers.push(marker);
                        
                        // Recreate routes for consecutive visits
                        if (index > 0) {
                            const prevVisit = filteredVisits[index - 1];
                            const routeLine = L.polyline([
                                [prevVisit.latitude, prevVisit.longitude],
                                [visit.latitude, visit.longitude]
                            ], {
                                color: '#6366f1',
                                weight: 3,
                                opacity: 0.7,
                                dashArray: '8, 8',
                                className: 'route-line'
                            }).addTo(map);
                            
                            const routePopup = `
                                <div class="text-center p-2">
                                    <div class="font-semibold text-sm text-indigo-700 mb-1">Rute Kunjungan</div>
                                    <div class="text-xs text-gray-600">
                                        Dari: <span class="font-medium">${prevVisit.visit_id}</span><br>
                                        Ke: <span class="font-medium">${visit.visit_id}</span>
                                    </div>
                                    <div class="text-xs text-indigo-600 mt-1 font-medium">Urutan ${index} → ${index + 1}</div>
                                </div>
                            `;
                            
                            routeLine.bindPopup(routePopup, { className: 'route-popup' });
                            routeLines.push(routeLine);
                        }
                    }
                }
            });
            
            updateDisplayCount(filteredVisits.length);
            
            // Fit map to filtered markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
        
        // Event listeners for auditor filter
        if (auditorFilter) auditorFilter.addEventListener('change', applyFilters);

        // Update display count
        function updateDisplayCount(count) {
            const countElement = document.querySelector('.text-blue-600.font-bold');
            if (countElement) {
                countElement.textContent = count;
            }
        }
        
        // Add custom zoom control
        L.control.zoom({
            position: 'topright'
        }).addTo(map);
    });

    // Function to show visit detail in modal
    function showVisitDetail(visitId) {
        const visitsData = @json($mapData);
        const visit = visitsData.find(v => v.id === visitId);
        
        if (!visit) return;
        
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = generateModalContent(visit);
        
        const modal = document.getElementById('visitModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Add smooth animation
        setTimeout(() => {
            modal.querySelector('.max-w-5xl').classList.remove('scale-95');
            modal.querySelector('.max-w-5xl').classList.add('scale-100');
        }, 10);
        
        // Re-initialize feather icons and initialize pending maps
        setTimeout(() => {
            feather.replace();
            initializePendingMaps();
        }, 150);
    }

    function generateModalContent(visit) {
        var statusConfig = {
            'selesai': { color: 'orange', text: 'Selesai' },
            'menunggu_acc': { color: 'orange', text: 'Menunggu ACC' },
            'sedang_dikunjungi': { color: 'orange', text: 'Sedang Dikunjungi' },
            'dalam_perjalanan': { color: 'orange', text: 'Dalam Perjalanan' },
            'belum_dikunjungi': { color: 'orange', text: 'Belum Dikunjungi' },
            'dikonfirmasi': { color: 'orange', text: 'Dikonfirmasi' },
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
        content += '<div class="text-sm font-semibold text-gray-900">' + visit.visit_id + '</div>';
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
        } else if (visit.author_name) {
            content += '<div class="text-sm font-medium text-gray-900 mb-1">' + visit.author_name + '</div>';
            if (visit.author_email) {
                content += '<div class="flex items-center text-xs text-gray-600 mb-1">';
                content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
                content += '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>';
                content += '<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>';
                content += '</svg>' + visit.author_email + '</div>';
            }
            if (visit.author_phone) {
                content += '<div class="flex items-center text-xs text-gray-600">';
                content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
                content += '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>';
                content += '</svg>' + visit.author_phone + '</div>';
            }
        } else {
            content += '<div class="text-sm text-gray-500 italic">Informasi author tidak tersedia</div>';
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
        } else if (visit.auditor_name) {
            content += '<div class="text-sm font-medium text-gray-900 mb-1">' + visit.auditor_name + '</div>';
            if (visit.auditor_email) {
                content += '<div class="flex items-center text-xs text-gray-600 mb-1">';
                content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
                content += '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>';
                content += '<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>';
                content += '</svg>' + visit.auditor_email + '</div>';
            }
            if (visit.auditor_phone) {
                content += '<div class="flex items-center text-xs text-gray-600">';
                content += '<svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">';
                content += '<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>';
                content += '</svg>' + visit.auditor_phone + '</div>';
            }
        } else {
            content += '<div class="text-sm text-gray-500 italic">Informasi auditor tidak tersedia</div>';
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

    // Function to initialize Leaflet maps
    function initializePendingMaps() {
        if (!window.pendingMaps) return;
        
        window.pendingMaps.forEach(function(mapConfig) {
            const mapElement = document.getElementById(mapConfig.id);
            if (mapElement && !mapElement._leaflet_id) {
                const map = L.map(mapConfig.id, {
                    center: [mapConfig.lat, mapConfig.lng],
                    zoom: 15,
                    zoomControl: false,
                    scrollWheelZoom: false,
                    dragging: false,
                    touchZoom: false,
                    doubleClickZoom: false
                });
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                L.marker([mapConfig.lat, mapConfig.lng])
                    .addTo(map)
                    .bindPopup('<div class="text-sm font-semibold">' + mapConfig.title + '</div>')
                    .openPopup();
            }
        });
        
        // Clear pending maps after initialization
        window.pendingMaps = [];
    }

    // Function to close modal
    function closeModal() {
        const modal = document.getElementById('visitModal');
        modal.querySelector('.max-w-5xl').classList.remove('scale-100');
        modal.querySelector('.max-w-5xl').classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    // Close modal when clicking outside
    document.getElementById('visitModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>

<style>
    /* Custom popup styling */
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        padding: 0;
        font-family: 'Inter', sans-serif;
    }
    
    .custom-popup .leaflet-popup-tip {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        background: white;
    }
    
    .custom-popup .leaflet-popup-close-button {
        color: #6b7280 !important;
        font-size: 16px;
        padding: 8px;
        top: 8px;
        right: 8px;
        width: auto;
        height: auto;
    }
    
    .custom-popup .leaflet-popup-close-button:hover {
        background-color: #f3f4f6 !important;
        color: #374151 !important;
        border-radius: 4px;
    }
    
    /* Modal Enhancement */
    #visitModal {
        backdrop-filter: blur(4px);
    }
    
    #visitModal .max-w-5xl {
        transition: transform 0.3s ease-out;
    }
    
    /* Map Container Enhancement */
    #map {
        border-radius: 0 0 8px 8px;
    }
    
    /* Inline Map Styling */
    .leaflet-container {
        border-radius: 6px;
    }
    
    /* Photo hover effects */
    .documentation-photo {
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .documentation-photo:hover {
        transform: scale(1.05);
    }
    
    /* Modal content scrollbar styling */
    .max-h-96::-webkit-scrollbar {
        width: 6px;
    }
    
    .max-h-96::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .max-h-96::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .max-h-96::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Prevent modal overflow */
    body.modal-open {
        overflow: hidden;
    }
    
    /* Responsive modal */
    @media (max-width: 768px) {
        #visitModal .max-w-5xl {
            max-width: calc(100vw - 1rem);
            margin: 0.5rem;
        }
        
        #visitModal .grid-cols-1.lg\\:grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
    
    /* Custom numbered icon styling */
    .custom-numbered-icon {
        background: transparent !important;
        border: none !important;
    }
    
    .custom-numbered-icon > div {
        transition: all 0.3s ease-in-out;
        cursor: pointer;
    }
    
    .custom-numbered-icon:hover > div > div:first-child {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3), 0 0 0 4px rgba(255,255,255,0.9) !important;
    }
    
    /* Route line styling */
    .route-line {
        transition: all 0.2s ease-in-out;
    }
    
    .route-line:hover {
        opacity: 1 !important;
        weight: 5;
    }
    
    /* Route popup styling */
    .route-popup .leaflet-popup-content-wrapper {
        border-radius: 8px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 2px solid #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }
    
    .route-popup .leaflet-popup-tip {
        background: #f8fafc;
        border: 1px solid #6366f1;
    }
    
    /* Custom div icon styling */
    .custom-div-icon {
        background: transparent !important;
        border: none !important;
    }
    
    .custom-div-icon > div {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    
    .custom-div-icon:hover > div > div:first-child {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3), 0 0 0 3px rgba(255,255,255,0.9) !important;
    }
    
    /* Custom marker styling */
    .custom-marker {
        border-radius: 50% !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3), 0 0 0 3px rgba(255,255,255,0.8) !important;
        transition: all 0.2s ease !important;
    }
    
    .custom-marker:hover {
        transform: scale(1.2) !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4), 0 0 0 4px rgba(255,255,255,0.9) !important;
    }
    
    /* Standard leaflet marker styling */
    .leaflet-marker-icon {
        border-radius: 50% !important;
        border: 3px solid white !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
    }
    
    /* Remove any black hover effects on map elements */
    .leaflet-control-layers-toggle:hover,
    .leaflet-control-zoom-in:hover,
    .leaflet-control-zoom-out:hover {
        background-color: #f9fafb !important;
        color: #374151 !important;
    }
    
    /* Ensure no black text on any hover states */
    * :hover {
        color: inherit !important;
    }
    
    button:hover {
        color: white !important;
    }
    
    /* No data popup styling */
    .no-data-popup .leaflet-popup-content-wrapper {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border: 2px solid #d1d5db;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .no-data-popup .leaflet-popup-tip {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
    }
</style>

<script>
    // Initialize feather icons
    feather.replace();
</script>

</body>
</html>