<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#EA580C" />
  <title>{{ $post->title }} | Blogger</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style> 
    body { font-family: 'Inter', sans-serif; }
    .article-content { 
      font-size: 1rem; 
      color: #374151;
    }
    /* PERBAIKAN FINAL: Targetkan P dan DIV (untuk konten utama) dengan !important */
    .article-content p, 
    .article-content div { 
      line-height: 1.6 !important; 
      margin-bottom: 1rem; 
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
    .hover-lift {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3);
    }
    /* Custom scrollbar for right content */
    .lg\:overflow-y-auto::-webkit-scrollbar {
      width: 8px;
    }
    .lg\:overflow-y-auto::-webkit-scrollbar-track {
      background: transparent;
    }
    .lg\:overflow-y-auto::-webkit-scrollbar-thumb {
      background: #EA580C;
      border-radius: 10px;
    }
    .lg\:overflow-y-auto::-webkit-scrollbar-thumb:hover {
      background: #C2410C;
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
  <style>
    /* Page transition animations */
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

    .spinner-circle {
      animation: dashOffset 2s ease-in-out infinite;
      stroke-dasharray: 90, 150;
      stroke-dashoffset: 0;
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

    @keyframes dashOffset {
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

    @keyframes pulse {
      0%, 100% {
        opacity: 0.7;
      }
      50% {
        opacity: 1;
      }
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
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50 text-gray-800 antialiased lg:overflow-hidden lg:h-screen fade-in-page">
  
  <script>
    // Initial loading for detail page (auto-hide)
    (function() {
      const initialLoading = document.createElement('div');
      initialLoading.id = 'initialDetailLoading';
      initialLoading.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;background:linear-gradient(135deg,#1a1a1a,#2d2d2d,#1a1a1a);z-index:999999;display:flex;flex-direction:column;align-items:center;justify-content:center;';
      initialLoading.innerHTML = `
        <div style="width:80px;height:80px;border:4px solid #333;border-top:4px solid #FF5722;border-radius:50%;animation:spin 1s linear infinite"></div>
        <p style="color:#FFCCBC;font-size:18px;font-weight:600;margin-top:20px">Memuat artikel...</p>
        <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
      `;
      document.body.appendChild(initialLoading);
      
      // Auto-hide with multiple triggers
      const hideInitial = () => {
        if (initialLoading.parentNode) {
          initialLoading.style.opacity = '0';
          initialLoading.style.transition = 'opacity 0.3s';
          setTimeout(() => initialLoading.remove(), 300);
        }
      };
      
      // Hide on DOM ready
      if (document.readyState !== 'loading') {
        setTimeout(hideInitial, 100);
      } else {
        document.addEventListener('DOMContentLoaded', hideInitial, { once: true });
      }
      
      // Force hide after 2 seconds
      setTimeout(hideInitial, 2000);
    })();
  </script>

  <!-- Page Transition Overlay (Hidden by default) -->
  <div class="page-transition" id="pageTransition" style="opacity: 0; visibility: hidden; pointer-events: none; display: none;">
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
  <header class="bg-white/90 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('user.views') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700 transition-all hover:-translate-x-1 duration-200 page-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        <span>Kembali</span>
      </a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
    <div class="bg-white/50 backdrop-blur-sm rounded-3xl border border-gray-200/60 shadow-2xl shadow-gray-300/20 p-3 sm:p-4">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <aside class="lg:col-span-4">
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xl shadow-gray-200/50 p-4 animate-fade-in-up sticky top-24 overflow-hidden" style="max-height: calc(100vh - 7rem);">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight mb-3">
              {{ $post->title }}
            </h1>
            
            <div class="relative h-32 rounded-xl overflow-hidden mb-3">
            <img src="{{ $post->thumbnail ?? $post->cover_image ?? 'https://via.placeholder.com/600x400?text=No+Cover' }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
          </div>

          <div class="space-y-5 text-xs text-gray-600 pb-3 border-b border-gray-100">
            @if($post->category)
            <div class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg w-full" style="background-color: {{ $post->category->color }}15; color: {{ $post->category->color }}">
              <span class="text-base">{{ $post->category->icon }}</span>
              <span class="font-semibold">{{ $post->category->name }}</span>
            </div>
            @endif
            <div class="inline-flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-brand-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span class="font-medium">{{ optional($post->created_at)->format('d M Y') }}</span>
            </div>
            <div class="inline-flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-brand-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <span class="font-medium">{{ $post->author ?? 'Admin' }}</span>
            </div>
            @php
              $words = str_word_count(strip_tags($post->content ?? ''));
              $minutes = max(1, ceil($words / 200));
            @endphp
            <div class="inline-flex items-center gap-2 bg-gray-50 px-2.5 py-1.5 rounded-lg w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-brand-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span class="font-medium">{{ $minutes }} menit baca</span>
            </div>
          </div>

          <div class="mt-3 pt-3 border-t border-gray-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                  </svg>
                </span>
                <p class="text-xs font-semibold text-gray-900">Bagikan Artikel</p>
              </div>
              <button type="button" aria-label="Salin tautan" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-800 text-white hover:bg-gray-900 shadow-sm hover:shadow transition" onclick="navigator.clipboard.writeText(window.location.href); this.classList.add('ring-2','ring-brand-500'); setTimeout(()=> this.classList.remove('ring-2','ring-brand-500'), 1200)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
            </div>
          </div>
          </div>
        </aside>

        <div class="lg:col-span-8">
        <div class="lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2" style="scrollbar-width: thin; scrollbar-color: #EA580C transparent;">
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xl shadow-gray-200/50 overflow-hidden animate-fade-in-up">
            <div class="p-4 sm:p-5">
              <div class="article-content prose prose-lg max-w-none">
                <div class="whitespace-pre-line text-gray-700">{!! nl2br(e($post->content)) !!}</div>
              </div>
            </div>
            <div class="border-t border-gray-100"></div>

            <div class="px-4 py-3 sm:px-5 sm:py-4 border-b border-gray-100">
              <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                  </svg>
                </span>
                <span>Komentar</span>
              </h2>
              <p class="mt-0.5 text-xs text-gray-600">Berikan pendapat atau tanggapan Anda tentang artikel ini</p>
            </div>

            <div class="px-4 py-3 sm:px-5 sm:py-4 bg-gray-50/50">
              @if(session('success'))
                <div class="mb-4 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2">{{ session('success') }}</div>
              @endif
              <form action="{{ route('comments.store', $post->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5">
                      <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                          <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Nama
                      </span>
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900"
                           placeholder="Masukkan nama Anda">
                  </div>
                  <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                      <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                          <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Email
                      </span>
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900"
                           placeholder="email@example.com">
                  </div>
                </div>
                
                <div>
                  <label for="comment" class="block text-xs font-semibold text-gray-700 mb-1.5">
                    <span class="flex items-center gap-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                      </svg>
                      Komentar
                    </span>
                  </label>
                  <textarea id="comment" name="comment" rows="2" required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900 resize-none"
                            placeholder="Tulis komentar Anda di sini..."></textarea>
                </div>

                <div class="flex items-center justify-between pt-1">
                  <p class="text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="12" y1="16" x2="12" y2="12"></line>
                      <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Email Anda tidak akan dipublikasikan
                  </p>
                  <button type="submit" 
                          class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white hover:from-brand-600 hover:to-brand-700 px-5 py-2 rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="22" y1="2" x2="11" y2="13"></line>
                      <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Kirim Komentar
                  </button>
                </div>
              </form>
            </div>

            <div class="px-4 py-3 sm:px-5 sm:py-4">
              <h3 class="text-sm font-bold text-gray-900 mb-2.5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>{{ ($comments->count() ?? 0) }} Komentar</span>
              </h3>

              <div class="space-y-2.5">
                @forelse($comments as $c)
                  <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-200 hover:border-brand-200 transition-colors">
                    <div class="flex gap-2.5">
                      <div class="flex-shrink-0">
                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                          {{ strtoupper(substr($c->name,0,1)) }}
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                          <h4 class="font-semibold text-sm text-gray-900">{{ $c->name }}</h4>
                          <span class="text-xs text-gray-400">•</span>
                          <time class="text-xs text-gray-500">{{ optional($c->created_at)->diffForHumans() }}</time>
                        </div>
                        <p class="text-xs text-gray-700 leading-relaxed">{{ $c->body }}</p>
                        <div class="mt-2 flex items-center gap-3">
                          <form method="POST" action="{{ route('comments.like', [$post->id, $c->id]) }}" class="inline">
                            @csrf
                            <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors" type="submit">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                              </svg>
                              <span class="font-medium">Suka ({{ $c->likes ?? 0 }})</span>
                            </button>
                          </form>
                          <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors" type="button" onclick="document.getElementById('reply-{{ $c->id }}').classList.toggle('hidden')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="9 11 12 14 22 4"></polyline>
                              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            <span class="font-medium">Balas</span>
                          </button>
                        </div>

                        <div id="reply-{{ $c->id }}" class="mt-4 hidden">
                          <form action="{{ route('comments.reply', [$post->id, $c->id]) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                              <input type="text" name="name" required placeholder="Nama" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                              <input type="email" name="email" required placeholder="Email" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                            </div>
                            <textarea name="comment" rows="3" required placeholder="Tulis balasan..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none resize-none"></textarea>
                            <div class="flex justify-end">
                              <button type="submit" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm">Kirim Balasan</button>
                            </div>
                          </form>
                        </div>

                        @if($c->replies && $c->replies->count())
                          <div class="mt-2.5 space-y-2 pl-3 border-l-2 border-gray-200">
                            @foreach($c->replies as $r)
                              <div class="flex gap-2.5">
                                <div class="flex-shrink-0">
                                  <div class="h-8 w-8 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-xs shadow">
                                    {{ strtoupper(substr($r->name,0,1)) }}
                                  </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                  <div class="flex items-center gap-1.5 mb-0.5">
                                    <h5 class="font-semibold text-gray-900 text-xs">{{ $r->name }}</h5>
                                    <span class="text-xs text-gray-400">•</span>
                                    <time class="text-xs text-gray-500">{{ optional($r->created_at)->diffForHumans() }}</time>
                                  </div>
                                  <p class="text-xs text-gray-700 leading-relaxed">{{ $r->body }}</p>
                                  <div class="mt-2">
                                    <form method="POST" action="{{ route('comments.like', [$post->id, $r->id]) }}" class="inline">
                                      @csrf
                                      <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                                        </svg>
                                        <span class="font-medium">Suka ({{ $r->likes ?? 0 }})</span>
                                      </button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            @endforeach
                          </div>
                        @endif

                      </div>
                    </div>
                  </div>
                @empty
                  <p class="text-sm text-gray-500">Belum ada komentar. Jadilah yang pertama!</p>
                @endforelse
              </div>
            </div>
          </div>
      </div>
    </div>
        </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Enhanced Animation System -->
  @vite(['resources/js/animations.js'])

</body>
</html>