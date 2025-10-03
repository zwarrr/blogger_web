<!-- resources/views/admin/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
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
    
    /* Trading chart styles */
    .chart-point {
      transition: all 0.2s ease;
      cursor: pointer;
    }
    
    .chart-point:hover {
      filter: drop-shadow(0 0 8px rgba(249, 115, 22, 0.8));
    }
    
    /* Chart grid animation */
    @keyframes gridPulse {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 0.6; }
    }
    
    /* Trading line animation */
    @keyframes drawLine {
      to {
        stroke-dashoffset: 0;
      }
    }
    
    .trading-line {
      filter: drop-shadow(0 0 4px rgba(249, 115, 22, 0.4));
    }
    
    /* Pulse animation for data points */
    @keyframes pointPulse {
      0%, 100% { r: 5; }
      50% { r: 6; }
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

    {{-- Sidebar --}}
    @include('admin.sidebar')

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
            <p class="text-2xl font-bold mt-2">{{ $totalPosts ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">All time</p>
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
            <p class="text-2xl font-bold mt-2">{{ $totalComments ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">All time</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="grid" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Categories</h3>
              </div>
              <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $totalCategories ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">All time</p>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                  <i data-feather="users" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-500">Users</h3>
              </div>
              <span class="text-xs text-green-600">&nbsp;</span>
            </div>
            <p class="text-2xl font-bold mt-2">{{ $totalUsers ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">All time</p>
          </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg border border-gray-200 p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-feather="bar-chart-2" class="w-5 h-5 text-orange-500"></i>
                Posts Created
                @if(!empty($postsByDay))
                  @php
                    $total = collect($postsByDay)->sum('count');
                    $recent = collect($postsByDay)->slice(-2)->sum('count');
                    $previous = collect($postsByDay)->slice(-4, 2)->sum('count');
                    $trend = $previous > 0 ? (($recent - $previous) / $previous) * 100 : ($recent > 0 ? 100 : 0);
                  @endphp
                  <span class="ml-2 px-2 py-1 bg-orange-100 text-orange-700 text-sm rounded-full font-medium">
                    {{ $total }} total
                  </span>
                  @if($trend > 0)
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full flex items-center gap-1">
                      <i data-feather="trending-up" class="w-3 h-3"></i>
                      +{{ number_format($trend, 1) }}%
                    </span>
                  @elseif($trend < 0)
                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full flex items-center gap-1">
                      <i data-feather="trending-down" class="w-3 h-3"></i>
                      {{ number_format($trend, 1) }}%
                    </span>
                  @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full flex items-center gap-1">
                      <i data-feather="minus" class="w-3 h-3"></i>
                      0%
                    </span>
                  @endif
                @endif
              </h3>
              <div class="text-sm text-gray-500 flex items-center gap-2">
                Last 7 days
                <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></span>
                <span class="text-xs">Live</span>
              </div>
            </div>
            <!-- Trading-style line chart -->
            <div class="relative h-48 bg-gray-50 rounded-lg p-4 border border-gray-200">
              <!-- Grid lines -->
              <div class="absolute inset-4 grid grid-rows-4 grid-cols-6 gap-0">
                @for($i = 0; $i < 4; $i++)
                  <div class="col-span-6 border-t border-gray-200 opacity-50"></div>
                @endfor
              </div>
              
              <!-- Chart container -->
              <svg class="w-full h-full" viewBox="0 0 420 180" preserveAspectRatio="none">
                @php 
                  $maxCount = $maxCount ?? 1;
                  $points = [];
                  $chartData = [];
                @endphp
                
                @if(!empty($postsByDay) && is_countable($postsByDay))
                  @foreach($postsByDay as $index => $d)
                    @php
                      $count = $d['count'] ?? 0;
                      $x = ($index * 60) + 30; // 60px spacing
                      $y = 160 - (($count / max($maxCount, 1)) * 140); // Invert Y for SVG
                      $points[] = $x . ',' . $y;
                      $chartData[] = ['x' => $x, 'y' => $y, 'count' => $count, 'label' => $d['label'] ?? ''];
                    @endphp
                  @endforeach
                  
                  <!-- Area fill gradient -->
                  <defs>
                    <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" style="stop-color:#f97316;stop-opacity:0.3" />
                      <stop offset="100%" style="stop-color:#f97316;stop-opacity:0.05" />
                    </linearGradient>
                  </defs>
                  
                  <!-- Area fill -->
                  <path d="M30,160 {{ implode(' ', array_map(fn($p) => 'L'.$p, $points)) }} L390,160 Z" 
                        fill="url(#areaGradient)" opacity="0.8"/>
                  
                  <!-- Main line with animation -->
                  <polyline points="{{ implode(' ', $points) }}" 
                           fill="none" 
                           stroke="#f97316" 
                           stroke-width="3" 
                           stroke-linecap="round" 
                           stroke-linejoin="round"
                           class="trading-line"
                           style="stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: drawLine 2s ease-out forwards;"/>
                  
                  <!-- Data points -->
                  @foreach($chartData as $point)
                    <circle cx="{{ $point['x'] }}" 
                            cy="{{ $point['y'] }}" 
                            r="5" 
                            fill="#f97316" 
                            stroke="white" 
                            stroke-width="2" 
                            class="hover:r-6 transition-all cursor-pointer chart-point" 
                            data-count="{{ $point['count'] }}" 
                            data-label="{{ $point['label'] }}"/>
                  @endforeach
                @else
                  <!-- No data line -->
                  <line x1="30" y1="160" x2="390" y2="160" stroke="#d1d5db" stroke-width="2" stroke-dasharray="5,5"/>
                  <text x="210" y="90" text-anchor="middle" fill="#9ca3af" font-size="14">No data available</text>
                @endif
              </svg>
              
              <!-- Y-axis labels -->
              <div class="absolute left-0 top-4 bottom-4 flex flex-col justify-between text-xs text-gray-500">
                <span>{{ $maxCount }}</span>
                <span>{{ intval($maxCount * 0.75) }}</span>
                <span>{{ intval($maxCount * 0.5) }}</span>
                <span>{{ intval($maxCount * 0.25) }}</span>
                <span>0</span>
              </div>
              
              <!-- X-axis labels -->
              <div class="absolute bottom-0 left-8 right-8 flex justify-between text-xs text-gray-600">
                @if(!empty($postsByDay) && is_countable($postsByDay))
                  @foreach($postsByDay as $d)
                    <span>{{ $d['label'] ?? '' }}</span>
                  @endforeach
                @else
                  @for($i=0;$i<7;$i++)
                    @php $dayName = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][now()->subDays(6-$i)->dayOfWeek]; @endphp
                    <span>{{ $dayName }}</span>
                  @endfor
                @endif
              </div>
              
              <!-- Floating tooltip -->
              <div id="chart-tooltip" class="absolute bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 pointer-events-none transition-opacity z-20 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                  <span id="tooltip-content">0 posts</span>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i data-feather="tag" class="w-5 h-5 text-orange-500"></i>
              Top Categories
            </h3>
            <ul class="space-y-3 text-sm">
              @if(!empty($topCategories) && count($topCategories) > 0)
                @foreach($topCategories as $c)
                  <li class="flex items-center justify-between"><span>{{ $c['name'] }}</span><span class="font-semibold">{{ $c['pct'] }}%</span></li>
                @endforeach
              @else
                <li class="text-gray-500">No data</li>
              @endif
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
              @if(!empty($recentPosts) && count($recentPosts) > 0)
                @foreach($recentPosts as $p)
                  <tr class="border-t">
                    <td class="px-6 py-4">{{ $p['title'] }}</td>
                    <td class="px-6 py-4">{{ $p['date'] }}</td>
                    <td class="px-6 py-4">
                      @if(($p['status'] ?? '') === 'Published')
                        <span class="text-green-600">Published</span>
                      @else
                        <span class="text-yellow-600">Draft</span>
                      @endif
                    </td>
                    <td class="px-6 py-4"><a href="{{ url('/admin/posts') }}" class="text-orange-600 hover:underline">Manage</a></td>
                  </tr>
                @endforeach
              @else
                <tr class="border-t"><td colspan="4" class="px-6 py-4 text-gray-500">No recent posts</td></tr>
              @endif
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
            @if(!empty($recentComments) && count($recentComments) > 0)
              @foreach($recentComments as $c)
                <div class="p-4">
                  <p class="text-sm"><strong>{{ $c['name'] }}:</strong> {{ $c['excerpt'] }}</p>
                  <p class="text-xs text-gray-500 mt-1">On "{{ $c['post_title'] }}" • {{ $c['date'] }}</p>
                </div>
              @endforeach
            @else
              <div class="p-4 text-gray-500">No recent comments</div>
            @endif
          </div>
        </div>
</main>
      <script>
        if (window.feather) {
          window.feather.replace({ 'stroke-width': 2 });
        }
        
        // Trading chart interactions
        document.addEventListener('DOMContentLoaded', function() {
          const tooltip = document.getElementById('chart-tooltip');
          const tooltipContent = document.getElementById('tooltip-content');
          
          // Chart point hover effects
          document.querySelectorAll('.chart-point').forEach(point => {
            point.addEventListener('mouseenter', function(e) {
              const count = this.getAttribute('data-count');
              const label = this.getAttribute('data-label');
              
              if (tooltip && tooltipContent) {
                tooltipContent.textContent = `${label}: ${count} post${count != 1 ? 's' : ''}`;
                tooltip.classList.remove('opacity-0');
                tooltip.classList.add('opacity-100');
              }
              
              // Highlight point
              this.setAttribute('r', '7');
              this.style.filter = 'drop-shadow(0 0 6px rgba(249, 115, 22, 0.6))';
            });
            
            point.addEventListener('mouseleave', function() {
              if (tooltip) {
                tooltip.classList.remove('opacity-100');
                tooltip.classList.add('opacity-0');
              }
              
              // Reset point
              this.setAttribute('r', '5');
              this.style.filter = 'none';
            });
            
            point.addEventListener('mousemove', function(e) {
              if (tooltip) {
                const rect = this.closest('.relative').getBoundingClientRect();
                tooltip.style.left = (e.clientX - rect.left + 10) + 'px';
                tooltip.style.top = (e.clientY - rect.top - 10) + 'px';
              }
            });
            
            // Click to copy data
            point.addEventListener('click', function() {
              const count = this.getAttribute('data-count');
              const label = this.getAttribute('data-label');
              const text = `${label}: ${count} posts`;
              
              if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                  if (tooltipContent) {
                    const originalText = tooltipContent.textContent;
                    tooltipContent.textContent = 'Copied!';
                    setTimeout(() => {
                      tooltipContent.textContent = originalText;
                    }, 1000);
                  }
                });
              }
            });
          });
        });
      </script>
      
    </div>
  </div>

  <!-- Enhanced CRUD Loading Setup -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let loadingTimeout;
      
      // Function to show loading with better control
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
          
          // Auto-hide loading after specified duration
          if (duration) {
            clearTimeout(loadingTimeout);
            loadingTimeout = setTimeout(() => {
              hideLoading();
            }, duration);
          }
        }
      }
      
      // Function to hide loading
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
      
      // Make functions globally accessible
      window.showLoading = showLoading;
      window.hideLoading = hideLoading;
      
      // Add loading only to actual form submissions (server requests)
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          showLoading('Processing request...', 5000);
        });
      });
      
      // Add loading to delete actions only
      document.querySelectorAll('form[method="POST"] button[type="submit"]').forEach(button => {
        button.addEventListener('click', function(e) {
          const form = this.closest('form');
          if (form && form.querySelector('input[name="_method"][value="DELETE"]')) {
            showLoading('Deleting...', 3000);
          }
        });
      });
      
      // Navigation links should show loading
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
    // Enhanced loading protection system
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
      
      // Clear any pending timeouts
      if (window.loadingTimeout) {
        clearTimeout(window.loadingTimeout);
      }
    };
    
    // Auto-hide loading on page fully loaded
    window.addEventListener('load', () => {
      setTimeout(() => {
        if (window.hideLoading) {
          window.hideLoading();
        } else {
          window.forceHideLoading();
        }
      }, 500);
    });
    
    // Multiple escape mechanisms
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        if (window.hideLoading) {
          window.hideLoading();
        } else {
          window.forceHideLoading();
        }
      }
    });
    
    // Click anywhere to hide stuck loading (emergency)
    let clickCount = 0;
    document.addEventListener('click', (e) => {
      clickCount++;
      if (clickCount > 3) {
        const transition = document.getElementById('pageTransition');
        if (transition && transition.style.opacity === '1') {
          if (window.hideLoading) {
            window.hideLoading();
          } else {
            window.forceHideLoading();
          }
        }
        clickCount = 0;
      }
    });
    
    // Reset click count after 2 seconds
    setInterval(() => {
      clickCount = 0;
    }, 2000);
  </script>
  
  @vite(['resources/js/animations-fixed.js', 'resources/js/loading-fix.js'])

</body>
</html>
