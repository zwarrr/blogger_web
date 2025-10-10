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
                                    <p class="text-sm font-medium text-gray-900 truncate">#{{ $visit->id ?? 'N/A' }}</p>
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

        // PDF Export Function
        async function exportToPDF() {
            const { jsPDF } = window.jspdf;
            
            // Show loading
            document.body.style.cursor = 'wait';
            const exportBtn = document.querySelector('button[onclick="exportToPDF()"]');
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i>Menggenerate PDF...';
            exportBtn.disabled = true;
            
            try {
                // Get current date range from form inputs
                const startDate = document.getElementById('start_date')?.value || '{{ $startDate }}';
                const endDate = document.getElementById('end_date')?.value || '{{ $endDate }}';
                
                // Fetch fresh data from server
                const url = new URL('{{ route('admin.visits.export-pdf') }}');
                if (startDate) url.searchParams.append('start_date', startDate);
                if (endDate) url.searchParams.append('end_date', endDate);
                
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error('Gagal mengambil data laporan dari server');
                }
                
                const data = await response.json();
                
                // Initialize PDF
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                let yPosition = 20;
                
                // Helper function to check if new page is needed
                function checkAddPage(requiredSpace = 20) {
                    if (yPosition > pageHeight - requiredSpace) {
                        pdf.addPage();
                        yPosition = 20;
                        return true;
                    }
                    return false;
                }
                
                // Header
                pdf.setFontSize(18);
                pdf.setFont(undefined, 'bold');
                pdf.text('Laporan Kunjungan Auditor', pageWidth / 2, yPosition, { align: 'center' });
                yPosition += 10;
                
                // Date range
                pdf.setFontSize(12);
                pdf.setFont(undefined, 'normal');
                pdf.text(`Periode: ${formatDate(data.startDate)} - ${formatDate(data.endDate)}`, pageWidth / 2, yPosition, { align: 'center' });
                yPosition += 15;
                
                // Statistics Summary
                pdf.setFontSize(14);
                pdf.setFont(undefined, 'bold');
                pdf.text('Ringkasan Statistik', 20, yPosition);
                yPosition += 10;
                
                pdf.setFontSize(11);
                pdf.setFont(undefined, 'normal');
                
                const stats = [
                    ['Total Kunjungan', data.totalVisits.toLocaleString()],
                    ['Belum Dikunjungi', `${data.pendingVisits.toLocaleString()} (${data.totalVisits > 0 ? Math.round((data.pendingVisits / data.totalVisits) * 100) : 0}%)`],
                    ['Kunjungan Selesai', `${data.completedVisits.toLocaleString()} (${data.totalVisits > 0 ? Math.round((data.completedVisits / data.totalVisits) * 100) : 0}%)`],
                    ['Tingkat Keberhasilan', `${data.successRate}%`]
                ];
                
                stats.forEach(([label, value]) => {
                    pdf.text(`• ${label}: ${value}`, 25, yPosition);
                    yPosition += 7;
                });
                
                yPosition += 10;
                
                // Top Authors
                if (data.visitsByAuthor && data.visitsByAuthor.length > 0) {
                    checkAddPage(50);
                    pdf.setFontSize(14);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('Top 5 Author Terbanyak', 20, yPosition);
                    yPosition += 10;
                    
                    pdf.setFontSize(11);
                    pdf.setFont(undefined, 'normal');
                    
                    data.visitsByAuthor.forEach((author, index) => {
                        const name = author.author?.name || author.author_name || 'Author tidak tersedia';
                        pdf.text(`${index + 1}. ${name}: ${author.total} kunjungan`, 25, yPosition);
                        yPosition += 7;
                    });
                    yPosition += 10;
                }
                
                // Top Auditors
                if (data.visitsByAuditor && data.visitsByAuditor.length > 0) {
                    checkAddPage(50);
                    pdf.setFontSize(14);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('Top 5 Auditor Teraktif', 20, yPosition);
                    yPosition += 10;
                    
                    pdf.setFontSize(11);
                    pdf.setFont(undefined, 'normal');
                    
                    data.visitsByAuditor.forEach((auditor, index) => {
                        const name = auditor.auditor?.name || auditor.auditor_name || 'Auditor tidak tersedia';
                        pdf.text(`${index + 1}. ${name}: ${auditor.total} kunjungan`, 25, yPosition);
                        yPosition += 7;
                    });
                    yPosition += 10;
                }
                
                // Recent Activities
                if (data.recentVisits && data.recentVisits.length > 0) {
                    checkAddPage(80);
                    
                    pdf.setFontSize(14);
                    pdf.setFont(undefined, 'bold');
                    pdf.text('Aktivitas Terbaru', 20, yPosition);
                    yPosition += 10;
                    
                    pdf.setFontSize(10);
                    pdf.setFont(undefined, 'normal');
                    
                    data.recentVisits.slice(0, 10).forEach((visit, index) => {
                        checkAddPage(25);
                        
                        const authorName = visit.author?.name || 'Author tidak tersedia';
                        const auditorName = visit.auditor?.name || '';
                        const status = formatStatus(visit.status);
                        const visitDate = new Date(visit.visit_date).toLocaleDateString('id-ID');
                        
                        pdf.text(`#${visit.id} - ${authorName}${auditorName ? ' ← ' + auditorName : ''}`, 25, yPosition);
                        yPosition += 5;
                        pdf.text(`Status: ${status} | Tanggal: ${visitDate}`, 30, yPosition);
                        yPosition += 8;
                    });
                }
                
                // Footer on all pages
                const totalPages = pdf.internal.getNumberOfPages();
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    pdf.setFontSize(8);
                    pdf.setFont(undefined, 'normal');
                    pdf.text(`Dibuat pada ${data.generatedAt} | Halaman ${i} dari ${totalPages}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
                }
                
                // Save the PDF
                const filename = `Laporan_Kunjungan_${data.startDate}_to_${data.endDate}.pdf`;
                pdf.save(filename);
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Terjadi kesalahan saat menggenerate PDF: ' + error.message);
            } finally {
                // Reset button
                document.body.style.cursor = 'default';
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                feather.replace(); // Re-initialize icons
            }
        }
        
        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric' 
            });
        }
        
        function formatDateTime(dateString) {
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
            return statusMap[status] || status;
        }
    </script>
</body>
</html>