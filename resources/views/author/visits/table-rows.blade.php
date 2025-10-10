@forelse($visits as $index => $visit)
    <tr class="hover:bg-gray-50 transition-colors duration-200">
        <!-- No -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $visits->firstItem() + $index }}
        </td>
        
        <!-- ID Kunjungan -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <div class="font-medium text-blue-600">{{ $visit->visit_id ?? 'N/A' }}</div>
        </td>
        
        <!-- Auditor -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-green-600">
                            {{ $visit->auditor ? substr($visit->auditor->name, 0, 1) : substr($visit->auditor_name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="font-medium">{{ $visit->auditor->name ?? $visit->auditor_name ?? 'Unknown Auditor' }}</div>
                    <div class="text-gray-500">{{ $visit->auditor->email ?? 'No email' }}</div>
                </div>
            </div>
        </td>
        
        <!-- Tujuan/Lokasi -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <div class="max-w-48 truncate">{{ $visit->location_address ?? 'No Address' }}</div>
        </td>
        
        <!-- Tanggal -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <div class="font-medium">{{ $visit->visit_date ? $visit->visit_date->format('d M Y') : 'No Date' }}</div>
            <div class="text-gray-500">{{ $visit->visit_date ? $visit->visit_date->format('H:i') : '-' }}</div>
            @if($visit->reschedule_count > 0)
                <div class="text-xs text-orange-600">
                    Diundur {{ $visit->reschedule_count }}x
                </div>
            @endif
        </td>
        
        <!-- Status -->
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <span class="px-2 py-1 text-xs font-medium rounded-full 
                @switch($visit->status)
                    @case('belum_dikunjungi')
                        bg-yellow-100 text-yellow-800
                        @break
                    @case('dalam_perjalanan')
                        bg-blue-100 text-blue-800
                        @break
                    @case('sedang_dikunjungi')
                        bg-purple-100 text-purple-800
                        @break
                    @case('selesai')
                        bg-green-100 text-green-800
                        @break
                    @default
                        bg-gray-100 text-gray-800
                @endswitch
            ">
                @switch($visit->status)
                    @case('belum_dikunjungi')
                        Belum Dikunjungi
                        @break
                    @case('dalam_perjalanan')
                        Dalam Perjalanan
                        @break
                    @case('sedang_dikunjungi')
                        Sedang Dikunjungi
                        @break
                    @case('selesai')
                        Selesai
                        @break
                    @default
                        {{ $visit->status }}
                @endswitch
            </span>
        </td>
        
        <!-- Actions -->
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="relative inline-block text-left dropdown-container">
                <button type="button" 
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-all duration-150 focus:outline-none"
                        onclick="toggleDropdown({{ $visit->id }})"
                        title="Menu Aksi">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                        <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="dropdown-{{ $visit->id }}" 
                     class="dropdown-menu hidden"
                     style="z-index: 99999;">
                    <div class="py-1">
                        <button onclick="showVisitDetail({{ $visit->id }})" 
                                class="group flex items-center w-full px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Detail
                        </button>
                        
                        @if($visit->status === 'belum_dikunjungi')
                            <button onclick="confirmVisit({{ $visit->id }})" 
                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-green-700 hover:bg-green-50 transition-colors duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Konfirmasi
                            </button>
                            
                            <button onclick="showRescheduleModal({{ $visit->id }})" 
                                    class="group flex items-center w-full px-4 py-2.5 text-sm text-orange-700 hover:bg-orange-50 transition-colors duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-orange-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Undur
                            </button>
                        @endif
                        
                        <div class="border-t border-gray-100 my-1"></div>
                        
                        <button onclick="downloadReport({{ $visit->id }})" 
                                class="group flex items-center w-full px-4 py-2.5 text-sm text-gray-400 cursor-not-allowed transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-3 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Download
                            <span class="ml-auto text-xs text-gray-400 italic">Soon</span>
                        </button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p>Tidak ada kunjungan yang ditemukan.</p>
                <p class="text-xs text-gray-400">Coba ubah filter atau parameter pencarian.</p>
            </div>
        </td>
    </tr>
@endforelse

{{-- Include Modals --}}
@include('visits.modals.reschedule-modal')