<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Kunjungan</h1>
                    <p class="text-gray-600 mt-1">{{ $visit->visit_id }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.visits.show', $visit) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                    <a href="{{ route('admin.visits.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8">
            <!-- Flash Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-8 hover:shadow-xl transition-all duration-300">
                <form method="POST" action="{{ route('admin.visits.update', $visit) }}" enctype="multipart/form-data" x-data="visitForm()">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    ID Kunjungan
                                </label>
                                <input type="text" value="{{ $visit->visit_id }}" readonly
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 text-gray-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Author <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="author_name" value="{{ old('author_name', $visit->author_name) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Masukkan nama author">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Auditor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="auditor_name" value="{{ old('auditor_name', $visit->auditor_name) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       placeholder="Masukkan nama auditor">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal & Waktu Kunjungan <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="visit_date" value="{{ old('visit_date', $visit->visit_date->format('Y-m-d\TH:i')) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status (Otomatis)
                                </label>
                                @php
                                    $statusTexts = [
                                        'pending' => 'Menunggu Konfirmasi',
                                        'confirmed' => 'Proses',
                                        'in_progress' => 'Sedang Proses',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$visit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusTexts[$visit->status] ?? ucfirst(str_replace('_', ' ', $visit->status)) }}
                                    </span>
                                    <span class="text-sm text-gray-500 ml-2">Status dikelola otomatis oleh sistem workflow</span>
                                </div>
                                <input type="hidden" name="status" value="{{ $visit->status }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan
                                </label>
                                <textarea name="notes" rows="4" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Catatan tambahan untuk kunjungan ini...">{{ old('notes', $visit->notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lokasi <span class="text-red-500">*</span>
                                </label>
                                <textarea name="location_address" rows="3" required
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Masukkan alamat lengkap lokasi kunjungan">{{ old('location_address', $visit->location_address) }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Latitude
                                    </label>
                                    <input type="number" name="latitude" value="{{ old('latitude', $visit->latitude) }}" step="any"
                                           x-model="latitude"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="-6.175">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Longitude
                                    </label>
                                    <input type="number" name="longitude" value="{{ old('longitude', $visit->longitude) }}" step="any"
                                           x-model="longitude"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                           placeholder="106.827">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Dapatkan Koordinat
                                </label>
                                <button type="button" @click="getCurrentLocation()" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lokasi Saat Ini
                                </button>
                                <p class="text-xs text-gray-500 mt-1">Klik untuk mengisi koordinat otomatis berdasarkan lokasi browser</p>
                            </div>

                            <!-- Existing Photos -->
                            @if($visit->photos && count($visit->photos) > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Foto Saat Ini
                                    </label>
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        @foreach($visit->photos as $index => $photo)
                                            <div class="relative">
                                                <img src="{{ asset('storage/' . $photo) }}" alt="Foto {{ $index + 1 }}" class="w-full h-20 object-cover rounded-lg">
                                                <button type="button" onclick="removePhoto({{ $visit->id }}, {{ $index }})" 
                                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                                    ×
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tambah Foto Baru
                                </label>
                                <input type="file" name="photos[]" multiple accept="image/*"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <p class="text-xs text-gray-500 mt-1">Pilih foto baru untuk ditambahkan (maksimal 2MB per foto, format: JPEG, PNG, JPG)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.visits.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function visitForm() {
            return {
                latitude: '{{ old('latitude', $visit->latitude) }}',
                longitude: '{{ old('longitude', $visit->longitude) }}',
                
                getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude.toFixed(8);
                                this.longitude = position.coords.longitude.toFixed(8);
                                
                                // Update the input fields
                                document.querySelector('input[name="latitude"]').value = this.latitude;
                                document.querySelector('input[name="longitude"]').value = this.longitude;
                                
                                alert('Koordinat berhasil diambil!');
                            },
                            (error) => {
                                console.error('Error getting location:', error);
                                alert('Gagal mendapatkan lokasi. Pastikan browser mengizinkan akses lokasi.');
                            }
                        );
                    } else {
                        alert('Geolocation tidak didukung oleh browser ini.');
                    }
                }
            }
        }

        function removePhoto(visitId, photoIndex) {
            if (confirm('Yakin ingin menghapus foto ini?')) {
                fetch(`/admin/visits/${visitId}/photo/${photoIndex}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus foto');
                });
            }
        }
    </script>
</body>
</html>