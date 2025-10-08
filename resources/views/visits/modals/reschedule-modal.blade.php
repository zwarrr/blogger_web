<!-- Reschedule Visit Modal -->
<div id="rescheduleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50" style="backdrop-filter: blur(4px);">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-lg">
        <div class="relative bg-white rounded-xl shadow-2xl mx-4 sm:mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Undur Jadwal Kunjungan</h3>
                        <p class="text-sm text-gray-500">Ubah tanggal dan waktu kunjungan</p>
                    </div>
                </div>
                <button onclick="closeRescheduleModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <form id="rescheduleForm" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Warning Info -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-amber-800">Informasi Penting</h4>
                                <p class="text-sm text-amber-700 mt-1">Anda dapat mengundur jadwal maksimal 3 kali per kunjungan.</p>
                                <p id="remainingAttempts" class="text-sm font-medium text-amber-800 mt-1"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-5">
                        <!-- Date Input -->
                        <div>
                            <label for="new_visit_date" class="block text-sm font-semibold text-gray-900 mb-2">
                                <svg class="w-4 h-4 inline mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Tanggal & Waktu Baru
                            </label>
                            <input type="datetime-local" 
                                   id="new_visit_date" 
                                   name="new_visit_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                                   required>
                        </div>
                        
                        <!-- Reason Input -->
                        <div>
                            <label for="reschedule_reason" class="block text-sm font-semibold text-gray-900 mb-2">
                                <svg class="w-4 h-4 inline mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Alasan Pengunduran Jadwal
                            </label>
                            <textarea id="reschedule_reason" 
                                      name="reschedule_reason" 
                                      rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none"
                                      placeholder="Jelaskan alasan pengunduran jadwal dengan detail..."
                                      required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="flex items-center justify-end space-x-3 px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                <button type="button" 
                        onclick="closeRescheduleModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </button>
                <button type="submit" 
                        form="rescheduleForm"
                        id="rescheduleSubmitBtn"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-orange-600 to-red-600 rounded-lg hover:from-orange-700 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="rescheduleButtonText">Undur Jadwal</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentVisitId = null;

function rescheduleVisit(visitId, remainingAttempts = null) {
    currentVisitId = visitId;
    const modal = document.getElementById('rescheduleModal');
    const form = document.getElementById('rescheduleForm');
    
    // Set form action URL
    form.action = `/author/visits/${visitId}/reschedule`;
    
    // Update remaining attempts info
    const remainingElement = document.getElementById('remainingAttempts');
    if (remainingAttempts !== null) {
        remainingElement.textContent = `Sisa kesempatan: ${remainingAttempts} kali lagi`;
    } else {
        remainingElement.textContent = 'Sisa kesempatan: 3 kali lagi';
    }
    
    // Set minimum date to today
    const now = new Date();
    const today = now.toISOString().slice(0, 16);
    document.getElementById('new_visit_date').min = today;
    
    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.relative').classList.add('animate-pulse');
    }, 50);
    
    // Focus on first input
    document.getElementById('new_visit_date').focus();
}

function closeRescheduleModal() {
    const modal = document.getElementById('rescheduleModal');
    modal.classList.add('hidden');
    
    // Reset form and state
    document.getElementById('rescheduleForm').reset();
    resetSubmitButton();
    currentVisitId = null;
}

function setSubmitButtonLoading(loading = true) {
    const submitBtn = document.getElementById('rescheduleSubmitBtn');
    const buttonText = document.getElementById('rescheduleButtonText');
    
    if (loading) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75');
        buttonText.innerHTML = `
            <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75');
        buttonText.innerHTML = `
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Undur Jadwal
        `;
    }
}

function resetSubmitButton() {
    setSubmitButtonLoading(false);
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
    
    if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                ${message}
            </div>
        `;
    } else if (type === 'error') {
        notification.classList.add('bg-red-500', 'text-white');
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                ${message}
            </div>
        `;
    }
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// Handle form submission
document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentVisitId) {
        showNotification('Terjadi kesalahan: Visit ID tidak ditemukan', 'error');
        return;
    }
    
    setSubmitButtonLoading(true);
    
    const formData = new FormData(this);
    const data = {
        new_visit_date: formData.get('new_visit_date'),
        reschedule_reason: formData.get('reschedule_reason')
    };
    
    fetch(`/author/visits/${currentVisitId}/reschedule`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        setSubmitButtonLoading(false);
        
        if (data.success) {
            showNotification(data.message || 'Jadwal berhasil diundur!', 'success');
            closeRescheduleModal();
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Gagal mengundur jadwal', 'error');
        }
    })
    .catch(error => {
        setSubmitButtonLoading(false);
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memproses permintaan', 'error');
    });
});

// Close modal when clicking outside
document.getElementById('rescheduleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRescheduleModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('rescheduleModal').classList.contains('hidden')) {
        closeRescheduleModal();
    }
});
</script>