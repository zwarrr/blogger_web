<!-- resources/views/author/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Author Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    
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
  @php
    // Ensure variables are defined to avoid blade errors when controller not providing them (fallbacks for safety)
    $totalPosts = $totalPosts ?? 0;
    $publishedPosts = $publishedPosts ?? 0;
    $draftPosts = $draftPosts ?? 0;
    $totalComments = $totalComments ?? 0;
    $postsByDay = $postsByDay ?? collect([]);
    $maxCount = $maxCount ?? 1;
    $topCategories = $topCategories ?? collect([]);
    $recentPosts = $recentPosts ?? collect([]);
    $recentComments = $recentComments ?? collect([]);
  @endphp
  
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
  <style>
    .bar { transition: height .2s ease; }
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

    {{-- Sidebar --}}
    @include('author.sidebar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

      <!-- Topbar -->
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Dashboard</h2>
      </header>

      <!-- Page Content -->
      <main class="p-6 space-y-6">

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="file-text" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Total Posts</h3>
              </div>
              <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $totalPosts }}</p>
            <p class="text-xs text-gray-500 mt-1">All time</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="check-circle" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Published</h3>
              </div>
              <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $publishedPosts }}</p>
            <p class="text-xs text-gray-500 mt-1">Posts visible publicly</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="file" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Drafts</h3>
              </div>
              <span class="text-xs text-yellow-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $draftPosts }}</p>
            <p class="text-xs text-gray-500 mt-1">Not yet published</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="message-square" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Comments</h3>
              </div>
              <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $totalComments }}</p>
            <p class="text-xs text-gray-500 mt-1">All on your posts</p>
          </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg border border-gray-200 p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-feather="bar-chart-2" class="w-5 h-5 text-orange-500"></i>
                Posts Created (7 days)
              </h3>
              <div class="text-sm text-gray-500">Daily counts</div>
            </div>
            <!-- Simple bar chart using CSS; height scaled to maxCount -->
            <div class="flex items-end gap-3 h-40">
              @foreach($postsByDay as $d)
                @php
                  $h = $maxCount > 0 ? max(4, intval(($d['count'] / $maxCount) * 100)) : 4; // ensure visible
                @endphp
                <div class="flex-1 flex flex-col items-center justify-end">
                  <div class="w-full bg-orange-400 bar rounded-t-md" style="height: {{ $h }}%"></div>
                  <div class="text-xs mt-1 text-gray-600">{{ $d['label'] }}</div>
                </div>
              @endforeach
              @if($postsByDay->isEmpty())
                @for($i=0;$i<7;$i++)
                  <div class="flex-1 flex flex-col items-center justify-end">
                    <div class="w-full bg-orange-200 bar rounded-t-md" style="height: 5%"></div>
                    <div class="text-xs mt-1 text-gray-400">&nbsp;</div>
                  </div>
                @endfor
              @endif
            </div>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i data-feather="tag" class="w-5 h-5 text-orange-500"></i>
              Top Categories
            </h3>
            <ul class="space-y-3 text-sm">
              @forelse($topCategories as $c)
                <li class="flex items-center justify-between"><span>{{ $c['name'] }}</span><span class="font-semibold">{{ $c['pct'] }}%</span></li>
              @empty
                <li class="text-gray-500">No data</li>
              @endforelse
            </ul>
          </div>
        </div>

        <!-- Recent Posts Table -->
        <div class="bg-white border border-gray-200 rounded-lg">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
              <i data-feather="list" class="w-5 h-5 text-orange-500"></i>
              Recent Posts
            </h3>
          </div>
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-gray-600">Title</th>
                <th class="px-6 py-3 text-gray-600">Date</th>
                <th class="px-6 py-3 text-gray-600">Status</th>
                <th class="px-6 py-3 text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentPosts as $p)
                <tr class="border-t">
                  <td class="px-6 py-4">{{ $p['title'] }}</td>
                  <td class="px-6 py-4">{{ $p['date'] }}</td>
                  <td class="px-6 py-4">
                    @if($p['status'] === 'Published')
                      <span class="text-green-600">Published</span>
                    @else
                      <span class="text-yellow-600">Draft</span>
                    @endif
                  </td>
                  <td class="px-6 py-4">
                    <a href="{{ url('/author/posts') }}" class="text-orange-600 hover:underline">Manage</a>
                  </td>
                </tr>
              @empty
                <tr class="border-t">
                  <td colspan="4" class="px-6 py-4 text-gray-500">No recent posts</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white border border-gray-200 rounded-lg">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
              <i data-feather="message-circle" class="w-5 h-5 text-orange-500"></i>
              Recent Comments
            </h3>
          </div>
          <div class="divide-y">
            @forelse($recentComments as $c)
              <div class="p-4">
                <p class="text-sm"><strong>{{ $c['name'] }}:</strong> {{ $c['excerpt'] }}</p>
                <p class="text-xs text-gray-500 mt-1">On "{{ $c['post_title'] }}" • {{ $c['date'] }}</p>
              </div>
            @empty
              <div class="p-4 text-gray-500">No recent comments</div>
            @endforelse
          </div>
        </div>

      </main>
      <script>
        if (window.feather) { window.feather.replace({ 'stroke-width': 2 }); }
      </script>
    </div>
  </div>

  <!-- CRUD Loading Setup -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Function to show loading
      function showLoading(message = 'loadinggg......') {
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
        }
      }
      
      // Add loading to all forms
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          showLoading('loadinggg......');
        });
      });
      
      // Add loading to action buttons
      document.querySelectorAll('button[type="submit"], [data-action], .btn-create, .btn-edit, .btn-delete').forEach(button => {
        button.addEventListener('click', function() {
          showLoading('loadinggg......');
        });
      });
    });
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
