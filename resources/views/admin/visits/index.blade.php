@extends('layouts.admin')

@section('title', 'Daftar Kunjungan - Admin')

@push('head')
<meta name="user-role" content="{{ auth()->user()->role }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- Main Content Area -->
<main class="flex-1">
    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Kunjungan</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan monitor seluruh kunjungan auditor</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.visits.create') }}" 
                   class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kunjungan</span>
                </a>
                <div class="text-sm text-gray-500">
                    {{ now()->format('l, d F Y') }}
                </div>
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

    <!-- Main Content -->
    <div class="p-6" x-data="visitTable()">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            @isset($stats)
                <!-- Total Kunjungan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Kunjungan</p>
                            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Semua kunjungan</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Belum Dikunjungi -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Belum Dikunjungi</p>
                            <p class="text-2xl font-bold text-gray-600 mt-2">{{ $stats['belum_dikunjungi'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Belum dijalankan</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Dalam Perjalanan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Dalam Perjalanan</p>
                            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $stats['dalam_perjalanan'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Sedang berlangsung</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Menunggu ACC -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Menunggu ACC</p>
                            <p class="text-2xl font-bold text-orange-600 mt-2">{{ $stats['menunggu_acc'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Perlu persetujuan</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Selesai -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Selesai</p>
                            <p class="text-2xl font-bold text-green-600 mt-2">{{ $stats['selesai'] ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Telah selesai</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endisset
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Auditor Filter -->
                    <select x-model="filters.auditor_filter" @change="applyFilters()"
                            class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Auditor</option>
                        @isset($auditors)
                            @foreach($auditors as $auditor)
                                <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                            @endforeach
                        @endisset
                    </select>

                    <!-- Status Filter -->
                    <select x-model="filters.status_filter" @change="applyFilters()"
                            class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Semua Status</option>
                        @isset($statuses)
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        @endisset
                    </select>
                    
                    <!-- Date Filter -->
                    <input type="date" x-model="filters.date_filter" @change="applyFilters()"
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()"
                           placeholder="Cari author..." 
                           class="border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-full sm:w-80">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auditor</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="bg-white divide-y divide-gray-200">
                        @include('visits.table-rows')
                    </tbody>
                </table>
            </div>
            
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-8">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-primary-500 transition ease-in-out duration-150">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang memuat...
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="mt-8">
            {{ $visits->appends(request()->query())->links() }}
        </div>
    </div>
</main>

@push('scripts')
<script>
    function visitTable() {
        return {
            loading: false,
            showModal: false,
            filters: {
                auditor_filter: '',
                status_filter: '',
                date_filter: '',
                search: ''
            },

            applyFilters() {
                this.loading = true;
                
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.set(key, this.filters[key]);
                    }
                });
                
                const url = `${window.location.pathname}?${params.toString()}`;
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('table-body').innerHTML = data.html;  
                    document.getElementById('pagination').innerHTML = data.pagination;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
            },

            refreshTable() {
                this.applyFilters();
            }
        }
    }

    // Global function to refresh table (called from modal)
    window.refreshTable = function() {
        const tableComponent = document.querySelector('[x-data="visitTable()"]');
        if (tableComponent && tableComponent._x_dataStack) {
            const data = tableComponent._x_dataStack[0];
            if (data && typeof data.refreshTable === 'function') {
                data.refreshTable();
            }
        }
    };

    // Include visit scripts after DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Load visit modal script
        if (!document.getElementById('visitModal')) {
            const modalScript = document.createElement('script');
            modalScript.src = '{{ asset("js/visit-modal.js") }}';
            document.head.appendChild(modalScript);
        }
        
        // Load visit table script
        const tableScript = document.createElement('script');
        tableScript.src = '{{ asset("js/visit-table.js") }}';
        document.head.appendChild(tableScript);
    });
</script>
@endpush
@endsection