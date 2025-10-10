<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kunjungan - Admin Panel</title>
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
            border-color: #ea580c !important;
        }

        /* Map Styles */
        #map {
            height: 280px;
            width: 100%;
            border-radius: 8px;
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
        
        /* Override Leaflet default styles */
        .leaflet-popup-close-button {
            color: #6b7280 !important;
            font-size: 18px !important;
            padding: 4px 6px !important;
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
                        Edit Kunjungan
                        <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-sm rounded-full font-medium">
                            {{ $visit->visit_id ?: 'VST' . str_pad($visit->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Perbarui informasi kunjungan yang sudah ada</p>
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

            <!-- Form Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 form-section">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        Form Edit Kunjungan
                        <span class="ml-3 px-3 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">
                            Edit Mode
                        </span>
                    </h2>
                    <p class="mt-2 text-gray-600 text-sm">Perbarui informasi kunjungan dengan data yang akurat</p>
                </div>
                    <form method="POST" action="{{ route('admin.visits.update', $visit) }}" enctype="multipart/form-data" x-data="visitForm()">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Visit ID Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ID Kunjungan
                                    </label>
                                    <input type="text" value="{{ $visit->visit_id ?: 'VST' . str_pad($visit->id, 4, '0', STR_PAD_LEFT) }}" readonly
                                           class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-600 font-medium">
                                </div>

                                <!-- Author Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Author <span class="text-red-500">*</span>
                                    </label>
                                    <select name="author_id" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                        <option value="">Pilih Author</option>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" 
                                                    {{ old('author_id', $visit->author_id) == $author->id ? 'selected' : '' }}>
                                                {{ $author->name }} - {{ $author->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @if($visit->author)
                                        <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium text-blue-900">{{ $visit->author->name }}</div>
                                                    <div class="text-blue-700">{{ $visit->author->email }}</div>
                                                    @if($visit->author->phone)
                                                        <div class="text-blue-600">{{ $visit->author->phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Auditor Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Auditor <span class="text-red-500">*</span>
                                    </label>
                                    <select name="auditor_id" required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                        <option value="">Pilih Auditor</option>
                                        @foreach($auditors as $auditor)
                                            <option value="{{ $auditor->id }}" 
                                                    {{ old('auditor_id', $visit->auditor_id) == $auditor->id ? 'selected' : '' }}>
                                                {{ $auditor->name }} - {{ $auditor->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @if($visit->auditor)
                                        <div class="mt-2 p-3 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium text-green-900">{{ $visit->auditor->name }}</div>
                                                    <div class="text-green-700">{{ $visit->auditor->email }}</div>
                                                    @if($visit->auditor->phone)
                                                        <div class="text-green-600">{{ $visit->auditor->phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Visit Date Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal & Waktu Kunjungan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="visit_date" value="{{ old('visit_date', $visit->visit_date ? $visit->visit_date->format('Y-m-d\TH:i') : '') }}" required
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field">
                                </div>

                                <!-- Status Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Status Kunjungan
                                        <span class="text-gray-500 text-xs ml-2">(Otomatis)</span>
                                    </label>
                                @php
                                    $statusTexts = [
                                        'pending' => 'Menunggu Konfirmasi',
                                        'confirmed' => 'Dikonfirmasi',
                                        'in_progress' => 'Sedang Berlangsung',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                                        'confirmed' => 'bg-blue-50 text-blue-800 border-blue-200',
                                        'in_progress' => 'bg-purple-50 text-purple-800 border-purple-200',
                                        'completed' => 'bg-green-50 text-green-800 border-green-200',
                                        'cancelled' => 'bg-red-50 text-red-800 border-red-200',
                                    ];
                                @endphp
                                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClasses[$visit->status] ?? 'bg-gray-50 text-gray-800 border-gray-200' }}">
                                                {{ $statusTexts[$visit->status] ?? ucfirst(str_replace('_', ' ', $visit->status)) }}
                                            </span>
                                            <span class="text-xs text-gray-500">Otomatis</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">Status dikelola otomatis oleh sistem workflow</p>
                                        <input type="hidden" name="status" value="{{ $visit->status }}">
                                    </div>
                                </div>

                                <!-- Notes Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan Tambahan
                                    </label>
                                    <textarea name="notes" rows="4" 
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field resize-none"
                                              placeholder="Tambahkan catatan atau informasi penting untuk kunjungan ini...">{{ old('notes', $visit->notes) }}</textarea>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Location Field -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Alamat Lokasi
                                        <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <textarea name="location_address" rows="3" required
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 input-field resize-none"
                                              placeholder="Masukkan alamat lengkap lokasi kunjungan">{{ old('location_address', $visit->location_address) }}</textarea>
                                </div>

                                <!-- Maps Section -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Lokasi pada Peta
                                    </label>
                                    <div id="map" class="border border-gray-300 shadow-sm rounded-lg"></div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Klik dan seret penanda untuk menyesuaikan lokasi
                                    </p>
                                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $visit->latitude) }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $visit->longitude) }}">
                                    <div class="mt-2 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                                        <span class="font-medium text-gray-700">Koordinat:</span> 
                                        <span id="coordinates-display">{{ $visit->latitude ?? '-6.2088' }}, {{ $visit->longitude ?? '106.8456' }}</span>
                                    </div>
                                </div>



                                <!-- Existing Photos Section -->
                                @if($visit->photos && count($visit->photos) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Foto Saat Ini
                                    </label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach($visit->photos as $index => $photo)
                                                <div class="relative">
                                                    <img src="{{ asset('storage/' . $photo) }}" alt="Foto {{ $index + 1 }}" 
                                                         class="w-full h-20 object-cover rounded-lg border border-gray-200">
                                                    <button type="button" onclick="removePhoto({{ $visit->id }}, {{ $index }})" 
                                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow transition-colors duration-200">
                                                        ×
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif


                            </div>
                        </div>

                        <!-- Report Information Section (if exists) -->
                        @if($visit->report_notes || $visit->auditor_notes || $visit->started_at || $visit->completed_at || $visit->selfie_photo || $visit->photos)
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                                Laporan Kunjungan
                                <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                    Tersedia
                                </span>
                            </h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Report Details -->
                                <div class="space-y-4">
                                    @if($visit->report_notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Laporan</label>
                                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="text-sm text-gray-900">{{ $visit->report_notes }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($visit->auditor_notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Auditor</label>
                                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="text-sm text-gray-900">{{ $visit->auditor_notes }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($visit->started_at || $visit->completed_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Kunjungan</label>
                                        <div class="space-y-2">
                                            @if($visit->started_at)
                                            <div class="flex items-center text-sm">
                                                <span class="font-medium text-gray-600 w-16">Mulai:</span>
                                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($visit->started_at)->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                            @endif
                                            @if($visit->completed_at)
                                            <div class="flex items-center text-sm">
                                                <span class="font-medium text-gray-600 w-16">Selesai:</span>
                                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($visit->completed_at)->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Selfie Photo and Location -->
                                @if($visit->selfie_photo || ($visit->selfie_latitude && $visit->selfie_longitude))
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <h4 class="font-medium text-gray-800 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        Foto Selfie & Lokasi
                                    </h4>
                                    
                                    @if($visit->selfie_photo && $visit->selfie_latitude && $visit->selfie_longitude)
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Selfie Photo -->
                                            <div>
                                                <p class="text-sm font-medium text-gray-700 mb-2">Foto Selfie</p>
                                                <img src="{{ $visit->selfie_photo }}" alt="Foto Selfie" 
                                                     class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-all shadow-sm" 
                                                     onclick="window.open(this.src)" title="Klik untuk memperbesar">
                                            </div>
                                            
                                            <!-- Selfie Location Map -->
                                            <div>
                                                <p class="text-sm font-medium text-gray-700 mb-2">Lokasi Selfie</p>
                                                <div id="selfieMap-{{ $visit->id }}" class="w-full h-32 rounded-lg border border-gray-300 shadow-sm"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 p-2 bg-gray-100 rounded text-sm text-gray-600">
                                            <strong>Koordinat:</strong> {{ number_format($visit->selfie_latitude, 6) }}, {{ number_format($visit->selfie_longitude, 6) }}
                                        </div>
                                    @else
                                        <!-- Only photo or only coordinates -->
                                        @if($visit->selfie_photo)
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Foto Selfie</p>
                                            <img src="{{ $visit->selfie_photo }}" alt="Foto Selfie" 
                                                 class="w-40 h-40 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-90 transition-all shadow-sm" 
                                                 onclick="window.open(this.src)" title="Klik untuk memperbesar">
                                        </div>
                                        @endif
                                        
                                        @if($visit->selfie_latitude && $visit->selfie_longitude)
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Lokasi Selfie</p>
                                            <div id="selfieMap-{{ $visit->id }}" class="w-full h-32 rounded-lg border border-gray-300 shadow-sm"></div>
                                            <div class="mt-2 text-sm text-gray-600">
                                                <strong>Koordinat:</strong> {{ number_format($visit->selfie_latitude, 6) }}, {{ number_format($visit->selfie_longitude, 6) }}
                                            </div>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Documentation Photos -->
                            @if($visit->photos && count($visit->photos) > 0)
                            <div class="mt-6 bg-green-50 p-4 rounded-lg border border-green-200">
                                <h4 class="font-medium text-green-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                    Foto Dokumentasi ({{ count($visit->photos) }} foto)
                                </h4>
                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach($visit->photos as $index => $photo)
                                    <div class="relative">
                                        <img src="{{ $photo }}" alt="Dokumentasi {{ $index + 1 }}" 
                                             class="w-full h-20 object-cover rounded-lg border-2 border-green-300 cursor-pointer hover:opacity-90 transition-all shadow-md" 
                                             onclick="window.open(this.src)" title="Klik untuk memperbesar">
                                        <div class="absolute top-1 right-1 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">{{ $index + 1 }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    <span class="font-medium">Last updated:</span> {{ $visit->updated_at->format('d M Y, H:i') }}
                                </div>
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('admin.visits.index') }}" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                        Batal
                                    </a>
                                    <button type="submit" 
                                            class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    let map;
    let marker;

    // Alpine.js data function  
    function visitForm() {
        return {
            loading: false,
            latitude: '{{ $visit->latitude ?? -6.2088 }}',
            longitude: '{{ $visit->longitude ?? 106.8456 }}',
            init() {
                // Initialize map when component is ready
                this.$nextTick(() => {
                    setTimeout(() => {
                        initMap();
                    }, 100);
                });
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map with existing visit coordinates
        initMap();

        function removePhoto(visitId, photoIndex) {
            if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
                // Add your remove photo logic here
                console.log('Remove photo', visitId, photoIndex);
            }
        }

        // Make functions globally available
        window.removePhoto = removePhoto;
    });

    function initMap() {
        // Get existing coordinates from the visit data
        const defaultLat = {{ $visit->latitude ?? '-6.2088' }};
        const defaultLng = {{ $visit->longitude ?? '106.8456' }};

        if (map) {
            map.remove();
        }

        // Enable interactions for edit mode - allow adjusting location
        map = L.map('map', {
            dragging: true,
            touchZoom: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            boxZoom: true,
            keyboard: true,
            zoomControl: true
        }).setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create custom orange icon - minimalist
        const orangeIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div class='orange-marker'></div>",
            iconSize: [24, 32],
            iconAnchor: [12, 24],
            popupAnchor: [0, -24]
        });

        // Add draggable marker for edit mode
        marker = L.marker([defaultLat, defaultLng], { 
            icon: orangeIcon,
            draggable: true
        }).addTo(map);
        
        // Set initial popup text - minimalist style
        updateMarkerPopup(defaultLat, defaultLng);
        
        // Update coordinates when marker is dragged
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            const lat = position.lat.toFixed(6);
            const lng = position.lng.toFixed(6);
            
            // Update hidden inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update coordinates display
            document.getElementById('coordinates-display').textContent = `${lat}, ${lng}`;
            
            // Update popup
            updateMarkerPopup(lat, lng);
        });

        // Allow clicking on map to move marker
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            marker.setLatLng(e.latlng);
            
            // Update hidden inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update coordinates display
            document.getElementById('coordinates-display').textContent = `${lat}, ${lng}`;
            
            // Update popup
            updateMarkerPopup(lat, lng);
        });

        // Invalidate size to fix display issues
        setTimeout(function() {
            map.invalidateSize();
        }, 100);
    }

    function updateMarkerPopup(lat, lng) {
        const popupText = '<div style="color: #374151; font-weight: 500; font-size: 13px;">Lokasi Kunjungan</div><div style="margin-top: 4px; font-size: 11px; color: #6b7280;"><b>Lat:</b> ' + lat + '<br><b>Lng:</b> ' + lng + '</div>';
        marker.bindPopup(popupText);
    }

    // Initialize selfie map if it exists
    @if($visit->selfie_latitude && $visit->selfie_longitude)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const selfieMapElement = document.getElementById('selfieMap-{{ $visit->id }}');
            if (selfieMapElement) {
                const selfieMap = L.map('selfieMap-{{ $visit->id }}', {
                    center: [{{ $visit->selfie_latitude }}, {{ $visit->selfie_longitude }}],
                    zoom: 16,
                    zoomControl: true,
                    scrollWheelZoom: true,
                    dragging: true,
                    touchZoom: true,
                    doubleClickZoom: true
                });
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(selfieMap);
                
                L.marker([{{ $visit->selfie_latitude }}, {{ $visit->selfie_longitude }}])
                    .addTo(selfieMap)
                    .bindPopup('<div style="color: #374151; font-weight: 500; font-size: 13px;">Lokasi Selfie</div><div style="margin-top: 4px; font-size: 11px; color: #6b7280;"><b>Lat:</b> {{ number_format($visit->selfie_latitude, 6) }}<br><b>Lng:</b> {{ number_format($visit->selfie_longitude, 6) }}</div>')
                    .openPopup();
                
                // Invalidate size to fix display issues
                setTimeout(() => {
                    selfieMap.invalidateSize();
                }, 200);
            }
        }, 500);
    });
    @endif
    </script>
</body>
</html>