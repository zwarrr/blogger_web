<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan - {{ $visit->visit_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .camera-container { position: relative; }
        .camera-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); color: white; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased">
    @include('auditor.sidebar')
    
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laporan Kunjungan</h1>
                    <p class="text-gray-600 mt-1">{{ $visit->visit_id }} - {{ $visit->author_name }}</p>
                </div>
                <a href="{{ route('auditor.visits.index') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </header>

        <main class="p-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Visit Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kunjungan</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">ID Kunjungan</span>
                                <p class="text-gray-900">{{ $visit->visit_id }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Author</span>
                                <p class="text-gray-900">{{ $visit->author_name }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Alamat</span>
                                <p class="text-gray-900">{{ $visit->location_address ?: 'Alamat akan diisi saat kunjungan' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Jadwal</span>
                                <p class="text-gray-900">{{ $visit->formatted_visit_date }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Tujuan</span>
                                <p class="text-gray-900">{{ $visit->visit_purpose }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Status</span>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $visit->status_label['color'] }}">
                                    {{ $visit->status_label['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Form Laporan Kunjungan</h3>
                        
                        <form action="{{ route('auditor.visits.submit-report', $visit->id) }}" method="POST" enctype="multipart/form-data" x-data="reportForm()">
                            @csrf
                            
                            <!-- Report Notes -->
                            <div class="mb-6">
                                <label for="report_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Laporan Hasil Kunjungan
                                    </span>
                                </label>
                                <textarea id="report_notes" name="report_notes" rows="8" required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                          placeholder="Tulis laporan lengkap hasil kunjungan Anda...">{{ old('report_notes') }}</textarea>
                                @error('report_notes')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location Capture -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Latitude
                                        </span>
                                    </label>
                                    <input type="number" id="latitude" name="latitude" x-model="latitude" required step="any"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Latitude saat kunjungan" readonly>
                                    @error('latitude')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Longitude
                                        </span>
                                    </label>
                                    <input type="number" id="longitude" name="longitude" x-model="longitude" required step="any"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           placeholder="Longitude saat kunjungan" readonly>
                                    @error('longitude')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Get Location Button -->
                            <div class="mb-6">
                                <button type="button" @click="getCurrentLocation()" 
                                        class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Ambil Lokasi Saat Ini
                                </button>
                                <p class="text-xs text-gray-500 mt-1">Klik untuk mendapatkan koordinat lokasi Anda saat kunjungan</p>
                            </div>

                            <!-- Selfie Photo -->
                            <div class="mb-6">
                                <label for="selfie_photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Foto Selfie Sebagai Bukti Kunjungan
                                    </span>
                                </label>
                                <input type="file" id="selfie_photo" name="selfie_photo" accept="image/*" capture="user" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <p class="text-xs text-gray-500 mt-1">Ambil foto selfie Anda di lokasi kunjungan sebagai bukti kehadiran</p>
                                @error('selfie_photo')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Photos -->
                            <div class="mb-8">
                                <label for="photos" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Foto Tambahan (Opsional)
                                    </span>
                                </label>
                                <input type="file" id="photos" name="photos[]" accept="image/*" multiple
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <p class="text-xs text-gray-500 mt-1">Foto tambahan yang mendukung laporan kunjungan (maksimal 2MB per foto)</p>
                                @error('photos.*')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-4">
                                <a href="{{ route('auditor.visits.show', $visit->id) }}" 
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold transition-all duration-200 hover:bg-gray-50">
                                    Batal
                                </a>
                                <button type="submit" 
                                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function reportForm() {
            return {
                latitude: '',
                longitude: '',
                
                getCurrentLocation() {
                    if (navigator.geolocation) {
                        const button = event.target;
                        const originalText = button.innerHTML;
                        
                        button.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengambil Lokasi...
                        `;
                        button.disabled = true;
                        
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude.toFixed(8);
                                this.longitude = position.coords.longitude.toFixed(8);
                                
                                // Show success message
                                const successMsg = document.createElement('div');
                                successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                                successMsg.innerHTML = `
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Lokasi berhasil diambil!
                                    </div>
                                `;
                                document.body.appendChild(successMsg);
                                setTimeout(() => successMsg.remove(), 3000);
                                
                                button.innerHTML = originalText;
                                button.disabled = false;
                            },
                            (error) => {
                                console.error('Error getting location:', error);
                                
                                let errorMessage = 'Gagal mendapatkan lokasi. ';
                                switch(error.code) {
                                    case error.PERMISSION_DENIED:
                                        errorMessage += 'Izin akses lokasi ditolak.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        errorMessage += 'Informasi lokasi tidak tersedia.';
                                        break;
                                    case error.TIMEOUT:
                                        errorMessage += 'Timeout mendapatkan lokasi.';
                                        break;
                                    default:
                                        errorMessage += 'Terjadi kesalahan yang tidak diketahui.';
                                        break;
                                }
                                
                                const errorMsg = document.createElement('div');
                                errorMsg.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                                errorMsg.innerHTML = `
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        ${errorMessage}
                                    </div>
                                `;
                                document.body.appendChild(errorMsg);
                                setTimeout(() => errorMsg.remove(), 5000);
                                
                                button.innerHTML = originalText;
                                button.disabled = false;
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        alert('Geolocation tidak didukung oleh browser ini.');
                    }
                }
            }
        }
    </script>
</body>
</html>