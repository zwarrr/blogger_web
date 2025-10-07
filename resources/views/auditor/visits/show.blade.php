<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Kunjungan - Auditor</title>
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
          <h2 class="text-xl font-semibold text-gray-900">Detail Kunjungan</h2>
          <p class="text-sm text-gray-600 mt-1">{{ $visit->visit_id }}</p>
        </div>
        <div class="flex space-x-3">
          <a href="{{ route('auditor.visits.edit', $visit->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i data-feather="edit" class="w-4 h-4 mr-2"></i>
            Edit
          </a>
          <a href="{{ route('auditor.visits.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar
          </a>
        </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Visit Info -->
            <div class="bg-white shadow rounded-lg border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Kunjungan</h3>
              </div>
              <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">ID Kunjungan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->visit_id }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $visit->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $visit->status === 'konfirmasi' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $visit->status === 'selesai' ? 'bg-green-100 text-green-800' : '' }}
                      ">
                        {{ ucfirst($visit->status) }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Penulis</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->author_name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Auditor</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->auditor->name ?? 'N/A' }}</dd>
                  </div>
                  <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Alamat Lokasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->location_address ?? 'Alamat belum diisi' }}</dd>
                  </div>
                  @if($visit->latitude && $visit->longitude)
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Latitude</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->latitude }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Longitude</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->longitude }}</dd>
                  </div>
                  @endif
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->created_at->format('d M Y H:i') }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->updated_at->format('d M Y H:i') }}</dd>
                  </div>
                  @if($visit->notes)
                  <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $visit->notes }}</dd>
                  </div>
                  @endif
                </dl>
              </div>
            </div>

            <!-- Photos -->
            @if($visit->photos && count($visit->photos) > 0)
            <div class="bg-white shadow rounded-lg border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Foto Kunjungan</h3>
              </div>
              <div class="px-6 py-4">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                  @foreach($visit->photos as $photo)
                  <div class="relative group">
                    <img class="w-full h-32 object-cover rounded-lg shadow-sm" src="{{ Storage::url($photo) }}" alt="Foto kunjungan">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity rounded-lg flex items-center justify-center">
                      <a href="{{ Storage::url($photo) }}" target="_blank" class="opacity-0 group-hover:opacity-100 transition-opacity text-white bg-black bg-opacity-50 rounded-full p-2">
                        <i data-feather="external-link" class="w-4 h-4"></i>
                      </a>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            @endif
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Aksi Cepat</h3>
              </div>
              <div class="px-6 py-4 space-y-3">
                <a href="{{ route('auditor.visits.edit', $visit->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i data-feather="edit" class="w-4 h-4 mr-2"></i>
                  Edit Kunjungan
                </a>
                
                @if($visit->status === 'pending')
                <form action="{{ route('auditor.visits.update', $visit->id) }}" method="POST" class="w-full">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="status" value="konfirmasi">
                  <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i data-feather="check" class="w-4 h-4 mr-2"></i>
                    Konfirmasi
                  </button>
                </form>
                @endif

                @if($visit->status === 'konfirmasi')
                <form action="{{ route('auditor.visits.update', $visit->id) }}" method="POST" class="w-full">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="status" value="selesai">
                  <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                    Selesaikan
                  </button>
                </form>
                @endif

                <form action="{{ route('auditor.visits.destroy', $visit->id) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin menghapus kunjungan ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i>
                    Hapus Kunjungan
                  </button>
                </form>
              </div>
            </div>

            <!-- Map -->
            @if($visit->latitude && $visit->longitude)
            <div class="bg-white shadow rounded-lg border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Lokasi</h3>
              </div>
              <div class="px-6 py-4">
                <div id="map" class="w-full h-64 rounded-lg"></div>
                <div class="mt-3 text-sm text-gray-600">
                  <p><strong>Koordinat:</strong></p>
                  <p>Lat: {{ $visit->latitude }}</p>
                  <p>Lng: {{ $visit->longitude }}</p>
                </div>
              </div>
            </div>
            @endif

            <!-- Visit Timeline -->
            <div class="bg-white shadow rounded-lg border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Timeline</h3>
              </div>
              <div class="px-6 py-4">
                <div class="flow-root">
                  <ul class="-mb-8">
                    <li>
                      <div class="relative pb-8">
                        <div class="relative flex space-x-3">
                          <div>
                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                              <i data-feather="plus" class="w-4 h-4 text-white"></i>
                            </span>
                          </div>
                          <div class="flex-1 min-w-0">
                            <div>
                              <p class="text-sm text-gray-500">Kunjungan dibuat</p>
                              <p class="text-xs text-gray-400">{{ $visit->created_at->format('d M Y H:i') }}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    
                    @if($visit->status !== 'pending')
                    <li>
                      <div class="relative pb-8">
                        <div class="relative flex space-x-3">
                          <div>
                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                              <i data-feather="check" class="w-4 h-4 text-white"></i>
                            </span>
                          </div>
                          <div class="flex-1 min-w-0">
                            <div>
                              <p class="text-sm text-gray-500">Status: Konfirmasi</p>
                              <p class="text-xs text-gray-400">{{ $visit->updated_at->format('d M Y H:i') }}</p>
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
                            <span class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center ring-8 ring-white">
                              <i data-feather="check-circle" class="w-4 h-4 text-white"></i>
                            </span>
                          </div>
                          <div class="flex-1 min-w-0">
                            <div>
                              <p class="text-sm text-gray-500">Kunjungan selesai</p>
                              <p class="text-xs text-gray-400">{{ $visit->updated_at->format('d M Y H:i') }}</p>
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
  </div>

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
        .bindPopup('{{ $visit->location_address ?? "Lokasi Kunjungan" }}');
    @endif
  </script>
</body>
</html>
