<!-- resources/views/admin/users/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; margin: 0; }
  </style>
</head>
<body class="bg-[#F5F7FB] text-gray-800">
  <div class="ml-64 flex-1 flex flex-col min-h-screen">
    @include('admin.sidebar')
    <div class="flex-1 flex flex-col">
      <header class="bg-white border-b border-gray-200 px-6 py-4">
        <h2 class="text-xl font-semibold text-gray-900">User Management</h2>
      </header>
      <main class="p-6 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200">
          @if(session('status'))
            <div class="px-5 pt-4">
              <div class="rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">{{ session('status') }}</div>
            </div>
          @endif
          <!-- Card header (match Manage Posts: title on the left, + New on the right) -->
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">All Users</h3>
            <button id="btnOpenNewUser" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
              New User
            </button>
          </div>
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-gray-600">Name</th>
                <th class="px-6 py-3 text-gray-600">Email</th>
                <th class="px-6 py-3 text-gray-600">Role</th>
                <th class="px-6 py-3 text-gray-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach(($users ?? []) as $user)
              <tr class="border-t">
                <td class="px-6 py-4">{{ $user['name'] }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $user['email'] }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2 py-1 text-xs rounded bg-orange-50 text-orange-700">{{ ucfirst($user['role']) }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="relative inline-block text-left">
                    <!-- Vertical kebab button -->
                    <button
                      type="button"
                      data-menu-button
                      data-menu-id="menu-{{ $loop->index }}"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100"
                      aria-haspopup="true" aria-expanded="false" aria-controls="menu-{{ $loop->index }}"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z"/>
                      </svg>
                    </button>
                    <!-- Menu -->
                    <div id="menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-30">
                      <button class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2" data-action="edit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-500"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm15.71-9.04a1.003 1.003 0 000-1.42l-2.5-2.5a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.99-1.66z"/></svg>
                        Edit
                      </button>
                      <button class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2" data-action="delete">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-red-500"><path d="M6 7h12v2H6V7zm2 3h8l-1 9H9L8 10zm3-6h2l1 1h4v2H6V5h4l1-1z"/></svg>
                        Delete
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- New User Modal (styling copied from Manage Posts modal) -->
        <div id="modalNewUser" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Create New User</h3>
                <button id="btnCloseNewUser" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <form id="formNewUser" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                  <input name="name" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Full name">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input name="email" type="email" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Email address">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                  <select name="role" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="user" selected>User</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                  <input name="password" type="password" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Set password">
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelNewUser" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Save User</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <script>
          (function(){
            // Modal logic
            const modal = document.getElementById('modalNewUser');
            const openBtn = document.getElementById('btnOpenNewUser');
            const closeBtn = document.getElementById('btnCloseNewUser');
            const cancelBtn = document.getElementById('btnCancelNewUser');

            function open(){
              modal.classList.remove('hidden');
              document.documentElement.classList.add('overflow-hidden');
              document.body.classList.add('overflow-hidden');
            }
            function close(){
              modal.classList.add('hidden');
              document.documentElement.classList.remove('overflow-hidden');
              document.body.classList.remove('overflow-hidden');
            }

            openBtn?.addEventListener('click', open);
            closeBtn?.addEventListener('click', close);
            cancelBtn?.addEventListener('click', close);
            modal?.addEventListener('click', (e)=>{ if(e.target === modal) close(); });

            // Actions dropdown logic (professional vertical kebab)
            const buttons = document.querySelectorAll('[data-menu-button]');
            let openMenu = null;

            function toggleMenu(id){
              const menu = document.getElementById(id);
              if(!menu) return;
              const isHidden = menu.classList.contains('hidden');
              if(openMenu && openMenu !== menu){ openMenu.classList.add('hidden'); }
              menu.classList.toggle('hidden', !isHidden ? true : false);
              openMenu = menu.classList.contains('hidden') ? null : menu;
            }

            buttons.forEach(btn => {
              btn.addEventListener('click', (e)=>{
                e.stopPropagation();
                const id = btn.getAttribute('data-menu-id');
                toggleMenu(id);
                // set aria-expanded for accessibility
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', (!expanded).toString());
              });
            });

            function closeAny(){ if(openMenu){ openMenu.classList.add('hidden'); openMenu = null; } }
            document.addEventListener('click', closeAny);
            window.addEventListener('scroll', closeAny, { passive: true });
            window.addEventListener('resize', closeAny);
            document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeAny(); });
          })();
        </script>
      </main>
    </div>
  </div>
</body>
</html>
