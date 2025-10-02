<!-- resources/views/admin/sidebar.blade.php -->
<aside class="fixed top-0 left-0 h-screen w-64 bg-white shadow-lg border-r border-gray-200 overflow-hidden">
  <div class="p-6">
    <div class="flex items-center gap-2">
      <div class="bg-orange-500 w-9 h-9 rounded-full flex items-center justify-center text-white">
        <!-- Brand icon -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
          <path d="M12 2a10 10 0 100 20 10 10 0 000-20zM7.1 17.9l9.8-9.8A8 8 0 017.1 17.9zM12 20a7.9 7.9 0 01-4.9-1.7l9.2-9.2A8 8 0 0112 20z"/>
        </svg>
      </div>
      <h1 class="text-xl font-semibold text-gray-800">Blogger Admin</h1>
    </div>
  </div>
  <nav class="mt-4 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 py-2.5 px-6 rounded-md text-gray-700 hover:bg-orange-100 {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-700 font-semibold' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
      </svg>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-3 py-2.5 px-6 rounded-md text-gray-700 hover:bg-orange-100 {{ request()->routeIs('admin.posts*') ? 'bg-orange-50 text-orange-700 font-semibold' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
        <path d="M4 4h11a1 1 0 011 1v4h-2V6H6v12h8v-2h2v3a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/>
        <path d="M20.7 7.3l-1-1a1 1 0 00-1.4 0L12 11.6V14h2.4l6.3-6.3a1 1 0 000-1.4z"/>
      </svg>
      <span>Manage Posts</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 py-2.5 px-6 rounded-md text-gray-700 hover:bg-orange-100 {{ request()->routeIs('admin.users*') ? 'bg-orange-50 text-orange-700 font-semibold' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
        <path d="M16 11a4 4 0 10-8 0 4 4 0 008 0z"/>
        <path d="M2 20a7 7 0 0114 0v1H2v-1zM18.5 10a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
        <path d="M22 21v-1a4.5 4.5 0 00-3-4.24"/>
      </svg>
      <span>User Management</span>
    </a>
  </nav>
</aside>
