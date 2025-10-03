<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#EA580C" />
  <title>Berita | Blogger</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"rel="stylesheet" />
  @vite(['resources/css/app.css'])
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    .clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.5;
      height: 3rem;
      text-overflow: ellipsis;
      word-break: break-word;
    }

    .clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.6;
      height: 4.8rem;
      text-overflow: ellipsis;
      word-break: break-word;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out;
    }

    .animate-stagger>* {
      animation: fadeInUp 0.6s ease-out;
    }

    .animate-stagger>*:nth-child(1) {
      animation-delay: 0.1s;
    }

    .animate-stagger>*:nth-child(2) {
      animation-delay: 0.2s;
    }

    .animate-stagger>*:nth-child(3) {
      animation-delay: 0.3s;
    }

    .animate-stagger>*:nth-child(4) {
      animation-delay: 0.4s;
    }

    .animate-stagger>*:nth-child(5) {
      animation-delay: 0.5s;
    }

    .animate-stagger>*:nth-child(6) {
      animation-delay: 0.6s;
    }

    .hover-lift {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3);
    }

    /* Animasi floating ringan untuk gambar promosi */
    @keyframes float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-8px);
      }
    }

    .animate-float {
      animation: float 6s ease-in-out infinite;
    }

    /* Partikel background */
    #particles-js {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
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

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Smooth section scroll */
    html {
      scroll-behavior: smooth;
    }

    /* Button press animation */
    .btn-press {
      transform: scale(1);
      transition: all 0.1s ease;
    }

    .btn-press:active {
      transform: scale(0.95);
    }

    /* Card hover animations */
    .card-hover {
      transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      transform: translateY(0px) scale(1);
      box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.1);
    }

    .card-hover:hover {
      transform: translateY(-12px) scale(1.03);
      box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.35), 0 0 0 1px rgba(234, 88, 12, 0.1);
      z-index: 10;
    }

    /* Ripple effect for buttons */
    .ripple {
      position: relative;
      overflow: hidden;
    }

    .ripple::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .ripple:active::before {
      width: 300px;
      height: 300px;
    }

    /* Fade in animation for page load */
    .fade-in-page {
      opacity: 0;
      animation: fadeInPage 0.8s ease-out forwards;
    }

    @keyframes fadeInPage {
      to {
        opacity: 1;
      }
    }

    /* Cards are always visible - no reveal animations */
    .stagger-container .stagger-item,
    .stagger-container article {
      opacity: 1 !important;
      transform: translateY(0) !important;
      visibility: visible !important;
    }
    
    /* Ensure main content is visible */
    main {
      opacity: 1 !important;
      transform: translateY(0) !important;
      visibility: visible !important;
    }

    /* Navigation smooth transition - hover removed */
    .nav-link {
      position: relative;
    }

    /* Loading spinner improvements */
    .page-transition .loader::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 80px;
      height: 80px;
      margin: -40px 0 0 -40px;
      border: 3px solid rgba(255, 255, 255, 0.1);
      border-top: 3px solid rgba(255, 255, 255, 0.8);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    /* Improved hover effects for cards */
    .card-hover:hover .card-content {
      transform: translateY(-4px);
    }

    .card-content {
      transition: transform 0.4s ease;
    }

    /* Card image hover effect */
    .card-hover:hover .card-image {
      transform: scale(1.1);
    }

    .card-image {
      transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Card overlay effect on hover */
    .card-overlay {
      background: linear-gradient(135deg, rgba(234, 88, 12, 0.1), rgba(249, 115, 22, 0.05));
      opacity: 0;
      transition: opacity 0.4s ease;
    }

    .card-hover:hover .card-overlay {
      opacity: 1;
    }

    /* Card shimmer effect */
    .card-shimmer {
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.6s ease;
      pointer-events: none;
    }

    .card-hover:hover .card-shimmer {
      left: 100%;
    }

    /* Card bounce effect */
    @keyframes cardBounce {
      0%, 100% { transform: translateY(-12px) scale(1.03); }
      50% { transform: translateY(-14px) scale(1.03); }
    }

    .card-hover:hover {
      animation: cardBounce 2s ease-in-out infinite;
    }

    /* Bounce animation for statistics */
    @keyframes bounce-gentle {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-4px);
      }
      60% {
        transform: translateY(-2px);
      }
    }

    .bounce-gentle {
      animation: bounce-gentle 2s infinite;
    }

    /* Pulse animation for active elements */
    @keyframes pulse-subtle {
      0% {
        box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(234, 88, 12, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(234, 88, 12, 0);
      }
    }

    .pulse-subtle {
      animation: pulse-subtle 2s infinite;
    }

    /* Gradient text animation */
    @keyframes gradient-shift {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .gradient-animated {
      background: linear-gradient(-45deg, #EA580C, #F97316, #FB923C, #EA580C);
      background-size: 400% 400%;
      animation: gradient-shift 3s ease infinite;
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#FFF7ED',
              100: '#FFEDD5',
              200: '#FED7AA',
              300: '#FDBA74',
              400: '#FB923C',
              500: '#F97316',
              600: '#EA580C',
              700: '#C2410C',
              800: '#9A3412',
              900: '#7C2D12'
            }
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="dns-prefetch" href="https://cdn.tailwindcss.com" />
  <link rel="icon" href="/favicon.ico" />
</head>

<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50 text-gray-800 antialiased fade-in-page">
  


  <!-- Page Transition Overlay (Hidden by default) -->
  <div class="page-transition" id="pageTransition">
    <div class="loading-container">
      <div class="loading-spinner"></div>
      <p class="loading-text">loadinggg......</p>
    </div>
  </div>

  <!-- Partikel Layer -->
  <div id="particles-js"></div>

<!-- Top Bar -->
<header class="bg-white/90 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 shadow-sm">
  <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
    <a href="{{ route('user.views') }}" class="flex items-center gap-2 page-link no-underline">
      <span
        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white font-bold shadow-md">B</span>
      <span class="text-lg font-bold text-gray-900">Blogger</span>
    </a>
  </div>
</header>


  <!-- Hero / Search -->
  <section
    class="bg-gradient-to-br from-brand-50 via-orange-50 to-white border-b border-gray-200/70 shadow-sm overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 py-16 sm:py-20 lg:py-24">
      <div class="flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-16">
        <!-- Left Content -->
        <div class="flex-1 animate-fade-in-up max-w-2xl lg:max-w-xl">
          <div
            class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-sm mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24"
              fill="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            <span class="text-sm font-medium text-gray-700">Platform Berita Terpercaya</span>
          </div>
          <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-5">
            Berita Terbaru <span class="text-brand-600">Hari Ini</span>
          </h1>
          <p class="text-lg md:text-xl text-gray-600 mb-8 leading-relaxed">Rangkuman informasi terkini dengan gaya yang
            ringan dan profesional. Akses ribuan artikel berkualitas tanpa batas.</p>

          <!-- Stats (Pembaca Aktif & Artikel) -->
          <div class="flex flex-wrap items-center gap-8 mb-10">
            <div class="flex items-center gap-3">
              <div class="flex -space-x-2">
                <div
                  class="w-12 h-12 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 border-2 border-white flex items-center justify-center shadow-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                      d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                  </svg>
                </div>
                <div
                  class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 border-2 border-white shadow-md">
                </div>
                <div
                  class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 border-2 border-white shadow-md">
                </div>
              </div>
              <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers ?? 0) }}+</p>
                <p class="text-sm text-gray-600">Pembaca Aktif</p>
              </div>
            </div>
            <div class="h-12 w-px bg-gray-300"></div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPosts ?? 0) }}+</p>
              <p class="text-sm text-gray-600">Artikel Tersedia</p>
            </div>
          </div>

          <!-- CTA Buttons -->
          <div class="flex flex-wrap gap-4">
            <a href="#artikel"
              class="inline-flex items-center gap-2 text-white bg-brand-600 hover:bg-brand-700 px-8 py-4 rounded-xl text-base font-semibold shadow-lg transition-all hover:shadow-xl hover:-translate-y-1 ripple btn-press smooth-scroll">
              Mulai Membaca
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
            <a href="https://www.blogger.com/about"
              class="inline-flex items-center gap-2 text-brand-600 bg-white hover:bg-gray-50 px-8 py-4 rounded-xl text-base font-semibold border-2 border-brand-600 transition-all hover:shadow-md ripple btn-press page-link">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
              Pelajari Lebih Lanjut
            </a>
          </div>
        </div>

        <!-- Right Image (Promosi) -->
        <div class="relative w-full lg:w-[500px] animate-fade-in-up">
          <div class="absolute -top-8 -right-8 w-80 h-80 bg-brand-200/30 rounded-full blur-3xl"></div>
          <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-orange-200/40 rounded-full blur-3xl"></div>

          <div class="relative z-10 group">
            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&q=80" alt="Reading News"
              class="w-full h-auto rounded-3xl shadow-xl transform transition duration-700 ease-in-out group-hover:scale-105 animate-float" />

            <div class="absolute top-8 -left-4 bg-white rounded-2xl px-4 py-3 shadow-xl bounce-gentle pulse-subtle">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                  </svg>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Live Update</p>
                  <p class="text-sm font-bold text-gray-900">Real-time</p>
                </div>
              </div>
            </div>

            <div class="absolute bottom-12 -right-6 bg-white rounded-2xl px-4 py-3 shadow-xl bounce-gentle" 
              style="animation-delay: 1s;">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center pulse-subtle">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                  </svg>
                </div>
                <div>
                  <p class="text-xs text-gray-500">Total Artikel</p>
                  <p class="text-sm font-bold text-gray-900">{{ number_format($totalPosts ?? 0) }}+ Posts</p>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-4 py-12" id="artikel">
    <div class="flex items-center justify-between mb-10">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-900 flex items-center gap-3 gradient-animated">
        <span
          class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
          </svg>
        </span>
        <span>Daftar Berita</span>
      </h2>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 stagger-container">
      @foreach($posts as $post)
        <article
          class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-lg shadow-gray-200/50 hover-lift card-hover group h-full flex flex-col relative">
          <!-- Card Overlay -->
          <div class="absolute inset-0 card-overlay rounded-2xl pointer-events-none z-10"></div>
          <!-- Card Shimmer Effect -->
          <div class="absolute inset-0 card-shimmer rounded-2xl z-20"></div>
          
          <div class="relative h-48 overflow-hidden">
            <img src="{{ $post->thumbnail ?? $post->cover_image ?? 'https://via.placeholder.com/600x300?text=No+Cover' }}"
              alt="{{ $post->title }}"
              class="w-full h-full object-cover card-image">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            @if($post->category)
              <div
                class="absolute left-4 bottom-4 inline-flex items-center gap-1.5 rounded-full bg-white/80 backdrop-blur-sm px-3 py-1.5 text-xs font-semibold shadow-md"
                style="color: {{ $post->category->color }}">
                <span>{{ $post->category->icon }}</span>
                <span>{{ $post->category->name }}</span>
              </div>
            @else
              <div
                class="absolute left-4 bottom-4 inline-flex items-center gap-2 rounded-full bg-white/95 backdrop-blur-sm text-brand-700 border border-white/80 px-3 py-1.5 text-xs font-semibold shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                Tidak Berkategori
              </div>
            @endif
          </div>
          <div class="p-5 flex flex-col flex-1 card-content relative z-20">
            <div class="flex-1">
              <h3 class="text-base font-bold text-gray-900 clamp-2 group-hover:text-brand-600 transition-all duration-300 group-hover:scale-105 mb-3">
                {{ $post->title }}
              </h3>
              <p class="text-gray-600 text-sm clamp-3 group-hover:text-gray-700 transition-colors duration-300">{{ Str::limit(strip_tags($post->content), 120) }}</p>
            </div>
            <div class="mt-5 flex items-center justify-between">
              <span class="inline-flex items-center gap-2 text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span class="font-medium">{{ optional($post->created_at)->format('d M Y') }}</span>
              </span>
              <a href="{{ route('user.detail', $post->id) }}"
                class="inline-flex items-center gap-1.5 text-white bg-brand-600 hover:bg-brand-700 px-4 py-2 rounded-lg text-sm font-semibold shadow-md transition-all hover:shadow-lg group-hover:shadow-brand-300 group-hover:scale-105 ripple btn-press page-link">
                Baca
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-8 flex justify-center">{{ $posts->links() }}</div>
  </main>

  <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200 mt-16">
    <div class="max-w-6xl mx-auto px-4 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <div>
        <div class="flex items-center gap-2"> <span
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white font-bold shadow-md">B</span>
          <span class="text-base font-bold text-gray-900">Blogger</span>
        </div>
        <p class="mt-4 text-sm text-gray-600 leading-relaxed">Platform untuk membaca dan membagikan cerita, opini, dan
          berita terbaru dengan tampilan modern.</p>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900">Jelajahi</p>
        <ul class="mt-3 space-y-2 text-sm text-gray-600">
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors" href="https://www.blogger.com/about"
              target="_blank" rel="noopener noreferrer">Tentang Kami</a></li>
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors"
              href="https://support.google.com/blogger/answer/42095" target="_blank"
              rel="noopener noreferrer">Kontak</a></li>
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors" href="https://www.blogger.com/features"
              target="_blank" rel="noopener noreferrer">Kontributor</a></li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900">Bantuan</p>
        <ul class="mt-3 space-y-2 text-sm text-gray-600">
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors" href="https://support.google.com/blogger"
              target="_blank" rel="noopener noreferrer">Pusat Bantuan</a></li>
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors" href="https://www.blogger.com/terms"
              target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a></li>
          <li><a class="hover:text-brand-700 text-brand-600 transition-colors"
              href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a>
          </li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900 mb-4">Ikuti Kami</p>
        <div class="flex items-center gap-3"> <a href="https://facebook.com/blogger" target="_blank"
            rel="noopener noreferrer" aria-label="Facebook"
            class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#1877F2] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 btn-press">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
            </svg> <span
              class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Facebook</span>
          </a> <a href="https://twitter.com/blogger" target="_blank" rel="noopener noreferrer" aria-label="Twitter"
            class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#000000] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 btn-press">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
            </svg> <span
              class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Twitter</span>
          </a> <a href="https://instagram.com/blogger" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
            class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-gradient-to-br from-[#833AB4] via-[#FD1D1D] to-[#F77737] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 btn-press">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
            </svg> <span
              class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Instagram</span>
          </a> <a href="https://linkedin.com/company/blogger" target="_blank" rel="noopener noreferrer"
            aria-label="LinkedIn"
            class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#0A66C2] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 btn-press">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
            </svg> <span
              class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">LinkedIn</span>
          </a> </div>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="max-w-6xl mx-auto px-4 py-6 text-center">
        <p class="text-xs text-gray-500">© {{ date('Y') }} <span class="font-semibold text-gray-700">Blogger</span>. All
          rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Particles.js Script -->
  <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
  <script>
    tsParticles.load("particles-js", {
      fullScreen: { enable: false },
      background: { color: "transparent" },
      particles: {
        number: { value: 50 },
        color: { value: "#F97316" },
        shape: { type: "circle" },
        opacity: { value: 0.4 },
        size: { value: { min: 1, max: 4 } },
        move: {
          enable: true,
          speed: 1,
          direction: "none",
          outModes: { default: "bounce" },
        },
      },
      interactivity: {
        events: { onHover: { enable: true, mode: "repulse" } },
        modes: { repulse: { distance: 100 } },
      },
      detectRetina: true
    });
  </script>


  
  <!-- Enhanced Animation System -->
  @vite(['resources/js/animations-fixed.js', 'resources/js/loading-fix.js'])
  
  <!-- AJAX untuk tracking pembaca aktif -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      fetch("{{ route('user.tracker') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ page: window.location.href })
      }).catch(err => {
        console.error("Gagal kirim tracking:", err);
      });
    });
  </script>

</body>

</html>