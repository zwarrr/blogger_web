<!-- Complete Visit Modal -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium leading-6 text-gray-900">Selesaikan Kunjungan</h3>
                <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="completeForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <!-- Content Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan/Keterangan Audit <span class="text-red-500">*</span>
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Masukkan detail hasil kunjungan, temuan audit, dan catatan penting..."
                                      required></textarea>
                        </div>
                        
                        <!-- Selfie Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Selfie <span class="text-red-500">*</span>
                            </label>
                            
                            <!-- Camera Preview Area -->
                            <div id="cameraArea" class="hidden mb-3 border-2 border-dashed border-gray-300 rounded-lg p-4">
                                <video id="cameraStream" 
                                       class="w-full max-w-sm mx-auto rounded-lg" 
                                       autoplay 
                                       muted></video>
                                <div class="flex gap-2 mt-3 justify-center">
                                    <button type="button" id="captureButton" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                        📷 Ambil Foto
                                    </button>
                                    <button type="button" id="stopCameraButton" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                                        ❌ Tutup Kamera
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Captured Image Area -->
                            <div id="capturedImageArea" class="hidden mb-3 border-2 border-green-300 rounded-lg p-4 bg-green-50">
                                <canvas id="capturedCanvas" class="w-full max-w-sm mx-auto rounded-lg"></canvas>
                                <div class="flex gap-2 mt-3 justify-center">
                                    <button type="button" id="retakeButton" 
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                                        🔄 Ambil Ulang
                                    </button>
                                    <button type="button" id="useImageButton" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                        ✓ Gunakan Foto Ini
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Control Buttons -->
                            <div class="flex gap-2 mb-3">
                                <button type="button" id="openCameraButton" 
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Buka Kamera
                                </button>
                                <label for="selfie_photo_file" 
                                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm cursor-pointer text-center">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Pilih dari Galeri
                                </label>
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