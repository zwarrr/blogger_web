@extends('author.layout')

@section('title', 'Kelola Artikel')
@section('page-title', 'Artikel Saya')
@section('page-description', 'Kelola semua artikel yang Anda tulis')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari artikel..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent w-64">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                @endforeach
            </select>
            <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <option value="">Semua Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <button onclick="openCreateModal()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Artikel Baru
        </button>
    </div>

    <!-- Posts Grid -->
    <div id="postsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($posts as $post)
            <div class="post-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow" 
                 data-title="{{ strtolower($post['title']) }}" 
                 data-category="{{ $post['category_id'] }}" 
                 data-status="{{ strtolower($post['status']) }}">
                
                @if($post['cover_image'])
                    <div class="aspect-video bg-gray-100 overflow-hidden">
                        <img src="{{ asset('storage/' . $post['cover_image']) }}" alt="{{ $post['title'] }}" 
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                        <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        @if($post['category'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $post['category']['color'] }}20; color: {{ $post['category']['color'] }}">
                                @if($post['category']['icon'])
                                    <span class="mr-1">{!! $post['category']['icon'] !!}</span>
                                @endif
                                {{ $post['category']['name'] }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Tanpa Kategori
                            </span>
                        @endif
                        
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post['status'] === 'Published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $post['status'] }}
                        </span>
                    </div>

                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $post['title'] }}</h3>
                    
                    @if($post['description'])
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $post['description'] }}</p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span>{{ $post['date'] }}</span>
                        @if($post['location'])
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ $post['location'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="editPost({{ json_encode($post) }})" 
                                class="flex-1 bg-orange-50 text-orange-600 px-3 py-2 rounded-md hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 text-sm font-medium">
                            Edit
                        </button>
                        <button onclick="deletePost('{{ $post['id'] }}', '{{ $post['title'] }}')" 
                                class="px-3 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada artikel</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai menulis artikel pertama Anda.</p>
                <div class="mt-6">
                    <button onclick="openCreateModal()" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                        Buat Artikel Baru
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="postModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <form id="postForm" enctype="multipart/form-data">
            @csrf
            <div class="p-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Artikel Baru</h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                    <input type="file" name="cover" accept="image/*" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul *</label>
                    <input type="text" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="location" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Publikasi</label>
                    <input type="datetime-local" name="published_at" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_comments" value="1" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700">Izinkan Komentar</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700">Publikasikan</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_pinned" value="1" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700">Pin Artikel</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700">Artikel Unggulan</span>
                    </label>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    <span id="submitText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let editingPostId = null;

    // Search and Filter Functions
    document.getElementById('searchInput').addEventListener('input', filterPosts);
    document.getElementById('categoryFilter').addEventListener('change', filterPosts);
    document.getElementById('statusFilter').addEventListener('change', filterPosts);

    function filterPosts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('categoryFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const postCards = document.querySelectorAll('.post-card');

        postCards.forEach(card => {
            const title = card.dataset.title;
            const category = card.dataset.category;
            const status = card.dataset.status;

            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = !categoryFilter || category === categoryFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesCategory && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Modal Functions
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Artikel Baru';
        document.getElementById('submitText').textContent = 'Simpan';
        document.getElementById('postForm').reset();
        document.getElementById('postForm').action = '{{ route("author.posts.store") }}';
        document.getElementById('postForm').method = 'POST';
        editingPostId = null;
        document.getElementById('postModal').classList.remove('hidden');
    }

    function editPost(post) {
        document.getElementById('modalTitle').textContent = 'Edit Artikel';
        document.getElementById('submitText').textContent = 'Update';
        
        const form = document.getElementById('postForm');
        form.action = `/author/posts/${post.id}`;
        
        // Add method spoofing for PUT
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        // Fill form with post data
        form.querySelector('[name="title"]').value = post.title || '';
        form.querySelector('[name="category_id"]').value = post.category_id || '';
        form.querySelector('[name="description"]').value = post.description || '';
        form.querySelector('[name="location"]').value = post.location || '';
        form.querySelector('[name="published_at"]').value = post.published_at || '';
        form.querySelector('[name="allow_comments"]').checked = post.allow_comments;
        form.querySelector('[name="is_published"]').checked = post.is_published;
        form.querySelector('[name="is_pinned"]').checked = post.is_pinned;
        form.querySelector('[name="is_featured"]').checked = post.is_featured;
        
        editingPostId = post.id;
        document.getElementById('postModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('postModal').classList.add('hidden');
        editingPostId = null;
    }

    function deletePost(postId, postTitle) {
        if (confirm(`Apakah Anda yakin ingin menghapus artikel "${postTitle}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/author/posts/${postId}`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Form submission
    document.getElementById('postForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    // Close modal when clicking outside
    document.getElementById('postModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection