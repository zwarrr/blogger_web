<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kunjungan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/b.svg') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 font-sans antialiased">
    <!-- Sidebar -->
    @include('admin.sidebar')
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Kunjungan Baru</h1>
                    <p class="text-gray-600 mt-1">Buat jadwal kunjungan auditor ke author</p>
                </div>
                <a href="{{ route('admin.visits.index') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </header>

        <!-- Content -->
        <main class="p-8">
            <!-- Flash Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Penugasan Admin -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
                <form action="{{ route('admin.visits.store') }}" method="POST" x-data="visitAssignmentForm()">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Author Selection -->
                            <div>
                                <label for="author_id" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Nama Author <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="author_id" name="author_id" x-model="selectedAuthor" @change="updateAuthorData()" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <option value="">-- Pilih Author --</option>
                                    @if(isset($allAuthors) && $allAuthors->count() > 0)
                                        @foreach($allAuthors as $author)
                                            <option value="{{ $author->id }}" 
                                                    data-name="{{ $author->name }}"
                                                    data-email="{{ $author->email }}" 
                                                    data-address="{{ $author->address ?? '' }}"
                                                    data-phone="{{ $author->phone ?? '' }}">
                                                {{ $author->name }} ({{ $author->email }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled>-- Tidak ada author tersedia --</option>
                                    @endif
                                </select>
                                @error('author_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat/Lokasi Author (Auto-fill) -->
                            <div x-show="selectedAuthor">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Alamat/Lokasi Author
                                    </span>
                                </label>
                                <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-700">
                                    <span x-text="authorData.address || 'Alamat akan diverifikasi oleh auditor'"></span>
                                </div>
                                <div x-show="authorData.phone" class="text-sm text-gray-500 mt-1">
                                    <span class="font-medium">No. HP:</span> <span x-text="authorData.phone"></span>
                                </div>
                            </div>

                            <!-- Auditor Selection -->
                            <div>
                                <label for="auditor_id" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Nama Auditor yang Ditugaskan <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="auditor_id" name="auditor_id" required 
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <option value="">-- Pilih Auditor --</option>
                                    @if(isset($auditors) && $auditors->count() > 0)
                                        @foreach($auditors as $auditor)
                                            <option value="{{ $auditor->id }}">{{ $auditor->name }} ({{ $auditor->email }})</option>
                                        @endforeach
                                    @else
                                        <option disabled>-- Tidak ada auditor tersedia --</option>
                                    @endif
                                </select>
                                @error('auditor_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Tanggal Kunjungan -->
                            <div>
                                <label for="tanggal_kunjungan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Tanggal Kunjungan <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="datetime-local" id="tanggal_kunjungan" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan') }}" required
                                       min="{{ date('Y-m-d\TH:i') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md">
                                @error('tanggal_kunjungan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tujuan Kunjungan -->
                            <div>
                                <label for="tujuan" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Tujuan Kunjungan <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <textarea id="tujuan" name="tujuan" rows="4" required maxlength="1000"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                          placeholder="Jelaskan tujuan dan maksud kunjungan secara detail...">{{ old('tujuan') }}</textarea>
                                <div class="text-sm text-gray-500 mt-1">Maksimal 1000 karakter</div>
                                @error('tujuan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan Internal Admin -->
                            <div>
                                <label for="catatan_admin" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Catatan Internal (Opsional)
                                    </span>
                                </label>
                                <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="500"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 shadow-sm hover:shadow-md resize-none"
                                          placeholder="Catatan internal admin (tidak ditampilkan ke auditor)...">{{ old('catatan_admin') }}</textarea>
                                <div class="text-sm text-gray-500 mt-1">Maksimal 500 karakter - hanya untuk keperluan internal admin</div>
                                @error('catatan_admin')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.visits.index') }}" 
                           class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Buat Penugasan Kunjungan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function visitAssignmentForm() {
            return {
                selectedAuthor: '',
                authorData: {
                    name: '',
                    email: '',
                    address: '',
                    phone: ''
                },
                
                updateAuthorData() {
                    const select = document.getElementById('author_id');
                    const selectedOption = select.options[select.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        this.authorData = {
                            name: selectedOption.dataset.name || '',
                            email: selectedOption.dataset.email || '',
                            address: selectedOption.dataset.address || '',
                            phone: selectedOption.dataset.phone || ''
                        };
                    } else {
                        this.authorData = {
                            name: '',
                            email: '',
                            address: '',
                            phone: ''
                        };
                    }
                },

                // Validate form before submit
                validateForm() {
                    const authorId = document.getElementById('author_id').value;
                    const auditorId = document.getElementById('auditor_id').value;
                    const tanggalKunjungan = document.getElementById('tanggal_kunjungan').value;
                    const tujuan = document.getElementById('tujuan').value;

                    if (!authorId || !auditorId || !tanggalKunjungan || !tujuan) {
                        alert('Mohon lengkapi semua field yang wajib diisi!');
                        return false;
                    }

                    // Check if visit date is in the future
                    const selectedDate = new Date(tanggalKunjungan);
                    const now = new Date();
                    if (selectedDate <= now) {
                        alert('Tanggal kunjungan harus di masa depan!');
                        return false;
                    }

                    return true;
                }
            }
        }
    </script>
</body>
</html>