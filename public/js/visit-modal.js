// Visit Modal Handler
window.visitModal = {
    modalElement: null,
    modalContent: null,
    loadingState: false,

    init() {
        this.createModal();
        this.bindEvents();
    },

    createModal() {
        // Create modal if it doesn't exist
        if (document.getElementById('visitModal')) {
            this.modalElement = document.getElementById('visitModal');
            this.modalContent = this.modalElement.querySelector('#visitModalContent');
            return;
        }

        const modalHTML = `
            <div id="visitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
                <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Detail Kunjungan</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="visitModal.close()">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="visitModalContent" class="max-h-96 overflow-y-auto">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modalElement = document.getElementById('visitModal');
        this.modalContent = document.getElementById('visitModalContent');
    },

    bindEvents() {
        // Close modal when clicking outside
        this.modalElement.addEventListener('click', (e) => {
            if (e.target === this.modalElement) {
                this.close();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modalElement.classList.contains('hidden')) {
                this.close();
            }
        });

        // Bind detail buttons
        this.bindDetailButtons();
    },

    bindDetailButtons() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-visit-detail]') || e.target.closest('[data-visit-detail]')) {
                e.preventDefault();
                const button = e.target.matches('[data-visit-detail]') ? e.target : e.target.closest('[data-visit-detail]');
                const visitId = button.getAttribute('data-visit-detail');
                this.show(visitId);
            }
        });
    },

    async show(visitId) {
        if (this.loadingState) return;

        this.loadingState = true;
        this.modalElement.classList.remove('hidden');
        
        // Show loading state
        this.modalContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-600"></div>
                <span class="ml-2 text-gray-600">Memuat data kunjungan...</span>
            </div>
        `;

        try {
            // Determine the correct route based on user role
            const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content') || 'admin';
            let route;
            
            switch(userRole) {
                case 'admin':
                    route = `/admin/visits/${visitId}`;
                    break;
                case 'auditor':
                    route = `/auditor/visits/${visitId}`;
                    break;
                case 'author':
                    route = `/author/visits/${visitId}`;
                    break;
                default:
                    route = `/admin/visits/${visitId}`;
            }

            const response = await fetch(route, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            this.modalContent.innerHTML = html;
            
            // Bind form submissions in modal
            this.bindModalForms();
            
        } catch (error) {
            console.error('Error loading visit details:', error);
            this.modalContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-500 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Data</h3>
                    <p class="text-gray-600 mb-4">Terjadi kesalahan saat memuat detail kunjungan.</p>
                    <button onclick="visitModal.close()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Tutup
                    </button>
                </div>
            `;
        } finally {
            this.loadingState = false;
        }
    },

    bindModalForms() {
        // Handle form submissions in modal
        const forms = this.modalContent.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                `;

                try {
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        // Close modal and refresh page or table
                        this.close();
                        
                        // Show success message
                        this.showToast('Berhasil memproses laporan!', 'success');
                        
                        // Refresh the table if it exists
                        if (window.refreshTable && typeof window.refreshTable === 'function') {
                            window.refreshTable();
                        } else {
                            // Fallback: reload page
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        throw new Error('Server error');
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    this.showToast('Terjadi kesalahan saat memproses data.', 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });
        });
    },

    close() {
        this.modalElement.classList.add('hidden');
        this.modalContent.innerHTML = '';
    },

    showToast(message, type = 'info') {
        // Create or get toast container
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
        }

        const typeClasses = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-black'
        };

        const toast = document.createElement('div');
        toast.className = `px-4 py-2 rounded-lg shadow-lg ${typeClasses[type]} transition-all duration-300 transform translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button class="ml-2 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    visitModal.init();
});