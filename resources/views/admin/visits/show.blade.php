<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kunjungan - Admin Panel</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Detail Kunjungan</h1>
                    <p class="text-gray-600 mt-1">{{ $visit->visit_id }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.visits.edit', $visit) }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
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
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gradient-to-r from-white to-blue-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Kunjungan
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID Kunjungan</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->visit_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <div x-data="{ open: false, currentStatus: '{{ $visit->status }}' }">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $visit->status_label['color'] }}">
                                            {{ $visit->status_label['text'] }}
                                        </span>
                                        <button @click="open = true" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Ubah Status
                                        </button>
                                    </div>
                                    
                                    <!-- Status Update Modal -->
                                    <div x-show="open" @click.away="open = false" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
                                        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                                            <h3 class="text-lg font-semibold mb-4">Ubah Status Kunjungan</h3>
                                            <form id="statusForm">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                                                    <select name="status" x-model="currentStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                                        <option value="pending">Menunggu</option>
                                                        <option value="konfirmasi">Dikonfirmasi</option>
                                                        <option value="selesai">Selesai</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                                    <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Catatan untuk perubahan status..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="open = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Author</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->author_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Auditor</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->auditor_name }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Kunjungan</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->formatted_visit_date }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="bg-gradient-to-r from-white to-green-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Informasi Lokasi
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->location_address }}</p>
                            </div>
                            @if($visit->latitude && $visit->longitude)
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                        <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->latitude }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                        <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->longitude }}</p>
                                    </div>
                                </div>
                                <!-- Simple Map -->
                                <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p class="text-sm">Peta akan ditampilkan di sini</p>
                                        <a href="https://www.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Buka di Google Maps
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($visit->notes)
                        <div class="bg-gradient-to-r from-white to-yellow-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Catatan
                            </h2>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $visit->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-r from-white to-purple-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Aksi Cepat
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.visits.edit', $visit) }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center px-4 py-2 rounded-lg font-medium transition-colors">
                                Edit Kunjungan
                            </a>
                            <button onclick="window.print()" class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium transition-colors">
                                Print Detail
                            </button>
                            <form method="POST" action="{{ route('admin.visits.destroy', $visit) }}" class="block" onsubmit="return confirm('Yakin ingin menghapus kunjungan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Hapus Kunjungan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Photos -->
                    @if($visit->photos && count($visit->photos) > 0)
                        <div class="bg-gradient-to-r from-white to-indigo-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Foto Kunjungan
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($visit->photos as $index => $photo)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto kunjungan {{ $index + 1 }}" class="w-full h-24 object-cover rounded-lg">
                                        <button onclick="showImageModal('{{ asset('storage/' . $photo) }}')" class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Timeline
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Kunjungan Dibuat</p>
                                    <p class="text-xs text-gray-500">{{ $visit->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @if($visit->updated_at != $visit->created_at)
                                <div class="flex items-start gap-3">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Terakhir Diperbarui</p>
                                        <p class="text-xs text-gray-500">{{ $visit->updated_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Foto kunjungan" class="max-w-full max-h-full object-contain">
            <button onclick="hideImageModal()" class="absolute top-4 right-4 text-white text-2xl font-bold">&times;</button>
        </div>
    </div>

    <script>
        function showImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function hideImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Status update form
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("admin.visits.status", $visit) }}', {
                method: 'POST',
                body: formData,
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
                alert('Terjadi kesalahan saat memperbarui status');
            });
        });
    </script>
</body>
</html>