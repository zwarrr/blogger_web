<!-- resources/views/admin/manage-posts.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Posts</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; margin: 0; }
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="turbolinks-cache-control" content="no-cache">
  <meta name="robots" content="noindex">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Expires" content="0">
</head>
<body class="bg-[#F5F7FB] text-gray-800">
  <div class="ml-64 flex-1 flex flex-col min-h-screen">
    @include('admin.sidebar')
    <div class="flex-1 flex flex-col">
      <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Posts</h2>
      </header>
      <main class="p-6 space-y-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
          @if(session('status'))
            <div class="mb-4 rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">{{ session('status') }}</div>
          @endif
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">All Posts</h3>
            <button id="btnOpenNewPost" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
              New Post
            </button>
          </div>
          <div class="mt-4 overflow-x-auto overflow-y-visible relative">
            <table class="min-w-full text-sm text-left">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-gray-600">Cover</th>
                  <th class="px-6 py-3 text-gray-600">Title</th>
                  <th class="px-6 py-3 text-gray-600">Description</th>
                  <th class="px-6 py-3 text-gray-600">Location</th>
                  <th class="px-6 py-3 text-gray-600">Publish At</th>
                  <th class="px-6 py-3 text-gray-600">Date</th>
                  <th class="px-6 py-3 text-gray-600">Status</th>
                  <th class="px-6 py-3 text-gray-600">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach(($posts ?? []) as $post)
                  <tr class="border-t">
                    <td class="px-6 py-3">
                      @if(!empty($post['cover_image']))
                        <img src="{{ asset('storage/'.$post['cover_image']) }}" alt="{{ $post['title'] }}" class="w-12 h-12 rounded object-cover border"/>
                      @else
                        <div class="w-12 h-12 rounded bg-gray-100 border flex items-center justify-center text-xs text-gray-400">—</div>
                      @endif
                    </td>
                    <td class="px-6 py-4">{{ $post['title'] }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $post['description'] ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $post['location'] ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $post['published_at'] ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $post['date'] ?? '—' }}</td>
                    <td class="px-6 py-4">
                      <span class="{{ $post['status']==='Published' ? 'text-green-600' : 'text-yellow-600' }}">{{ $post['status'] }}</span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="relative inline-block text-left">
                        <button type="button" data-menu-button data-menu-id="post-menu-{{ $loop->index }}" class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-haspopup="true" aria-expanded="false">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z"/></svg>
                        </button>
                        <div id="post-menu-{{ $loop->index }}" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                          <button class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2" data-action="edit" data-post='@json($post)'>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-500"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm15.71-9.04a1.003 1.003 0 000-1.42l-2.5-2.5a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.99-1.66z"/></svg>
                            Edit
                          </button>
                          <form method="POST" action="{{ route('admin.posts.destroy', $post['id']) }}" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-red-500"><path d="M6 7h12v2H6V7zm2 3h8l-1 9H9L8 10zm3-6h2l1 1h4v2H6V5h4l1-1z"/></svg>
                              Delete
                            </button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        
  <!-- New Post Modal -->
        <div id="modalNewPost" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <!-- Backdrop covering entire viewport -->
          <div class="fixed inset-0 bg-black/70"></div>
          <!-- Centered modal container -->
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <!-- Header -->
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Create New Post</h3>
              <button id="btnCloseNewPost" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
            </div>

            <!-- Form -->
            <form id="formNewPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
              @csrf
              <!-- Title -->
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input name="title" type="text" required 
                       class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" 
                       placeholder="Post title">
              </div>
              <!-- Description -->
              <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" 
                          class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" 
                          placeholder="Write a short description..."></textarea>
              </div>
              <!-- Cover Image -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                <input name="cover" type="file" accept="image/*"
                       class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700 focus:outline-none">
              </div>
              <!-- Location -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input name="location" type="text" 
                       class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500" 
                       placeholder="City, Country">
              </div>
              <!-- Publish At -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publish At (Realtime)</label>
                <input id="publishedAt" name="published_at" type="datetime-local" 
                       class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-xs text-gray-500 mt-1">Auto-updating to current time. You can adjust manually.</p>
              </div>
              <!-- Options -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                <div class="space-y-1">
                  <label class="flex items-center gap-2"><input type="checkbox" name="allow_comments" value="1" class="rounded text-orange-600"> Allow comments</label>
                  <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1" class="rounded text-orange-600"> Pin this post</label>
                  <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" class="rounded text-orange-600"> Mark as featured</label>
                  <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" class="rounded text-orange-600" checked> Publish immediately</label>
                </div>
              </div>
              <!-- Actions -->
              <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                <button type="button" id="btnCancelNewPost" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Save Post</button>
              </div>
            </form>
            </div>
          </div>
        </div>

        <!-- Edit Post Modal (reuse fields) -->
        <div id="modalEditPost" class="fixed inset-0 z-[9999] hidden" aria-hidden="true">
          <div class="fixed inset-0 bg-black/70"></div>
          <div class="relative z-10 w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
              <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Post</h3>
                <button id="btnCloseEditPost" class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
              </div>
              <form id="formEditPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                  <input name="title" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea name="description" class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Replace Cover (optional)</label>
                  <input name="cover" type="file" accept="image/*" class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700 focus:outline-none">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                  <input name="location" type="text" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Publish At</label>
                  <input name="published_at" type="datetime-local" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                  <div class="space-y-1">
                    <label class="flex items-center gap-2"><input type="checkbox" name="allow_comments" value="1" class="rounded text-orange-600"> Allow comments</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1" class="rounded text-orange-600"> Pin this post</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" class="rounded text-orange-600"> Mark as featured</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" class="rounded text-orange-600"> Publish immediately</label>
                  </div>
                </div>
                <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                  <button type="button" id="btnCancelEditPost" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Update Post</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <script>
          // Modal controls
          (function(){
            const modal = document.getElementById('modalNewPost');
            const openBtn = document.getElementById('btnOpenNewPost');
            const closeBtn = document.getElementById('btnCloseNewPost');
            const cancelBtn = document.getElementById('btnCancelNewPost');
            const publishedAt = document.getElementById('publishedAt');
            let sync = true;

            function fmt(dt){
              const pad = n => String(n).padStart(2,'0');
              const y = dt.getFullYear();
              const m = pad(dt.getMonth()+1);
              const d = pad(dt.getDate());
              const hh = pad(dt.getHours());
              const mm = pad(dt.getMinutes());
              return `${y}-${m}-${d}T${hh}:${mm}`;
            }

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
            modal?.addEventListener('click', (e) => { if(e.target === modal) close(); });

            // Realtime publish time
            const timer = setInterval(()=>{
              if(sync && publishedAt){ publishedAt.value = fmt(new Date()); }
            }, 1000);

            publishedAt?.addEventListener('input', ()=>{ sync = false; });
            publishedAt?.addEventListener('change', ()=>{ if(!publishedAt.value) sync = true; });

            if(publishedAt){ publishedAt.value = fmt(new Date()); }

            window.addEventListener('beforeunload', ()=> clearInterval(timer));
          })();

          // Kebab dropdown + Edit modal wiring
          (function(){
            const buttons = document.querySelectorAll('[data-menu-button]');
            let openMenu = null;

            function closeAny(){ if(openMenu){ openMenu.classList.add('hidden'); openMenu = null; } }
            function placeMenuByButton(menu, btn){
              // Portal to body and use fixed positioning to avoid clipping.
              if (menu.parentElement !== document.body) {
                document.body.appendChild(menu);
              }
              menu.classList.remove('absolute');
              menu.style.position = 'fixed';
              menu.style.zIndex = '9999';
              const b = btn.getBoundingClientRect();
              // First, position below and right-aligned to button
              const menuWidth = menu.offsetWidth || 160;
              const menuHeight = menu.offsetHeight || 80;
              let left = Math.min(window.innerWidth - menuWidth - 8, Math.max(8, b.right - menuWidth));
              let top = b.bottom + 8;
              // If bottom overflows, flip above
              if (top + menuHeight > window.innerHeight - 8) {
                top = Math.max(8, b.top - menuHeight - 8);
              }
              // If left still overflows, clamp
              if (left < 8) left = 8;
              menu.style.left = left + 'px';
              menu.style.top = top + 'px';
              menu.style.transform = 'none';
              menu.style.willChange = 'auto';
            }

            buttons.forEach(btn => {
              btn.addEventListener('click', (e)=>{
                e.stopPropagation();
                const id = btn.getAttribute('data-menu-id');
                const menu = document.getElementById(id);
                if(!menu) return;
                if(openMenu && openMenu!==menu){ openMenu.classList.add('hidden'); openMenu = null; }
                const willOpen = menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                if(willOpen){ placeMenuByButton(menu, btn); }
                openMenu = willOpen ? menu : null;
              });
            });
            document.addEventListener('click', closeAny);
            window.addEventListener('scroll', closeAny, { passive:true });
            window.addEventListener('resize', closeAny);
            document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeAny(); });

            // Edit handlers
            const editModal = document.getElementById('modalEditPost');
            const closeEdit = () => { editModal.classList.add('hidden'); document.documentElement.classList.remove('overflow-hidden'); document.body.classList.remove('overflow-hidden'); };
            const openEdit = () => { editModal.classList.remove('hidden'); document.documentElement.classList.add('overflow-hidden'); document.body.classList.add('overflow-hidden'); };
            document.getElementById('btnCloseEditPost')?.addEventListener('click', closeEdit);
            document.getElementById('btnCancelEditPost')?.addEventListener('click', closeEdit);
            editModal?.addEventListener('click', (e)=>{ if(e.target===editModal) closeEdit(); });

            document.querySelectorAll('[data-action="edit"]').forEach(btn => {
              btn.addEventListener('click', ()=>{
                const post = btn.dataset.post ? JSON.parse(btn.dataset.post) : null;
                if(!post) return;
                const form = document.getElementById('formEditPost');
                form.action = '{{ route('admin.posts.update', '__ID__') }}'.replace('__ID__', post.id);
                form.querySelector('[name="title"]').value = post.title || '';
                form.querySelector('[name="description"]').value = post.description || '';
                form.querySelector('[name="location"]').value = post.location || '';
                form.querySelector('[name="published_at"]').value = (post.published_at ? post.published_at.replace(' ', 'T') : '');
                form.querySelector('[name="allow_comments"]').checked = !!post.allow_comments;
                form.querySelector('[name="is_pinned"]').checked = !!post.is_pinned;
                form.querySelector('[name="is_featured"]').checked = !!post.is_featured;
                form.querySelector('[name="is_published"]').checked = (post.status === 'Published');
                openEdit();
              });
            });
          })();
        </script>
      </main>
    </div>
  </div>
</body>
</html>
