<!-- Visit Basic Info -->
<div class="bg-gray-50 rounded-lg p-4 mb-6">
    <h4 class="font-semibold text-gray-900 mb-3">Informasi Kunjungan</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="text-sm font-medium text-gray-600">Author</label>
            <div class="font-medium text-gray-900">{{ $visit->author->name ?? $visit->author_name ?? 'Unknown Author' }}</div>
            <div class="text-sm text-gray-500">{{ $visit->author->email ?? 'No email' }}</div>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Auditor</label>
            <div class="font-medium text-gray-900">{{ $visit->auditor->name ?? $visit->auditor_name ?? 'Unknown Auditor' }}</div>
            <div class="text-sm text-gray-500">{{ $visit->auditor->email ?? 'No email' }}</div>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Tanggal Kunjungan</label>
            <div class="font-medium text-gray-900">{{ $visit->visit_date ? $visit->visit_date->format('d M Y') : 'No Date' }}</div>
            <div class="text-sm text-gray-500">{{ $visit->visit_date ? $visit->visit_date->format('H:i') : '-' }} WIB</div>
            @if($visit->reschedule_count > 0)
                <div class="text-xs text-orange-600 mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        Diundur {{ $visit->reschedule_count }}x
                    </span>
                </div>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <label class="text-sm font-medium text-gray-600">Tujuan Kunjungan</label>
            <div class="mt-1 text-gray-900">{{ $visit->visit_purpose ?? 'No purpose specified' }}</div>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-600">Status</label>
            <div class="mt-1">
                @php
                    $status = $visit->status_label;
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $status['text'] ?? ucfirst($visit->status) }}
                </span>
            </div>
        </div>
    </div>
    
    @if($visit->confirmed_at)
        <div class="mt-4">
            <label class="text-sm font-medium text-gray-600">Waktu Konfirmasi</label>
            <div class="mt-1 text-gray-900">{{ $visit->confirmed_at->format('d M Y H:i') }} WIB</div>
            @if($visit->confirmed_by)
                <div class="text-sm text-gray-500">oleh {{ $visit->confirmed_by }}</div>
            @endif
        </div>
    @endif
    
    @if($visit->started_at)
        <div class="mt-4">
            <label class="text-sm font-medium text-gray-600">Waktu Mulai Proses</label>
            <div class="mt-1 text-gray-900">{{ $visit->started_at->format('d M Y H:i') }} WIB</div>
        </div>
    @endif
    
    @if($visit->completed_at)
        <div class="mt-4">
            <label class="text-sm font-medium text-gray-600">Waktu Selesai</label>
            <div class="mt-1 text-gray-900">{{ $visit->completed_at->format('d M Y H:i') }} WIB</div>
        </div>
    @endif
</div>

<!-- Auditor Notes (New Workflow) -->
@if($visit->auditor_notes)
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <h4 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Catatan Auditor
    </h4>
    <div class="text-gray-800 whitespace-pre-line">{{ $visit->auditor_notes }}</div>
</div>
@endif

<!-- Selfie and GPS Information (New Workflow) -->
@if($visit->selfie_photo || ($visit->selfie_latitude && $visit->selfie_longitude))
<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
    <h4 class="font-semibold text-green-900 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Bukti Kehadiran
    </h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($visit->selfie_photo)
        <div>
            <label class="text-sm font-medium text-gray-600">Foto Selfie</label>
            <div class="mt-2">
                <img src="{{ Storage::url($visit->selfie_photo) }}" 
                     alt="Selfie Auditor" 
                     class="max-w-full h-auto max-h-64 rounded-lg border border-gray-300 object-cover">
            </div>
        </div>
        @endif
        
        @if($visit->selfie_latitude && $visit->selfie_longitude)
        <div>
            <label class="text-sm font-medium text-gray-600">Koordinat GPS</label>
            <div class="mt-2 space-y-1">
                <div class="text-sm">
                    <span class="font-medium">Latitude:</span> {{ $visit->selfie_latitude }}
                </div>
                <div class="text-sm">
                    <span class="font-medium">Longitude:</span> {{ $visit->selfie_longitude }}
                </div>
                <a href="https://maps.google.com/?q={{ $visit->selfie_latitude }},{{ $visit->selfie_longitude }}" 
                   target="_blank" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Lihat di Google Maps
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Additional Photos (New Workflow) -->
@if($visit->photos && count($visit->photos) > 0)
<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
    <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Foto Tambahan ({{ count($visit->photos) }})
    </h4>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($visit->photos as $index => $photo)
        <div class="relative group">
            <img src="{{ Storage::url($photo) }}" 
                 alt="Foto {{ $index + 1 }}" 
                 class="w-full h-32 object-cover rounded-lg border border-gray-300 cursor-pointer hover:opacity-80 transition-opacity"
                 onclick="openPhotoModal('{{ Storage::url($photo) }}')">
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($visit->visitReport)
    <!-- Visit Report -->
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Laporan Kunjungan
            @php
                $reportStatusConfig = [
                    'submitted' => ['bg-orange-100', 'text-orange-800', 'border-orange-200', 'Menunggu Review'],
                    'approved' => ['bg-green-100', 'text-green-800', 'border-green-200', 'Disetujui'],
                    'revision_required' => ['bg-red-100', 'text-red-800', 'border-red-200', 'Perlu Revisi']
                ];
                $reportConfig = $reportStatusConfig[$visit->visitReport->status] ?? ['bg-gray-100', 'text-gray-800', 'border-gray-200', $visit->visitReport->status];
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $reportConfig[0] }} {{ $reportConfig[1] }} {{ $reportConfig[2] }}">
                {{ $reportConfig[3] }}
            </span>
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Tanggal Kunjungan Aktual</label>
                <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($visit->visitReport->tanggal_kunjungan_aktual)->format('d M Y') }}</div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Waktu Kunjungan</label>
                <div class="font-medium text-gray-900">{{ $visit->visitReport->waktu_mulai }} - {{ $visit->visitReport->waktu_selesai }}</div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Status Kunjungan</label>
                <div class="font-medium text-gray-900">{{ ucfirst($visit->visitReport->status_kunjungan) }}</div>
            </div>
            @if($visit->visitReport->latitude && $visit->visitReport->longitude)
                <div>
                    <label class="text-sm font-medium text-gray-600">Koordinat GPS</label>
                    <div class="font-medium text-gray-900">{{ $visit->visitReport->latitude }}, {{ $visit->visitReport->longitude }}</div>
                </div>
            @endif
        </div>
        
        <div class="mb-4">
            <label class="text-sm font-medium text-gray-600">Lokasi Kunjungan</label>
            <div class="mt-1 text-gray-900">{{ $visit->visitReport->lokasi_kunjungan }}</div>
        </div>
        
        <div class="mb-4">
            <label class="text-sm font-medium text-gray-600">Hasil Kunjungan</label>
            <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->hasil_kunjungan }}</div>
        </div>
        
        @if($visit->visitReport->temuan)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Temuan</label>
                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->temuan }}</div>
            </div>
        @endif
        
        @if($visit->visitReport->rekomendasi)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Rekomendasi</label>
                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->rekomendasi }}</div>
            </div>
        @endif
        
        @if($visit->visitReport->kendala)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Kendala/Hambatan</label>
                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->kendala }}</div>
            </div>
        @endif
        
        @if($visit->visitReport->catatan_auditor)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Catatan Auditor</label>
                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->catatan_auditor }}</div>
            </div>
        @endif
        
        <!-- Photos -->
        @if($visit->visitReport->photo_paths)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Foto Kunjungan</label>
                <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($visit->visitReport->photo_paths as $photo)
                        <div class="relative group">
                            <img src="{{ Storage::url($photo) }}" alt="Foto Kunjungan" 
                                 class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Documents -->
        @if($visit->visitReport->document_paths)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Dokumen Pendukung</label>
                <div class="mt-2 space-y-2">
                    @foreach($visit->visitReport->document_paths as $document)
                        <a href="{{ Storage::url($document) }}" target="_blank" 
                           class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ basename($document) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($visit->visitReport->admin_notes)
            <div class="mb-4">
                <label class="text-sm font-medium text-gray-600">Catatan Admin</label>
                <div class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $visit->visitReport->admin_notes }}</div>
            </div>
        @endif
    </div>
    
    @if(auth()->user()->role === 'admin' && $visit->visitReport->status === 'submitted')
        <!-- Admin Actions -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Tindakan Admin</h4>
            <div class="flex gap-3">
                <!-- Approve Form -->
                <form action="{{ route('admin.visits.approve-report', $visit) }}" method="POST" class="flex-1">
                    @csrf
                    <div class="mb-3">
                        <label for="admin_notes" class="block text-sm font-medium text-gray-600 mb-1">Catatan (Opsional)</label>
                        <textarea name="admin_notes" id="admin_notes" rows="2" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                    <button type="submit" onclick="return confirm('ACC laporan ini?')"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        ACC Laporan
                    </button>
                </form>
                
                <!-- Reject Form -->
                <form action="{{ route('admin.visits.reject-report', $visit) }}" method="POST" class="flex-1">
                    @csrf
                    <div class="mb-3">
                        <label for="revision_notes" class="block text-sm font-medium text-gray-600 mb-1">Catatan Revisi *</label>
                        <textarea name="revision_notes" id="revision_notes" rows="2" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
                    </div>
                    <button type="submit" onclick="return confirm('Kembalikan laporan untuk revisi?')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Minta Revisi
                    </button>
                </form>
            </div>
        </div>
    @endif
@else
    <!-- No Report Yet -->
    <div class="text-center py-8">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Laporan</h3>
        <p class="text-gray-500">
            @if(auth()->user()->role === 'admin')
                Auditor belum membuat laporan untuk kunjungan ini.
            @elseif(auth()->user()->role === 'auditor')
                Anda belum membuat laporan untuk kunjungan ini.
            @else
                Auditor belum menyelesaikan kunjungan dan membuat laporan.
            @endif
        </p>
        
        @if(auth()->user()->role === 'auditor' && $visit->status === 'accepted')
            <a href="{{ route('auditor.visits.create-report', $visit) }}" 
               class="inline-flex items-center mt-4 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Buat Laporan Sekarang
            </a>
        @endif
    </div>
@endif

<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center" onclick="closePhotoModal()">
    <div class="relative max-w-4xl max-h-full p-4">
        <img id="photoModalImage" src="" alt="Photo" class="max-w-full max-h-full object-contain">
        <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function openPhotoModal(photoUrl) {
    document.getElementById('photoModalImage').src = photoUrl;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
}

// Close photo modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePhotoModal();
    }
});
</script>