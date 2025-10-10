<!DOCTYPE html>
<html lang="id">
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
<!DOCTYPE html>
<html lang="id">
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
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('admin.sidebar')
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
                    <a href="{{ route('admin.visits.edit', $visit) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        <i data-feather="edit-3" class="w-4 h-4 mr-2"></i> Edit
                    </a>
                    <a href="{{ route('admin.visits.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
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
                                    <label class="block text-sm text-gray-700 mb-1">ID Kunjungan</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->visit_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Status</label>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $visit->status_label['color'] ?? 'bg-gray-100 text-gray-800' }}">{{ $visit->status_label['text'] ?? ucfirst($visit->status) }}</span>
                                        <p class="text-xs text-gray-500 mt-2">Status dikelola otomatis berdasarkan aksi Author dan Auditor</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Nama Author</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->author_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Nama Auditor</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->auditor_name }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm text-gray-700 mb-1">Tanggal & Waktu Kunjungan</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->formatted_visit_date }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Lokasi</h2>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Alamat</label>
                                <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->location_address }}</p>
                            </div>
                            @if($visit->latitude && $visit->longitude)
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div><label class="block text-sm text-gray-700">Latitude</label><p class="text-sm bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->latitude }}</p></div>
                                    <div><label class="block text-sm text-gray-700">Longitude</label><p class="text-sm bg-gray-50 px-3 py-2 rounded-lg">{{ $visit->longitude }}</p></div>
                                </div>
                                <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center mt-4">
                                    <div class="text-center text-gray-500">
                                        <p class="text-sm">Peta akan ditampilkan di halaman peta.</p>
                                        <a href="https://www.google.com/maps?q={{ $visit->latitude }},{{ $visit->longitude }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">Buka di Google Maps</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($visit->notes)
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h2>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $visit->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                            <div class="space-y-3">
                                <a href="{{ route('admin.visits.edit', $visit) }}" class="block w-full bg-orange-500 text-white px-4 py-2 rounded-lg text-center">Edit Kunjungan</a>
                                <button onclick="window.print()" class="block w-full bg-blue-500 text-white px-4 py-2 rounded-lg">Print Detail</button>
                                <form method="POST" action="{{ route('admin.visits.destroy', $visit) }}" onsubmit="return confirm('Yakin ingin menghapus kunjungan ini?')">@csrf @method('DELETE')<button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg">Hapus Kunjungan</button></form>
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

    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        function showImageModal(src){ document.getElementById('modalImage').src=src; document.getElementById('imageModal').classList.remove('hidden') }
        function hideImageModal(){ document.getElementById('imageModal').classList.add('hidden') }
        document.addEventListener('DOMContentLoaded', function(){ feather.replace(); });
    </script>

    @include('visits.workflow-modals')
</body>
</html>