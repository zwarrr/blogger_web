
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Comment</title>
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
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Comment</h2>
      </header>
      <main class="p-6 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
          @if(session('status'))
            <div class="mb-4 rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">{{ session('status') }}</div>
          @endif
          <div class="mb-8">
            
            <!-- Filter Panel -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
              <div class="flex flex-col sm:flex-row gap-4">
                <!-- Filter by Post -->
                <div class="flex-1">
                  <label for="postFilter" class="block text-sm font-medium text-gray-700 mb-2">
                    Filter by Post
                  </label>
                  <select id="postFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Posts</option>
                    @php
                      $uniquePosts = collect($comments)->unique('post_id')->sortBy('post_title');
                    @endphp
                    @foreach($uniquePosts as $post)
                      <option value="{{ $post['post_id'] }}" data-title="{{ $post['post_title'] ?? 'Post '.$post['post_id'] }}">
                        {{ Str::limit($post['post_title'] ?? 'Post '.$post['post_id'], 50) }} ({{ collect($comments)->where('post_id', $post['post_id'])->count() }})
                      </option>
                    @endforeach
                  </select>
                </div>
                
                <!-- Search -->
                <div class="flex-1">
                  <label for="commentSearch" class="block text-sm font-medium text-gray-700 mb-2">
                    Search Comments
                  </label>
                  <div class="relative">
                    <input type="text" id="commentSearch" placeholder="Search comments, authors, or posts..." 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="relative">
            <table class="w-full text-sm text-left table-fixed">
              <colgroup>
                <col style="width: 10%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
                <col style="width: 13%;">
                <col style="width: 22%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 8%;">
              </colgroup>
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">ID</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Post</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Author</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Email</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Comment</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Visible</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Created</th>
                  <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach(($comments ?? []) as $c)
                  <tr class="border-t hover:bg-gray-50 transition-colors comment-row" data-post-id="{{ $c['post_id'] }}" data-post-title="{{ $c['post_title'] ?? 'Post '.$c['post_id'] }}">
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                          CMT{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <div class="text-center">
                          <div class="clamp-1 text-sm font-medium text-gray-900" title="{{ $c['post_title'] ?? $c['post_id'] }}">
                            {{ $c['post_title'] ?? $c['post_id'] }}
                          </div>
                          <div class="text-xs text-gray-500 mt-0.5">ID: {{ $c['post_id'] }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm max-w-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                          </svg>
                          <span class="truncate min-w-0" title="{{ $c['name'] }}">{{ $c['name'] }}</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 max-w-full">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                          </svg>
                          <span class="truncate min-w-0" title="{{ $c['email'] }}">{{ $c['email'] }}</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <div class="clamp-2 text-xs text-gray-600 leading-relaxed text-center max-w-full" title="{{ $c['body'] }}">
                          {{ $c['body'] }}
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $c['is_visible'] ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                          <span class="w-2 h-2 rounded-full {{ $c['is_visible'] ? 'bg-green-500' : 'bg-gray-400' }} flex-shrink-0"></span>
                          <span>{{ $c['is_visible'] ? 'Shown' : 'Hidden' }}</span>
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="text-xs text-gray-600 whitespace-nowrap">
                        {{ $c['created_at'] ?? '—' }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      <div class="relative inline-block text-left">
                        <button type="button" data-menu-button data-menu-id="comment-menu-{{ $loop->index }}"
                          class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none"
                          aria-haspopup="true" aria-expanded="false">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                          </svg>
                        </button>
                        <div id="comment-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                          <form method="POST" action="{{ route('admin.comments.toggle', $c['id']) }}" class="block">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm {{ $c['is_visible'] ? 'text-yellow-700 hover:bg-yellow-50' : 'text-green-700 hover:bg-green-50' }} flex items-center gap-2 transition-colors">
                              @if($c['is_visible'])
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                  <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46A11.804 11.804 0 001 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                                </svg>
                                <span>Hide Comment</span>
                              @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                  <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                                <span>Show Comment</span>
                              @endif
                            </button>
                          </form>
                          <div class="border-t border-gray-100 my-1"></div>
                          <form method="POST" action="{{ route('admin.comments.destroy', $c['id']) }}" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.');" class="block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 flex-shrink-0 text-red-500">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                              </svg>
                              <span>Delete Comment</span>
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
      </main>
    </div>
  </div>

  <script>
    // Kebab dropdown menu
    (function () {
      const buttons = document.querySelectorAll('[data-menu-button]');
      let openMenu = null;

      function closeAny() { 
        if (openMenu) { 
          openMenu.classList.add('hidden'); 
          openMenu = null; 
        } 
      }
      
      function placeMenuByButton(menu, btn) {
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
      }

      buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
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
        });
      });
      
      document.addEventListener('click', closeAny);
      window.addEventListener('scroll', closeAny, { passive: true });
      window.addEventListener('resize', closeAny);
      document.addEventListener('keydown', (e) => { 
        if (e.key === 'Escape') closeAny(); 
      });
    })();
  </script>
  
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
            showLoading('Deleting comment...', 3000);
          } else {
            showLoading('Updating comment...', 5000);
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
  
  <!-- Simple Comment Filter System -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const postFilter = document.getElementById('postFilter');
      const commentSearch = document.getElementById('commentSearch');
      const commentRows = document.querySelectorAll('.comment-row');
      const tableBody = document.querySelector('tbody');
      const totalComments = document.getElementById('totalComments');
      const filteredCount = document.getElementById('filteredCount');
      
      let filterTimeout;
      
      // Update counter displays
      function updateCounters(visible, total) {
        totalComments.textContent = `${total} Total`;
        filteredCount.textContent = `${visible} Showing`;
      }
      
      // Filter function
      function performFilter() {
        const selectedPostId = postFilter.value;
        const searchTerm = commentSearch.value.toLowerCase().trim();
        let visibleCount = 0;
        const totalCount = commentRows.length;
        
        commentRows.forEach(row => {
          const rowPostId = row.dataset.postId;
          const commentText = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
          const authorName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
          const postTitle = row.dataset.postTitle.toLowerCase();
          const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
          
          const matchesPost = !selectedPostId || rowPostId === selectedPostId;
          const matchesSearch = !searchTerm || 
            commentText.includes(searchTerm) || 
            authorName.includes(searchTerm) || 
            postTitle.includes(searchTerm) ||
            email.includes(searchTerm);
          
          if (matchesPost && matchesSearch) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });
        
        // Update counters
        updateCounters(visibleCount, totalCount);
        
        // Handle empty state
        handleEmptyState(visibleCount, selectedPostId, searchTerm);
      }
      
      // Handle empty state
      function handleEmptyState(visibleCount, postId, searchTerm) {
        const existingEmptyState = tableBody.querySelector('.empty-state-row');
        if (existingEmptyState) {
          existingEmptyState.remove();
        }
        
        if (visibleCount === 0 && (postId || searchTerm)) {
          const emptyRow = document.createElement('tr');
          emptyRow.className = 'empty-state-row';
          emptyRow.innerHTML = `
            <td colspan="8" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center gap-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <circle cx="11" cy="11" r="8"/>
                  <path d="m21 21-4.35-4.35"/>
                </svg>
                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-1">No comments found</h3>
                  <p class="text-sm text-gray-500">Try adjusting your filters to see more results.</p>
                </div>
              </div>
            </td>
          `;
          tableBody.appendChild(emptyRow);
        }
      }
      
      // Debounced filter function
      function debouncedFilter() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(performFilter, 300);
      }
      
      // Event listeners
      postFilter.addEventListener('change', performFilter);
      commentSearch.addEventListener('input', debouncedFilter);
      
      // Initialize
      updateCounters(commentRows.length, commentRows.length);
    });
  </script>
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>
