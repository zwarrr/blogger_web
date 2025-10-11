<!-- resources/views/auditor/sidebar.blade.php -->
<style>
  [x-cloak] { display: none !important; }
</style>
<aside x-data="{ open: false, showComingSoonModal: false, showProfileModal: false, showSettingsModal: false }" class="fixed left-0 top-0 w-64 h-full bg-gradient-to-b from-white to-gray-100 text-gray-800 flex flex-col px-6 py-8 shadow-2xl border-r border-gray-200 z-20 overflow-visible">
  <!-- Logo / Brand -->
  <div class="flex items-center gap-2 mb-12">
    <div class="bg-orange-500 w-9 h-9 rounded-full flex items-center justify-center text-white">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zM7.1 17.9l9.8-9.8A8 8 0 717.1 17.9zM12 20a7.9 7.9 0 01-4.9-1.7l9.2-9.2A8 8 0 0112 20z"/></svg>
    </div>
    <h1 class="text-xl font-semibold text-gray-800">Auditor</h1>
  </div>

  @php($displayName = auth()->user()->name ?? null)

  <!-- Navigation -->
  <nav class="flex flex-col space-y-4 text-sm font-medium">
    <a href="{{ route('auditor.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('auditor.dashboard') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7m-9 2v6m-4 4h16v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6z"/></svg>
      Dashboard
    </a>
    
    <!-- Visit Management Section -->
    <div class="mt-6 mb-3">
      <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4">Kunjungan Author</h3>
    </div>
    <a href="{{ route('auditor.visits.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('auditor.visits.index') || request()->routeIs('auditor.visits.show') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      Daftar Kunjungan
    </a>
    <button @click="showComingSoonModal = true" class="flex items-center gap-3 px-4 py-2 rounded-md transition hover:bg-gray-200 text-gray-700 w-full text-left">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <span>Peta Kunjungan</span>
      <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Soon</span>
    </button>

    
    <!-- Reports Section -->
    <div class="mt-6 mb-3">
      <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4">Laporan & Statistik</h3>
    </div>
    <a href="{{ route('auditor.visits.statistics') }}" class="flex items-center gap-3 px-4 py-2 rounded-md transition {{ request()->routeIs('auditor.visits.statistics') ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:bg-gray-200 text-gray-700' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Statistik
    </a>
  </nav>

  <!-- Spacer -->
  <div class="flex-1"></div>

  <!-- Profile with Dropdown to the RIGHT -->
  <div class="relative flex items-center">
    <button @click="open = !open" class="w-full flex items-center gap-3 px-3 py-3 rounded-md bg-gray-100 hover:bg-gray-200 transition">
      <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName ?? 'A') }}&background=f97316&color=fff" alt="Avatar" class="w-8 h-8 rounded-full ring-2 ring-orange-300" />
      <span class="text-sm font-semibold truncate">{{ $displayName ?? 'Auditor' }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-auto transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div
      x-show="open"
      @click.away="open = false"
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
              A
            @endif
          </div>
          <div>
            <p class="font-semibold">{{ $displayName ?? 'Auditor' }}</p>
            <p class="text-sm text-orange-100">Akun Auditor</p>
          </div>
        </div>
      </div>

      <div class="py-2">
        <button @click="showProfileModal = true; open = false" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors w-full text-left">
          <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span>Profil Saya</span>
          <span class="ml-auto text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Soon</span>
        </button>
        <button @click="showSettingsModal = true; open = false" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors w-full text-left">
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
       style="display: none;"
       x-cloak>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
          </div>
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Peta Kunjungan</h4>
          <p class="text-gray-600 mb-6">Fitur peta kunjungan sedang dalam tahap pengembangan dan akan segera tersedia untuk meningkatkan pengalaman auditing Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Visualisasi lokasi kunjungan pada peta interaktif
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Tracking real-time posisi auditor
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Filter dan pencarian berdasarkan lokasi
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Optimisasi rute kunjungan
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
          <h3 class="text-lg font-semibold text-white">Profil Auditor</h3>
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
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Manajemen Profil Auditor</h4>
          <p class="text-gray-600 mb-6">Fitur manajemen profil auditor sedang dalam tahap pengembangan untuk memberikan kontrol penuh atas akun dan informasi auditing Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Edit informasi profil (nama, email, ID auditor)
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Upload dan ganti foto profil auditor
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
                Sertifikat dan kredensial auditor
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Riwayat audit dan performance tracking
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
          <h3 class="text-lg font-semibold text-white">Pengaturan Auditor</h3>
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
          <h4 class="text-xl font-semibold text-gray-900 mb-2">Pengaturan Auditor Panel</h4>
          <p class="text-gray-600 mb-6">Panel pengaturan auditor sedang dalam pengembangan untuk memberikan kontrol penuh atas preferensi dan konfigurasi audit Anda.</p>
          
          <!-- Features Preview -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h5 class="font-medium text-gray-900 mb-3">Fitur yang akan datang:</h5>
            <ul class="space-y-2 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Pengaturan tema dan tampilan dashboard audit
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Konfigurasi notifikasi dan alert sistem
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Pengaturan kriteria dan standar audit
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Template dan format laporan audit
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Backup dan sync data audit
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
