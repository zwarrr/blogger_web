<style>
/* Remove camera mirror effect - make it like looking at photo, not mirror */
#cameraStream {
    transform: scaleX(-1) !important;
    -webkit-transform: scaleX(-1) !important;
    -moz-transform: scaleX(-1) !important;
    -ms-transform: scaleX(-1) !important;
}

/* Keep captured image normal (not mirrored) for actual photo result */
#capturedImage {
    transform: scaleX(1) !important;
    -webkit-transform: scaleX(1) !important;
    -moz-transform: scaleX(1) !important;
    -ms-transform: scaleX(1) !important;
}

/* Ensure canvas captures normal orientation */
#captureCanvas {
    transform: scaleX(1) !important;
}
</style>

<!-- Reschedule Visit Modal -->
<div id="rescheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Undur Jadwal Kunjungan</h3>
                <button onclick="closeRescheduleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="rescheduleForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label for="new_visit_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal & Waktu Baru
                    </label>
                    <input type="datetime-local" 
                           id="new_visit_date" 
                           name="new_visit_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                
                <div class="mb-4">
                    <label for="reschedule_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Pengunduran Jadwal
                    </label>
                    <textarea id="reschedule_reason" 
                              name="reschedule_reason" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Jelaskan alasan pengunduran jadwal..."
                              required></textarea>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Perhatian!</p>
                            <p>Anda hanya dapat mengundur jadwal maksimal 3 kali.</p>
                            <p id="remainingAttempts" class="mt-1"></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeRescheduleModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Undur Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Visit Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Konfirmasi Kunjungan</h3>
                <button onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-green-700">
                            <p class="font-medium">Konfirmasi Kunjungan</p>
                            <p class="mt-1">Dengan mengkonfirmasi, Anda menyetujui bahwa jadwal kunjungan ini dapat dilaksanakan oleh auditor.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="closeConfirmModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </button>
                <button type="button" 
                        onclick="submitConfirmVisit()"
                        id="confirmSubmitBtn"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span id="confirmBtnText">Ya, Konfirmasi</span>
                    <div id="confirmBtnLoading" class="hidden flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path>
                        </svg>
                        Memproses...
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Visit Modal -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-2/3 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Selesaikan Kunjungan</h3>
                <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="completeForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="auditor_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan/Keterangan Auditor <span class="text-red-500">*</span>
                            </label>
                            <textarea id="auditor_notes" 
                                      name="auditor_notes" 
                                      rows="6" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Tuliskan catatan, temuan, atau keterangan hasil kunjungan..."
                                      required></textarea>
                        </div>
                        
                        <div>
                            <label for="selfie_photo" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Selfie <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- File Input (hidden) untuk hasil capture -->
                            <input type="file" id="selfie_photo_file" name="selfie_photo" accept="image/*" class="hidden" required>
                            
                            <!-- Camera Control Button -->
                            <div class="mb-3">
                                <button type="button" id="openCameraButton" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Buka Kamera Selfie
                                </button>
                            </div>
                            
                            <!-- Camera Preview Area -->
                            <div id="cameraArea" class="mb-3 hidden">
                                <video id="cameraStream" class="w-full max-w-sm h-64 bg-gray-100 rounded-lg object-cover"></video>
                                <div class="flex justify-center gap-2 mt-2">
                                    <button type="button" id="captureButton" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Ambil Foto
                                    </button>
                                    <button type="button" id="stopCameraButton" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Tutup Kamera
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Canvas for captured image (hidden) -->
                            <canvas id="captureCanvas" class="hidden"></canvas>
                            
                            <!-- Captured Image Preview -->
                            <div id="capturedImageArea" class="mb-3 hidden">
                                <img id="capturedImage" class="w-full max-w-sm h-64 bg-gray-100 rounded-lg object-cover">
                                <div class="flex justify-center gap-2 mt-2">
                                    <button type="button" id="retakeButton" 
                                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Foto Ulang
                                    </button>
                                    <button type="button" id="useImageButton" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Gunakan Foto Ini
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Hidden file input -->
                            <input type="file" 
                                   id="selfie_photo_file" 
                                   name="selfie_photo" 
                                   accept="image/*"
                                   capture="user"
                                   class="hidden"
                                   required>
                                   
                            <p class="text-xs text-gray-500 mt-1">
                                Ambil foto selfie sebagai bukti kehadiran di lokasi. Pastikan wajah Anda terlihat jelas.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Tambahan (Opsional)
                            </label>
                            <input type="file" 
                                   id="photos" 
                                   name="photos[]" 
                                   accept="image/*"
                                   multiple
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">
                                Maksimal 5 foto tambahan (bukti kondisi lokasi, dokumen, dll.)
                            </p>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Informasi GPS</p>
                                    <p>Koordinat lokasi akan direkam otomatis saat menyimpan.</p>
                                    <p id="gpsStatus" class="mt-1 text-xs"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeCompleteModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Selesaikan Kunjungan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function rescheduleVisit(visitId) {
    const modal = document.getElementById('rescheduleModal');
    const form = document.getElementById('rescheduleForm');
    
    // Set form action
    form.action = `/author/visits/${visitId}/reschedule`;
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().slice(0, 16);
    document.getElementById('new_visit_date').min = minDate;
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.add('hidden');
}

let currentConfirmVisitId = null;

function confirmVisit(visitId) {
    currentConfirmVisitId = visitId;
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentConfirmVisitId = null;
}

function submitConfirmVisit() {
    if (!currentConfirmVisitId) return;
    
    const submitBtn = document.getElementById('confirmSubmitBtn');
    const btnText = document.getElementById('confirmBtnText');
    const btnLoading = document.getElementById('confirmBtnLoading');
    
    // Show loading state
    submitBtn.disabled = true;
    btnText.classList.add('hidden');
    btnLoading.classList.remove('hidden');
    
    fetch(`/author/visits/${currentConfirmVisitId}/confirm`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification(data.message, 'success');
            
            // Close modal
            closeConfirmModal();
            
            // Refresh the visit table
            if (typeof applyFilters === 'function') {
                applyFilters();
            } else {
                location.reload();
            }
        } else {
            // Show error notification
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat mengkonfirmasi kunjungan', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoading.classList.add('hidden');
    });
}

function resetModalState() {
    // Stop any existing camera stream
    if (currentStream) {
        stopCamera();
    }
    
    // Reset UI elements
    const cameraArea = document.getElementById('cameraArea');
    const capturedImageArea = document.getElementById('capturedImageArea');
    const openCameraButton = document.getElementById('openCameraButton');
    
    if (cameraArea) cameraArea.classList.add('hidden');
    if (capturedImageArea) capturedImageArea.classList.add('hidden');
    if (openCameraButton) openCameraButton.classList.remove('hidden');
    
    // Clear canvas and image previews
    const canvas = document.getElementById('captureCanvas');
    const capturedImg = document.getElementById('capturedImage');
    
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    
    if (capturedImg) {
        capturedImg.src = '';
    }
}

function completeVisit(visitId) {
    console.log('completeVisit called with visitId:', visitId);
    
    const modal = document.getElementById('completeModal');
    const form = document.getElementById('completeForm');
    
    if (!modal || !form) {
        console.error('Complete modal or form not found');
        alert('Error: Modal tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    // Reset modal state
    resetModalState();
    
    // Set form action
    form.action = `/auditor/visits/${visitId}/complete`;
    
    // Show modal
    modal.classList.remove('hidden');
    console.log('Modal opened for visit:', visitId);
    
    // Initialize camera functionality after modal is shown
    setTimeout(() => {
        try {
            initializeCameraControls();
        } catch (error) {
            console.warn('Camera initialization failed:', error);
        }
    }, 100);
    
    // Get GPS coordinates
    if (navigator.geolocation) {
        document.getElementById('gpsStatus').textContent = 'Mengambil koordinat GPS...';
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Remove existing coordinate inputs if any
                const existingLatInput = form.querySelector('input[name="selfie_latitude"]');
                const existingLngInput = form.querySelector('input[name="selfie_longitude"]');
                if (existingLatInput) existingLatInput.remove();
                if (existingLngInput) existingLngInput.remove();
                
                // Add hidden inputs for coordinates
                let latInput = document.createElement('input');
                latInput.type = 'hidden';
                latInput.name = 'selfie_latitude';
                latInput.value = position.coords.latitude;
                form.appendChild(latInput);
                
                let lngInput = document.createElement('input');
                lngInput.type = 'hidden';
                lngInput.name = 'selfie_longitude';
                lngInput.value = position.coords.longitude;
                form.appendChild(lngInput);
                
                document.getElementById('gpsStatus').textContent = 
                    `Koordinat berhasil diambil: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
            },
            function(error) {
                document.getElementById('gpsStatus').textContent = 'Tidak dapat mengambil koordinat GPS';
                console.error('GPS Error:', error);
            }
        );
    } else {
        document.getElementById('gpsStatus').textContent = 'GPS tidak didukung browser';
    }
}

// Camera functionality
let currentStream = null;

function initializeCameraControls() {
    console.log('Initializing camera controls...');
    
    const openCameraButton = document.getElementById('openCameraButton');
    if (!openCameraButton) {
        console.error('Open camera button not found');
        return;
    }

    // Remove any existing event listeners
    openCameraButton.replaceWith(openCameraButton.cloneNode(true));
    const newOpenCameraButton = document.getElementById('openCameraButton');
    
    // Open camera
    newOpenCameraButton.addEventListener('click', async function() {
        console.log('Opening camera...');
        try {
            // Check if browser supports camera
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Browser tidak mendukung akses kamera');
            }

            const constraints = {
                video: { 
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('Camera stream obtained');
            
            currentStream = stream;
            const video = document.getElementById('cameraStream');
            if (video) {
                video.srcObject = stream;
                await video.play();
                console.log('Video playing');
                
                // Show camera area, hide button
                document.getElementById('cameraArea')?.classList.remove('hidden');
                newOpenCameraButton.classList.add('hidden');
                document.getElementById('capturedImageArea')?.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            let errorMsg = 'Tidak dapat mengakses kamera. ';
            if (error.name === 'NotAllowedError') {
                errorMsg += 'Mohon berikan izin akses kamera.';
            } else if (error.name === 'NotFoundError') {
                errorMsg += 'Kamera tidak ditemukan.';
            } else {
                errorMsg += 'Error: ' + error.message;
            }
            alert(errorMsg);
        }
    });

    // Setup other camera controls
    setupCameraEventListeners();
}

function setupCameraEventListeners() {
    // Stop camera
    const stopCameraButton = document.getElementById('stopCameraButton');
    if (stopCameraButton) {
        stopCameraButton.addEventListener('click', function() {
            stopCamera();
        });
    }
    
    // Capture photo
    const captureButton = document.getElementById('captureButton');
    if (captureButton) {
        captureButton.addEventListener('click', function() {
            capturePhoto();
        });
    }
    
    // Retake photo
    const retakeButton = document.getElementById('retakeButton');
    if (retakeButton) {
        retakeButton.addEventListener('click', function() {
            document.getElementById('capturedImageArea')?.classList.add('hidden');
            document.getElementById('cameraArea')?.classList.remove('hidden');
        });
    }
    
    // Use captured image
    const useImageButton = document.getElementById('useImageButton');
    if (useImageButton) {
        useImageButton.addEventListener('click', function() {
            convertCanvasToFile();
            stopCamera();
            document.getElementById('cameraArea')?.classList.add('hidden');
            document.getElementById('capturedImageArea')?.classList.add('hidden');
            document.getElementById('openCameraButton')?.classList.remove('hidden');
        });
    }
}

function stopCamera() {
    console.log('Stopping camera...');
    if (currentStream) {
        try {
            currentStream.getTracks().forEach(track => {
                track.stop();
                console.log('Camera track stopped:', track.kind);
            });
            currentStream = null;
            
            const video = document.getElementById('cameraStream');
            if (video) {
                video.srcObject = null;
                video.load(); // Force reload to clear stream
            }
            
            console.log('Camera stopped successfully');
        } catch (error) {
            console.error('Error stopping camera:', error);
        }
        
        // Reset UI
        const cameraArea = document.getElementById('cameraArea');
        const openCameraButton = document.getElementById('openCameraButton');
        const photoPreview = document.getElementById('photoPreview');
        const captureButton = document.getElementById('captureButton');
        
        if (cameraArea) cameraArea.classList.add('hidden');
        if (openCameraButton) openCameraButton.classList.remove('hidden');
        if (photoPreview) {
            photoPreview.classList.add('hidden');
            photoPreview.innerHTML = '';
        }
        if (captureButton) captureButton.classList.remove('hidden');
    }
}

function capturePhoto() {
    const video = document.getElementById('cameraStream');
    const canvas = document.getElementById('captureCanvas');
    const capturedImage = document.getElementById('capturedImage');
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw current video frame to canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert to image URL and show preview
    const imageUrl = canvas.toDataURL('image/jpeg', 0.8);
    capturedImage.src = imageUrl;
    
    // Hide camera, show captured image
    document.getElementById('cameraArea').classList.add('hidden');
    document.getElementById('capturedImageArea').classList.remove('hidden');
}

function capturePhoto() {
    const video = document.getElementById('cameraStream');
    const canvas = document.getElementById('captureCanvas');
    const capturedImg = document.getElementById('capturedImage');
    
    const context = canvas.getContext('2d');
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Flip the image horizontally to remove mirror effect
    context.save();
    context.scale(-1, 1);
    context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
    context.restore();
    
    // Convert canvas to image and display preview
    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
    capturedImg.src = dataURL;
    
    // Show captured image area, hide camera area
    document.getElementById('cameraArea').classList.add('hidden');
    document.getElementById('capturedImageArea').classList.remove('hidden');
}

function convertCanvasToFile() {
    const canvas = document.getElementById('captureCanvas');
    const fileInput = document.getElementById('selfie_photo_file');
    
    canvas.toBlob(function(blob) {
        // Create a File object from the blob
        const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
        
        // Create a new FileList-like object
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        // Set the files to the input
        fileInput.files = dataTransfer.files;
        
        // Trigger change event
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }, 'image/jpeg', 0.8);
}

function closeCompleteModal() {
    const modal = document.getElementById('completeModal');
    if (modal) {
        modal.classList.add('hidden');
        
        // Reset modal state
        resetModalState();
        
        // Clear form
        const form = document.getElementById('completeForm');
        if (form) {
            form.reset();
            // Remove GPS coordinate inputs
            const latInput = form.querySelector('input[name="selfie_latitude"]');
            const lngInput = form.querySelector('input[name="selfie_longitude"]');
            if (latInput) latInput.remove();
            if (lngInput) lngInput.remove();
        }
        
        console.log('Complete modal closed and reset');
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const rescheduleModal = document.getElementById('rescheduleModal');
    const completeModal = document.getElementById('completeModal');
    
    if (event.target === rescheduleModal) {
        closeRescheduleModal();
    }
    if (event.target === completeModal) {
        closeCompleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRescheduleModal();
        closeCompleteModal();
    }
});

// Global function for viewing visit details
function viewVisit(visitId) {
    // Show detail modal with proper route based on user role
    const modal = document.getElementById('visitDetailModal') || createDetailModal();
    
    // Determine the correct route based on user role
    let detailUrl;
    const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
    
    if (userRole === 'admin') {
        detailUrl = `/admin/visits/${visitId}`;
    } else if (userRole === 'author') {
        detailUrl = `/author/visits/${visitId}/detail`;
    } else if (userRole === 'auditor') {
        detailUrl = `/auditor/visits/${visitId}/detail`;
    } else {
        detailUrl = `/visits/${visitId}`;
    }
    
    modal.classList.remove('hidden');
    
    // Load content
    fetch(detailUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('visit-detail-content').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('visit-detail-content').innerHTML = '<p class="text-red-500">Error loading details</p>';
    });
}

// Create detail modal if it doesn't exist
function createDetailModal() {
    const modal = document.createElement('div');
    modal.id = 'visitDetailModal';
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50';
    modal.innerHTML = `
        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Kunjungan</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="visit-detail-content" class="space-y-6">
                <div class="text-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
}

// Close detail modal
function closeDetailModal() {
    document.getElementById('visitDetailModal')?.classList.add('hidden');
}

// Global function for editing visit (admin)
function editVisit(visitId) {
    window.location.href = `/admin/visits/${visitId}/edit`;
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    let bgColor, textColor, icon;
    switch (type) {
        case 'success':
            bgColor = 'bg-green-500';
            textColor = 'text-white';
            icon = '✓';
            break;
        case 'error':
            bgColor = 'bg-red-500';
            textColor = 'text-white';
            icon = '✕';
            break;
        case 'warning':
            bgColor = 'bg-yellow-500';
            textColor = 'text-white';
            icon = '⚠';
            break;
        default:
            bgColor = 'bg-blue-500';
            textColor = 'text-white';
            icon = 'ℹ';
    }
    
    notification.className += ` ${bgColor} ${textColor}`;
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <span class="text-xl">${icon}</span>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Slide in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing camera controls');
    
    // Setup camera button event listener
    const openCameraButton = document.getElementById('openCameraButton');
    if (openCameraButton) {
        console.log('Found openCameraButton, adding event listener');
        openCameraButton.addEventListener('click', function() {
            console.log('Camera button clicked');
            initializeCameraControls();
        });
    } else {
        console.log('openCameraButton not found');
    }
    
    // Setup other camera event listeners if they exist
    setupCameraEventListeners();
});
</script>