<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Statistik Kunjungan - Auditor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        @include('auditor.sidebar')

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Statistik Kunjungan</h1>
                        <p class="mt-1 text-sm text-gray-600">Analisis data kunjungan dalam bentuk grafik dan tabel</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Session Messages -->
            @if(session('success'))
                <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Statistics Content -->
            <div class="p-6">
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Kunjungan -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Kunjungan</p>
                                <p class="text-3xl font-bold text-primary-600 mt-2">{{ $totalVisits ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Semua waktu</p>
                            </div>
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Pending</p>
                                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pendingVisits ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Butuh review</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dikonfirmasi -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Dikonfirmasi</p>
                                <p class="text-3xl font-bold text-primary-600 mt-2">{{ $confirmedVisits ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Sudah dikonfirmasi</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Selesai</p>
                                <p class="text-3xl font-bold text-green-600 mt-2">{{ $completedVisits ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Telah selesai</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Monthly Statistics Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Statistik Bulanan {{ now()->year }}</h3>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                <span class="text-xs text-gray-600">Kunjungan</span>
                            </div>
                        </div>
                        <div class="h-80">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>

                    <!-- Status Distribution Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Distribusi Status</h3>
                            <div class="text-xs text-gray-500">Total: {{ $totalVisits ?? 0 }}</div>
                        </div>
                        <div class="h-80 flex items-center justify-center">
                            @if(($totalVisits ?? 0) > 0)
                                <canvas id="statusChart"></canvas>
                            @else
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Belum ada data</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Data</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Monthly Breakdown -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Data per Bulan ({{ now()->year }})</h4>
                                <div class="space-y-3">
                                    @php
                                        $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                    @endphp
                                    
                                    @foreach($months as $num => $month)
                                        @php $count = $monthlyStats[$num] ?? 0; @endphp
                                        <div class="flex items-center justify-between py-2">
                                            <span class="text-sm text-gray-600">{{ $month }}</span>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $totalVisits > 0 ? ($count / max(array_values($monthlyStats ?? [1]))) * 100 : 0 }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 w-8 text-right">{{ $count }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Status Breakdown -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Breakdown Status</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-gray-900">Pending</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-yellow-600">{{ $pendingVisits ?? 0 }}</span>
                                            <p class="text-xs text-gray-500">
                                                {{ $totalVisits > 0 ? round((($pendingVisits ?? 0) / $totalVisits) * 100, 1) : 0 }}%
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-gray-900">Dikonfirmasi</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-primary-600">{{ $confirmedVisits ?? 0 }}</span>
                                            <p class="text-xs text-gray-500">
                                                {{ $totalVisits > 0 ? round((($confirmedVisits ?? 0) / $totalVisits) * 100, 1) : 0 }}%
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <span class="text-sm font-medium text-gray-900">Selesai</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-green-600">{{ $completedVisits ?? 0 }}</span>
                                            <p class="text-xs text-gray-500">
                                                {{ $totalVisits > 0 ? round((($completedVisits ?? 0) / $totalVisits) * 100, 1) : 0 }}%
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Kunjungan',
                        data: @json(array_values($monthlyStats ?? array_fill(1, 12, 0))),
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#f97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx && {{ $totalVisits ?? 0 }} > 0) {
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Dikonfirmasi', 'Selesai'],
                    datasets: [{
                        data: [{{ $pendingVisits ?? 0 }}, {{ $confirmedVisits ?? 0 }}, {{ $completedVisits ?? 0 }}],
                        backgroundColor: ['#eab308', '#f97316', '#10b981'],
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
