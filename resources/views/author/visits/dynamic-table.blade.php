<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kunjungan - Author Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ auth()->user()->role }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Page transition animation */
        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .page-transition.active {
            opacity: 1;
            visibility: visible;
        }
        
        .spinner-circle {
            stroke-dasharray: 90, 150;
            stroke-dashoffset: 0;
            animation: spin 1.5s linear infinite;
        }
        
        @keyframes spin {
            0% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: 0;
            }
            50% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -35;
            }
            100% {
                stroke-dasharray: 90, 150;
                stroke-dashoffset: -124;
            }
        }
        
        .loading-text {
            margin-top: 20px;
            color: white;
            font-size: 18px;
            font-weight: 500;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        
        /* Smart dropdown positioning */
        .dropdown-menu {
            transition: all 0.2s ease;
            transform-origin: top right;
            animation: dropdownFadeIn 0.15s ease-out;
        }
        
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-5px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .dropdown-item {
            transition: all 0.15s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8fafc;
            padding-left: 1rem;
        }
        
        /* Dropdown Positioning Fix */
        .visits-table-container {
            overflow: visible !important;
        }
        
        .table-overflow-wrapper {
            overflow: visible !important;
        }
        
        /* Force all containers to allow dropdown visibility */
        main, 
        .p-6,
        .bg-white.rounded-lg.shadow-sm.border.border-gray-200,
        .table-container,
        .overflow-x-auto,
        table,
        tbody,
        tr,
        td {
            overflow: visible !important;
            position: relative !important;
        }
        
        /* Dropdown positioning */
        .relative {
            position: relative !important;
        }
        
        [id^="dropdown-"] {
            position: fixed !important;
            z-index: 99999 !important;
            min-width: 180px;
            max-width: 220px;
            white-space: nowrap;
            background: white !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        /* Remove all outlines and borders from dropdown elements */
        [id^="dropdown-"] button,
        [id^="dropdown-"] a {
            outline: none !important;
            border: none !important;
        }
        
        [id^="dropdown-"] button:focus,
        [id^="dropdown-"] a:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        /* Force parent containers to not clip dropdowns */
        .ml-64 {
            overflow: visible !important;
        }
        
        /* Ensure table cells don't clip dropdowns */
        td.px-6.py-4.whitespace-nowrap.text-sm.font-medium {
            overflow: visible !important;
            position: relative !important;
        }
        
        /* Improved button and form styling */
        .form-input {
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 font-sans antialiased">
    @include('author.sidebar')

    <div class="ml-64 min-h-screen">
        <!-- Page Transition Overlay -->
        <div class="page-transition" id="pageTransition">
            <svg class="w-24 h-24 text-orange-600" viewBox="0 0 50 50" fill="none">
                <circle class="spinner-circle" cx="25" cy="25" r="20" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <p class="loading-text">Loading...</p>
        </div>

        <main class="flex-1">
            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i data-feather="clipboard-list" class="w-6 h-6 mr-3 text-orange-600"></i>
                            Daftar Kunjungan
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">Riwayat kunjungan auditor yang dilakukan kepada Anda</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-6 mt-6 mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-6 mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="alert-circle" class="w-5 h-5 mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="p-6" x-data="visitTable()">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @isset($stats)
                        <!-- Total Kunjungan -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="clipboard-list" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Total Kunjungan</h3>
                                </div>
                                <span class="text-xs text-green-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2">{{ number_format($stats['total'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total keseluruhan</p>
                        </div>

                        <!-- Belum Dikunjungi -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="clock" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Menunggu</h3>
                                </div>
                                <span class="text-xs text-yellow-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2 text-yellow-600">{{ number_format($stats['belum_dikunjungi'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Belum dikunjungi</p>
                        </div>

                        <!-- Dalam Proses -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="navigation" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Dalam Proses</h3>
                                </div>
                                <span class="text-xs text-blue-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2 text-blue-600">{{ number_format($stats['dalam_perjalanan'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Sedang berlangsung</p>
                        </div>

                        <!-- Selesai -->
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                                        <i data-feather="check-circle" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-500">Selesai</h3>
                                </div>
                                <span class="text-xs text-green-600">&nbsp;</span>
                            </div>
                            <p class="text-2xl font-bold mt-2 text-green-600">{{ number_format($stats['selesai'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Telah selesai</p>
                        </div>
                    @endisset
                </div>

                <!-- Search and Filter Bar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Search Input -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="search" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   x-model="filters.search" 
                                   @input.debounce.500ms="applyFilters()"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                                   placeholder="Cari berdasarkan auditor atau tujuan...">
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Status Filter -->
                            <select x-model="filters.status_filter" 
                                    @change="applyFilters()"
                                    class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                <option value="">Semua Status</option>
                                @isset($statuses)
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}">
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>

                            <!-- Date Filter -->
                            <input type="date" 
                                   x-model="filters.date_filter" 
                                   @change="applyFilters()"
                                   class="px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">

                            <!-- Reset Filters Button -->
                            <button @click="resetFilters()" 
                                    class="px-4 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                                <i data-feather="refresh-ccw" class="w-4 h-4"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

        <!-- Dynamic Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 visits-table-container" style="overflow: visible !important;">
            <div class="table-overflow-wrapper" style="overflow: visible !important;">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">ID Kunjungan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Auditor</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tujuan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="bg-white divide-y divide-gray-200" style="overflow: visible !important; position: relative !important;">
                        @include('author.visits.table-rows')
                    </tbody>
                </table>
            </div>
            
            <!-- Loading State -->
            <div x-show="loading" class="text-center py-8">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-orange-500 transition ease-in-out duration-150">
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
        
        // Initialize dropdown functionality - Enhanced for proper positioning
        window.toggleDropdown = function(id) {
            console.log('toggleDropdown called with id:', id);
            const dropdown = document.getElementById(`dropdown-${id}`);
            const button = document.querySelector(`[onclick="toggleDropdown(${id})"]`);
            console.log('Dropdown element:', dropdown);
            console.log('Button element:', button);
            const isHidden = dropdown ? dropdown.classList.contains('hidden') : true;
            
            if (!dropdown) {
                console.error('Dropdown not found for id:', id);
                return;
            }
            
            if (!button) {
                console.error('Button not found for id:', id);
                return;
            }
            
            // Close all dropdowns first
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                d.classList.add('hidden');
            });
            
            if (isHidden) {
                // Get button position
                const buttonRect = button.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const dropdownWidth = 180;
                const dropdownHeight = 140;
                
                // Reset positioning
                dropdown.style.position = 'fixed';
                dropdown.style.zIndex = '99999';
                
                // Calculate positions
                let top = buttonRect.bottom + 5;
                let left = buttonRect.left;
                
                // Adjust if dropdown goes off right edge
                if (left + dropdownWidth > viewportWidth) {
                    left = buttonRect.right - dropdownWidth;
                }
                
                // Adjust if dropdown goes off bottom edge
                if (top + dropdownHeight > viewportHeight) {
                    top = buttonRect.top - dropdownHeight - 5;
                }
                
                // Ensure dropdown doesn't go off left edge
                if (left < 10) {
                    left = 10;
                }
                
                // Apply positions
                dropdown.style.top = `${top}px`;
                dropdown.style.left = `${left}px`;
                
                // Show dropdown with background and border
                dropdown.style.background = 'white';
                dropdown.style.border = '1px solid #e5e7eb';
                dropdown.style.borderRadius = '0.5rem';
                dropdown.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
                dropdown.style.minWidth = '180px';
                dropdown.classList.remove('hidden');
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });

        // Filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            filterTable();
        });

        document.getElementById('search-input').addEventListener('input', function() {
            filterTable();
        });

        function filterTable() {
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search-input').value.toLowerCase();
            
            // Here you can implement AJAX filtering or client-side filtering
            console.log('Filtering with status:', status, 'and search:', search);
        }

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Load visit table script (optional)
        // const tableScript = document.createElement('script');
        // tableScript.src = '{{ asset("js/visit-table.js") }}';
        // document.head.appendChild(tableScript);
    });

    // Global functions for visit management (must be outside DOMContentLoaded)
    window.showVisitDetail = function(visitId) {
        // Close dropdown first
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        fetch(`/author/visits/${visitId}/detail`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('visitDetailContent').innerHTML = html;
                document.getElementById('visitDetailModal').classList.remove('hidden');
            })
            .catch(error => console.error('Error:', error));
    };

    window.closeVisitDetailModal = function() {
        document.getElementById('visitDetailModal').classList.add('hidden');
    };

    window.showRescheduleModal = function(visitId) {
        // Close dropdown first
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        document.getElementById('rescheduleVisitId').value = visitId;
        document.getElementById('rescheduleModal').classList.remove('hidden');
    };

    window.closeRescheduleModal = function() {
        document.getElementById('rescheduleModal').classList.add('hidden');
    };

    window.confirmVisit = function(visitId) {
        // Close dropdown first
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
        
        if (confirm('Apakah Anda yakin ingin mengkonfirmasi kunjungan ini?')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/author/visits/${visitId}/confirm`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = csrfToken;
            form.appendChild(csrfField);
            
            // Add method field
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    };
</script>

<!-- Visit Detail Modal -->
<div id="visitDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeVisitDetailModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detail Kunjungan</h3>
                    <button onclick="closeVisitDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="visitDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeRescheduleModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Atur Ulang Jadwal</h3>
                    <button onclick="closeRescheduleModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form>
                    <input type="hidden" id="rescheduleVisitId" />
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Baru</label>
                        <input type="datetime-local" class="w-full border border-gray-300 rounded-md px-3 py-2" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                        <textarea class="w-full border border-gray-300 rounded-md px-3 py-2" rows="3" placeholder="Masukkan alasan perubahan jadwal"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm font-medium hover:bg-orange-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include workflow modals -->
@include('visits.workflow-modals')

</body>
</html>