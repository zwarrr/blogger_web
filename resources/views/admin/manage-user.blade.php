
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>User Management</title>
  <script>
    window.Laravel = {
      csrfToken: '{{ csrf_token() }}'
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
  <style>
    body { font-family: 'Inter', sans-serif; margin: 0; }
    .clamp-1,
    .clamp-2,
    .clamp-3 {
      display: -webkit-box;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .clamp-1 { -webkit-line-clamp: 1; }
    .clamp-2 { -webkit-line-clamp: 2; }
    .clamp-3 { -webkit-line-clamp: 3; }
    
    /* Enhanced animations */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.6s ease-out;
    }
    
    .modal-backdrop {
      backdrop-filter: blur(8px);
      animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .btn-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      transition: all 0.3s ease;
    }
    
    .table-row:hover {
      background-color: rgba(59, 130, 246, 0.05);
      transform: scale(1.002);
      transition: all 0.2s ease;
    }
    
    /* Page transition animations with realistic loading */
    .page-transition {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 100vw !important;
      height: 100vh !important;
      background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%) !important;
      backdrop-filter: blur(8px) !important;
      z-index: 99999 !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
      pointer-events: none;
    }

    .page-transition.active {
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: all !important;
    }

    /* Realistic loading spinner */
    @keyframes rotate {
      100% {
        transform: rotate(360deg);
      }
    }

    @keyframes dash {
      0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
      }
      50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35;
      }
      100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124;
      }
    }

    .spinner-circle {
      animation:
        dash 1.5s ease-in-out infinite,
        rotate 2s linear infinite;
      transform-origin: center;
    }

    .loading-text {
      color: #FFCCBC;
      font-size: 1.125rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      animation: pulse 2s ease-in-out infinite;
      user-select: none;
      margin-top: 1.25rem;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.7; }
      50% { opacity: 1; }
    }
  </style>
</head>
<body class="bg-[#F5F7FB] text-gray-800">
  
  <script>
    // Clean initial loading
    document.addEventListener('DOMContentLoaded', function() {
      const initialLoading = document.getElementById('initialLoading');
      if (initialLoading) {
        setTimeout(() => {
          initialLoading.classList.remove('active');
          setTimeout(() => {
            initialLoading.style.display = 'none';
          }, 300);
        }, 800);
      }
    });
  </script>

  <!-- Page Transition Overlay (Hidden by default) -->
  <div class="page-transition" id="pageTransition" style="opacity: 0; visibility: hidden; pointer-events: none;">
    <svg class="w-24 h-24 text-[#FF5722]" viewBox="0 0 50 50">
      <circle
        class="spinner-circle"
        cx="25"
        cy="25"
        r="20"
        fill="none"
        stroke="currentColor"
        stroke-width="4"
        stroke-linecap="round"
      />
    </svg>
    <p class="loading-text">loadinggg......</p>
  </div>
  
  <div class="ml-64 flex-1 flex flex-col min-h-screen">
    @include('admin.sidebar')
    <div class="flex-1 flex flex-col">
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <h2 class="text-xl font-semibold text-gray-900">User Management</h2>
      </header>
      <main class="p-6 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5 fade-in">
          @if(session('status'))
            <div class="mb-4 rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">{{ session('status') }}</div>
          @endif
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">All Users</h3>
            <button id="btnOpenNewUser" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 shadow-sm btn-hover">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
              New User
            </button>
          </div>
          <div class="mt-4 relative overflow-hidden">
            <table class="w-full text-sm text-left table-fixed">
              <colgroup>
                <col style="width: 10%;">
                <col style="width: 28%;">
                <col style="width: 35%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
              </colgroup>
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">ID</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Name</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Email</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Role</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($users ?? []) as $user)
                  <tr class="border-t hover:bg-gray-50 transition-colors table-row">
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                          USR{{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm max-w-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                          </svg>
                          <span class="truncate min-w-0" title="{{ $user['name'] }}">{{ $user['name'] }}</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 max-w-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                          </svg>
                          <span class="truncate min-w-0" title="{{ $user['email'] }}">{{ $user['email'] }}</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                          {{ ucfirst($user['role']) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="relative inline-block text-left">
                        <button type="button" data-menu-button data-menu-id="user-menu-{{ $loop->index }}"
                          class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none"
                          aria-haspopup="true" aria-expanded="false">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                          </svg>
                        </button>
                        <div id="user-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                          <button type="button" 
                                  class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-2 transition-colors"
                                  onclick="openEditUserModal({{ json_encode($user ?? []) }})">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 flex-shrink-0">
                              <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm15.71-9.04a1.003 1.003 0 000-1.42l-2.5-2.5a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.99-1.66z"/>
                            </svg>
                            <span>Edit User</span>
                          </button>
                          <div class="border-t border-gray-100 my-1"></div>
                          <button type="button"
                                  onclick="if(confirm('Are you sure you want to delete user: {{ $user['name'] ?? 'Unknown User' }}? This action cannot be undone.')) { alert('Delete functionality will be implemented when route is added.'); }"
                                  class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 flex-shrink-0">
                              <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                            <span>Delete User</span>
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                      <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                          </svg>
                        </div>
                        <div>
                          <p class="text-sm font-medium text-gray-900 mb-1">No users found</p>
                          <p class="text-xs text-gray-500">Create your first user to get started.</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- New User Modal -->
        <div id="modalNewUser" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Create New User</h3>
                <button id="btnCloseNewUser" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <form id="formNewUser" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" action="{{ route('admin.users.store') ?? '#' }}">
                @csrf
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                  <input name="name" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Full name">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input name="email" type="email" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Email address">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                  <select name="role" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none bg-white">
                    <option value="user">User</option>
                    <option value="author" selected>Author</option>
                    <option value="auditor">Auditor</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                  <input name="password" type="password" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Set password">
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelNewUser" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none" onclick="return validateForm(this.form)">Save User</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Edit User Modal -->
        <div id="modalEditUser" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
                <button id="btnCloseEditUser" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <div class="p-5 grid grid-cols-2 gap-5 text-sm">
                <input type="hidden" id="editUserId">
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                  <input id="editUserName" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Full name">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input id="editUserEmail" type="email" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Email address">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                  <select id="editUserRole" disabled class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none bg-gray-100 cursor-not-allowed">
                    <option value="admin">Admin</option>
                    <option value="author">Author</option>
                    <option value="auditor">Auditor</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Role cannot be changed after creation</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                  <input id="editUserPassword" type="password" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Leave blank to keep current">
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelEditUser" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="button" id="btnUpdateUser" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Update User</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          // Form validation function
          window.validateForm = function(form) {
            try {
              const nameInput = form.querySelector('input[name="name"]');
              const emailInput = form.querySelector('input[name="email"]');
              const passwordInput = form.querySelector('input[name="password"]');
              
              if (!nameInput || !nameInput.value.trim()) {
                alert('Please enter a name');
                if (nameInput) nameInput.focus();
                return false;
              }
              
              if (!emailInput || !emailInput.value.trim()) {
                alert('Please enter an email');
                if (emailInput) emailInput.focus();
                return false;
              }
              
              // Basic email validation
              const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(emailInput.value.trim())) {
                alert('Please enter a valid email address');
                emailInput.focus();
                return false;
              }
              
              if (!passwordInput || !passwordInput.value.trim()) {
                alert('Please enter a password');
                if (passwordInput) passwordInput.focus();
                return false;
              }
              
              if (passwordInput.value.length < 6) {
                alert('Password must be at least 6 characters long');
                passwordInput.focus();
                return false;
              }
              
              return true;
            } catch (error) {
              console.error('Form validation error:', error);
              return false;
            }
          };

          (function(){
            try {
              // New User Modal logic
              const modal = document.getElementById('modalNewUser');
              const openBtn = document.getElementById('btnOpenNewUser');
              const closeBtn = document.getElementById('btnCloseNewUser');
              const cancelBtn = document.getElementById('btnCancelNewUser');

              function open(){
                if (modal) {
                  modal.classList.remove('hidden');
                  document.documentElement.classList.add('overflow-hidden');
                  document.body.classList.add('overflow-hidden');
                }
              }
              function close(){
                if (modal) {
                  modal.classList.add('hidden');
                  document.documentElement.classList.remove('overflow-hidden');
                  document.body.classList.remove('overflow-hidden');
                }
              }

              if (openBtn) openBtn.addEventListener('click', open);
              if (closeBtn) closeBtn.addEventListener('click', close);
              if (cancelBtn) cancelBtn.addEventListener('click', close);
              if (modal) modal.addEventListener('click', (e)=>{ if(e.target === modal) close(); });

              // Edit User Modal logic
              const editModal = document.getElementById('modalEditUser');
              const editCloseBtn = document.getElementById('btnCloseEditUser');
              const editCancelBtn = document.getElementById('btnCancelEditUser');

              function openEditModal(){
                if (editModal) {
                  editModal.classList.remove('hidden');
                  document.documentElement.classList.add('overflow-hidden');
                  document.body.classList.add('overflow-hidden');
                }
              }
              function closeEditModal(){
                if (editModal) {
                  editModal.classList.add('hidden');
                  document.documentElement.classList.remove('overflow-hidden');
                  document.body.classList.remove('overflow-hidden');
                }
              }

              if (editCloseBtn) editCloseBtn.addEventListener('click', closeEditModal);
              if (editCancelBtn) editCancelBtn.addEventListener('click', closeEditModal);
              if (editModal) editModal.addEventListener('click', (e)=>{ if(e.target === editModal) closeEditModal(); });
              
              // Update user button event listener
              const updateUserBtn = document.getElementById('btnUpdateUser');
              if (updateUserBtn) {
                updateUserBtn.addEventListener('click', handleUpdateUser);
              }

            // Global function to open edit modal with user data
            window.openEditUserModal = function(user) {
              try {
                if (!user || typeof user !== 'object') {
                  console.error('Invalid user data provided');
                  return;
                }
                
                const editUserId = document.getElementById('editUserId');
                const editUserName = document.getElementById('editUserName');
                const editUserEmail = document.getElementById('editUserEmail');
                const editUserRole = document.getElementById('editUserRole');
                
                if (editUserId) editUserId.value = user.id || '';
                if (editUserName) editUserName.value = user.name || '';
                if (editUserEmail) editUserEmail.value = user.email || '';
                if (editUserRole) editUserRole.value = user.role || 'user';
                
                openEditModal();
                closeAny(); // Close any open dropdown
              } catch (error) {
                console.error('Error opening edit modal:', error);
                alert('Error opening edit form. Please try again.');
              }
            };

            // Function to handle user update
            function handleUpdateUser() {
              try {
                const userId = document.getElementById('editUserId').value;
                const userName = document.getElementById('editUserName').value;
                const userEmail = document.getElementById('editUserEmail').value;
                const userPassword = document.getElementById('editUserPassword').value;

                if (!userId || !userName || !userEmail) {
                  alert('Please fill in all required fields');
                  return;
                }

                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(userEmail)) {
                  alert('Please enter a valid email address');
                  return;
                }

                // Show loading
                showLoading('Updating user...');

                // Debug log
                console.log('Updating user:', {userId, userName, userEmail});

                // Prepare form data
                const formData = new FormData();
                formData.append('name', userName);
                formData.append('email', userEmail);
                if (userPassword) {
                  formData.append('password', userPassword);
                }
                formData.append('_method', 'PUT');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                  formData.append('_token', csrfToken.getAttribute('content'));
                } else {
                  // Try to get from Laravel's global window variable as fallback
                  const laravelCsrf = window.Laravel && window.Laravel.csrfToken;
                  if (laravelCsrf) {
                    formData.append('_token', laravelCsrf);
                  } else {
                    console.error('CSRF token not found');
                    alert('CSRF token not found. Please refresh the page.');
                    hideLoading();
                    return;
                  }
                }

                // Send AJAX request
                fetch(`/admin/users/${userId}`, {
                  method: 'POST',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  },
                  body: formData
                })
                .then(response => response.json().then(data => ({
                  status: response.status,
                  ok: response.ok,
                  data: data
                })))
                .then(({status, ok, data}) => {
                  hideLoading();
                  console.log('Response:', {status, ok, data});
                  if (ok && data.success) {
                    alert('User updated successfully!');
                    closeEditModal();
                    location.reload(); // Reload page to show updated data
                  } else {
                    console.error('Update failed:', data);
                    alert(data.message || 'Failed to update user');
                  }
                })
                .catch(error => {
                  hideLoading();
                  console.error('Error updating user:', error);
                  alert('Error updating user. Please try again.');
                });

              } catch (error) {
                hideLoading();
                console.error('Error in handleUpdateUser:', error);
                alert('Error updating user. Please try again.');
              }
            }

              // Kebab dropdown menu
              const buttons = document.querySelectorAll('[data-menu-button]');
              let openMenu = null;

              function closeAny() { 
                if (openMenu) { 
                  openMenu.classList.add('hidden'); 
                  openMenu = null; 
                } 
              }
              
              function placeMenuByButton(menu, btn) {
                try {
                  // Portal to body and use fixed positioning to avoid clipping
                  if (menu.parentElement !== document.body) {
                    document.body.appendChild(menu);
                  }
                  menu.classList.remove('absolute');
                  menu.style.position = 'fixed';
                  menu.style.zIndex = '9999';
                  const b = btn.getBoundingClientRect();
                  
                  // Position below and right-aligned to button
                  const menuWidth = menu.offsetWidth || 160;
                  const menuHeight = menu.offsetHeight || 80;
                  let left = Math.min(window.innerWidth - menuWidth - 8, Math.max(8, b.right - menuWidth));
                  let top = b.bottom + 8;
                  
                  // If bottom overflows, flip above
                  if (top + menuHeight > window.innerHeight - 8) {
                    top = Math.max(8, b.top - menuHeight - 8);
                  }
                  
                  // If left still overflows, clamp
                  if (left < 8) left = 8;
                  
                  menu.style.left = left + 'px';
                  menu.style.top = top + 'px';
                  menu.style.transform = 'none';
                  menu.style.willChange = 'auto';
                } catch (error) {
                  console.error('Error positioning menu:', error);
                }
              }

              buttons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                  try {
                    e.stopPropagation();
                    const id = btn.getAttribute('data-menu-id');
                    const menu = document.getElementById(id);
                    if (!menu) return;
                    
                    if (openMenu && openMenu !== menu) { 
                      openMenu.classList.add('hidden'); 
                      openMenu = null; 
                    }
                    
                    const willOpen = menu.classList.contains('hidden');
                    menu.classList.toggle('hidden');
                    
                    if (willOpen) { 
                      placeMenuByButton(menu, btn); 
                    }
                    
                    openMenu = willOpen ? menu : null;
                  } catch (error) {
                    console.error('Error handling menu click:', error);
                  }
                });
              });
              
              document.addEventListener('click', closeAny);
              window.addEventListener('scroll', closeAny, { passive: true });
              window.addEventListener('resize', closeAny);
              document.addEventListener('keydown', (e) => { 
                if (e.key === 'Escape') closeAny(); 
              });
            } catch (error) {
              console.error('Error initializing user management:', error);
            }
          })();
        </script>
      </main>
    </div>
  </div>
  
  <!-- Enhanced CRUD Loading Setup -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let loadingTimeout;
      
      function showLoading(message = 'Processing...', duration = null) {
        const pageTransition = document.getElementById('pageTransition');
        if (pageTransition) {
          pageTransition.style.display = 'flex';
          pageTransition.style.opacity = '1';
          pageTransition.style.visibility = 'visible';
          pageTransition.style.pointerEvents = 'all';
          pageTransition.classList.add('active');
          
          const loadingText = pageTransition.querySelector('.loading-text');
          if (loadingText) {
            loadingText.textContent = message;
          }
          
          if (duration) {
            clearTimeout(loadingTimeout);
            loadingTimeout = setTimeout(() => {
              hideLoading();
            }, duration);
          }
        }
      }

      function hideLoading() {
        const pageTransition = document.getElementById('pageTransition');
        if (pageTransition) {
          pageTransition.style.opacity = '0';
          setTimeout(() => {
            pageTransition.style.display = 'none';
            pageTransition.style.visibility = 'hidden';
            pageTransition.style.pointerEvents = 'none';
            pageTransition.classList.remove('active');
          }, 300);
        }
      }

      function showLoading2(message = 'Processing...', duration = null) {
        const pageTransition = document.getElementById('pageTransition');
        if (pageTransition) {
          pageTransition.style.display = 'flex';
          pageTransition.style.opacity = '1';
          pageTransition.style.visibility = 'visible';
          pageTransition.style.pointerEvents = 'all';
          pageTransition.classList.add('active');
          
          const loadingText = pageTransition.querySelector('.loading-text');
          if (loadingText) {
            loadingText.textContent = message;
          }
          
          if (duration) {
            clearTimeout(loadingTimeout);
            loadingTimeout = setTimeout(() => {
              hideLoading();
            }, duration);
          }
        }
      }
      
      function hideLoading() {
        const pageTransition = document.getElementById('pageTransition');
        if (pageTransition) {
          pageTransition.style.opacity = '0';
          pageTransition.style.visibility = 'hidden';
          pageTransition.style.pointerEvents = 'none';
          pageTransition.classList.remove('active');
          setTimeout(() => {
            pageTransition.style.display = 'none';
          }, 300);
        }
        clearTimeout(loadingTimeout);
      }
      
      window.showLoading = showLoading;
      window.hideLoading = hideLoading;
      
      // Only actual form submissions
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');
          if (isDelete) {
            showLoading('Deleting user...', 3000);
          } else {
            showLoading('Saving changes...', 5000);
          }
        });
      });
      
      // Navigation links
      document.querySelectorAll('a[href*="/admin/"], a[href*="/author/"]').forEach(link => {
        link.addEventListener('click', function(e) {
          if (!this.href.includes('#') && !this.target) {
            showLoading('Loading page...', 2000);
          }
        });
      });
    });
  </script>
  
  <!-- Enhanced Anti-Stuck Loading Protection -->
  <script>
    window.loadingDebug = true;
    window.forceHideLoading = function() {
      const loadings = document.querySelectorAll('[id*="Loading"], [id*="Transition"], .page-transition');
      loadings.forEach(el => {
        if (el) {
          el.style.opacity = '0';
          el.style.visibility = 'hidden';
          el.style.pointerEvents = 'none';
          el.classList.remove('active');
          setTimeout(() => {
            el.style.display = 'none';
          }, 300);
        }
      });
      if (window.loadingTimeout) clearTimeout(window.loadingTimeout);
    };
    
    window.addEventListener('load', () => {
      setTimeout(() => {
        if (window.hideLoading) {
          window.hideLoading();
        } else {
          window.forceHideLoading();
        }
      }, 500);
    });
    
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        if (window.hideLoading) {
          window.hideLoading();
        } else {
          window.forceHideLoading();
        }
      }
    });
    
    let clickCount = 0;
    document.addEventListener('click', () => {
      clickCount++;
      if (clickCount > 3) {
        const transition = document.getElementById('pageTransition');
        if (transition && transition.style.opacity === '1') {
          if (window.hideLoading) window.hideLoading();
          else window.forceHideLoading();
        }
        clickCount = 0;
      }
    });
    
    setInterval(() => { clickCount = 0; }, 2000);
  </script>
  
  <!-- Custom JavaScript -->
  <script>
    // Enhanced user management functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize tooltips and animations
      initializeUserManagement();
    });

    function initializeUserManagement() {
      // Add smooth transitions
      const elements = document.querySelectorAll('.fade-in');
      elements.forEach((el, index) => {
        setTimeout(() => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
        }, index * 100);
      });

      // Form validation
      const form = document.getElementById('userForm');
      if (form) {
        form.addEventListener('submit', validateUserForm);
      }
    }

    function validateUserForm(event) {
      const form = event.target;
      const email = form.querySelector('input[name="email"]');
      const name = form.querySelector('input[name="name"]');
      const password = form.querySelector('input[name="password"]');

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email.value)) {
        alert('Please enter a valid email address');
        event.preventDefault();
        return false;
      }

      // Name validation
      if (name.value.length < 2) {
        alert('Name must be at least 2 characters long');
        event.preventDefault();
        return false;
      }

      // Password validation
      if (password.value.length < 6) {
        alert('Password must be at least 6 characters long');
        event.preventDefault();
        return false;
      }

      return true;
    }

    // Modal functions
    function openModal() {
      const modal = document.getElementById('userModal');
      if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
          modal.classList.add('opacity-100');
        }, 10);
      }
    }

    function closeModal() {
      const modal = document.getElementById('userModal');
      if (modal) {
        modal.classList.remove('opacity-100');
        setTimeout(() => {
          modal.classList.add('hidden');
        }, 300);
      }
    }

    // Close modal on escape
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeModal();
      }
    });
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>
