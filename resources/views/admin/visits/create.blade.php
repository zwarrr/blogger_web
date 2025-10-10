<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        .form-section { transition: all 0.3s ease; }
        .form-section:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1),0 10px 10px -5px rgba(0,0,0,0.04); }
        .input-field { transition: all 0.2s ease; }
        .input-field:focus { transform: scale(1.01); }
        body { font-family: 'Inter', sans-serif; }
        #map { height: 400px; width: 100%; border-radius: 0.5rem; }
        
        /* Remove black outline on all inputs */
        input:focus,
        select:focus,
        textarea:focus,
        button:focus {
            outline: none !important;
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1) !important;
        }
        
        input:hover,
        select:hover,
        textarea:hover {
            border-color: #fb923c !important;
        }
        
        /* Custom Orange Marker - Minimalist */
        .orange-marker {
            background-color: #ea580c;
            width: 24px;
            height: 24px;
            display: block;
            position: relative;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }
        
        .orange-marker::after {
            content: '';
            width: 8px;
            height: 8px;
            margin: 6px 0 0 6px;
            background: #fff;
            position: absolute;
            border-radius: 50%;
        }
        
        .custom-div-icon {
            background: transparent !important;
            border: none !important;
        }
        
        /* Minimalist Popup Styling */
        .leaflet-popup-content-wrapper {
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .leaflet-popup-tip {
            background-color: #fff;
            border-left: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .leaflet-popup-content {
            margin: 12px 14px;
            font-size: 13px;
        }
        
        /* Override Leaflet popup colors - Minimalist */
        .leaflet-popup-content-wrapper {
            background-color: #fff;
            border: 1px solid #e5e7eb;
        }
        
        .leaflet-popup-tip {
            background-color: #fff;
            border-left: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Remove all black hover effects */
        a:hover {
            color: inherit !important;
        }
        
        .leaflet-container a {
            color: #ea580c !important;
        }
        
        .leaflet-container a:hover {
            color: #c2410c !important;
        }
        
        /* Remove black borders and shadows on active elements */
        .leaflet-marker-icon:hover,
        .leaflet-marker-icon:active,
        .leaflet-marker-icon:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('admin.sidebar')
    <div class="ml-64 min-h-screen">
    
    <!-- Page Transition Loading -->
    <div id="pageTransition" class="fixed inset-0 bg-white z-50 flex items-center justify-center transition-opacity duration-500 opacity-0 pointer-events-none">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mb-4"></div>
            <p class="text-gray-600 font-medium">Memuat halaman...</p>
        </div>
    </div>

    <main class="flex-1">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Tambah Kunjungan Baru
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Buat jadwal kunjungan auditor ke author</p>
                </div>
                <a href="{{ route('admin.visits.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-150 shadow-sm">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Flash Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div>
                            <h4 class="text-red-800 font-medium mb-2">Terdapat kesalahan:</h4>
                            <ul class="list-disc list-inside text-red-700 space-y-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form Penugasan Admin -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 form-section" x-data="visitAssignmentForm()">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        Form Penugasan Kunjungan
                        <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">
                            Admin Assignment
                        </span>
                    </h2>
                    <p class="mt-2 text-gray-600 text-sm">Lengkapi informasi di bawah untuk membuat jadwal kunjungan baru</p>
                </div>

                <form action="{{ route('admin.visits.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Auditor Selection -->
                            <div>
                                <label for="auditor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Auditor yang Ditugaskan <span class="text-red-500">*</span>
                                </label>
                                <select id="auditor_id" name="auditor_id" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                    <option value="">-- Pilih Auditor --</option>
                                    @if(isset($auditors) && $auditors->count() > 0)
                                        @foreach($auditors as $auditor)
                                            <option value="{{ $auditor->id }}">{{ $auditor->name }} ({{ $auditor->email }})</option>
                                        @endforeach
                                    @else
                                        <option disabled>-- Tidak ada auditor tersedia --</option>
                                    @endif
                                </select>
                                @error('auditor_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Author Selection -->
                            <div class="form-group">
                                <label for="author_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Author <span class="text-red-500">*</span>
                                </label>
                                <select id="author_id" name="author_id" x-model="selectedAuthor" @change="updateAuthorData()" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                    <option value="">-- Pilih Author --</option>
                                    @if(isset($allAuthors) && $allAuthors->count() > 0)
                                        @foreach($allAuthors as $author)
                                            <option value="{{ $author->id }}" 
                                                    data-name="{{ $author->name }}"
                                                    data-email="{{ $author->email }}" 
                                                    data-address="{{ $author->address ?? '' }}"
                                                    data-phone="{{ $author->phone ?? '' }}"
                                                    data-lat="{{ $author->latitude ?? '' }}"
                                                    data-lng="{{ $author->longitude ?? '' }}">
                                                {{ $author->name }} ({{ $author->email }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled>-- Tidak ada author tersedia --</option>
                                    @endif
                                </select>
                                @error('author_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat/Lokasi Author (Auto-fill) -->
                            <div x-show="selectedAuthor" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat/Lokasi Author
                                </label>
                                <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-700 text-sm">
                                    <span x-text="authorData.address || 'Alamat akan diverifikasi oleh auditor'"></span>
                                </div>
                                <div x-show="authorData.phone" class="text-sm text-gray-500 mt-1">
                                    <span class="font-medium">No. HP:</span> <span x-text="authorData.phone"></span>
                                </div>
                            </div>

                            <!-- Maps Leaflet -->
                            <div x-show="selectedAuthor" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Lokasi pada Peta
                                </label>
                                <div id="map" class="border border-gray-300 shadow-sm"></div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Penanda lokasi berdasarkan alamat author
                                </p>
                                <input type="hidden" id="latitude" name="latitude" x-model="latitude">
                                <input type="hidden" id="longitude" name="longitude" x-model="longitude">
                                <div x-show="latitude && longitude" class="mt-2 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                    <span class="font-medium text-gray-700">Koordinat:</span> 
                                    <span x-text="`${latitude}, ${longitude}`" class="text-gray-600"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Tanggal Kunjungan -->
                            <div>
                                <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Kunjungan <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required
                                       min="{{ date('Y-m-d\TH:i') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                @error('visit_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tujuan Kunjungan -->
                            <div>
                                <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tujuan Kunjungan <span class="text-red-500">*</span>
                                </label>
                                <textarea id="tujuan" name="tujuan" rows="4" required maxlength="1000"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field resize-none"
                                          placeholder="Jelaskan tujuan dan maksud kunjungan secara detail...">{{ old('tujuan') }}</textarea>
                                <div class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</div>
                                @error('tujuan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan Internal Admin -->
                            <div>
                                <label for="catatan_admin" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Internal (Opsional)
                                </label>
                                <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="500"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field resize-none"
                                          placeholder="Catatan internal admin (tidak ditampilkan ke auditor)...">{{ old('catatan_admin') }}</textarea>
                                <div class="text-xs text-gray-500 mt-1">Maksimal 500 karakter - hanya untuk keperluan internal admin</div>
                                @error('catatan_admin')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.visits.index') }}" 
                           class="inline-flex items-center px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-150">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-150 shadow-sm">
                            Buat Penugasan Kunjungan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const transition = document.getElementById('pageTransition');
        if (transition) {
            transition.style.opacity = '1';
            setTimeout(() => { transition.style.opacity = '0'; setTimeout(()=>transition.style.pointerEvents='none',500); }, 300);
        }
        const form = document.querySelector('form');
        if (form) form.addEventListener('submit', function(e) {
            const authorId = document.getElementById('author_id').value;
            const auditorId = document.getElementById('auditor_id').value;
            const visitDate = document.getElementById('visit_date').value;
            const tujuan = document.getElementById('tujuan').value;
            if (!authorId || !auditorId || !visitDate || !tujuan) { e.preventDefault(); alert('Mohon lengkapi semua field yang wajib diisi!'); return false; }
            const selectedDate = new Date(visitDate); const now = new Date(); if (selectedDate <= now) { e.preventDefault(); alert('Tanggal kunjungan harus di masa depan!'); return false; }
        });
    });

    function visitAssignmentForm() {
        return {
            selectedAuthor: '',
            authorData: { name: '', email: '', address: '', phone: '', lat: '', lng: '' },
            latitude: '',
            longitude: '',
            updateAuthorData() {
                const select = document.getElementById('author_id');
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    this.authorData = { 
                        name: selectedOption.dataset.name || '', 
                        email: selectedOption.dataset.email || '', 
                        address: selectedOption.dataset.address || '', 
                        phone: selectedOption.dataset.phone || '',
                        lat: selectedOption.dataset.lat || '',
                        lng: selectedOption.dataset.lng || ''
                    };
                    
                    // Set coordinates from author data
                    if (this.authorData.lat && this.authorData.lng) {
                        this.latitude = this.authorData.lat;
                        this.longitude = this.authorData.lng;
                        document.getElementById('latitude').value = this.authorData.lat;
                        document.getElementById('longitude').value = this.authorData.lng;
                    }
                    
                    // Initialize map when author is selected
                    setTimeout(() => {
                        initMap(this.authorData.lat, this.authorData.lng);
                    }, 100);
                } else {
                    this.authorData = { name: '', email: '', address: '', phone: '', lat: '', lng: '' };
                    this.latitude = '';
                    this.longitude = '';
                }
            }
        }
    }

    let map = null;
    let marker = null;

    function initMap(authorLat, authorLng) {
        if (map) {
            map.remove();
        }

        // Use author coordinates if available, otherwise use default (Jakarta)
        const defaultLat = authorLat && authorLat !== '' ? parseFloat(authorLat) : -6.2088;
        const defaultLng = authorLng && authorLng !== '' ? parseFloat(authorLng) : 106.8456;

        // Disable all interactions - make map read-only
        map = L.map('map', {
            dragging: false,
            touchZoom: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false,
            zoomControl: true
        }).setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create custom orange icon - minimalist
        const orangeIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div class='orange-marker' style='margin-left: 12px; margin-top: 12px;'></div>",
            iconSize: [24, 32],
            iconAnchor: [12, 24],
            popupAnchor: [0, -24]
        });

        // Always add marker automatically (non-draggable)
        marker = L.marker([defaultLat, defaultLng], { 
            icon: orangeIcon,
            draggable: false
        }).addTo(map);
        
        // Set popup text - minimalist style
        const popupText = (authorLat && authorLat !== '' && authorLng && authorLng !== '') 
            ? '<div style="color: #374151; font-weight: 500; font-size: 13px;">Lokasi Author</div><div style="margin-top: 4px; font-size: 11px; color: #6b7280;"><b>Lat:</b> ' + defaultLat.toFixed(6) + '<br><b>Lng:</b> ' + defaultLng.toFixed(6) + '</div>'
            : '<div style="color: #374151; font-weight: 500; font-size: 13px;">Lokasi Default</div><div style="margin-top: 4px; font-size: 11px; color: #6b7280;"><b>Lat:</b> ' + defaultLat.toFixed(6) + '<br><b>Lng:</b> ' + defaultLng.toFixed(6) + '</div>';
        
        marker.bindPopup(popupText);

        // Invalidate size to fix display issues
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    }
    </script>
</body>
</html>