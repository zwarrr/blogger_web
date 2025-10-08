/**
 * Visit Modal Management System
 * Handles all visit-related modal operations
 */

class VisitModalManager {
    constructor() {
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.bindModalEvents();
        });
    }

    bindModalEvents() {
        // Close modals when clicking outside
        this.bindOutsideClickClose('rescheduleModal', () => this.closeRescheduleModal());
        this.bindOutsideClickClose('completeModal', () => this.closeCompleteModal());
        this.bindOutsideClickClose('viewModal', () => this.closeViewModal());
    }

    bindOutsideClickClose(modalId, closeFunction) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeFunction();
                }
            });
        }
    }

    // Reschedule Modal Methods
    openRescheduleModal(visitId) {
        const modal = document.getElementById('rescheduleModal');
        const form = document.getElementById('rescheduleForm');
        
        if (form) {
            // Set form action URL
            form.action = `/author/visits/${visitId}/reschedule`;
            
            // Show modal
            modal?.classList.remove('hidden');
            
            // Set minimum date to today
            const now = new Date();
            const today = now.toISOString().slice(0, 16);
            const dateInput = document.getElementById('new_visit_date');
            if (dateInput) {
                dateInput.min = today;
            }
        }
    }

    closeRescheduleModal() {
        const modal = document.getElementById('rescheduleModal');
        modal?.classList.add('hidden');
        
        // Reset form
        const form = document.getElementById('rescheduleForm');
        if (form) {
            form.reset();
        }
    }

    // Complete Modal Methods  
    openCompleteModal(visitId) {
        const modal = document.getElementById('completeModal');
        const form = document.getElementById('completeForm');
        
        if (form) {
            // Set form action
            form.action = `/auditor/visits/${visitId}/complete`;
            
            // Show modal
            modal?.classList.remove('hidden');
            
            // Get GPS coordinates
            this.getCurrentGPSLocation(form);
        }
    }

    closeCompleteModal() {
        const modal = document.getElementById('completeModal');
        modal?.classList.add('hidden');
        
        // Cleanup camera if exists
        if (window.visitCameraSystem) {
            window.visitCameraSystem.cleanup();
        }
        
        // Reset form and UI states
        const form = document.getElementById('completeForm');
        if (form) {
            form.reset();
            
            // Reset camera UI
            document.getElementById('cameraArea')?.classList.add('hidden');
            document.getElementById('capturedImageArea')?.classList.add('hidden');
            document.getElementById('openCameraButton')?.classList.remove('hidden');
        }
    }

    // View Modal Methods
    openViewModal(visitId) {
        // This would typically load visit details via AJAX
        console.log('Opening view modal for visit:', visitId);
        
        // For now, just show the modal if it exists
        const modal = document.getElementById('viewModal');
        modal?.classList.remove('hidden');
    }

    closeViewModal() {
        const modal = document.getElementById('viewModal');
        modal?.classList.add('hidden');
    }

    // GPS Helper Method
    getCurrentGPSLocation(form) {
        const gpsStatus = document.getElementById('gpsStatus');
        
        if (navigator.geolocation) {
            if (gpsStatus) gpsStatus.textContent = 'Mengambil koordinat GPS...';
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Remove existing coordinate inputs if any
                    const existingLatInput = form.querySelector('input[name="selfie_latitude"]');
                    const existingLngInput = form.querySelector('input[name="selfie_longitude"]');
                    if (existingLatInput) existingLatInput.remove();
                    if (existingLngInput) existingLngInput.remove();
                    
                    // Add hidden inputs for coordinates
                    const latInput = document.createElement('input');
                    latInput.type = 'hidden';
                    latInput.name = 'selfie_latitude';
                    latInput.value = position.coords.latitude;
                    form.appendChild(latInput);
                    
                    const lngInput = document.createElement('input');
                    lngInput.type = 'hidden';
                    lngInput.name = 'selfie_longitude';
                    lngInput.value = position.coords.longitude;
                    form.appendChild(lngInput);
                    
                    if (gpsStatus) {
                        gpsStatus.textContent = 
                            `Koordinat berhasil diambil: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
                    }
                },
                (error) => {
                    if (gpsStatus) gpsStatus.textContent = 'Tidak dapat mengambil koordinat GPS';
                    console.error('GPS Error:', error);
                }
            );
        } else {
            if (gpsStatus) gpsStatus.textContent = 'GPS tidak didukung browser';
        }
    }

    // Form Validation Helper
    validateCompleteForm() {
        const form = document.getElementById('completeForm');
        if (!form) return false;

        const notes = form.querySelector('#notes');
        const selfieInput = form.querySelector('#selfie_photo_file');

        if (!notes || !notes.value.trim()) {
            alert('Catatan/keterangan audit harus diisi!');
            notes?.focus();
            return false;
        }

        if (!selfieInput || !selfieInput.files.length) {
            alert('Foto selfie harus diambil!');
            return false;
        }

        return true;
    }
}

// Initialize the modal manager
const visitModalManager = new VisitModalManager();

// Global functions for backward compatibility and easy access
function rescheduleVisit(visitId) {
    visitModalManager.openRescheduleModal(visitId);
}

function closeRescheduleModal() {
    visitModalManager.closeRescheduleModal();
}

function completeVisit(visitId) {
    visitModalManager.openCompleteModal(visitId);
}

function closeCompleteModal() {
    visitModalManager.closeCompleteModal();
}

function viewVisit(visitId) {
    visitModalManager.openViewModal(visitId);
}

function closeViewModal() {
    visitModalManager.closeViewModal();
}

// Form submission handler for complete visit
document.addEventListener('DOMContentLoaded', function() {
    const completeForm = document.getElementById('completeForm');
    if (completeForm) {
        completeForm.addEventListener('submit', function(e) {
            if (!visitModalManager.validateCompleteForm()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';
            }
        });
    }
});