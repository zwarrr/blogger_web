/**
 * Visit Table Management Script
 * Handles the dynamic functionality for visits table in admin and auditor panels
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize visit modal if available
    if (typeof visitModal !== 'undefined' && visitModal.init) {
        visitModal.init();
    }
    
    // Add global event listeners for table actions
    initializeTableActions();
});

/**
 * Initialize table action handlers
 */
function initializeTableActions() {
    // Handle pagination links
    document.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a') || e.target.closest('.pagination a')) {
            e.preventDefault();
            const link = e.target.matches('a') ? e.target : e.target.closest('a');
            const url = link.href;
            
            if (url && !link.classList.contains('disabled')) {
                loadPage(url);
            }
        }
    });
}

/**
 * Load page content via AJAX
 */
function loadPage(url) {
    const tableBody = document.getElementById('table-body');
    const paginationDiv = document.getElementById('pagination');
    
    if (!tableBody || !paginationDiv) return;
    
    // Show loading state
    showLoadingState(tableBody);
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.html && data.pagination) {
            tableBody.innerHTML = data.html;
            paginationDiv.innerHTML = data.pagination;
        } else {
            throw new Error('Invalid response format');
        }
    })
    .catch(error => {
        console.error('Error loading page:', error);
        showError(tableBody, 'Gagal memuat data. Silakan refresh halaman.');
    });
}

/**
 * Show loading state in table
 */
function showLoadingState(tableBody) {
    const colCount = tableBody.querySelector('tr')?.children.length || 6;
    tableBody.innerHTML = `
        <tr>
            <td colspan="${colCount}" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500">Memuat data...</p>
                </div>
            </td>
        </tr>
    `;
}

/**
 * Show error state in table
 */
function showError(tableBody, message) {
    const colCount = tableBody.querySelector('tr')?.children.length || 6;
    tableBody.innerHTML = `
        <tr>
            <td colspan="${colCount}" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-gray-500">${message}</p>
                    <button onclick="window.location.reload()" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Refresh Halaman
                    </button>
                </div>
            </td>
        </tr>
    `;
}

/**
 * Utility function to get CSRF token
 */
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

/**
 * Utility function to get user role
 */
function getUserRole() {
    const role = document.querySelector('meta[name="user-role"]');
    return role ? role.getAttribute('content') : 'admin';
}