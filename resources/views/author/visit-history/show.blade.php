@extends('author.layout')

@section('title', 'Detail Kunjungan')
@section('page-title', 'Detail Kunjungan')
@section('page-description', 'Detail hasil kunjungan dari auditor')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('author.visits.history') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Riwayat Kunjungan
        </a>
    </div>

    <!-- Visit Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $visit->visit_id }}</h2>
                <p class="text-sm text-gray-600 mt-1">Tanggal: {{ $visit->formatted_visit_date }}</p>
            </div>
            <div>
                @php
                    $statusLabel = $visit->status_label;
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusLabel['color'] }}">
                    {{ $statusLabel['text'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Visit Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kunjungan</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Auditor</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->auditor_name ?? ($visit->auditor->name ?? '-') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Author</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->author_name ?? ($visit->author->name ?? '-') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal & Waktu Kunjungan</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->formatted_visit_date }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->location_address ?? '-' }}</p>
                    @if($visit->latitude && $visit->longitude)
                        <p class="mt-1 text-xs text-gray-500">
                            Koordinat: {{ $visit->latitude }}, {{ $visit->longitude }}
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusLabel['color'] }} mt-1">
                        {{ $statusLabel['text'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Visit Notes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Catatan Auditor</h3>
            
            @if($visit->notes)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $visit->notes }}</p>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 italic">Tidak ada catatan khusus</p>
                </div>
            @endif

            <!-- Map Link (if coordinates available) -->
            @if($visit->latitude && $visit->longitude)
                <div class="mt-4">
                    <a href="https://maps.google.com?q={{ $visit->latitude }},{{ $visit->longitude }}" 
                       target="_blank"
                       class="inline-flex items-center text-sm text-orange-600 hover:text-orange-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Lihat di Google Maps
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Photos Section -->
    @if($visit->photos && count($visit->photos) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Kunjungan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($visit->photos as $index => $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo) }}" 
                             alt="Foto kunjungan {{ $index + 1 }}"
                             class="w-full h-48 object-cover rounded-lg border border-gray-200 group-hover:opacity-75 transition-opacity cursor-pointer"
                             onclick="openPhotoModal('{{ Storage::url($photo) }}', 'Foto kunjungan {{ $index + 1 }}')">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="bg-black bg-opacity-50 rounded-full p-2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto Kunjungan</h3>
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Tidak ada foto yang diupload untuk kunjungan ini</p>
            </div>
        </div>
    @endif
</div>

<!-- Photo Modal -->
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closePhotoModal()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    function openPhotoModal(imageSrc, imageAlt) {
        const modal = document.getElementById('photoModal');
        const modalImage = document.getElementById('modalImage');
        
        modalImage.src = imageSrc;
        modalImage.alt = imageAlt;
        modal.classList.remove('hidden');
        
        // Close modal when clicking outside image
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePhotoModal();
            }
        });
    }

    function closePhotoModal() {
        const modal = document.getElementById('photoModal');
        modal.classList.add('hidden');
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoModal();
        }
    });
</script>
@endpush
@endsection