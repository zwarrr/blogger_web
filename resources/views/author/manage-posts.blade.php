<!-- resources/views/author/manage-posts.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Author - Manage Posts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
  body { font-family: 'Inter', sans-serif; margin: 0; }
  .clamp-1, .clamp-2, .clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .clamp-1 { -webkit-line-clamp: 1; }
  .clamp-2 { -webkit-line-clamp: 2; }
  .clamp-3 { -webkit-line-clamp: 3; }
  
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
    @include('author.sidebar')
    <div class="flex-1 flex flex-col">
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Posts</h2>
      </header>
      <main class="p-6 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
          @if(session('status'))
            <div class="mb-4 rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">{{ session('status') }}</div>
          @endif
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Your Posts</h3>
            <button id="btnOpenNewPost" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
              New Post
            </button>
          </div>
          <div class="mt-4 overflow-x-auto overflow-y-visible relative">
            <table class="min-w-full text-sm text-left table-fixed">
              <colgroup>
                <col style="width: 90px;">
                <col style="width: 250px;">
                <col style="width: 160px;">
                <col style="width: 220px;">
                <col style="width: 150px;">
                <col style="width: 110px;">
                <col style="width: 90px;">
              </colgroup>
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Cover</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Title</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Category</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Description</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Publish At</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach(($posts ?? []) as $post)
                  <tr class="border-t hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                      @if(!empty($post['cover_image']))
                        <img src="{{ asset('storage/'.$post['cover_image']) }}" alt="{{ $post['title'] }}" class="w-14 h-14 rounded-lg object-cover border border-gray-200 shadow-sm"/>
                      @else
                        <div class="w-14 h-14 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-xs text-gray-400">—</div>
                      @endif
                    </td>
                    <td class="px-4 py-3">
                      <div class="clamp-2 text-sm font-medium text-gray-900 leading-snug" title="{{ $post['title'] }}">{{ $post['title'] }}</div>
                    </td>

                    <td class="px-4 py-3">
                      @if(!empty($post['category']))
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium whitespace-nowrap" style="background-color: {{ $post['category']['color'] }}15; color: {{ $post['category']['color'] }}">
                          <span class="text-sm flex-shrink-0">{{ $post['category']['icon'] }}</span>
                          <span class="truncate max-w-[110px]" title="{{ $post['category']['name'] }}">{{ $post['category']['name'] }}</span>
                        </span>
                      @else
                        <span class="text-gray-400 text-xs">—</span>
                      @endif
                    </td>
                    <td class="px-4 py-3">
                      <div class="clamp-2 text-xs text-gray-600 leading-relaxed" title="{{ $post['description'] ?? '' }}">{{ $post['description'] ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-xs text-gray-600 whitespace-nowrap">{{ $post['published_at'] ?? '—' }}</div>
                    </td>
                    <td class="px-4 py-3">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $post['status']==='Published' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }} whitespace-nowrap">
                        {{ $post['status'] }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="relative inline-block text-left">
                        <button type="button" data-menu-button data-menu-id="post-menu-{{ $loop->index }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-haspopup="true" aria-expanded="false">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z"/></svg>
                        </button>
                        <div id="post-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                          <button class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2" data-action="edit" data-post='@json($post)'>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-500"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm15.71-9.04a1.003 1.003 0 000-1.42l-2.5-2.5a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.99-1.66z"/></svg>
                            Edit
                          </button>
                          <form method="POST" action="{{ route('author.posts.destroy', $post['id']) }}" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-red-500"><path d="M6 7h12v2H6V7zm2 3h8l-1 9H9L8 10zm3-6h2l1 1h4v2H6V5h4l1-1z"/></svg>
                              Delete
                            </button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- New Post Modal -->
        <div id="modalNewPost" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Create New Post</h3>
                <button id="btnCloseNewPost" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <form id="formNewPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" action="{{ route('author.posts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                  <input name="title" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Post title">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <select name="category_id" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="" selected>Pilih Kategori</option>
                    @foreach(($categories ?? []) as $cat)
                      <option value="{{ $cat['id'] }}">{{ $cat['icon'] }} {{ $cat['name'] }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea name="description" class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Write a short description..."></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                  <input name="cover" type="file" accept="image/*" class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700 focus:outline-none">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                  <input name="location" type="text" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="City, Country">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Publish At</label>
                  <input name="published_at" type="datetime-local" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                  <div class="space-y-1">
                    <label class="flex items-center gap-2"><input type="checkbox" name="allow_comments" value="1" class="rounded text-orange-600"> Allow comments</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1" class="rounded text-orange-600"> Pin this post</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" class="rounded text-orange-600"> Mark as featured</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" class="rounded text-orange-600" checked> Publish immediately</label>
                  </div>
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelNewPost" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Save Post</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Edit Post Modal -->
        <div id="modalEditPost" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Post</h3>
                <button id="btnCloseEditPost" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <form id="formEditPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                  <input name="title" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <select name="category_id" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(($categories ?? []) as $cat)
                      <option value="{{ $cat['id'] }}">{{ $cat['icon'] }} {{ $cat['name'] }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea name="description" class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Replace Cover (optional)</label>
                  <input name="cover" type="file" accept="image/*" class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700 focus:outline-none">
                </div>
                <div>
                  <label class="block text sm font-medium text-gray-700 mb-1">Location</label>
                  <input name="location" type="text" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Publish At</label>
                  <input name="published_at" type="datetime-local" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                  <div class="space-y-1">
                    <label class="flex items-center gap-2"><input type="checkbox" name="allow_comments" value="1" class="rounded text-orange-600"> Allow comments</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1" class="rounded text-orange-600"> Pin this post</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" class="rounded text-orange-600"> Mark as featured</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" class="rounded text-orange-600"> Publish immediately</label>
                  </div>
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelEditPost" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Update Post</button>
                </div>
              </form>
            </div>
          </div>
        </div>


        

      </main>
    </div>
  </div>
  
  <!-- Fixed Modal and UI Controls -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      console.log('✅ Author Manage Posts - DOM loaded');
      
      // Modal elements
      const modalNew = document.getElementById('modalNewPost');
      const modalEdit = document.getElementById('modalEditPost');
      
      // New Post Modal Controls
      const btnOpenNew = document.getElementById('btnOpenNewPost');
      const btnCloseNew = document.getElementById('btnCloseNewPost');
      const btnCancelNew = document.getElementById('btnCancelNewPost');
      
      if (btnOpenNew && modalNew) {
        btnOpenNew.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('🔵 Opening New Post Modal');
          modalNew.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        });
      }
      
      if (btnCloseNew && modalNew) {
        btnCloseNew.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('🔴 Closing New Post Modal');
          modalNew.classList.add('hidden');
          document.body.style.overflow = 'auto';
        });
      }
      
      if (btnCancelNew && modalNew) {
        btnCancelNew.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('🔴 Canceling New Post Modal');
          modalNew.classList.add('hidden');
          document.body.style.overflow = 'auto';
        });
      }
      
      // Edit Post Modal Controls
      const btnCloseEdit = document.getElementById('btnCloseEditPost');
      const btnCancelEdit = document.getElementById('btnCancelEditPost');
      
      if (btnCloseEdit && modalEdit) {
        btnCloseEdit.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('🔴 Closing Edit Post Modal');
          modalEdit.classList.add('hidden');
          document.body.style.overflow = 'auto';
        });
      }
      
      if (btnCancelEdit && modalEdit) {
        btnCancelEdit.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('🔴 Canceling Edit Post Modal');
          modalEdit.classList.add('hidden');
          document.body.style.overflow = 'auto';
        });
      }
      
      // Dropdown menu controls
      document.querySelectorAll('[data-menu-button]').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const menuId = this.getAttribute('data-menu-id');
          const menu = document.getElementById(menuId);
          console.log('🔵 Toggle menu:', menuId);
          
          // Close all other menus first
          document.querySelectorAll('[id^="post-menu-"]').forEach(m => {
            if (m !== menu && !m.classList.contains('hidden')) {
              m.classList.add('hidden');
            }
          });
          
          // Toggle current menu
          if (menu) {
            menu.classList.toggle('hidden');
          }
        });
      });
      
      // Edit Post Actions
      document.querySelectorAll('[data-action="edit"]').forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          try {
            const postDataStr = this.getAttribute('data-post');
            const postData = JSON.parse(postDataStr);
            console.log('🔵 Edit post:', postData.title);
            
            const form = document.getElementById('formEditPost');
            if (form && modalEdit) {
              // Set form action with proper Laravel route
              form.action = '{{ route('author.posts.update', '__ID__') }}'.replace('__ID__', postData.id);
              
              // Populate form fields
              const titleField = form.querySelector('[name="title"]');
              const categoryField = form.querySelector('[name="category_id"]');
              const descField = form.querySelector('[name="description"]');
              const locationField = form.querySelector('[name="location"]');
              const publishField = form.querySelector('[name="published_at"]');
              
              if (titleField) titleField.value = postData.title || '';
              if (categoryField) categoryField.value = postData.category_id || '';
              if (descField) descField.value = postData.description || '';
              if (locationField) locationField.value = postData.location || '';
              if (publishField && postData.published_at) {
                publishField.value = postData.published_at.replace(' ', 'T');
              }
              
              // Set checkboxes
              const allowComments = form.querySelector('[name="allow_comments"]');
              const isPinned = form.querySelector('[name="is_pinned"]');
              const isFeatured = form.querySelector('[name="is_featured"]');
              const isPublished = form.querySelector('[name="is_published"]');
              
              if (allowComments) allowComments.checked = !!postData.allow_comments;
              if (isPinned) isPinned.checked = !!postData.is_pinned;
              if (isFeatured) isFeatured.checked = !!postData.is_featured;
              if (isPublished) isPublished.checked = (postData.status === 'Published');
              
              // Close dropdown menu
              const menu = this.closest('[id^="post-menu-"]');
              if (menu) menu.classList.add('hidden');
              
              // Open modal
              modalEdit.classList.remove('hidden');
              document.body.style.overflow = 'hidden';
            }
          } catch (error) {
            console.error('❌ Error editing post:', error);
          }
        });
      });
      
      // Close menus when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-menu-button]') && !e.target.closest('[id^="post-menu-"]')) {
          document.querySelectorAll('[id^="post-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
          });
        }
      });
      
      // Close modals when clicking backdrop
      if (modalNew) {
        modalNew.addEventListener('click', function(e) {
          if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = 'auto';
          }
        });
      }
      
      if (modalEdit) {
        modalEdit.addEventListener('click', function(e) {
          if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = 'auto';
          }
        });
      }
    });
  </script>
  

  
  <!-- Enhanced Animation System -->
  @vite(['resources/js/animations-fixed.js', 'resources/js/loading-fix.js'])
</body>
</html>
