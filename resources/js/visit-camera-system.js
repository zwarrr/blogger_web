/**
 * Visit Camera System
 * Handles camera functionality for visit completion
 */

class VisitCameraSystem {
    constructor() {
        this.currentStream = null;
        this.capturedImageBlob = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', () => {
            this.bindCameraEvents();
        });
    }

    bindCameraEvents() {
        const openCameraButton = document.getElementById('openCameraButton');
        const stopCameraButton = document.getElementById('stopCameraButton');
        const captureButton = document.getElementById('captureButton');
        const retakeButton = document.getElementById('retakeButton');
        const useImageButton = document.getElementById('useImageButton');
        const fileInput = document.getElementById('selfie_photo_file');
        
        if (openCameraButton) {
            openCameraButton.addEventListener('click', () => this.openCamera());
        }
        
        if (stopCameraButton) {
            stopCameraButton.addEventListener('click', () => this.stopCamera());
        }
        
        if (captureButton) {
            captureButton.addEventListener('click', () => this.capturePhoto());
        }
        
        if (retakeButton) {
            retakeButton.addEventListener('click', () => this.retakePhoto());
        }
        
        if (useImageButton) {
            useImageButton.addEventListener('click', () => this.useCurrentImage());
        }
        
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }
    }

    async openCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user' }, 
                audio: false 
            });
            
            this.currentStream = stream;
            const video = document.getElementById('cameraStream');
            if (video) {
                video.srcObject = stream;
                video.play();
                
                // Show camera area, hide open button
                document.getElementById('cameraArea')?.classList.remove('hidden');
                document.getElementById('openCameraButton')?.classList.add('hidden');
                document.getElementById('capturedImageArea')?.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        }
    }

    stopCamera() {
        if (this.currentStream) {
            this.currentStream.getTracks().forEach(track => track.stop());
            this.currentStream = null;
        }
        
        // Hide camera area, show open button
        document.getElementById('cameraArea')?.classList.add('hidden');
        document.getElementById('openCameraButton')?.classList.remove('hidden');
        document.getElementById('capturedImageArea')?.classList.add('hidden');
    }

    capturePhoto() {
        const video = document.getElementById('cameraStream');
        const canvas = document.getElementById('capturedCanvas');
        
        if (video && canvas) {
            const context = canvas.getContext('2d');
            
            // Set canvas dimensions to match video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw the video frame to canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convert canvas to blob
            canvas.toBlob((blob) => {
                this.capturedImageBlob = blob;
                
                // Hide camera, show captured image
                document.getElementById('cameraArea')?.classList.add('hidden');
                document.getElementById('capturedImageArea')?.classList.remove('hidden');
                
                // Stop camera stream
                if (this.currentStream) {
                    this.currentStream.getTracks().forEach(track => track.stop());
                }
            }, 'image/jpeg', 0.8);
        }
    }

    retakePhoto() {
        // Clear captured image
        this.capturedImageBlob = null;
        
        // Show camera area again
        document.getElementById('capturedImageArea')?.classList.add('hidden');
        this.openCamera();
    }

    useCurrentImage() {
        if (this.capturedImageBlob) {
            // Create a File object from the blob
            const file = new File([this.capturedImageBlob], 'selfie.jpg', { type: 'image/jpeg' });
            
            // Create a new FileList and assign to the file input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            
            const fileInput = document.getElementById('selfie_photo_file');
            if (fileInput) {
                fileInput.files = dataTransfer.files;
            }
            
            // Hide captured image area, show open button
            document.getElementById('capturedImageArea')?.classList.add('hidden');
            document.getElementById('openCameraButton')?.classList.remove('hidden');
            
            // Show success message
            this.showMessage('Foto selfie berhasil dipilih!', 'success');
        }
    }

    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.showMessage('Foto berhasil dipilih dari galeri!', 'success');
        }
    }

    showMessage(message, type = 'info') {
        // Create a temporary message element
        const messageEl = document.createElement('div');
        messageEl.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        messageEl.textContent = message;
        
        document.body.appendChild(messageEl);
        
        // Remove after 3 seconds
        setTimeout(() => {
            document.body.removeChild(messageEl);
        }, 3000);
    }

    // Cleanup method
    cleanup() {
        if (this.currentStream) {
            this.currentStream.getTracks().forEach(track => track.stop());
            this.currentStream = null;
        }
    }
}

// Initialize the camera system
const visitCameraSystem = new VisitCameraSystem();

// Global functions for backward compatibility
function completeVisit(visitId) {
    const modal = document.getElementById('completeModal');
    const form = document.getElementById('completeForm');
    
    if (form) {
        // Set form action
        form.action = `/auditor/visits/${visitId}/complete`;
        
        // Show modal
        modal?.classList.remove('hidden');
        
        // Get GPS coordinates
        if (navigator.geolocation) {
            const gpsStatus = document.getElementById('gpsStatus');
            if (gpsStatus) gpsStatus.textContent = 'Mengambil koordinat GPS...';
            
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
                    
                    if (gpsStatus) {
                        gpsStatus.textContent = 
                            `Koordinat berhasil diambil: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
                    }
                },
                function(error) {
                    if (gpsStatus) gpsStatus.textContent = 'Tidak dapat mengambil koordinat GPS';
                    console.error('GPS Error:', error);
                }
            );
        } else {
            const gpsStatus = document.getElementById('gpsStatus');
            if (gpsStatus) gpsStatus.textContent = 'GPS tidak didukung browser';
        }
    }
}

function closeCompleteModal() {
    const modal = document.getElementById('completeModal');
    modal?.classList.add('hidden');
    
    // Cleanup camera
    visitCameraSystem.cleanup();
    
    // Reset form
    const form = document.getElementById('completeForm');
    if (form) {
        form.reset();
        
        // Reset UI states
        document.getElementById('cameraArea')?.classList.add('hidden');
        document.getElementById('capturedImageArea')?.classList.add('hidden');
        document.getElementById('openCameraButton')?.classList.remove('hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('completeModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCompleteModal();
            }
        });
    }
});