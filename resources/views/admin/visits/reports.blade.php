<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1 class="text-2xl font-bold text-gray-900">Statistik & Laporan Kunjungan</h1>
                    <p class="text-gray-600 mt-1">Analisis data kunjungan auditor ke author</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.visits.export') }}?format=csv&start_date={{ $startDate }}&end_date={{ $endDate }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('admin.visits.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Kunjungan
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8">
            <!-- Date Filter -->
            <div class="bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6 mb-8">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white shadow-sm">
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-white to-blue-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-3 rounded-full shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Kunjungan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalVisits }}</p>
                            <p class="text-xs text-gray-500">Periode: {{ Carbon\Carbon::parse($startDate)->format('d M') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-white to-yellow-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-3 rounded-full shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Kunjungan Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pendingVisits }}</p>
                            <p class="text-xs text-gray-500">{{ $totalVisits > 0 ? round(($pendingVisits / $totalVisits) * 100, 1) : 0 }}% dari total</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-white to-green-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 p-3 rounded-full shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Kunjungan Selesai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $completedVisits }}</p>
                            <p class="text-xs text-gray-500">{{ $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0 }}% dari total</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Timeline Chart -->
                <div class="bg-gradient-to-r from-white to-blue-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Tren Kunjungan (30 Hari Terakhir)
                    </h2>
                    <div class="relative h-64">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>

                <!-- Top Authors -->
                <div class="bg-gradient-to-r from-white to-green-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Top 5 Author (Periode Terpilih)
                    </h2>
                    <div class="space-y-3">
                        @forelse($visitsByAuthor as $index => $author)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-semibold">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $author->author_name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900">{{ $author->total }}</span>
                                    <p class="text-xs text-gray-500">kunjungan</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">
                                <p>Tidak ada data author</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Auditors -->
                <div class="bg-gradient-to-r from-white to-purple-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Top 5 Auditor (Periode Terpilih)
                    </h2>
                    <div class="space-y-3">
                        @forelse($visitsByAuditor as $index => $auditor)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-semibold">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $auditor->auditor_name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900">{{ $auditor->total }}</span>
                                    <p class="text-xs text-gray-500">kunjungan</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">
                                <p>Tidak ada data auditor</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-gradient-to-r from-white to-indigo-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Aktivitas Terbaru
                    </h2>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($recentVisits as $visit)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="bg-{{ $visit->status === 'pending' ? 'yellow' : ($visit->status === 'konfirmasi' ? 'blue' : 'green') }}-100 p-2 rounded-full">
                                    @if($visit->status === 'pending')
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($visit->status === 'konfirmasi')
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $visit->visit_id }}</p>
                                    <p class="text-xs text-gray-600">{{ $visit->author_name }} ← {{ $visit->auditor_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $visit->updated_at->diffForHumans() }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $visit->status_label['class'] ?? 'badge-secondary' }}">
                                    {{ $visit->status_label['text'] ?? 'Unknown' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8">
                                <p>Tidak ada aktivitas terbaru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Timeline Chart
        const timelineCtx = document.getElementById('timelineChart').getContext('2d');
        const timelineData = @json($timelineData);
        
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: timelineData.map(item => item.date),
                datasets: [
                    {
                        label: 'Menunggu',
                        data: timelineData.map(item => item.pending),
                        borderColor: '#eab308',
                        backgroundColor: '#eab30820',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Dikonfirmasi',
                        data: timelineData.map(item => item.konfirmasi),
                        borderColor: '#3b82f6',
                        backgroundColor: '#3b82f620',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Selesai',
                        data: timelineData.map(item => item.selesai),
                        borderColor: '#22c55e',
                        backgroundColor: '#22c55e20',
                        tension: 0.4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>
</html>