@extends('layouts.admin')

@section('title', 'Buat Laporan Kunjungan - Auditor')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Form Pelaporan Auditor</h1>
                        <p class="text-gray-600 mt-1">Buat laporan hasil kunjungan dengan lengkap dan akurat</p>
                    </div>
                </div>
                
                <!-- Visit Status Badge -->
                <div class="hidden lg:block">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold border bg-blue-100 text-blue-800 border-blue-200">
                        Kunjungan: {{ ucfirst($visit->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Visit Info Card (Read-only) -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informasi Tugas Kunjungan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Author Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                        Nama Author
                    </label>
                    <div class="text-gray-900 font-medium">{{ $visit->author->name }}</div>
                    <div class="text-sm text-gray-600">{{ $visit->author->email }}</div>
                </div>
                
                <!-- Tanggal Kunjungan -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                        Jadwal Kunjungan
                    </label>
                    <div class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($visit->tanggal_kunjungan)->format('d M Y') }}</div>
                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($visit->tanggal_kunjungan)->format('H:i') }} WIB</div>
                </div>
                
                <!-- Alamat/Lokasi -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                        Alamat/Lokasi Author
                    </label>
                    <div class="text-gray-900 font-medium">
                        {{ $visit->author->address ?? 'Alamat tidak tersedia' }}
                    </div>
                    @if($visit->author->phone)
                        <div class="text-sm text-gray-600">{{ $visit->author->phone }}</div>
                    @endif
                </div>
                
                <!-- Tujuan Kunjungan -->
                <div class="bg-gray-50 rounded-lg p-4 md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                        Tujuan Kunjungan
                    </label>
                    <div class="text-gray-900">{{ $visit->tujuan }}</div>
                </div>
            </div>
        </div>

        <!-- Report Form -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <form action="{{ route('auditor.visits.store-report', $visit) }}" method="POST" enctype="multipart/form-data" x-data="visitReportForm()">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Tanggal Kunjungan Aktual -->
                        <div>
                            <label for="tanggal_kunjungan_aktual" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Tanggal Kunjungan Aktual <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="date" id="tanggal_kunjungan_aktual" name="tanggal_kunjungan_aktual" value="{{ old('tanggal_kunjungan_aktual', date('Y-m-d')) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            @error('tanggal_kunjungan_aktual')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Mulai & Selesai -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="waktu_mulai" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Waktu Mulai <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="time" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                @error('waktu_mulai')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="waktu_selesai" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Waktu Selesai <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="time" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                @error('waktu_selesai')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Lokasi Kunjungan -->
                        <div>
                            <label for="lokasi_kunjungan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lokasi Kunjungan Aktual <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <textarea id="lokasi_kunjungan" name="lokasi_kunjungan" rows="3" required maxlength="500"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                      placeholder="Alamat lengkap lokasi kunjungan yang sebenarnya...">{{ old('lokasi_kunjungan') }}</textarea>
                            <div class="text-sm text-gray-500 mt-1">Maksimal 500 karakter</div>
                            @error('lokasi_kunjungan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Koordinat GPS (Opsional) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                        </svg>
                                        Latitude (Opsional)
                                    </span>
                                </label>
                                <input type="number" id="latitude" name="latitude" value="{{ old('latitude') }}" step="any" min="-90" max="90"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md"
                                       placeholder="-6.2146">
                                @error('latitude')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                        </svg>
                                        Longitude (Opsional)
                                    </span>
                                </label>
                                <input type="number" id="longitude" name="longitude" value="{{ old('longitude') }}" step="any" min="-180" max="180"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md"
                                       placeholder="106.8451">
                                @error('longitude')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Get Current Location Button -->
                        <div class="text-center">
                            <button type="button" @click="getCurrentLocation()" :disabled="gettingLocation"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!gettingLocation">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg class="inline-block w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24" x-show="gettingLocation">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="gettingLocation ? 'Mengambil Lokasi...' : 'Ambil Lokasi Saat Ini'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Hasil Kunjungan -->
                        <div>
                            <label for="hasil_kunjungan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Hasil Kunjungan <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <textarea id="hasil_kunjungan" name="hasil_kunjungan" rows="5" required maxlength="2000"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                      placeholder="Jelaskan secara detail hasil dari kunjungan yang telah dilakukan...">{{ old('hasil_kunjungan') }}</textarea>
                            <div class="text-sm text-gray-500 mt-1">Maksimal 2000 karakter</div>
                            @error('hasil_kunjungan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Temuan -->
                        <div>
                            <label for="temuan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Temuan (Opsional)
                                </span>
                            </label>
                            <textarea id="temuan" name="temuan" rows="4" maxlength="1500"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                      placeholder="Temuan-temuan penting yang diperoleh selama kunjungan...">{{ old('temuan') }}</textarea>
                            <div class="text-sm text-gray-500 mt-1">Maksimal 1500 karakter</div>
                            @error('temuan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rekomendasi -->
                        <div>
                            <label for="rekomendasi" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    Rekomendasi (Opsional)
                                </span>
                            </label>
                            <textarea id="rekomendasi" name="rekomendasi" rows="4" maxlength="1500"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                      placeholder="Rekomendasi dan saran untuk langkah selanjutnya...">{{ old('rekomendasi') }}</textarea>
                            <div class="text-sm text-gray-500 mt-1">Maksimal 1500 karakter</div>
                            @error('rekomendasi')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Kunjungan -->
                        <div>
                            <label for="status_kunjungan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status Kunjungan <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <select id="status_kunjungan" name="status_kunjungan" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                <option value="">-- Pilih Status --</option>
                                <option value="berhasil" {{ old('status_kunjungan') === 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                                <option value="tidak_berhasil" {{ old('status_kunjungan') === 'tidak_berhasil' ? 'selected' : '' }}>Tidak Berhasil</option>
                                <option value="tertunda" {{ old('status_kunjungan') === 'tertunda' ? 'selected' : '' }}>Tertunda</option>
                            </select>
                            @error('status_kunjungan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kendala (conditional) -->
                        <div x-show="document.getElementById('status_kunjungan').value === 'tidak_berhasil' || document.getElementById('status_kunjungan').value === 'tertunda'"
                             x-transition:enter="transition-opacity duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100">
                            <label for="kendala" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    Kendala/Hambatan
                                </span>
                            </label>
                            <textarea id="kendala" name="kendala" rows="3" maxlength="1000"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                      placeholder="Jelaskan kendala atau hambatan yang dialami...">{{ old('kendala') }}</textarea>
                            <div class="text-sm text-gray-500 mt-1">Maksimal 1000 karakter</div>
                            @error('kendala')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Uploads Section -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Upload Dokumentasi
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Foto Kunjungan -->
                        <div>
                            <label for="foto_kunjungan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Foto Kunjungan (Opsional)
                                </span>
                            </label>
                            <input type="file" id="foto_kunjungan" name="foto_kunjungan[]" multiple accept="image/*"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="text-sm text-gray-500 mt-1">
                                Format: JPEG, JPG, PNG. Maksimal 5MB per file. Dapat upload multiple file.
                            </div>
                            @error('foto_kunjungan.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dokumen Pendukung -->
                        <div>
                            <label for="dokumen_pendukung" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Dokumen Pendukung (Opsional)
                                </span>
                            </label>
                            <input type="file" id="dokumen_pendukung" name="dokumen_pendukung[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="text-sm text-gray-500 mt-1">
                                Format: PDF, DOC, DOCX, XLS, XLSX. Maksimal 10MB per file. Dapat upload multiple file.
                            </div>
                            @error('dokumen_pendukung.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan Auditor -->
                <div class="mt-8">
                    <label for="catatan_auditor" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Catatan Auditor (Opsional)
                        </span>
                    </label>
                    <textarea id="catatan_auditor" name="catatan_auditor" rows="4" maxlength="1000"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                              placeholder="Catatan tambahan atau informasi penting lainnya...">{{ old('catatan_auditor') }}</textarea>
                    <div class="text-sm text-gray-500 mt-1">Maksimal 1000 karakter</div>
                    @error('catatan_auditor')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-end gap-4">
                    <a href="{{ route('auditor.visits.show', $visit) }}" 
                       class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Submit Laporan
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    function visitReportForm() {
        return {
            gettingLocation: false,
            
            getCurrentLocation() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser ini.');
                    return;
                }
                
                this.gettingLocation = true;
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                        document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                        this.gettingLocation = false;
                        alert('Lokasi berhasil diambil!');
                    },
                    (error) => {
                        this.gettingLocation = false;
                        let message = 'Gagal mengambil lokasi: ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                message += 'Permission ditolak. Mohon izinkan akses lokasi.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                message += 'Informasi lokasi tidak tersedia.';
                                break;
                            case error.TIMEOUT:
                                message += 'Request timeout.';
                                break;
                            default:
                                message += 'Error tidak diketahui.';
                                break;
                        }
                        alert(message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            }
        }
    }

    // Show/hide kendala field based on status selection
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status_kunjungan');
        const kendalaDiv = document.querySelector('[x-show*="status_kunjungan"]');
        
        statusSelect.addEventListener('change', function() {
            // Force Alpine.js to re-evaluate the condition
            if (this.value === 'tidak_berhasil' || this.value === 'tertunda') {
                kendalaDiv.style.display = 'block';
            } else {
                kendalaDiv.style.display = 'none';
            }
        });
    });
</script>
@endsection