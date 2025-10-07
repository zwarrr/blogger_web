<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Kunjungan - Auditor</title>
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
    @if($visit->latitude && $visit->longitude)
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    @endif
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
          <h2 class="text-xl font-semibold text-gray-900">Edit Kunjungan</h2>
          <p class="text-sm text-gray-600 mt-1">{{ $visit->visit_id }}</p>
        </div>
        <a href="{{ route('auditor.visits.show', $visit->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
          <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
          Kembali
        </a>
      </header>

      <!-- Page Content -->
      <main class="p-6 space-y-6">
        @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 border border-green-200">
          <div class="flex">
            <div class="flex-shrink-0">
              <i data-feather="check-circle" class="h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">
                {{ session('success') }}
              </p>
            </div>
          </div>
        </div>
        @endif

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

        <form action="{{ route('auditor.visits.update', $visit->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Informasi Kunjungan</h3>
                </div>
                <div class="px-6 py-4 space-y-6">
                  <!-- Read-only fields -->
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">ID Kunjungan</label>
                      <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2 border border-gray-200">{{ $visit->visit_id }}</div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Nama Penulis</label>
                      <div class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2 border border-gray-200">{{ $visit->author_name }}</div>
                    </div>
                  </div>

                  <!-- Location Address -->
                  <div>
                    <label for="location_address" class="block text-sm font-medium text-gray-700">Alamat Lokasi</label>
                    <textarea id="location_address" name="location_address" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="Masukkan alamat lengkap...">{{ old('location_address', $visit->location_address) }}</textarea>
                  </div>

                  <!-- GPS Coordinates -->
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                      <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                      <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $visit->latitude) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                      <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                      <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $visit->longitude) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
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
                      <option value="pending" {{ $visit->status === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="konfirmasi" {{ $visit->status === 'konfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
                      <option value="selesai" {{ $visit->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                  </div>

                  <!-- Notes -->
                  <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="Tambahkan catatan...">{{ old('notes', $visit->notes) }}</textarea>
                  </div>

                  <!-- Photo Upload -->
                  <div>
                    <label for="photos" class="block text-sm font-medium text-gray-700">Foto Tambahan</label>
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
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                  <div class="flex justify-end space-x-3">
                    <a href="{{ route('auditor.visits.show', $visit->id) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i data-feather="save" class="w-4 h-4 mr-2"></i>
                      Simpan Perubahan
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
              <!-- Current Photos -->
              @if($visit->photos && count($visit->photos) > 0)
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Foto Saat Ini</h3>
                </div>
                <div class="px-6 py-4">
                  <div class="grid grid-cols-1 gap-4">
                    @foreach($visit->photos as $photo)
                    <div class="relative">
                      <img class="h-24 w-full object-cover rounded-lg" src="{{ Storage::url($photo) }}" alt="Foto kunjungan">
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              @endif

              <!-- Location Preview -->
              @if($visit->latitude && $visit->longitude)
              <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Lokasi Saat Ini</h3>
                </div>
                <div class="px-6 py-4">
                  <div id="map" class="w-full h-48 rounded-lg"></div>
                  <div class="mt-3 text-sm text-gray-600">
                    <p>Lat: <span id="currentLat">{{ $visit->latitude }}</span></p>
                    <p>Lng: <span id="currentLng">{{ $visit->longitude }}</span></p>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </form>
      </main>
    </div>
  </div>

  <script>
    feather.replace();

    let map;
    let marker;

    @if($visit->latitude && $visit->longitude)
    // Initialize map
    map = L.map('map').setView([{{ $visit->latitude }}, {{ $visit->longitude }}], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    marker = L.marker([{{ $visit->latitude }}, {{ $visit->longitude }}])
        .addTo(map)
        .bindPopup('{{ $visit->location_address ?? "Lokasi Kunjungan" }}');
    @endif

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
                document.getElementById('currentLat').textContent = lat;
                document.getElementById('currentLng').textContent = lng;
                
                // Update map if exists
                if (map) {
                    map.setView([lat, lng], 15);
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                }
                
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
  </script>
</body>
</html>
