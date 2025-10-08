{{-- Include required modal components --}}
@include('visits.modals.reschedule-modal')
@include('visits.modals.complete-modal')

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
        
        @if(auth()->user()->role === 'admin')
            <!-- Author -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600">
                                {{ $visit->author ? substr($visit->author->name, 0, 1) : substr($visit->author_name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium">{{ $visit->author->name ?? $visit->author_name ?? 'Unknown Author' }}</div>
                        <div class="text-gray-500">{{ $visit->author->email ?? 'No email' }}</div>
                    </div>
                </div>
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
        @elseif(auth()->user()->role === 'auditor')
            <!-- Author -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600">
                                {{ $visit->author ? substr($visit->author->name, 0, 1) : substr($visit->author_name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="font-medium">{{ $visit->author->name ?? $visit->author_name ?? 'Unknown Author' }}</div>
                        <div class="text-gray-500">{{ $visit->author->email ?? 'No email' }}</div>
                    </div>
                </div>
            </td>
        @elseif(auth()->user()->role === 'author')
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
        @endif
        
        <!-- Tujuan/Lokasi -->
        <td class="px-6 py-4 text-sm text-gray-900">
            <div class="max-w-xs">
                <div class="font-medium">{{ $visit->visit_purpose ?? 'No purpose' }}</div>
                <div class="text-gray-500 text-xs truncate">{{ $visit->location_address ?? 'No location' }}</div>
            </div>
        </td>
        
        <!-- Status -->
        <td class="px-6 py-4 whitespace-nowrap">
            @php
                $statusClasses = [
                    'belum_dikunjungi' => 'bg-yellow-100 text-yellow-800',
                    'dalam_perjalanan' => 'bg-blue-100 text-blue-800',
                    'sedang_dikunjungi' => 'bg-purple-100 text-purple-800',
                    'menunggu_acc' => 'bg-orange-100 text-orange-800',
                    'selesai' => 'bg-green-100 text-green-800',
                    // Legacy status support
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-blue-100 text-blue-800',
                    'in_progress' => 'bg-purple-100 text-purple-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
                $statusTexts = [
                    'belum_dikunjungi' => 'Belum Dikunjungi',
                    'dalam_perjalanan' => 'Dalam Perjalanan',
                    'sedang_dikunjungi' => 'Proses',
                    'menunggu_acc' => 'Menunggu ACC',
                    'selesai' => 'Selesai',
                    // Legacy status support
                    'pending' => 'Pending',
                    'confirmed' => 'Proses',
                    'in_progress' => 'Sedang Berlangsung',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses[$visit->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusTexts[$visit->status] ?? ucfirst(str_replace('_', ' ', $visit->status)) }}
            </span>
            
            @if($visit->confirmed_at)
                <div class="text-xs text-gray-500 mt-1">
                    Dikonfirmasi: {{ $visit->confirmed_at->format('d/m H:i') }}
                </div>
            @endif
        </td>
        
        @if(auth()->user()->role === 'author')
            <!-- Reschedule Info for Author -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                @if($visit->canBeRescheduled())
                    <span class="text-green-600">Sisa {{ $visit->remaining_reschedule_attempts }}x</span>
                @elseif($visit->reschedule_count >= 3)
                    <span class="text-red-600">Habis (3x)</span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
        @endif
        
        <!-- Actions -->
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center justify-end space-x-2">
                <!-- Detail Button - Always available for all roles -->
                <button onclick="viewVisit('{{ $visit->id }}')"
                         class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-all duration-200 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Detail</span>
                </button>

                @if(auth()->user()->role === 'admin')
                    <!-- Admin Actions -->
                    @if($visit->status !== 'completed')
                        <!-- Edit: Only if not completed -->
                        <a href="{{ route('admin.visits.edit', $visit) }}"
                           class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-1 rounded-md transition-all duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit</span>
                        </a>
                    @endif

                @elseif(auth()->user()->role === 'author')
                    <!-- Author Actions -->
                    @if($visit->canBeConfirmed())
                        <button onclick="confirmVisit('{{ $visit->id }}')"
                                 class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-all duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Konfirmasi</span>
                        </button>
                    @endif

                    @if($visit->canBeRescheduled())
                        <button onclick="rescheduleVisit('{{ $visit->id }}')"
                                 class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 px-3 py-1 rounded-md transition-all duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Undur Jadwal</span>
                        </button>
                    @endif

                @elseif(auth()->user()->role === 'auditor')
                    <!-- Auditor Actions -->
                    @if($visit->status === 'sedang_dikunjungi' || $visit->status === 'dalam_perjalanan' || $visit->status === 'confirmed' || $visit->status === 'in_progress')
                        <!-- Selesaikan: Complete with selfie + details (sedang_dikunjungi/dalam_perjalanan -> selesai) -->
                        <button onclick="completeVisit('{{ $visit->id }}')"
                                 class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-all duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Selesaikan</span>
                        </button>
                    @endif
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ auth()->user()->role === 'admin' ? '8' : (auth()->user()->role === 'author' ? '8' : '7') }}" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kunjungan</h3>
                <p class="text-gray-500">
                    @if(auth()->user()->role === 'admin')
                        Belum ada kunjungan yang dijadwalkan
                    @elseif(auth()->user()->role === 'auditor')
                        Belum ada kunjungan yang ditugaskan kepada Anda
                    @else
                        Belum ada kunjungan yang dijadwalkan untuk Anda
                    @endif
                </p>
            </div>
        </td>
    </tr>
@endforelse