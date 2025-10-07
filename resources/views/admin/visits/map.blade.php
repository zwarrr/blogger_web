<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased">
    <!-- Sidebar -->
    @include('admin.sidebar')
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Peta Kunjungan Author</h1>
                    <p class="text-gray-600 mt-1">Visualisasi lokasi kunjungan auditor ke author</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.visits.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Kunjungan
                    </a>
                    <a href="{{ route('admin.visits.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Daftar Kunjungan
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Lokasi di Peta</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_with_coordinates'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Menunggu</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Dikonfirmasi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['konfirmasi'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Selesai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['selesai'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Legend -->
            <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-semibold text-gray-900">Filter Peta</h3>
                        <form method="GET" class="flex items-center gap-3">
                            <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="konfirmasi" {{ request('status') === 'konfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </form>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Menunggu</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Dikonfirmasi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="bg-gradient-to-r from-white to-green-50 rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div id="map" class="w-full h-96"></div>
            </div>

            <!-- Visit Details Modal -->
            <div id="visitModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold">Detail Kunjungan</h3>
                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                            &times;
                        </button>
                    </div>
                    <div id="modalContent">
                        <!-- Content will be filled by JavaScript -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Map initialization
        const map = L.map('map').setView([-6.2088, 106.8456], 10); // Default to Jakarta

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Visit data from backend
        const visitsData = @json($mapData);

        // Color mapping for status
        const statusColors = {
            'pending': '#eab308',    // yellow-500
            'konfirmasi': '#3b82f6', // blue-500  
            'selesai': '#22c55e'     // green-500
        };

        // Add markers to map
        const markers = [];
        visitsData.forEach(visit => {
            const color = statusColors[visit.status] || '#6b7280';
            
            const marker = L.circleMarker([visit.latitude, visit.longitude], {
                radius: 8,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            // Add popup with basic info
            marker.bindPopup(`
                <div class="text-sm">
                    <h4 class="font-semibold">${visit.visit_id}</h4>
                    <p><strong>Author:</strong> ${visit.author_name}</p>
                    <p><strong>Auditor:</strong> ${visit.auditor_name}</p>
                    <p><strong>Status:</strong> <span class="inline-flex px-2 py-1 text-xs rounded-full" style="background-color: ${color}20; color: ${color}">${visit.status_label.text}</span></p>
                    <button onclick="showVisitDetails(${visit.id})" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                        Lihat Detail
                    </button>
                </div>
            `);

            markers.push({marker, visit});
        });

        // Fit map to show all markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers.map(m => m.marker));
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Show visit details in modal
        function showVisitDetails(visitId) {
            const visit = visitsData.find(v => v.id === visitId);
            if (!visit) return;

            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID Kunjungan</label>
                            <p class="text-sm text-gray-900">${visit.visit_id}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background-color: ${statusColors[visit.status]}20; color: ${statusColors[visit.status]}">${visit.status_label.text}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Author</label>
                            <p class="text-sm text-gray-900">${visit.author_name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Auditor</label>
                            <p class="text-sm text-gray-900">${visit.auditor_name}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                            <p class="text-sm text-gray-900">${visit.visit_date}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat Lokasi</label>
                            <p class="text-sm text-gray-900">${visit.location_address}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Koordinat</label>
                            <p class="text-sm text-gray-900">${visit.latitude}, ${visit.longitude}</p>
                        </div>
                        ${visit.notes ? `
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                <p class="text-sm text-gray-900">${visit.notes}</p>
                            </div>
                        ` : ''}
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <a href="/admin/visits/${visit.id}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Lihat Detail Lengkap
                        </a>
                        <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('visitModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('visitModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('visitModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>