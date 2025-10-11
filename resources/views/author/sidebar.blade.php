<!-- resources/views/author/sidebar.blade.php -->
<style>
  [x-cloak] { display: none !important; }
</style>
<aside x-data="{ profileOpen: false, showComingSoonModal: false, showProfileModal: false, showSettingsModal: false }" class="fixed left-0 top-0 w-64 h-full bg-gradient-to-b from-white to-gray-100 text-gray-800 flex flex-col px-6 py-8 shadow-2xl border-r border-gray-200 z-20 overflow-visible">
  <!-- Logo / Brand -->
  <div class="flex items-center gap-2 mb-12">
    <div class="bg-orange-500 w-9 h-9 rounded-full flex items-center justify-center text-white">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zM7.1 17.9l9.8-9.8A8 8 0 017.1 17.9zM12 20a7.9 7.9 0 01-4.9-1.7l9.2-9.2A8 8 0 0112 20z"/></svg>
    </div>
    <h1 class="text-xl font-semibold text-gray-800">Author</h1>
  </div>

  @php($displayName = auth('web')->check() ? auth('web')->user()->name : null)

  <!-- Navigation -->
  <nav class="flex flex-col space-y-4 text-sm font-medium">
    <a href="{{ route('author.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('author.dashboard') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v6m-4 4h16v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6z"/>
      </svg>
      Dashboard
    </a>
    <a href="{{ route('author.posts.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('author.posts*') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
      </svg>
      Artikel Saya
    </a>
    <button @click="showComingSoonModal = true" class="flex items-center gap-3 px-4 py-2 rounded-md transition hover:bg-gray-200 text-gray-700 w-full text-left">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
      </svg>
      <span>Komentar</span>
      <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Soon</span>
    </button>
    <a href="{{ route('author.visits.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('author.visits*') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
      </svg>
      Riwayat Kunjungan
    </a>
  </nav>

  <!-- Spacer -->
  <div class="flex-1"></div>

  <!-- Profile with Dropdown to the RIGHT -->
  <div class="relative flex items-center">
    <button @click="profileOpen = !profileOpen" class="w-full flex items-center gap-3 px-3 py-3 rounded-md bg-gray-100 hover:bg-gray-200 transition">
      <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName ?? 'U') }}&background=f97316&color=fff" alt="Avatar" class="w-8 h-8 rounded-full ring-2 ring-orange-300" />
      <span class="text-sm font-semibold truncate">{{ $displayName ?? 'Pengguna' }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-auto transform transition-transform" :class="profileOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div
      x-show="profileOpen"
      @click.away="profileOpen = false"
      x-transition:enter="transition ease-out duration-150"
      x-transition:enter-start="opacity-0 transform scale-95"
      x-transition:enter-end="opacity-100 transform scale-100"
      x-transition:leave="transition ease-in duration-100"
      x-transition:leave-start="opacity-100 transform scale-100"
      x-transition:leave-end="opacity-0 transform scale-95"
      class="absolute left-full ml-2 bottom-0 w-60 bg-white text-gray-800 rounded-xl shadow-xl ring-1 ring-black/10 z-[9999] overflow-hidden"
      style="min-width:15rem; display: none;"
    >
      <div class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center text-white font-bold">
            @if($displayName ?? null)
              {{ strtoupper(substr($displayName, 0, 1)) }}
            @else
              User
            @endif
          </div>
          <div>
            <p class="font-semibold">{{ $displayName ?? 'User' }}</p>
            <p class="text-sm text-orange-100">Akun Author</p>
          </div>
        </div>
      </div>

      <div class="py-2">
        <button @click="showProfileModal = true; profileOpen = false" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors w-full text-left">
          <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span>Profil Saya</span>
          <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Soon</span>
        </button>
        <button @click="showSettingsModal = true; profileOpen = false" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors w-full text-left">
          <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span>Pengaturan</span>
          <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Soon</span>
        </button>
        <div class="border-t border-gray-100 my-2"></div>
        <form method="POST" action="{{ route('auth.logout') }}" class="px-4 py-2">
          @csrf
          <button type="submit" class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded transition-colors">
            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Logout</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Coming Soon Modal -->
  <div x-show="showComingSoonModal" 
       x-transition:enter="transition ease-out duration-300" 
       x-transition:enter-start="opacity-0" 
       x-transition:enter-end="opacity-100" 
       x-transition:leave="transition ease-in duration-200" 
       x-transition:leave-start="opacity-100" 
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999]" 
       style="display: none;">
    <div x-show="showComingSoonModal"
         @click.away="showComingSoonModal = false"
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Fitur Dalam Pengembangan</h3>
          <button @click="showComingSoonModal = false" class="text-white hover:text-orange-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="px-6 py-6">
        <div class="text-center">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
          </div>
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Sistem Komentar</h4>
          <p class="text-gray-600 mb-6">Fitur manajemen komentar sedang dalam tahap pengembangan dan akan segera tersedia untuk meningkatkan interaksi dengan pembaca Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Moderasi komentar dengan persetujuan otomatis
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Notifikasi real-time untuk komentar baru
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Sistem balasan bertingkat (threaded comments)
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Filter spam dan komentar tidak pantas
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Analitik engagement dan sentimen pembaca
              </li>
            </ul>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex gap-3">
          <button @click="showComingSoonModal = false" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            Tutup
          </button>
          <button @click="showComingSoonModal = false" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Mengerti
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <div x-show="showProfileModal" 
       x-transition:enter="transition ease-out duration-300" 
       x-transition:enter-start="opacity-0" 
       x-transition:enter-end="opacity-100" 
       x-transition:leave="transition ease-in duration-200" 
       x-transition:leave-start="opacity-100" 
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999]" 
       style="display: none;"
       x-cloak>
    <div x-show="showProfileModal"
         @click.away="showProfileModal = false"
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Profil Author</h3>
          <button @click="showProfileModal = false" class="text-white hover:text-orange-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="px-6 py-6">
        <div class="text-center">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Manajemen Profil Author</h4>
          <p class="text-gray-600 mb-6">Fitur manajemen profil author sedang dalam tahap pengembangan untuk memberikan kontrol penuh atas akun dan informasi pribadi Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Edit informasi profil (nama, email, bio author)
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Upload dan ganti foto profil author
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Ganti password dengan validasi keamanan
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Pengaturan preferensi artikel dan notifikasi
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Statistik dan performa artikel Anda
              </li>
            </ul>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex gap-3">
          <button @click="showProfileModal = false" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            Tutup
          </button>
          <button @click="showProfileModal = false" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Mengerti
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Settings Modal -->
  <div x-show="showSettingsModal" 
       x-transition:enter="transition ease-out duration-300" 
       x-transition:enter-start="opacity-0" 
       x-transition:enter-end="opacity-100" 
       x-transition:leave="transition ease-in duration-200" 
       x-transition:leave-start="opacity-100" 
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999]" 
       style="display: none;"
       x-cloak>
    <div x-show="showSettingsModal"
         @click.away="showSettingsModal = false"
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95"
         class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
      
      <!-- Modal Header -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-white">Pengaturan Author</h3>
          <button @click="showSettingsModal = false" class="text-white hover:text-orange-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="px-6 py-6">
        <div class="text-center">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Pengaturan Author Panel</h4>
          <p class="text-gray-600 mb-6">Panel pengaturan author sedang dalam pengembangan untuk memberikan kontrol penuh atas preferensi dan konfigurasi akun Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Pengaturan tema dan tampilan dashboard
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Konfigurasi notifikasi email dan in-app
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Pengaturan privasi dan keamanan akun
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Preferensi format artikel dan editor
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Backup otomatis dan sync data
              </li>
            </ul>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex gap-3">
          <button @click="showSettingsModal = false" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            Tutup
          </button>
          <button @click="showSettingsModal = false" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Mengerti
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Alpine.js for dropdown -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</aside>
