<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peta Kunjungan - Auditor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .leaflet-popup-content-wrapper { 
            border-radius: 8px; 
        }
        #map { 
            height: 500px; 
            border-radius: 0.75rem;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        @include('auditor.sidebar')

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Peta Kunjungan</h1>
                        <p class="mt-1 text-sm text-gray-600">Visualisasi lokasi kunjungan pada peta</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Session Messages -->
            @if(session('success'))
                <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Map Content -->
            <div class="p-6">
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Lokasi</p>
                                <p class="text-2xl font-bold text-primary-600 mt-2">{{ $visits->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending</p>
                                <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $visits->where('status', 'pending')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Dikonfirmasi</p>
                                <p class="text-2xl font-bold text-primary-600 mt-2">{{ $visits->where('status', 'konfirmasi')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Selesai</p>
                                <p class="text-2xl font-bold text-green-600 mt-2">{{ $visits->where('status', 'selesai')->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Peta Lokasi Kunjungan</h3>
                        <div class="flex space-x-2">
                            <button onclick="centerMap()" class="px-4 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                Reset View
                            </button>
                        </div>
                    </div>
                    
                    @if($visits->count() > 0)
                        <div id="map"></div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">Tidak ada data lokasi</h3>
                            <p class="mt-2 text-sm text-gray-500">Belum ada kunjungan dengan koordinat lokasi.</p>
                        </div>
                    @endif
                </div>

                <!-- Legend -->
                @if($visits->count() > 0)
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Keterangan Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Pending - Menunggu konfirmasi</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-primary-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Konfirmasi - Sedang diproses</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-700">Selesai - Telah diselesaikan</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    @if($visits->count() > 0)
    <script>
        // Initialize map
        var map = L.map('map').setView([-6.2088, 106.8456], 10); // Jakarta center

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Visit data
        var visits = @json($visits);
        var markers = [];

        // Add markers for each visit
        visits.forEach(function(visit) {
            if (visit.latitude && visit.longitude) {
                var color = visit.status === 'pending' ? 'yellow' : 
                           visit.status === 'konfirmasi' ? 'orange' : 'green';
                
                var marker = L.marker([visit.latitude, visit.longitude]).addTo(map);
                
                var popupContent = `
                    <div class="p-2">
                        <h4 class="font-semibold text-gray-900">${visit.visit_id}</h4>
                        <p class="text-sm text-gray-600 mt-1">${visit.author_name || 'N/A'}</p>
                        <p class="text-sm text-gray-600">${visit.location_address || 'N/A'}</p>
                        <p class="text-xs text-gray-500 mt-2">Status: 
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                ${visit.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''}
                                ${visit.status === 'konfirmasi' ? 'bg-primary-100 text-primary-800' : ''}
                                ${visit.status === 'selesai' ? 'bg-green-100 text-green-800' : ''}
                            ">
                                ${visit.status.charAt(0).toUpperCase() + visit.status.slice(1)}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Tanggal: ${new Date(visit.visit_date).toLocaleDateString('id-ID')}
                        </p>
                    </div>
                `;
                
                marker.bindPopup(popupContent);
                markers.push(marker);
            }
        });

        // Fit map to show all markers
        if (markers.length > 0) {
            var group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Center map function
        function centerMap() {
            if (markers.length > 0) {
                var group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                map.setView([-6.2088, 106.8456], 10);
            }
        }
    </script>
    @endif
</body>
</html>
