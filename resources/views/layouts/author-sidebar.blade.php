<!-- Author Sidebar -->
<aside class="w-64 bg-white shadow-lg border-r border-gray-200 sidebar-transition sidebar-closed lg:sidebar-open fixed lg:relative z-30 h-full" 
       :class="sidebarOpen ? 'sidebar-open' : 'sidebar-closed'">
    
    <!-- Logo Section -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">B</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">BlogSphere</h2>
                <p class="text-xs text-gray-500">Author Panel</p>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600 lg:hidden">
            <i data-feather="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-4">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('author.dashboard') }}" 
               class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition-colors duration-200 {{ request()->routeIs('author.dashboard') ? 'bg-orange-50 text-orange-600 border-r-2 border-orange-600' : '' }}">
                <i data-feather="home" class="w-5 h-5 mr-3"></i>
                Dashboard
            </a>

            <!-- Posts Management -->
            <div class="space-y-1">
                <div class="flex items-center px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <i data-feather="edit-3" class="w-4 h-4 mr-2"></i>
                    Content Management
                </div>
                <a href="{{ route('author.posts.index') }}" 
                   class="flex items-center px-4 py-3 ml-6 text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition-colors duration-200 {{ request()->routeIs('author.posts.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                    Artikel Saya
                </a>
            </div>

            <!-- Visit Management -->
            <div class="space-y-1">
                <div class="flex items-center px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                    Visit Management
                </div>
                <a href="{{ route('author.visits.index') }}" 
                   class="flex items-center px-4 py-3 ml-6 text-gray-700 hover:bg-orange-50 hover:text-orange-600 rounded-lg transition-colors duration-200 {{ request()->routeIs('author.visits.*') ? 'bg-orange-50 text-orange-600' : '' }}">
                    <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                    Daftar Kunjungan
                </a>
            </div>
        </div>
    </nav>

    <!-- User Profile Section -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
        <div class="flex items-center space-x-3 mb-3">
            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                <span class="text-sm font-semibold text-orange-600">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500">Author</p>
            </div>
        </div>
        <div class="space-y-2">
            <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200">
                <i data-feather="settings" class="w-4 h-4 mr-2"></i>
                Settings
            </a>
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                    <i data-feather="log-out" class="w-4 h-4 mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"
     x-cloak>
</div>