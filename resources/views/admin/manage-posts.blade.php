<!-- resources/views/admin/manage-posts.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Manage Posts</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
        <style>
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
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

            @keyframes pulse {
              0%, 100% { opacity: 0.7; }
              50% { opacity: 1; }
            }

            .clamp-1,
            .clamp-2,
            .clamp-3 {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .clamp-1 {
                -webkit-line-clamp: 1;
            }

            .clamp-2 {
                -webkit-line-clamp: 2;
            }

            .clamp-3 {
                -webkit-line-clamp: 3;
            }
        </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-[#F5F7FB] text-gray-800">
  
  <script>
    // Clean initial loading
    document.addEventListener('DOMContentLoaded', function() {
      const initialLoading = document.getElementById('initialLoading');
      if (initialLoading) {
        setTimeout(() => {
          initialLoading.classList.remove('active');
          setTimeout(() => {
            initialLoading.style.display = 'none';
          }, 300);
        }, 800);
      }
    });
  </script>
        
  <!-- Page Transition Overlay (Hidden by default) -->
  <div class="page-transition" id="pageTransition" style="opacity: 0; visibility: hidden; pointer-events: none;">
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
    <p class="loading-text">loadinggg......</p>
  </div>
        
  <div class="ml-64 flex-1 flex flex-col min-h-screen">

    {{-- Sidebar --}}
    @include('admin.sidebar')
                <div class="flex-1 flex flex-col">
                    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Manage Posts</h2>
                    </header>
                    <main class="p-6 space-y-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-5">
                            @if(session('status'))
                                <div
                                    class="mb-4 rounded-md bg-green-50 text-green-700 px-4 py-2 text-sm border border-green-200">
                                    {{ session('status') }}</div>
                            @endif
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">All Posts</h3>
                                <button id="btnOpenNewPost" type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z" />
                                    </svg>
                                    New Post
                                </button>
                            </div>
                            <div class="mt-4 relative">
                                    <table class="w-full text-sm text-left table-fixed">
                                        <colgroup>
                                            <col style="width: 90px;">
                                            <col style="width: 20%;">
                                            <col style="width: 14%;">
                                            <col style="width: 14%;">
                                            <col style="width: 18%;">
                                            <col style="width: 12%;">
                                            <col style="width: 14%;">
                                            <col style="width: 8%;">
                                            <col style="width: 90px;">
                                        </colgroup>
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Cover</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Title</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Author</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Category</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Description</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Location</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Publish At</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Status</th>
                                            <th class="px-4 py-3 text-xs font-semibold text-gray-700 uppercase tracking-wide text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($posts ?? []) as $post)
                                            <tr class="border-t hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center">
                                                        @if(!empty($post['cover_image']))
                                                            <img src="{{ asset('storage/' . $post['cover_image']) }}"
                                                                alt="{{ $post['title'] }}"
                                                                class="w-14 h-14 rounded-lg object-cover border border-gray-200 shadow-sm" />
                                                        @else
                                                            <div class="w-14 h-14 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-xs text-gray-400">—</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center">
                                                        <div class="clamp-2 text-sm font-medium text-gray-900 leading-snug text-center"
                                                            title="{{ $post['title'] }}">{{ $post['title'] }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center">
                                                        @if(!empty($post['author']))
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm max-w-full">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                                                </svg>
                                                                <span class="truncate min-w-0" title="{{ $post['author'] }}">{{ $post['author'] }}</span>
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                                                </svg>
                                                                <span>No Author</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center">
                                                        @if(!empty($post['category']))
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-medium max-w-full" style="background-color: {{ $post['category']['color'] }}15; color: {{ $post['category']['color'] }}; border: 1px solid {{ $post['category']['color'] }}30">
                                                                <span class="text-sm flex-shrink-0">{{ $post['category']['icon'] }}</span>
                                                                <span class="truncate min-w-0" title="{{ $post['category']['name'] }}">{{ $post['category']['name'] }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400 text-xs">—</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex justify-center">
                                                        <div class="clamp-2 text-xs text-gray-600 leading-relaxed text-center"
                                                            title="{{ $post['description'] ?? '' }}">
                                                            {{ $post['description'] ?? '—' }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="text-xs text-gray-600 truncate"
                                                        title="{{ $post['location'] ?? '' }}">{{ $post['location'] ?? '—' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="text-xs text-gray-600 whitespace-nowrap">
                                                        {{ $post['published_at'] ?? '—' }}</div>
                                                </td>
                                                <td class="px-4 py-5 text-center">
                                                    <div class="flex justify-center items-center">
                                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-medium {{ $post['status'] === 'Published' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }} whitespace-nowrap">
                                                            {{ $post['status'] }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="relative inline-block text-left">
                                                        <button type="button" data-menu-button
                                                            data-menu-id="post-menu-{{ $loop->index }}"
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                                fill="currentColor" class="w-5 h-5">
                                                                <path
                                                                    d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                                                            </svg>
                                                        </button>
                                                        <div id="post-menu-{{ $loop->index }}"
                                                            class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                                                            <button
                                                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                                data-action="edit" data-post='@json($post)'>
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                                    fill="currentColor" class="w-4 h-4 text-gray-500">
                                                                    <path
                                                                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm15.71-9.04a1.003 1.003 0 000-1.42l-2.5-2.5a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.99-1.66z" />
                                                                </svg>
                                                                Edit
                                                            </button>
                                                            <form method="POST"
                                                                action="{{ route('admin.posts.destroy', $post['id']) }}"
                                                                onsubmit="return confirm('Delete this post?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 24 24" fill="currentColor"
                                                                        class="w-4 h-4 text-red-500">
                                                                        <path
                                                                            d="M6 7h12v2H6V7zm2 3h8l-1 9H9L8 10zm3-6h2l1 1h4v2H6V5h4l1-1z" />
                                                                    </svg>
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
                        <div id="modalNewPost" class="fixed inset-0 z-[9999] hidden">
                            <div class="fixed inset-0 bg-black/70"></div>
                            <div class="relative z-10 w-full h-full flex items-center justify-center">
                                <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-3xl mx-4">
                                    <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">Create New Post</h3>
                                        <button id="btnCloseNewPost" class="text-gray-500 hover:text-gray-700">✕</button>
                                    </div>
                                    <form id="formNewPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                            <input name="title" type="text" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="Post title">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                            <select name="category_id" required class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none bg-white">
                                                <option value="">Pilih Kategori</option>
                                                <option value="1"> Tutorial & Tips</option>
                                                <option value="2"> Review & Opini</option>
                                                <option value="3"> Teknologi & Aplikasi</option>
                                                <option value="4"> Edukasi & Informasi</option>
                                                <option value="5"> Alam & Lingkungan</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none" placeholder="Write a short description..."></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                                            <input name="cover" type="file" accept="image/*" class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                            <input name="location" type="text" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none" placeholder="City, Country">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Publish At (Realtime)</label>
                                            <input id="publishedAt" name="published_at" type="datetime-local" class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none">
                                            <p class="text-xs text-gray-500 mt-1">Auto-updating to current time</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                                            <div class="space-y-1">
                                                <label class="flex items-center gap-2"><input type="checkbox" name="allow_comments" value="1" class="rounded text-orange-600"> Allow comments</label>
                                                <label class="flex items-center gap-2"><input type="checkbox" name="is_pinned" value="1" class="rounded text-orange-600"> Pin this post</label>
                                                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" class="rounded text-orange-600"> Mark as featured</label>
                                                <label class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" class="rounded text-orange-600" checked> Publish immediately</label>
                                            </div>
                                        </div>
                                        <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                                            <button type="button" id="btnCancelNewPost" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200">Cancel</button>
                                            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">Save Post</button>
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
                                        <button id="btnCloseEditPost"
                                            class="text-gray-500 hover:text-gray-700 focus:outline-none">✕</button>
                                    </div>
                                    <form id="formEditPost" class="p-5 grid grid-cols-2 gap-5 text-sm" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                            <input name="title" type="text" required
                                                class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none">
                                        </div>

                                        <!-- Category -->
                                        <div class="col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                            <select name="category_id" required
                                                class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none bg-white">
                                                <option value="" selected>Pilih Kategori</option>
                                                <option value="1">Tutorial & Tips</option>
                                                <option value="2">Review & Opini</option>
                                                <option value="3">Teknologi & Aplikasi</option>
                                                <option value="4">Edukasi & Informasi</option>
                                                <option value="5">Alam & Lingkungan</option>
                                            </select>
                                        </div>

                                        <div class="col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description"
                                                class="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Replace Cover
                                                (optional)</label>
                                            <input name="cover" type="file" accept="image/*"
                                                class="w-full h-10 border border-gray-300 rounded-md px-3 file:mr-3 file:px-3 file:py-1 file:border-0 file:bg-orange-50 file:text-orange-700 focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                            <input name="location" type="text"
                                                class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Publish
                                                At</label>
                                            <input name="published_at" type="datetime-local"
                                                class="w-full h-10 border border-gray-300 rounded-md px-3 focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
                                            <div class="space-y-1">
                                                <label class="flex items-center gap-2"><input type="checkbox"
                                                        name="allow_comments" value="1" class="rounded text-orange-600">
                                                    Allow comments</label>
                                                <label class="flex items-center gap-2"><input type="checkbox"
                                                        name="is_pinned" value="1" class="rounded text-orange-600"> Pin
                                                    this post</label>
                                                <label class="flex items-center gap-2"><input type="checkbox"
                                                        name="is_featured" value="1" class="rounded text-orange-600">
                                                    Mark as featured</label>
                                                <label class="flex items-center gap-2"><input type="checkbox"
                                                        name="is_published" value="1" class="rounded text-orange-600">
                                                    Publish immediately</label>
                                            </div>
                                        </div>
                                        <div class="col-span-2 flex items-center justify-end gap-3 pt-3">
                                            <button type="button" id="btnCancelEditPost"
                                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none">Update
                                                Post</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            // Modal controls
                            (function () {
                                const modal = document.getElementById('modalNewPost');
                                const openBtn = document.getElementById('btnOpenNewPost');
                                const closeBtn = document.getElementById('btnCloseNewPost');
                                const cancelBtn = document.getElementById('btnCancelNewPost');
                                const publishedAt = document.getElementById('publishedAt');
                                let sync = true;

                                function fmt(dt) {
                                    const pad = n => String(n).padStart(2, '0');
                                    const y = dt.getFullYear();
                                    const m = pad(dt.getMonth() + 1);
                                    const d = pad(dt.getDate());
                                    const hh = pad(dt.getHours());
                                    const mm = pad(dt.getMinutes());
                                    return `${y}-${m}-${d}T${hh}:${mm}`;
                                }

                                function open() {
                                    modal.classList.remove('hidden');
                                    document.documentElement.classList.add('overflow-hidden');
                                    document.body.classList.add('overflow-hidden');
                                }
                                function close() {
                                    modal.classList.add('hidden');
                                    document.documentElement.classList.remove('overflow-hidden');
                                    document.body.classList.remove('overflow-hidden');
                                }

                                openBtn?.addEventListener('click', open);
                                closeBtn?.addEventListener('click', close);
                                cancelBtn?.addEventListener('click', close);
                                modal?.addEventListener('click', (e) => { if (e.target === modal) close(); });

                                // Realtime publish time
                                const timer = setInterval(() => {
                                    if (sync && publishedAt) { publishedAt.value = fmt(new Date()); }
                                }, 1000);

                                publishedAt?.addEventListener('input', () => { sync = false; });
                                publishedAt?.addEventListener('change', () => { if (!publishedAt.value) sync = true; });

                                if (publishedAt) { publishedAt.value = fmt(new Date()); }

                                window.addEventListener('beforeunload', () => clearInterval(timer));
                            })();

                            // Kebab dropdown + Edit modal wiring
                            (function () {
                                const buttons = document.querySelectorAll('[data-menu-button]');
                                let openMenu = null;

                                function closeAny() { if (openMenu) { openMenu.classList.add('hidden'); openMenu = null; } }
                                function placeMenuByButton(menu, btn) {
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
                                    btn.addEventListener('click', (e) => {
                                        e.stopPropagation();
                                        const id = btn.getAttribute('data-menu-id');
                                        const menu = document.getElementById(id);
                                        if (!menu) return;
                                        if (openMenu && openMenu !== menu) { openMenu.classList.add('hidden'); openMenu = null; }
                                        const willOpen = menu.classList.contains('hidden');
                                        menu.classList.toggle('hidden');
                                        if (willOpen) { placeMenuByButton(menu, btn); }
                                        openMenu = willOpen ? menu : null;
                                    });
                                });
                                document.addEventListener('click', closeAny);
                                window.addEventListener('scroll', closeAny, { passive: true });
                                window.addEventListener('resize', closeAny);
                                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAny(); });

                                // Edit handlers
                                const editModal = document.getElementById('modalEditPost');
                                const closeEdit = () => { editModal.classList.add('hidden'); document.documentElement.classList.remove('overflow-hidden'); document.body.classList.remove('overflow-hidden'); };
                                const openEdit = () => { editModal.classList.remove('hidden'); document.documentElement.classList.add('overflow-hidden'); document.body.classList.add('overflow-hidden'); };
                                document.getElementById('btnCloseEditPost')?.addEventListener('click', closeEdit);
                                document.getElementById('btnCancelEditPost')?.addEventListener('click', closeEdit);
                                editModal?.addEventListener('click', (e) => { if (e.target === editModal) closeEdit(); });

                                document.querySelectorAll('[data-action="edit"]').forEach(btn => {
                                    btn.addEventListener('click', () => {
                                        const post = btn.dataset.post ? JSON.parse(btn.dataset.post) : null;
                                        if (!post) return;
                                        const form = document.getElementById('formEditPost');
                                        form.action = '{{ route('admin.posts.update', '__ID__') }}'.replace('__ID__', post.id);
                                        form.querySelector('[name="title"]').value = post.title || '';
                                        form.querySelector('[name="category_id"]').value = post.category_id || '';
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
                        
                        <!-- Clean Loading Script -->
                        <script>
                          document.addEventListener('DOMContentLoaded', function() {
                            setTimeout(() => {
                              const loading = document.getElementById('initialLoading');
                              if (loading) {
                                loading.classList.remove('active');
                                setTimeout(() => loading.style.display = 'none', 300);
                              }
                            }, 800);
                            
                            // Add CRUD loading animations
                            setupCRUDLoading();
                          });
                          
                          function setupCRUDLoading() {
                            let loadingTimeout;
                            
                            // Enhanced loading functions
                            function showLoading(message = 'Processing...', duration = null) {
                              const pageTransition = document.getElementById('pageTransition');
                              if (pageTransition) {
                                pageTransition.style.display = 'flex';
                                pageTransition.style.opacity = '1';
                                pageTransition.style.visibility = 'visible';
                                pageTransition.style.pointerEvents = 'all';
                                pageTransition.classList.add('active');
                                
                                const loadingText = pageTransition.querySelector('.loading-text');
                                if (loadingText) {
                                  loadingText.textContent = message;
                                }
                                
                                if (duration) {
                                  clearTimeout(loadingTimeout);
                                  loadingTimeout = setTimeout(() => {
                                    hideLoading();
                                  }, duration);
                                }
                              }
                            }
                            
                            function hideLoading() {
                              const pageTransition = document.getElementById('pageTransition');
                              if (pageTransition) {
                                pageTransition.style.opacity = '0';
                                pageTransition.style.visibility = 'hidden';
                                pageTransition.style.pointerEvents = 'none';
                                pageTransition.classList.remove('active');
                                setTimeout(() => {
                                  pageTransition.style.display = 'none';
                                }, 300);
                              }
                              clearTimeout(loadingTimeout);
                            }
                            
                            // Make functions globally accessible
                            window.showLoading = showLoading;
                            window.hideLoading = hideLoading;
                            
                            // Only actual form submissions with appropriate messages
                            document.querySelectorAll('form').forEach(form => {
                              form.addEventListener('submit', function(e) {
                                const action = form.getAttribute('action') || '';
                                let loadingMessage = 'Processing...';
                                let duration = 5000;
                                
                                if (action.includes('store')) {
                                  loadingMessage = 'Creating post...';
                                } else if (action.includes('update')) {
                                  loadingMessage = 'Updating post...';
                                } else if (action.includes('destroy')) {
                                  loadingMessage = 'Deleting post...';
                                  duration = 3000;
                                }
                                
                                showLoading(loadingMessage, duration);
                              });
                            });
                            
                            // Navigation links
                            document.querySelectorAll('a[href*="/admin/"], a[href*="/author/"]').forEach(link => {
                              link.addEventListener('click', function(e) {
                                if (!this.href.includes('#') && !this.target) {
                                  showLoading('Loading page...', 2000);
                                }
                              });
                            });
                            
                            // Modal controls - NO loading for these
                            document.querySelectorAll('[data-action="edit"]').forEach(button => {
                              button.addEventListener('click', function(e) {
                                e.preventDefault();
                                // Just open modal, no loading needed
                                const postData = JSON.parse(this.getAttribute('data-post'));
                                // Modal opening logic here without loading
                              });
                            });
                          }
                        </script>
                    </main>
                </div>
            </div>
            
            <!-- Enhanced Anti-Stuck Loading Protection -->
            <script>
              window.loadingDebug = true;
              window.forceHideLoading = function() {
                const loadings = document.querySelectorAll('[id*="Loading"], [id*="Transition"], .page-transition');
                loadings.forEach(el => {
                  if (el) {
                    el.style.opacity = '0';
                    el.style.visibility = 'hidden';
                    el.style.pointerEvents = 'none';
                    el.classList.remove('active');
                    setTimeout(() => {
                      el.style.display = 'none';
                    }, 300);
                  }
                });
                if (window.loadingTimeout) clearTimeout(window.loadingTimeout);
              };
              
              window.addEventListener('load', () => {
                setTimeout(() => {
                  if (window.hideLoading) {
                    window.hideLoading();
                  } else {
                    window.forceHideLoading();
                  }
                }, 500);
              });
              
              document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                  if (window.hideLoading) {
                    window.hideLoading();
                  } else {
                    window.forceHideLoading();
                  }
                }
              });
              
              let clickCount = 0;
              document.addEventListener('click', () => {
                clickCount++;
                if (clickCount > 3) {
                  const transition = document.getElementById('pageTransition');
                  if (transition && transition.style.opacity === '1') {
                    if (window.hideLoading) window.hideLoading();
                    else window.forceHideLoading();
                  }
                  clickCount = 0;
                }
              });
              
              setInterval(() => { clickCount = 0; }, 2000);
            </script>
            
            @vite(['resources/js/animations-fixed.js', 'resources/js/loading-fix.js'])
        </body>

        </html>