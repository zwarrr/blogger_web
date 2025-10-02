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
      font-size: 1.0625rem; 
      line-height: 1.75; 
      color: #374151;
    }
    .article-content p { 
      margin-bottom: 1.25rem; 
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
</head>
<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50 text-gray-800 antialiased">
  <header class="bg-white/90 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('user.views') }}" class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700 transition-all hover:-translate-x-1 duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        <span>Kembali</span>
      </a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-8 sm:py-12">
    <!-- Container Background -->
    <div class="bg-white/50 backdrop-blur-sm rounded-3xl border border-gray-200/60 shadow-2xl shadow-gray-300/20 p-4 sm:p-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Sidebar (Metadata, Cover, Share) -->
        <aside class="lg:col-span-4">
          <!-- Title Card -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xl shadow-gray-200/50 p-5 animate-fade-in-up sticky top-24 overflow-hidden" style="max-height: calc(100vh - 7rem);">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight mb-4">
              {{ $post->title }}
            </h1>
            
            <!-- Cover Image -->
            <div class="relative h-36 rounded-xl overflow-hidden mb-4">
            <img src="{{ $post->thumbnail ?? $post->cover_image ?? 'https://via.placeholder.com/600x400?text=No+Cover' }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
          </div>

          <!-- Meta Information -->
          <div class="space-y-2 text-xs text-gray-600 pb-4 border-b border-gray-100">
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

          <!-- Share Section -->
          <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 mb-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="18" cy="5" r="3"></circle>
                  <circle cx="6" cy="12" r="3"></circle>
                  <circle cx="18" cy="19" r="3"></circle>
                  <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                  <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
              </span>
              <div>
                <p class="text-xs font-semibold text-gray-900">Bagikan Artikel</p>
                <p class="text-[10px] text-gray-500">Sebarkan ke temanmu</p>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-1.5">
              <a target="_blank" rel="noopener" class="hover-lift inline-flex items-center justify-center gap-1.5 bg-[#1877F2] text-white hover:bg-[#166FE5] rounded-lg px-2 py-2 text-xs font-semibold shadow-md transition-all" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Facebook</span>
              </a>
              <a target="_blank" rel="noopener" class="hover-lift inline-flex items-center justify-center gap-1.5 bg-[#000000] text-white hover:bg-[#1a1a1a] rounded-lg px-2 py-2 text-xs font-semibold shadow-md transition-all" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span>Twitter</span>
              </a>
              <a target="_blank" rel="noopener" class="hover-lift inline-flex items-center justify-center gap-1.5 bg-[#0A66C2] text-white hover:bg-[#095196] rounded-lg px-2 py-2 text-xs font-semibold shadow-md transition-all" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                <span>LinkedIn</span>
              </a>
              <button type="button" class="hover-lift inline-flex items-center justify-center gap-1.5 bg-gray-700 text-white hover:bg-gray-800 rounded-lg px-2 py-2 text-xs font-semibold shadow-md transition-all" onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-3.5 w-3.5\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'20 6 9 17 4 12\'></polyline></svg><span>Tersalin!</span>'; setTimeout(() => this.innerHTML='<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-3.5 w-3.5\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'9\' y=\'9\' width=\'13\' height=\'13\' rx=\'2\' ry=\'2\'></rect><path d=\'M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1\'></path></svg><span>Salin</span>', 2000)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                <span>Salin</span>
              </button>
            </div>
          </div>
          </div>
        </aside>

        <!-- Right Content (Article & Comments) -->
        <div class="lg:col-span-8">
        <div class="space-y-8 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2" style="scrollbar-width: thin; scrollbar-color: #EA580C transparent;">
          <!-- Article Content -->
          <article class="bg-white rounded-2xl border border-gray-200/80 shadow-xl shadow-gray-200/50 p-6 sm:p-10 animate-fade-in-up">
            <div class="article-content prose prose-lg max-w-none">
              <div class="whitespace-pre-line text-gray-700">{!! nl2br(e($post->content)) !!}</div>
            </div>
          </article>

          <!-- Comments Section -->
          <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xl shadow-gray-200/50 overflow-hidden animate-fade-in-up">
      <div class="px-6 py-6 sm:px-10 sm:py-8 border-b border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
          </span>
          <span>Komentar</span>
        </h2>
        <p class="mt-2 text-sm text-gray-600">Berikan pendapat atau tanggapan Anda tentang artikel ini</p>
      </div>

      <!-- Comment Form -->
      <div class="px-6 py-6 sm:px-10 sm:py-8 bg-gray-50/50">
        <form action="#" method="POST" class="space-y-4">
          @csrf
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  Nama
                </span>
              </label>
              <input type="text" id="name" name="name" required
                     class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900"
                     placeholder="Masukkan nama Anda">
            </div>
            <div>
              <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                <span class="flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                  </svg>
                  Email
                </span>
              </label>
              <input type="email" id="email" name="email" required
                     class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900"
                     placeholder="email@example.com">
            </div>
          </div>
          
          <div>
            <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Komentar
              </span>
            </label>
            <textarea id="comment" name="comment" rows="4" required
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none text-gray-900 resize-none"
                      placeholder="Tulis komentar Anda di sini..."></textarea>
          </div>

          <div class="flex items-center justify-between">
            <p class="text-xs text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
              Email Anda tidak akan dipublikasikan
            </p>
            <button type="submit" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white hover:from-brand-600 hover:to-brand-700 px-6 py-3 rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
              Kirim Komentar
            </button>
          </div>
        </form>
      </div>

      <!-- Comments List -->
      <div class="px-6 py-6 sm:px-10 sm:py-8">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span>3 Komentar</span>
        </h3>

        <div class="space-y-6">
          <!-- Sample Comment 1 -->
          <div class="flex gap-4 p-5 bg-gray-50 rounded-xl border border-gray-200 hover:border-brand-200 transition-colors">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                A
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h4 class="font-semibold text-gray-900">Ahmad Rizki</h4>
                <span class="text-xs text-gray-500">•</span>
                <time class="text-xs text-gray-500">2 jam yang lalu</time>
              </div>
              <p class="text-sm text-gray-700 leading-relaxed">
                Artikel yang sangat informatif! Penjelasannya mudah dipahami dan memberikan wawasan baru untuk saya. Terima kasih sudah berbagi.
              </p>
              <div class="mt-3 flex items-center gap-4">
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                  </svg>
                  <span class="font-medium">Suka (12)</span>
                </button>
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                  </svg>
                  <span class="font-medium">Balas</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Sample Comment 2 -->
          <div class="flex gap-4 p-5 bg-gray-50 rounded-xl border border-gray-200 hover:border-brand-200 transition-colors">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                S
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h4 class="font-semibold text-gray-900">Siti Nurhaliza</h4>
                <span class="text-xs text-gray-500">•</span>
                <time class="text-xs text-gray-500">5 jam yang lalu</time>
              </div>
              <p class="text-sm text-gray-700 leading-relaxed">
                Sangat membantu! Saya sudah menunggu artikel seperti ini. Penjelasannya detail dan mudah diikuti.
              </p>
              <div class="mt-3 flex items-center gap-4">
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                  </svg>
                  <span class="font-medium">Suka (8)</span>
                </button>
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                  </svg>
                  <span class="font-medium">Balas</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Sample Comment 3 -->
          <div class="flex gap-4 p-5 bg-gray-50 rounded-xl border border-gray-200 hover:border-brand-200 transition-colors">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                B
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h4 class="font-semibold text-gray-900">Budi Santoso</h4>
                <span class="text-xs text-gray-500">•</span>
                <time class="text-xs text-gray-500">1 hari yang lalu</time>
              </div>
              <p class="text-sm text-gray-700 leading-relaxed">
                Konten yang berkualitas! Terus berkarya dan semoga bisa membuat artikel-artikel bermanfaat lainnya. 👍
              </p>
              <div class="mt-3 flex items-center gap-4">
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                  </svg>
                  <span class="font-medium">Suka (15)</span>
                </button>
                <button class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-brand-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                  </svg>
                  <span class="font-medium">Balas</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More Button -->
        @php
          $totalComments = 3; // Ganti dengan jumlah komentar sebenarnya dari database
        @endphp
        @if($totalComments > 3)
        <div class="mt-8 text-center">
          <button class="inline-flex items-center gap-2 text-brand-600 hover:text-brand-700 font-semibold text-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            Muat Lebih Banyak Komentar
          </button>
        </div>
        @endif
      </div>
    </div>
        </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="py-8 border-t border-gray-200 bg-white/60">
  <div class="max-w-6xl mx-auto px-4 text-center text-xs text-gray-500">
    <span>© {{ date('Y') }} <span class="font-semibold text-gray-700">Blogger</span></span>
  </div>
</footer>

</body>
</html>
