<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        
        /* Enhanced Table Styling */
        .table-enhanced tbody tr:hover {
            background: linear-gradient(90deg, rgba(239, 246, 255, 0.5), rgba(219, 234, 254, 0.3));
        }
        
        /* PDF Export Loading Overlay - Clean Design */
        .pdf-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(31, 41, 55, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .pdf-loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .pdf-loading-content {
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            min-width: 250px;
        }
        
        .pdf-loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e5e7eb;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .pdf-loading-content h3 {
            color: #1f2937;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .pdf-loading-content p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }
        
        /* Print-friendly styles for PDF generation */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
            }
            
            .bg-gradient-to-br {
                background: white !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('admin.sidebar')
    
    <div class="ml-64 min-h-screen">
        <main class="flex-1">
            <!-- Header - consistent with index -->
            <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i data-feather="bar-chart-3" class="w-6 h-6 mr-3 text-orange-600"></i>
                            Laporan Kunjungan
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Statistik dan analisis kunjungan auditor dari database real-time</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 text-sm text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="font-medium">Live Data</span>
                        </div>
                        <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-150 shadow-sm">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i>
                            Export PDF
                        </button>
                        <a href="{{ route('admin.visits.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-150 shadow-sm">
                            <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Date Filter -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-900">Filter Periode</h3>
                    </div>
                    <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-4">
                        <div class="flex flex-col sm:flex-row gap-3 flex-1">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" 
                                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white min-w-[140px]">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" 
                                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white min-w-[140px]">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-150 shadow-sm">
                                <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                                Filter
                            </button>
                            <a href="{{ route('admin.visits.reports') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-150 shadow-sm">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Statistics Overview - Real Database Data Only -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Visits -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <i data-feather="users" class="w-5 h-5 text-orange-600"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-500">Total Kunjungan</h3>
                            </div>
                            <span class="text-xs text-green-600">&nbsp;</span>
                        </div>
                        <p class="text-2xl font-bold mt-2">{{ number_format($totalVisits) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>

                    <!-- Pending Visits -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <i data-feather="clock" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-500">Belum Dikunjungi</h3>
                            </div>
                            <span class="text-xs text-green-600">&nbsp;</span>
                        </div>
                        <p class="text-2xl font-bold mt-2">{{ number_format($pendingVisits) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $totalVisits > 0 ? round(($pendingVisits / $totalVisits) * 100, 1) : 0 }}% dari total</p>
                    </div>

                    <!-- Completed Visits -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <i data-feather="check-circle" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-500">Kunjungan Selesai</h3>
                            </div>
                            <span class="text-xs text-green-600">&nbsp;</span>
                        </div>
                        <p class="text-2xl font-bold mt-2">{{ number_format($completedVisits) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0 }}% dari total</p>
                    </div>

                    <!-- Success Rate -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                    <i data-feather="trending-up" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-500">Tingkat Keberhasilan</h3>
                            </div>
                        </div>
                        <p class="text-2xl font-bold mt-2 text-black-600">{{ $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0 }}%</p>
                        <p class="text-xs text-gray-500 mt-1">Rasio selesai vs total</p>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Timeline Chart -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Tren Kunjungan</h3>
                                <p class="text-sm text-gray-500">30 hari terakhir dari database</p>
                            </div>
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">Real-time</span>
                        </div>
                        <div class="relative h-72">
                            <canvas id="timelineChart"></canvas>
                        </div>
                    </div>

                    <!-- Status Distribution -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Distribusi Status</h3>
                                <p class="text-sm text-gray-500">Persentase per kategori</p>
                            </div>
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded">{{ $totalVisits ?? 0 }} Total</span>
                        </div>
                        <div class="relative h-72">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Performers Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Top Authors -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Top 5 Author Terbanyak</h3>
                                <p class="text-sm text-gray-500">Berdasarkan jumlah kunjungan</p>
                            </div>
                            <span class="text-xs bg-orange-50 text-orange-700 px-2 py-1 rounded">Periode Terpilih</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($visitsByAuthor as $index => $author)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-orange-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-orange-100 text-orange-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-medium">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $author->author_name }}</span>
                                            <p class="text-xs text-gray-500">Author</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-gray-900">{{ $author->total }}</span>
                                        <p class="text-xs text-gray-500">kunjungan</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8">
                                    <i data-feather="inbox" class="w-8 h-8 mx-auto text-gray-300 mb-2"></i>
                                    <p class="text-sm">Tidak ada data author pada periode ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Auditors -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Top 5 Auditor Teraktif</h3>
                                <p class="text-sm text-gray-500">Berdasarkan jumlah audit</p>
                            </div>
                            <span class="text-xs bg-purple-50 text-purple-700 px-2 py-1 rounded">Periode Terpilih</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($visitsByAuditor as $index => $auditor)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-purple-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-purple-100 text-purple-600 rounded-full w-8 h-8 flex items-center justify-center text-sm font-medium">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $auditor->auditor_name }}</span>
                                            <p class="text-xs text-gray-500">Auditor</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-gray-900">{{ $auditor->total }}</span>
                                        <p class="text-xs text-gray-500">kunjungan</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8">
                                    <i data-feather="user-check" class="w-8 h-8 mx-auto text-gray-300 mb-2"></i>
                                    <p class="text-sm">Tidak ada data auditor pada periode ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                            <p class="text-sm text-gray-500">Update terkini dari database</p>
                        </div>
                        <a href="{{ route('admin.visits.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-150 shadow-sm">
                            Lihat Semua
                            <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                        @forelse($recentVisits as $visit)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-shrink-0 mt-1">
                                    @if(($visit->status ?? '') === 'belum_dikunjungi')
                                        <div class="bg-yellow-100 p-1.5 rounded-full">
                                            <i data-feather="clock" class="w-3 h-3 text-yellow-600"></i>
                                        </div>
                                    @elseif(($visit->status ?? '') === 'selesai')
                                        <div class="bg-green-100 p-1.5 rounded-full">
                                            <i data-feather="check-circle" class="w-3 h-3 text-green-600"></i>
                                        </div>
                                    @else
                                        <div class="bg-blue-100 p-1.5 rounded-full">
                                            <i data-feather="play-circle" class="w-3 h-3 text-blue-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $visit->visit_id ?: 'VST' . str_pad($visit->id ?? 0, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <span class="font-medium">{{ $visit->author_display_name ?? ($visit->author ? $visit->author->name : 'Author tidak tersedia') }}</span>
                                        @if($visit->auditor_display_name ?? ($visit->auditor ? $visit->auditor->name : null))
                                            ← {{ $visit->auditor_display_name ?? ($visit->auditor ? $visit->auditor->name : null) }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $visit->updated_at ? $visit->updated_at->diffForHumans() : 'Waktu tidak diketahui' }}</p>
                                    <div class="mt-1">
                                        @if(($visit->status ?? '') === 'belum_dikunjungi')
                                            <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-yellow-100 text-yellow-800">
                                                Belum Dikunjungi
                                            </span>
                                        @elseif(($visit->status ?? '') === 'selesai')
                                            <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800">
                                                {{ ucfirst(str_replace('_', ' ', $visit->status ?? 'unknown')) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500 py-8">
                                <i data-feather="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                                <p class="font-medium">Tidak ada aktivitas terbaru</p>
                                <p class="text-sm">Belum ada kunjungan yang tercatat dalam database</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- PDF Loading Overlay - Clean Design -->
    <div class="pdf-loading-overlay" id="pdfLoadingOverlay">
        <div class="pdf-loading-content">
            <div class="pdf-loading-spinner"></div>
            <h3>Generating Report</h3>
            <p>Processing data, please wait...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            feather.replace();

            // Timeline Chart - Real Database Data
            const timelineCtx = document.getElementById('timelineChart');
            const timelineData = @json($timelineData ?? []);
            
            if (timelineCtx && timelineData && timelineData.length > 0) {
                new Chart(timelineCtx, {
                    type: 'line',
                    data: {
                        labels: timelineData.map(item => item.date),
                        datasets: [
                            {
                                label: 'Belum Dikunjungi',
                                data: timelineData.map(item => item.belum_dikunjungi),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Dalam Perjalanan',
                                data: timelineData.map(item => item.dalam_perjalanan),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Sedang Dikunjungi',
                                data: timelineData.map(item => item.sedang_dikunjungi),
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Selesai',
                                data: timelineData.map(item => item.selesai),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Kunjungan'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else if (timelineCtx) {
                // Show message when no data available
                timelineCtx.parentElement.innerHTML = '<div class="h-72 flex items-center justify-center text-gray-500"><div class="text-center"><i data-feather="bar-chart-3" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i><p class="font-medium">Tidak ada data untuk chart</p><p class="text-sm">Belum ada data kunjungan dalam 30 hari terakhir</p></div></div>';
            }

            // Status Distribution Chart (Pie Chart) - Real Database Data
            const statusCtx = document.getElementById('statusChart');
            const totalVisits = {{ $totalVisits ?? 0 }};
            const pendingVisits = {{ $pendingVisits ?? 0 }};
            const completedVisits = {{ $completedVisits ?? 0 }};
            const inProgressVisits = totalVisits - pendingVisits - completedVisits;

            if (statusCtx && totalVisits > 0) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Belum Dikunjungi', 'Dalam Proses', 'Selesai'],
                        datasets: [{
                            data: [pendingVisits, inProgressVisits, completedVisits],
                            backgroundColor: [
                                '#f59e0b',
                                '#3b82f6', 
                                '#10b981'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15,
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const percentage = ((context.parsed / totalVisits) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (statusCtx) {
                // Show message when no data available
                statusCtx.parentElement.innerHTML = '<div class="h-72 flex items-center justify-center text-gray-500"><div class="text-center"><i data-feather="pie-chart" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i><p class="font-medium">Tidak ada data untuk chart</p><p class="text-sm">Belum ada data kunjungan pada periode ini</p></div></div>';
            }

            // Add loading state management
            function showLoading() {
                document.body.style.cursor = 'wait';
            }

            function hideLoading() {
                document.body.style.cursor = 'default';
            }

            // Handle form submission with loading
            const filterForm = document.querySelector('form');
            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    showLoading();
                });
            }
        });

        // PDF Export Function - Clean Professional Report (3 Colors: Black, Orange, Gray)
        async function exportToPDF() {
            const { jsPDF } = window.jspdf;
            
            // Show loading overlay
            const loadingOverlay = document.getElementById('pdfLoadingOverlay');
            loadingOverlay.classList.add('active');
            
            // Update button state
            const exportBtn = document.querySelector('button[onclick="exportToPDF()"]');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i>Generating...';
            exportBtn.disabled = true;
            
            try {
                // Get current data
                const totalVisits = {{ $totalVisits ?? 0 }};
                const pendingVisits = {{ $pendingVisits ?? 0 }};
                const completedVisits = {{ $completedVisits ?? 0 }};
                const inProgressVisits = totalVisits - pendingVisits - completedVisits;
                const visitsByAuthor = @json($visitsByAuthor ?? []);
                const visitsByAuditor = @json($visitsByAuditor ?? []);
                const recentVisits = @json($recentVisits ?? []);
                
                // Initialize PDF with professional settings
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const margin = 20;
                const contentWidth = pageWidth - (margin * 2);
                let yPos = margin;
                
                // Simple 3-Color Professional Theme (Black, Orange, Gray)
                const colors = {
                    black: [0, 0, 0],           // Pure black for text
                    orange: [249, 115, 22],     // Professional orange (orange-600)
                    gray: [107, 114, 128],      // Medium gray (gray-500)
                    lightGray: [243, 244, 246], // Very light gray (gray-100)
                    white: [255, 255, 255]     // White background
                };

                // Utility functions
                function setTextColor(color) { pdf.setTextColor(color[0], color[1], color[2]); }
                function setFillColor(color) { pdf.setFillColor(color[0], color[1], color[2]); }
                function setDrawColor(color) { pdf.setDrawColor(color[0], color[1], color[2]); }
                
                function newPageIfNeeded(space = 30) {
                    if (yPos > pageHeight - margin - space) {
                        pdf.addPage();
                        yPos = margin;
                        return true;
                    }
                    return false;
                }

                // CLEAN HEADER SECTION
                function createHeader() {
                    // Orange header background
                    setFillColor(colors.orange);
                    pdf.rect(0, 0, pageWidth, 25, 'F');
                    
                    // Main title
                    pdf.setFontSize(20);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.white);
                    pdf.text('LAPORAN KUNJUNGAN SISTEM', pageWidth / 2, 15, { align: 'center' });
                    
                    yPos = 35;
                    
                    // Date period section
                    setFillColor(colors.lightGray);
                    pdf.rect(margin, yPos, contentWidth, 12, 'F');
                    setDrawColor(colors.gray);
                    pdf.setLineWidth(0.5);
                    pdf.rect(margin, yPos, contentWidth, 12, 'S');
                    
                    pdf.setFontSize(10);
                    pdf.setFont('helvetica', 'normal');
                    setTextColor(colors.black);
                    const startDate = '{{ $startDate }}';
                    const endDate = '{{ $endDate }}';
                    pdf.text(`Periode Laporan: ${formatDate(startDate)} - ${formatDate(endDate)}`, pageWidth / 2, yPos + 7, { align: 'center' });
                    
                    yPos += 20;
                }

                // STATISTICS OVERVIEW SECTION
                function createStatistics() {
                    newPageIfNeeded(50);
                    
                    // Section header with orange accent
                    setFillColor(colors.orange);
                    pdf.rect(margin, yPos, contentWidth, 6, 'F');
                    
                    pdf.setFontSize(12);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.white);
                    pdf.text('RINGKASAN STATISTIK', margin + 5, yPos + 4);
                    
                    yPos += 12;
                    
                    // Statistics cards in clean 2x2 grid
                    const cardWidth = (contentWidth - 10) / 2;
                    const cardHeight = 28;
                    
                    const stats = [
                        { label: 'Total Kunjungan', value: totalVisits.toString(), desc: 'Keseluruhan data' },
                        { label: 'Tingkat Keberhasilan', value: `${totalVisits > 0 ? Math.round((completedVisits / totalVisits) * 100) : 0}%`, desc: 'Rasio penyelesaian' },
                        { label: 'Kunjungan Selesai', value: completedVisits.toString(), desc: 'Sudah diselesaikan' },
                        { label: 'Menunggu Proses', value: pendingVisits.toString(), desc: 'Belum diselesaikan' }
                    ];
                    
                    stats.forEach((stat, index) => {
                        const col = index % 2;
                        const row = Math.floor(index / 2);
                        const x = margin + (col * (cardWidth + 5));
                        const y = yPos + (row * (cardHeight + 8));
                        
                        // Card background
                        setFillColor(colors.white);
                        pdf.rect(x, y, cardWidth, cardHeight, 'F');
                        
                        // Card border
                        setDrawColor(colors.gray);
                        pdf.setLineWidth(0.5);
                        pdf.rect(x, y, cardWidth, cardHeight, 'S');
                        
                        // Orange accent line on top
                        setFillColor(colors.orange);
                        pdf.rect(x, y, cardWidth, 2, 'F');
                        
                        // Content
                        pdf.setFontSize(9);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.gray);
                        pdf.text(stat.label, x + 6, y + 10);
                        
                        pdf.setFontSize(18);
                        pdf.setFont('helvetica', 'bold');
                        setTextColor(colors.black);
                        pdf.text(stat.value, x + 6, y + 20);
                        
                        pdf.setFontSize(8);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.gray);
                        pdf.text(stat.desc, x + 6, y + 25);
                    });
                    
                    yPos += (cardHeight + 8) * 2 + 15;
                }

                // CLEAN CHART SECTION
                function createChart() {
                    if (totalVisits === 0) return;
                    
                    newPageIfNeeded(60);
                    
                    // Section header
                    setFillColor(colors.orange);
                    pdf.rect(margin, yPos, contentWidth, 6, 'F');
                    
                    pdf.setFontSize(12);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.white);
                    pdf.text('DISTRIBUSI STATUS', margin + 5, yPos + 4);
                    
                    yPos += 12;
                    
                    // Chart data with only 3 colors
                    const chartData = [
                        { label: 'Selesai', value: completedVisits, color: colors.black },
                        { label: 'Pending', value: pendingVisits, color: colors.orange },
                        { label: 'Proses', value: inProgressVisits, color: colors.gray }
                    ].filter(item => item.value > 0);
                    
                    // Chart area
                    const chartX = margin + 15;
                    const chartY = yPos + 8;
                    const chartWidth = 100;
                    const chartHeight = 40;
                    
                    // Background for chart
                    setFillColor(colors.lightGray);
                    pdf.rect(chartX - 5, chartY - 5, chartWidth + 10, chartHeight + 15, 'F');
                    
                    // Draw clean bar chart
                    const maxValue = Math.max(...chartData.map(d => d.value));
                    const barWidth = chartWidth / chartData.length - 8;
                    
                    chartData.forEach((data, index) => {
                        const barHeight = (data.value / maxValue) * chartHeight;
                        const x = chartX + (index * (barWidth + 8));
                        const y = chartY + chartHeight - barHeight;
                        
                        // Draw bar
                        setFillColor(data.color);
                        pdf.rect(x, y, barWidth, barHeight, 'F');
                        
                        // Value on top of bar
                        pdf.setFontSize(10);
                        pdf.setFont('helvetica', 'bold');
                        setTextColor(data.color);
                        pdf.text(data.value.toString(), x + barWidth/2, y - 2, { align: 'center' });
                        
                        // Label below bar
                        pdf.setFontSize(8);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.black);
                        pdf.text(data.label, x + barWidth/2, chartY + chartHeight + 8, { align: 'center' });
                    });
                    
                    // Simple legend
                    const legendX = chartX + chartWidth + 20;
                    let legendY = chartY + 8;
                    
                    chartData.forEach((data) => {
                        // Color box
                        setFillColor(data.color);
                        pdf.rect(legendX, legendY - 2, 3, 3, 'F');
                        
                        // Label with percentage
                        pdf.setFontSize(9);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.black);
                        const percentage = Math.round((data.value / totalVisits) * 100);
                        pdf.text(`${data.label}: ${percentage}%`, legendX + 6, legendY);
                        legendY += 6;
                    });
                    
                    yPos += chartHeight + 25;
                }

                // CLEAN TABLE FUNCTION
                function createTable(title, data, isAuthor = true) {
                    if (!data || data.length === 0) return;
                    
                    newPageIfNeeded(50);
                    
                    // Section header
                    setFillColor(colors.orange);
                    pdf.rect(margin, yPos, contentWidth, 6, 'F');
                    
                    pdf.setFontSize(12);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.white);
                    pdf.text(title, margin + 5, yPos + 4);
                    
                    yPos += 12;
                    
                    // Table setup
                    const colWidths = [15, 90, 25, 25];
                    const rowHeight = 8;
                    
                    // Table header
                    setFillColor(colors.lightGray);
                    pdf.rect(margin, yPos, contentWidth, rowHeight, 'F');
                    setDrawColor(colors.gray);
                    pdf.setLineWidth(0.5);
                    pdf.rect(margin, yPos, contentWidth, rowHeight, 'S');
                    
                    // Header text
                    pdf.setFontSize(9);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.black);
                    
                    let currentX = margin;
                    const headers = ['No.', isAuthor ? 'Nama Author' : 'Nama Auditor', 'Total', 'Persen'];
                    headers.forEach((header, i) => {
                        pdf.text(header, currentX + 3, yPos + 5.5);
                        currentX += colWidths[i];
                    });
                    
                    yPos += rowHeight;
                    
                    // Table rows
                    data.slice(0, 8).forEach((item, index) => {
                        currentX = margin;
                        
                        // Alternating row background
                        if (index % 2 === 1) {
                            setFillColor(colors.lightGray);
                            pdf.rect(margin, yPos, contentWidth, rowHeight, 'F');
                        }
                        
                        // Row border
                        setDrawColor(colors.gray);
                        pdf.setLineWidth(0.3);
                        pdf.line(margin, yPos + rowHeight, margin + contentWidth, yPos + rowHeight);
                        
                        const name = isAuthor ? (item.author_name || 'Unknown') : (item.auditor_name || 'Unknown');
                        const cleanName = name.replace(/[^\x20-\x7E]/g, '').substring(0, 30);
                        const percentage = totalVisits > 0 ? ((item.total / totalVisits) * 100).toFixed(1) : '0.0';
                        
                        // Row content
                        pdf.setFontSize(8);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.black);
                        
                        // Rank
                        pdf.text(`${index + 1}.`, currentX + colWidths[0]/2, yPos + 5.5, { align: 'center' });
                        currentX += colWidths[0];
                        
                        // Name
                        pdf.text(cleanName, currentX + 3, yPos + 5.5);
                        currentX += colWidths[1];
                        
                        // Total (highlighted in orange)
                        pdf.setFont('helvetica', 'bold');
                        setTextColor(colors.orange);
                        pdf.text(item.total.toString(), currentX + colWidths[2]/2, yPos + 5.5, { align: 'center' });
                        currentX += colWidths[2];
                        
                        // Percentage
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.gray);
                        pdf.text(`${percentage}%`, currentX + colWidths[3]/2, yPos + 5.5, { align: 'center' });
                        
                        yPos += rowHeight;
                    });
                    
                    yPos += 10;
                }

                // RECENT ACTIVITIES SECTION
                function createActivities() {
                    if (!recentVisits || recentVisits.length === 0) return;
                    
                    newPageIfNeeded(50);
                    
                    // Section header
                    setFillColor(colors.orange);
                    pdf.rect(margin, yPos, contentWidth, 6, 'F');
                    
                    pdf.setFontSize(12);
                    pdf.setFont('helvetica', 'bold');
                    setTextColor(colors.white);
                    pdf.text('AKTIVITAS TERBARU', margin + 5, yPos + 4);
                    
                    yPos += 12;
                    
                    recentVisits.slice(0, 6).forEach((visit, index) => {
                        const visitId = visit.visit_id || `VST${String(visit.id || 0).padStart(4, '0')}`;
                        const authorName = (visit.author_name || 'Unknown').replace(/[^\x20-\x7E]/g, '').substring(0, 20);
                        const auditorName = (visit.auditor_name || 'Unknown').replace(/[^\x20-\x7E]/g, '').substring(0, 20);
                        const status = visit.status === 'selesai' ? 'Selesai' : 'Pending';
                        
                        const cardHeight = 10;
                        
                        // Alternating background
                        if (index % 2 === 1) {
                            setFillColor(colors.lightGray);
                            pdf.rect(margin, yPos, contentWidth, cardHeight, 'F');
                        }
                        
                        // Orange left border
                        setFillColor(colors.orange);
                        pdf.rect(margin, yPos, 2, cardHeight, 'F');
                        
                        // Content
                        pdf.setFontSize(9);
                        pdf.setFont('helvetica', 'bold');
                        setTextColor(colors.black);
                        pdf.text(visitId, margin + 6, yPos + 3);
                        
                        pdf.setFontSize(8);
                        pdf.setFont('helvetica', 'normal');
                        setTextColor(colors.gray);
                        pdf.text(`${authorName} → ${auditorName}`, margin + 6, yPos + 7);
                        
                        // Status
                        const statusColor = status === 'Selesai' ? colors.black : colors.orange;
                        pdf.setFontSize(8);
                        pdf.setFont('helvetica', 'bold');
                        setTextColor(statusColor);
                        pdf.text(status, margin + contentWidth - 20, yPos + 5, { align: 'right' });
                        
                        yPos += cardHeight + 1;
                    });
                }

                // SIMPLE FOOTER
                function addFooter() {
                    const pageNum = pdf.internal.getCurrentPageInfo().pageNumber;
                    
                    // Footer line
                    setDrawColor(colors.gray);
                    pdf.setLineWidth(0.5);
                    pdf.line(margin, pageHeight - 15, pageWidth - margin, pageHeight - 15);
                    
                    // Footer text
                    pdf.setFontSize(8);
                    pdf.setFont('helvetica', 'normal');
                    setTextColor(colors.gray);
                    
                    const currentDate = new Date().toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    
                    pdf.text('Laporan Sistem Kunjungan', margin, pageHeight - 8);
                    pdf.text(`Dibuat: ${currentDate}`, pageWidth / 2, pageHeight - 8, { align: 'center' });
                    pdf.text(`Halaman ${pageNum}`, pageWidth - margin, pageHeight - 8, { align: 'right' });
                }

                // BUILD CLEAN REPORT
                createHeader();
                createStatistics();
                createChart();
                createTable('TOP AUTHOR TERBAIK', visitsByAuthor, true);
                createTable('TOP AUDITOR TERBAIK', visitsByAuditor, false);
                createActivities();
                
                // Add footers to all pages
                const totalPages = pdf.internal.getNumberOfPages();
                for (let page = 1; page <= totalPages; page++) {
                    pdf.setPage(page);
                    addFooter();
                }

                // Generate filename: REPORTS_DD_MM_YYYY.pdf
                const now = new Date();
                const day = String(now.getDate()).padStart(2, '0');
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = now.getFullYear();
                const filename = `REPORTS_${day}_${month}_${year}.pdf`;
                
                pdf.save(filename);

            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Terjadi kesalahan saat menggenerate PDF. Silakan coba lagi.');
            } finally {
                // Hide loading overlay
                const loadingOverlay = document.getElementById('pdfLoadingOverlay');
                loadingOverlay.classList.remove('active');
                
                // Reset button
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                feather.replace();
            }
        }
        
        // Helper functions for clean PDF formatting
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric' 
            });
        }
    </script>
</body>
</html>