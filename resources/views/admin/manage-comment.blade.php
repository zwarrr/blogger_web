<!-- resources/views/admin/manage-comment.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Comment</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <p class="loading-text">Memuat...</p>
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
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">All Comments</h3>
          </div>
          <div class="mt-4 relative">
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
                  <tr class="border-t hover:bg-gray-50 transition-colors">
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
  
  <!-- Anti-Stuck Loading Protection -->
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
        }
      });
    };
    
    // Auto force-hide any stuck loading after page load
    window.addEventListener('load', () => {
      setTimeout(window.forceHideLoading, 1000);
    });
    
    // Escape key protection
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') window.forceHideLoading();
    });
  </script>
  
  @vite(['resources/js/animations.js'])
</body>
</html>
