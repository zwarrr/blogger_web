<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kunjungan - Auditor Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if($visit->latitude && $visit->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    @endif
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('auditor.sidebar')
    <div class="ml-64 min-h-screen">
        <main class="flex-1">
            <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i data-feather="clipboard" class="w-6 h-6 mr-3 text-orange-600"></i>
                        Detail Kunjungan <span class="ml-3 px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">{{ $visit->visit_id }}</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Informasi lengkap kunjungan auditor</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($visit->status === 'belum_dikunjungi' || $visit->status === 'dalam_perjalanan')
                        <a href="{{ route('auditor.visits.create-report', $visit) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                            <i data-feather="edit-3" class="w-4 h-4 mr-2"></i> Buat Laporan
                        </a>
                    @endif
                    <a href="{{ route('auditor.visits.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kunjungan</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">ID Kunjungan</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $visit->visit_id ?: 'VST' . str_pad($visit->id, 4, '0', STR_PAD_LEFT) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                                    <dd>
                                        @php
                                            $statusClasses = [
                                                'belum_dikunjungi' => 'bg-yellow-100 text-yellow-800',
                                                'dalam_perjalanan' => 'bg-blue-100 text-blue-800',
                                                'selesai' => 'bg-green-100 text-green-800',
                                            ];
                                            $statusLabels = [
                                                'belum_dikunjungi' => 'Belum Dikunjungi',
                                                'dalam_perjalanan' => 'Dalam Perjalanan',
                                                'selesai' => 'Selesai',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$visit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$visit->status] ?? ucfirst($visit->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Author</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->author->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Auditor</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->auditor->name ?? auth()->user()->name }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Tujuan Kunjungan</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->tujuan }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Alamat Lokasi</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->alamat_lokasi ?? 'Alamat belum diisi' }}</dd>
                                </div>
                                @if($visit->latitude && $visit->longitude)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Latitude</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->latitude }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Longitude</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->longitude }}</dd>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Kunjungan</dt>
                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($visit->visit_date)->format('d F Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Jam Berangkat</dt>
                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($visit->visit_date)->format('H:i') }} WIB</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Dibuat</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->created_at->format('d M Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Diperbarui</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->updated_at->format('d M Y H:i') }}</dd>
                                </div>
                                @if($visit->catatan)
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 mb-1">Catatan</dt>
                                    <dd class="text-sm text-gray-900">{{ $visit->catatan }}</dd>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($visit->photos && count($visit->photos) > 0)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold mb-4">Foto Kunjungan</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($visit->photos as $photo)
                                    <div class="relative"><img src="{{ asset('storage/' . $photo) }}" class="w-full h-24 object-cover rounded-lg"></div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                                @if($visit->status === 'belum_dikunjungi' || $visit->status === 'dalam_perjalanan')
                                    <a href="{{ route('auditor.visits.create-report', $visit) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors mb-3">
                                        <i data-feather="edit-3" class="w-4 h-4 mr-2"></i>
                                        Buat Laporan
                                    </a>
                                @endif

                                @if($visit->status === 'belum_dikunjungi')
                                    <form action="{{ route('auditor.visits.update', $visit) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="dalam_perjalanan">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <i data-feather="navigation" class="w-4 h-4 mr-2"></i>
                                            Mulai Kunjungan
                                        </button>
                                    </form>
                                @endif

                                @if($visit->status === 'dalam_perjalanan')
                                    <form action="{{ route('auditor.visits.update', $visit) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                                            Selesaikan Kunjungan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Map -->
                        @if($visit->latitude && $visit->longitude)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Lokasi Kunjungan</h3>
                            <div id="map" style="height: 300px; width: 100%; border-radius: 0.5rem;"></div>
                            <div class="mt-3 text-sm text-gray-600">
                                <p><strong>Koordinat:</strong></p>
                                <p>Lat: {{ $visit->latitude }}, Lng: {{ $visit->longitude }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Visit Timeline -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Kunjungan</h3>
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center ring-8 ring-white">
                                                        <i data-feather="plus" class="w-4 h-4 text-white"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Kunjungan Dibuat</p>
                                                        <p class="text-xs text-gray-500">{{ $visit->created_at->format('d M Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    @if($visit->status !== 'belum_dikunjungi')
                                    <li>
                                        <div class="relative pb-8">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                        <i data-feather="navigation" class="w-4 h-4 text-white"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Dalam Perjalanan</p>
                                                        <p class="text-xs text-gray-500">{{ $visit->updated_at->format('d M Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif

                                    @if($visit->status === 'selesai')
                                    <li>
                                        <div class="relative">
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <i data-feather="check-circle" class="w-4 h-4 text-white"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Kunjungan Selesai</p>
                                                        <p class="text-xs text-gray-500">{{ $visit->updated_at->format('d M Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        feather.replace();

        @if($visit->latitude && $visit->longitude)
        // Initialize map
        const map = L.map('map').setView([{{ $visit->latitude }}, {{ $visit->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        L.marker([{{ $visit->latitude }}, {{ $visit->longitude }}])
            .addTo(map)
            .bindPopup('{{ $visit->alamat_lokasi ?? "Lokasi Kunjungan" }}');
        @endif
    </script>
</body>
</html>
