<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#EA580C" />
  <title>Berita | Blogger</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .clamp-3 { -webkit-line-clamp: 3; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
    .animate-stagger > * { animation: fadeInUp 0.6s ease-out; }
    .animate-stagger > *:nth-child(1) { animation-delay: 0.1s; }
    .animate-stagger > *:nth-child(2) { animation-delay: 0.2s; }
    .animate-stagger > *:nth-child(3) { animation-delay: 0.3s; }
    .animate-stagger > *:nth-child(4) { animation-delay: 0.4s; }
    .animate-stagger > *:nth-child(5) { animation-delay: 0.5s; }
    .animate-stagger > *:nth-child(6) { animation-delay: 0.6s; }
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -10px rgba(234, 88, 12, 0.3); }
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
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
  <link rel="icon" href="/favicon.ico" />
  </head>
<body class="bg-gradient-to-br from-gray-50 via-orange-50/30 to-gray-50 text-gray-800 antialiased">
  <!-- Top Bar -->
  <header class="bg-white/90 backdrop-blur-md border-b border-gray-200/80 sticky top-0 z-50 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="{{ route('user.views') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white font-bold shadow-md">B</span>
        <span class="text-lg font-bold text-gray-900">Blogger</span>
      </a>
    </div>
  </header>

  <!-- Hero / Search -->
  <section class="bg-gradient-to-br from-brand-50 via-orange-50 to-white border-b border-gray-200/70 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-12 sm:py-16">
      <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div class="animate-fade-in-up">
          <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-sm mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
            <span class="text-sm font-medium text-gray-700">Platform Berita Terpercaya</span>
          </div>
          <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 leading-tight">Berita Terbaru</h1>
          <p class="text-base md:text-lg text-gray-600 mt-3 max-w-2xl">Rangkuman informasi terkini dengan gaya yang ringan dan profesional.</p>
        </div>
      </div>
    </div>
  </section>

  <main class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
        </span>
        <span>Daftar Berita</span>
      </h2>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
      @foreach($posts as $post)
      <article class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-lg shadow-gray-200/50 hover-lift group">
        <div class="relative h-48 overflow-hidden">
          <img src="{{ $post->thumbnail ?? $post->cover_image ?? 'https://via.placeholder.com/600x300?text=No+Cover' }}" alt="{{ $post->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
          <div class="absolute left-4 bottom-4 inline-flex items-center gap-2 rounded-full bg-white/95 backdrop-blur-sm text-brand-700 border border-white/80 px-3 py-1.5 text-xs font-semibold shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            Berita
          </div>
        </div>
        <div class="p-5">
          <h3 class="text-lg font-bold text-gray-900 clamp-2 group-hover:text-brand-600 transition-colors leading-snug">{{ $post->title }}</h3>
          <p class="text-gray-600 text-sm mt-3 clamp-3 leading-relaxed">{{ Str::limit(strip_tags($post->content), 150) }}</p>
          <div class="mt-5 flex items-center justify-between">
            <span class="inline-flex items-center gap-2 text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              <span class="font-medium">{{ optional($post->created_at)->format('d M Y') }}</span>
            </span>
            <a href="{{ route('user.detail', $post->id) }}" class="inline-flex items-center gap-1.5 text-white bg-brand-600 hover:bg-brand-700 px-4 py-2 rounded-lg text-sm font-semibold shadow-md transition-all hover:shadow-lg">
              Baca
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
        <div class="flex items-center gap-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-white font-bold shadow-md">B</span>
          <span class="text-base font-bold text-gray-900">Blogger</span>
        </div>
        <p class="mt-4 text-sm text-gray-600 leading-relaxed">Platform untuk membaca dan membagikan cerita, opini, dan berita terbaru dengan tampilan modern.</p>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900">Jelajahi</p>
        <ul class="mt-3 space-y-2 text-sm text-gray-600">
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Tentang Kami</a></li>
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Kontak</a></li>
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Kontributor</a></li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900">Bantuan</p>
        <ul class="mt-3 space-y-2 text-sm text-gray-600">
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Pusat Bantuan</a></li>
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Syarat & Ketentuan</a></li>
          <li><a class="hover:text-brand-700 text-brand-600" href="#">Kebijakan Privasi</a></li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-900 mb-4">Ikuti Kami</p>
        <div class="flex items-center gap-3">
          <a href="#" aria-label="Facebook" class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#1877F2] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Facebook</span>
          </a>
          <a href="#" aria-label="Twitter" class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#000000] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Twitter</span>
          </a>
          <a href="#" aria-label="Instagram" class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-gradient-to-br from-[#833AB4] via-[#FD1D1D] to-[#F77737] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Instagram</span>
          </a>
          <a href="#" aria-label="LinkedIn" class="group relative inline-flex items-center justify-center h-11 w-11 rounded-xl bg-[#0A66C2] text-white shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">LinkedIn</span>
          </a>
        </div>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="max-w-6xl mx-auto px-4 py-6 text-center">
        <p class="text-xs text-gray-500">© {{ date('Y') }} <span class="font-semibold text-gray-700">Blogger</span>. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
