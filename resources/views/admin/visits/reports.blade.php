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
        
        /* PDF Export Loading Overlay */
        .pdf-loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
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
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .pdf-loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top: 4px solid #ea580c;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

    <!-- PDF Loading Overlay -->
    <div class="pdf-loading-overlay" id="pdfLoadingOverlay">
        <div class="pdf-loading-content">
            <div class="pdf-loading-spinner"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Menggenerate Laporan PDF</h3>
            <p class="text-sm text-gray-600">Mohon tunggu, sedang memproses data laporan...</p>
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

        // PDF Export Function - Enhanced Professional Styling
        async function exportToPDF() {
            const { jsPDF } = window.jspdf;
            
            // Show enhanced loading overlay
            const loadingOverlay = document.getElementById('pdfLoadingOverlay');
            loadingOverlay.classList.add('active');
            
            // Update button state
            const exportBtn = document.querySelector('button[onclick="exportToPDF()"]');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';
            exportBtn.disabled = true;
            
            try {
                // Get current date range from inputs
                const startDateInput = document.querySelector('input[name="start_date"]');
                const endDateInput = document.querySelector('input[name="end_date"]');
                const startDate = startDateInput?.value || '{{ $startDate }}';
                const endDate = endDateInput?.value || '{{ $endDate }}';
                
                // Get data from current page
                const totalVisits = {{ $totalVisits ?? 0 }};
                const pendingVisits = {{ $pendingVisits ?? 0 }};
                const completedVisits = {{ $completedVisits ?? 0 }};
                const visitsByAuthor = @json($visitsByAuthor ?? []);
                const visitsByAuditor = @json($visitsByAuditor ?? []);
                const recentVisits = @json($recentVisits ?? []);
                
                // Initialize PDF with professional settings
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const margin = 20;
                const contentWidth = pageWidth - (margin * 2);
                let yPosition = margin;
                
                // Color scheme
                const colors = {
                    primary: [51, 65, 85],      // slate-700
                    secondary: [100, 116, 139], // slate-500
                    accent: [234, 88, 12],      // orange-600
                    light: [248, 250, 252],     // slate-50
                    success: [34, 197, 94],     // green-500
                    warning: [245, 158, 11],    // amber-500
                    danger: [239, 68, 68]       // red-500
                };
                
                // Helper functions
                function addNewPageIfNeeded(requiredSpace = 25) {
                    if (yPosition > pageHeight - margin - requiredSpace) {
                        pdf.addPage();
                        yPosition = margin;
                        return true;
                    }
                    return false;
                }
                
                function drawLine(x1, y1, x2, y2, color = colors.secondary, width = 0.5) {
                    pdf.setDrawColor(...color);
                    pdf.setLineWidth(width);
                    pdf.line(x1, y1, x2, y2);
                }
                
                function drawRect(x, y, width, height, fillColor = null, strokeColor = null) {
                    if (fillColor) {
                        pdf.setFillColor(...fillColor);
                        pdf.rect(x, y, width, height, 'F');
                    }
                    if (strokeColor) {
                        pdf.setDrawColor(...strokeColor);
                        pdf.setLineWidth(0.5);
                        pdf.rect(x, y, width, height, 'S');
                    }
                }
                
                function addText(text, x, y, options = {}) {
                    const {
                        fontSize = 12,
                        fontStyle = 'normal',
                        color = colors.primary,
                        align = 'left',
                        maxWidth = null
                    } = options;
                    
                    pdf.setFontSize(fontSize);
                    pdf.setFont('helvetica', fontStyle);
                    pdf.setTextColor(...color);
                    
                    if (maxWidth) {
                        const lines = pdf.splitTextToSize(text, maxWidth);
                        pdf.text(lines, x, y, { align });
                        return lines.length * (fontSize * 0.352778); // Convert pt to mm
                    } else {
                        pdf.text(text, x, y, { align });
                        return fontSize * 0.352778;
                    }
                }
                
                // HEADER SECTION WITH LOGO AREA
                drawRect(margin, yPosition, contentWidth, 25, colors.light);
                drawRect(margin, yPosition, contentWidth, 25, null, colors.secondary);
                
                // Company/Institution Header
                addText('SISTEM MANAJEMEN KUNJUNGAN AUDITOR', pageWidth / 2, yPosition + 8, {
                    fontSize: 16,
                    fontStyle: 'bold',
                    color: colors.primary,
                    align: 'center'
                });
                
                addText('Laporan Komprehensif Aktivitas Audit', pageWidth / 2, yPosition + 15, {
                    fontSize: 12,
                    color: colors.secondary,
                    align: 'center'
                });
                
                addText(`Periode: ${formatDate(startDate)} - ${formatDate(endDate)}`, pageWidth / 2, yPosition + 21, {
                    fontSize: 10,
                    color: colors.secondary,
                    align: 'center'
                });
                
                yPosition += 35;
                
                // EXECUTIVE SUMMARY SECTION
                addNewPageIfNeeded(60);
                addText('RINGKASAN EKSEKUTIF', margin, yPosition, {
                    fontSize: 14,
                    fontStyle: 'bold',
                    color: colors.primary
                });
                
                yPosition += 8;
                drawLine(margin, yPosition, pageWidth - margin, yPosition, colors.accent, 2);
                yPosition += 10;
                
                // Statistics cards
                const statsData = [
                    {
                        label: 'Total Kunjungan',
                        value: totalVisits.toLocaleString('id-ID'),
                        subtitle: 'Keseluruhan aktivitas audit',
                        color: colors.primary
                    },
                    {
                        label: 'Belum Dikunjungi',
                        value: `${pendingVisits.toLocaleString('id-ID')} (${totalVisits > 0 ? Math.round((pendingVisits / totalVisits) * 100) : 0}%)`,
                        subtitle: 'Menunggu tindak lanjut',
                        color: colors.warning
                    },
                    {
                        label: 'Kunjungan Selesai',
                        value: `${completedVisits.toLocaleString('id-ID')} (${totalVisits > 0 ? Math.round((completedVisits / totalVisits) * 100) : 0}%)`,
                        subtitle: 'Telah diselesaikan',
                        color: colors.success
                    },
                    {
                        label: 'Tingkat Keberhasilan',
                        value: `${totalVisits > 0 ? Math.round((completedVisits / totalVisits) * 100) : 0}%`,
                        subtitle: 'Efektivitas audit',
                        color: colors.accent
                    }
                ];
                
                // Draw statistics in 2x2 grid
                const cardWidth = (contentWidth - 10) / 2;
                const cardHeight = 25;
                
                for (let i = 0; i < statsData.length; i++) {
                    const col = i % 2;
                    const row = Math.floor(i / 2);
                    const x = margin + (col * (cardWidth + 10));
                    const y = yPosition + (row * (cardHeight + 5));
                    
                    // Card background
                    drawRect(x, y, cardWidth, cardHeight, [255, 255, 255]);
                    drawRect(x, y, cardWidth, cardHeight, null, colors.secondary);
                    
                    // Colored accent bar
                    drawRect(x, y, 3, cardHeight, statsData[i].color);
                    
                    // Content
                    addText(statsData[i].label, x + 8, y + 8, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.secondary
                    });
                    
                    addText(statsData[i].value, x + 8, y + 15, {
                        fontSize: 14,
                        fontStyle: 'bold',
                        color: statsData[i].color
                    });
                    
                    addText(statsData[i].subtitle, x + 8, y + 21, {
                        fontSize: 8,
                        color: colors.secondary
                    });
                }
                
                yPosition += 60;
                
                // PERFORMANCE ANALYSIS SECTION
                if (visitsByAuthor && visitsByAuthor.length > 0) {
                    addNewPageIfNeeded(80);
                    
                    addText('ANALISIS KINERJA AUTHOR', margin, yPosition, {
                        fontSize: 14,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    yPosition += 8;
                    drawLine(margin, yPosition, pageWidth - margin, yPosition, colors.accent, 2);
                    yPosition += 15;
                    
                    // Table header
                    drawRect(margin, yPosition, contentWidth, 8, colors.light);
                    drawRect(margin, yPosition, contentWidth, 8, null, colors.secondary);
                    
                    addText('Ranking', margin + 5, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Nama Author', margin + 25, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Jumlah Kunjungan', pageWidth - margin - 35, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Persentase', pageWidth - margin - 5, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary,
                        align: 'right'
                    });
                    
                    yPosition += 8;
                    
                    // Table rows
                    visitsByAuthor.slice(0, 10).forEach((author, index) => {
                        const rowHeight = 8;
                        const isEven = index % 2 === 0;
                        
                        if (isEven) {
                            drawRect(margin, yPosition, contentWidth, rowHeight, [249, 250, 251]);
                        }
                        
                        const authorName = (author.author && author.author.name) || author.author_name || 'Author tidak tersedia';
                        const percentage = totalVisits > 0 ? ((author.total / totalVisits) * 100).toFixed(1) : '0.0';
                        
                        addText((index + 1).toString(), margin + 5, yPosition + 5, {
                            fontSize: 9,
                            color: colors.secondary
                        });
                        
                        addText(authorName, margin + 25, yPosition + 5, {
                            fontSize: 9,
                            color: colors.primary,
                            maxWidth: 80
                        });
                        
                        addText(author.total.toLocaleString('id-ID'), pageWidth - margin - 35, yPosition + 5, {
                            fontSize: 9,
                            fontStyle: 'bold',
                            color: colors.accent
                        });
                        
                        addText(`${percentage}%`, pageWidth - margin - 5, yPosition + 5, {
                            fontSize: 9,
                            color: colors.secondary,
                            align: 'right'
                        });
                        
                        yPosition += rowHeight;
                    });
                    
                    yPosition += 10;
                }
                
                // AUDITOR PERFORMANCE SECTION
                if (visitsByAuditor && visitsByAuditor.length > 0) {
                    addNewPageIfNeeded(80);
                    
                    addText('ANALISIS KINERJA AUDITOR', margin, yPosition, {
                        fontSize: 14,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    yPosition += 8;
                    drawLine(margin, yPosition, pageWidth - margin, yPosition, colors.accent, 2);
                    yPosition += 15;
                    
                    // Table header
                    drawRect(margin, yPosition, contentWidth, 8, colors.light);
                    drawRect(margin, yPosition, contentWidth, 8, null, colors.secondary);
                    
                    addText('Ranking', margin + 5, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Nama Auditor', margin + 25, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Jumlah Audit', pageWidth - margin - 35, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    addText('Produktivitas', pageWidth - margin - 5, yPosition + 5, {
                        fontSize: 10,
                        fontStyle: 'bold',
                        color: colors.primary,
                        align: 'right'
                    });
                    
                    yPosition += 8;
                    
                    // Table rows
                    visitsByAuditor.slice(0, 10).forEach((auditor, index) => {
                        const rowHeight = 8;
                        const isEven = index % 2 === 0;
                        
                        if (isEven) {
                            drawRect(margin, yPosition, contentWidth, rowHeight, [249, 250, 251]);
                        }
                        
                        const auditorName = (auditor.auditor && auditor.auditor.name) || auditor.auditor_name || 'Auditor tidak tersedia';
                        const productivity = auditor.total > 10 ? 'Tinggi' : auditor.total > 5 ? 'Sedang' : 'Rendah';
                        const productivityColor = auditor.total > 10 ? colors.success : auditor.total > 5 ? colors.warning : colors.danger;
                        
                        addText((index + 1).toString(), margin + 5, yPosition + 5, {
                            fontSize: 9,
                            color: colors.secondary
                        });
                        
                        addText(auditorName, margin + 25, yPosition + 5, {
                            fontSize: 9,
                            color: colors.primary,
                            maxWidth: 80
                        });
                        
                        addText(auditor.total.toLocaleString('id-ID'), pageWidth - margin - 35, yPosition + 5, {
                            fontSize: 9,
                            fontStyle: 'bold',
                            color: colors.accent
                        });
                        
                        addText(productivity, pageWidth - margin - 5, yPosition + 5, {
                            fontSize: 9,
                            fontStyle: 'bold',
                            color: productivityColor,
                            align: 'right'
                        });
                        
                        yPosition += rowHeight;
                    });
                    
                    yPosition += 15;
                }
                
                // RECENT ACTIVITIES SECTION
                if (recentVisits && recentVisits.length > 0) {
                    addNewPageIfNeeded(80);
                    
                    addText('AKTIVITAS TERBARU', margin, yPosition, {
                        fontSize: 14,
                        fontStyle: 'bold',
                        color: colors.primary
                    });
                    
                    yPosition += 8;
                    drawLine(margin, yPosition, pageWidth - margin, yPosition, colors.accent, 2);
                    yPosition += 15;
                    
                    recentVisits.slice(0, 15).forEach((visit, index) => {
                        addNewPageIfNeeded(20);
                        
                        const visitId = visit.visit_id || `VST${String(visit.id || 0).padStart(4, '0')}`;
                        const authorName = (visit.author && visit.author.name) || visit.author_name || 'Author tidak tersedia';
                        const auditorName = (visit.auditor && visit.auditor.name) || visit.auditor_name || 'Belum ditentukan';
                        const status = formatStatus(visit.status || 'unknown');
                        const visitDate = visit.visit_date ? new Date(visit.visit_date).toLocaleDateString('id-ID') : 'Tanggal tidak tersedia';
                        
                        // Visit card
                        drawRect(margin, yPosition, contentWidth, 15, [255, 255, 255]);
                        drawRect(margin, yPosition, contentWidth, 15, null, [229, 231, 235]);
                        
                        // Status indicator
                        const statusColor = getStatusColor(visit.status);
                        drawRect(margin, yPosition, 3, 15, statusColor);
                        
                        // Visit ID and Date
                        addText(`${visitId} - ${visitDate}`, margin + 8, yPosition + 6, {
                            fontSize: 10,
                            fontStyle: 'bold',
                            color: colors.primary
                        });
                        
                        // Author and Auditor info
                        addText(`Author: ${authorName} | Auditor: ${auditorName}`, margin + 8, yPosition + 11, {
                            fontSize: 9,
                            color: colors.secondary
                        });
                        
                        // Status
                        addText(status, pageWidth - margin - 5, yPosition + 9, {
                            fontSize: 9,
                            fontStyle: 'bold',
                            color: statusColor,
                            align: 'right'
                        });
                        
                        yPosition += 18;
                    });
                }
                
                // FOOTER AND METADATA
                const currentDate = new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                // Add footer to all pages
                const totalPages = pdf.internal.getNumberOfPages();
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    
                    // Footer line
                    drawLine(margin, pageHeight - 20, pageWidth - margin, pageHeight - 20, colors.secondary, 0.5);
                    
                    // Footer text
                    addText(`Laporan dibuat pada: ${currentDate}`, margin, pageHeight - 12, {
                        fontSize: 8,
                        color: colors.secondary
                    });
                    
                    addText(`Halaman ${i} dari ${totalPages}`, pageWidth - margin, pageHeight - 12, {
                        fontSize: 8,
                        color: colors.secondary,
                        align: 'right'
                    });
                    
                    addText('Sistem Manajemen Kunjungan Auditor - Confidential', pageWidth / 2, pageHeight - 8, {
                        fontSize: 7,
                        color: colors.secondary,
                        align: 'center'
                    });
                }
                
                // Save the PDF with professional naming
                const filename = `Laporan_Audit_${startDate.replace(/-/g, '')}_${endDate.replace(/-/g, '')}.pdf`;
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
        
        // Helper function for status colors
        function getStatusColor(status) {
            const statusColors = {
                'belum_dikunjungi': [245, 158, 11],   // amber-500
                'dikonfirmasi': [59, 130, 246],       // blue-500
                'dalam_perjalanan': [139, 92, 246],   // violet-500
                'sedang_dikunjungi': [168, 85, 247],  // purple-500
                'selesai': [34, 197, 94],             // green-500
                'menunggu_acc': [249, 115, 22]        // orange-500
            };
            return statusColors[status] || [107, 114, 128]; // gray-500
        }
        
        // Enhanced Helper functions for professional PDF formatting
        function formatDate(dateString) {
            if (!dateString) return 'Tidak diketahui';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric' 
            });
        }
        
        function formatDateTime(dateString) {
            if (!dateString) return 'Tidak diketahui';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function formatStatus(status) {
            const statusMap = {
                'belum_dikunjungi': 'Belum Dikunjungi',
                'dikonfirmasi': 'Dikonfirmasi',
                'dalam_perjalanan': 'Dalam Perjalanan',
                'sedang_dikunjungi': 'Sedang Dikunjungi',
                'selesai': 'Selesai',
                'menunggu_acc': 'Menunggu ACC'
            };
            return statusMap[status] || 'Status Tidak Diketahui';
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
        
        function capitalizeWords(str) {
            if (!str) return '';
            return str.split(' ')
                     .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                     .join(' ');
        }
    </script>
</body>
</html>