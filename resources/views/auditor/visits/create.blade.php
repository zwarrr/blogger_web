<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Kunjungan - Auditor</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 font-sans">
  
  <div class="ml-64 flex-1 flex flex-col min-h-screen">

    {{-- Sidebar --}}
    @include('auditor.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

      <!-- Topbar -->
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Tambah Kunjungan Baru</h2>
          <p class="text-sm text-gray-600 mt-1">Buat kunjungan author baru</p>
        </div>
        <a href="{{ route('auditor.visits.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
          Kembali
        </a>
      </header>

      <!-- Page Content -->
      <main class="p-6 space-y-6">
        @if($errors->any())
        <div class="rounded-md bg-red-50 p-4 border border-red-200">
          <div class="flex">
            <div class="flex-shrink-0">
              <i data-feather="alert-circle" class="h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan:</h3>
              <div class="mt-2 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                  @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
        @endif

        <form action="{{ route('auditor.visits.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Informasi Kunjungan</h3>
                </div>
                <div class="px-6 py-4 space-y-6">
                  <!-- Author Selection -->
                  <div>
                    <label for="author_id" class="block text-sm font-medium text-gray-700">Penulis *</label>
                    <select id="author_id" name="author_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                      <option value="">Pilih Penulis...</option>
                      @foreach($authors ?? [] as $author)
                      <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                      </option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Location Address -->
                  <div>
                    <label for="location_address" class="block text-sm font-medium text-gray-700">Alamat Lokasi *</label>
                    <textarea id="location_address" name="location_address" rows="3" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan alamat lengkap...">{{ old('location_address') }}</textarea>
                  </div>

                  <!-- GPS Coordinates -->
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                      <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="-6.200000">
                    </div>
                    <div>
                      <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                      <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="106.816666">
                    </div>
                  </div>

                  <!-- Get Current Location Button -->
                  <div>
                    <button type="button" id="getCurrentLocation" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                      Dapatkan Lokasi Saat Ini
                    </button>
                  </div>

                  <!-- Status -->
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select id="status" name="status" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                      <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="konfirmasi" {{ old('status') === 'konfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
                      <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                  </div>

                  <!-- Notes -->
                  <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="Tambahkan catatan...">{{ old('notes') }}</textarea>
                  </div>

                  <!-- Photo Upload -->
                  <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700">Foto Kunjungan</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                      <div class="space-y-1 text-center">
                        <i data-feather="upload" class="mx-auto h-12 w-12 text-gray-400"></i>
                        <div class="flex text-sm text-gray-600">
                          <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span>Upload foto</span>
                            <input id="photos" name="photos[]" type="file" class="sr-only" multiple accept="image/*">
                          </label>
                          <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB per file</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                  <div class="flex justify-end space-x-3">
                    <a href="{{ route('auditor.visits.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i data-feather="save" class="w-4 h-4 mr-2"></i>
                      Simpan Kunjungan
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
              <!-- Location Preview -->
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Preview Lokasi</h3>
                </div>
                <div class="px-6 py-4">
                  <div id="map" class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center">
                    <div class="text-center text-gray-500">
                      <i data-feather="map" class="w-8 h-8 mx-auto mb-2"></i>
                      <p class="text-sm">Masukkan koordinat GPS</p>
                    </div>
                  </div>
                  <div class="mt-3 text-sm text-gray-600">
                    <p>Lat: <span id="previewLat">-</span></p>
                    <p>Lng: <span id="previewLng">-</span></p>
                  </div>
                </div>
              </div>

              <!-- Info Card -->
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Informasi</h3>
                </div>
                <div class="px-6 py-4 space-y-3 text-sm text-gray-600">
                  <div class="flex items-start">
                    <i data-feather="info" class="w-4 h-4 mt-0.5 mr-2 text-primary-500"></i>
                    <p>ID kunjungan akan dibuat otomatis</p>
                  </div>
                  <div class="flex items-start">
                    <i data-feather="user" class="w-4 h-4 mt-0.5 mr-2 text-primary-500"></i>
                    <p>Pilih penulis dari daftar yang tersedia</p>
                  </div>
                  <div class="flex items-start">
                    <i data-feather="map-pin" class="w-4 h-4 mt-0.5 mr-2 text-primary-500"></i>
                    <p>Koordinat GPS opsional namun disarankan</p>
                  </div>
                  <div class="flex items-start">
                    <i data-feather="camera" class="w-4 h-4 mt-0.5 mr-2 text-primary-500"></i>
                    <p>Upload foto sebagai dokumentasi</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </main>
    </div>
  </div>

  <script>
    feather.replace();

    let map = null;
    let marker = null;

    // Initialize empty map
    function initMap() {
      if (!map) {
        map = L.map('map').setView([-2.5489, 118.0149], 5); // Indonesia center
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
      }
    }

    // Update map when coordinates change
    function updateMap(lat, lng) {
      if (!map) initMap();
      
      if (lat && lng) {
        map.setView([lat, lng], 15);
        
        if (marker) {
          marker.setLatLng([lat, lng]);
        } else {
          marker = L.marker([lat, lng]).addTo(map);
        }
        
        document.getElementById('previewLat').textContent = lat;
        document.getElementById('previewLng').textContent = lng;
      }
    }

    // Listen to coordinate input changes
    document.getElementById('latitude').addEventListener('input', function() {
      const lat = this.value;
      const lng = document.getElementById('longitude').value;
      if (lat && lng) updateMap(parseFloat(lat), parseFloat(lng));
    });

    document.getElementById('longitude').addEventListener('input', function() {
      const lat = document.getElementById('latitude').value;
      const lng = this.value;
      if (lat && lng) updateMap(parseFloat(lat), parseFloat(lng));
    });

    // Get current location
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
      if (navigator.geolocation) {
        this.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i>Mengambil lokasi...';
        feather.replace();
        
        navigator.geolocation.getCurrentPosition(function(position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          document.getElementById('latitude').value = lat;
          document.getElementById('longitude').value = lng;
          
          updateMap(lat, lng);
          
          document.getElementById('getCurrentLocation').innerHTML = '<i data-feather="check" class="w-4 h-4 mr-2"></i>Lokasi berhasil didapat';
          feather.replace();
          
          setTimeout(() => {
            document.getElementById('getCurrentLocation').innerHTML = '<i data-feather="map-pin" class="w-4 h-4 mr-2"></i>Dapatkan Lokasi Saat Ini';
            feather.replace();
          }, 2000);
        }, function() {
          document.getElementById('getCurrentLocation').innerHTML = '<i data-feather="x" class="w-4 h-4 mr-2"></i>Gagal mendapatkan lokasi';
          feather.replace();
          
          setTimeout(() => {
            document.getElementById('getCurrentLocation').innerHTML = '<i data-feather="map-pin" class="w-4 h-4 mr-2"></i>Dapatkan Lokasi Saat Ini';
            feather.replace();
          }, 2000);
        });
      } else {
        alert('Geolocation tidak didukung oleh browser ini.');
      }
    });

    // Initialize map on page load
    window.addEventListener('load', function() {
      const lat = document.getElementById('latitude').value;
      const lng = document.getElementById('longitude').value;
      if (lat && lng) {
        updateMap(parseFloat(lat), parseFloat(lng));
      } else {
        initMap();
      }
    });
  </script>
</body>
</html>
