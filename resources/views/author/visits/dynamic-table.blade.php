@extends('layouts.admin')

@section('title', 'Daftar Kunjungan - ' . ucfirst(auth()->user()->role))

@push('head')
<meta name="user-role" content="{{ auth()->user()->role }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" x-data="visitTable()">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Daftar Kunjungan</h1>
                        <p class="text-gray-600 mt-1">
                            @if(auth()->user()->role === 'admin')
                                Kelola dan monitor seluruh kunjungan
                            @elseif(auth()->user()->role === 'auditor')
                                Tugas kunjungan yang diberikan kepada Anda
                            @else
                                Riwayat kunjungan yang dilakukan terhadap Anda
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="hidden lg:flex items-center space-x-4">
                    @isset($stats)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                            <div class="text-sm text-blue-600 font-medium">Total</div>
                            <div class="text-2xl font-bold text-blue-700">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        @if(auth()->user()->role === 'admin')
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                                <div class="text-sm text-yellow-600 font-medium">Pending</div>
                                <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] ?? 0 }}</div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                                <div class="text-sm text-green-600 font-medium">Selesai</div>
                                <div class="text-2xl font-bold text-green-700">{{ $stats['completed'] ?? 0 }}</div>
                            </div>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters and Actions -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4">
                    @if(auth()->user()->role === 'admin')
                        <!-- Auditor Filter -->
                        <select x-model="filters.auditor_filter" @change="applyFilters()" 
                                class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Auditor</option>
                            @isset($auditors)
                                @foreach($auditors as $auditor)
                                    <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    @endif
                    
                    <!-- Status Filter -->
                    <select x-model="filters.status_filter" @change="applyFilters()"
                            class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        @isset($statuses)
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        @endisset
                    </select>
                    
                    <!-- Date Filter -->
                    <input type="date" x-model="filters.date_filter" @change="applyFilters()"
                           class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Search and Actions -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" x-model="filters.search" @input.debounce.500ms="applyFilters()"
                               placeholder="@if(auth()->user()->role === 'author')Cari auditor...@else Cari author...@endif" 
                               class="border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-80">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    
                    @if(auth()->user()->role === 'admin')
                        <!-- Add New Visit Button -->
                        <a href="{{ route('admin.visits.create') }}" 
                           class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 whitespace-nowrap">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Kunjungan
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dynamic Table -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            
                            @if(auth()->user()->role === 'admin')
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auditor</th>
                            @elseif(auth()->user()->role === 'auditor')
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            @else
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auditor</th>
                            @endif
                            
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            
                            @if(auth()->user()->role === 'author')
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Undur</th>
                            @endif
                            
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
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-blue-500 transition ease-in-out duration-150">
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
    </main>

    <!-- Detail Modal -->
    <div x-show="showModal" x-cloak @click.away="closeModal()" 
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <!-- Modal -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Detail Kunjungan
                        </h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal content will be loaded here dynamically -->
                    <div id="modal-content" class="space-y-6">
                        <!-- Loading spinner -->
                        <div class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function visitTable() {
        return {
            showModal: false,
            loading: false,
            filters: {
                auditor_filter: '',
                status_filter: '',
                date_filter: '',
                search: ''
            },
            
            applyFilters() {
                this.loading = true;
                
                // Build query string
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params.append(key, this.filters[key]);
                    }
                });
                
                fetch(`${window.location.pathname}?${params.toString()}`, {
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
            
            showDetail(visitId) {
                // Use the global viewVisit function which handles correct routes for each role
                if (typeof viewVisit === 'function') {
                    viewVisit(visitId);
                } else {
                    // Fallback - determine the correct route based on user role
                    const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
                    let detailUrl;
                    
                    if (userRole === 'admin') {
                        detailUrl = `/admin/visits/${visitId}`;
                    } else if (userRole === 'author') {
                        detailUrl = `/author/visits/${visitId}/detail`;
                    } else if (userRole === 'auditor') {
                        detailUrl = `/auditor/visits/${visitId}/detail`;
                    } else {
                        detailUrl = `/visits/${visitId}`;
                    }
                    
                    this.showModal = true;
                    
                    fetch(detailUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('modal-content').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('modal-content').innerHTML = '<p class="text-red-500">Error loading details</p>';
                    });
                }
            },
            
            closeModal() {
                this.showModal = false;
                // Clear modal content after transition
                setTimeout(() => {
                    document.getElementById('modal-content').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    `;
                }, 300);
            },

            refreshTable() {
                this.applyFilters();
            }
        }
    }

    // Global function to refresh table (called from modal)
    window.refreshTable = function() {
        // Find the Alpine component and call its refresh method
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
            modalScript.onload = function() {
                // Initialize modal after script loads
                if (window.visitModal) {
                    window.visitModal.init();
                }
            };
            document.head.appendChild(modalScript);
        }
        
        // Load visit table script
        const tableScript = document.createElement('script');
        tableScript.src = '{{ asset("js/visit-table.js") }}';
        document.head.appendChild(tableScript);
    });
</script>

<!-- Include workflow modals -->
@include('visits.workflow-modals')

@endsection