@extends('author.layout')

@section('title', 'Dashboard Author')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan aktivitas dan statistik artikel Anda')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Posts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPosts }}</p>
                    <p class="text-sm font-medium text-gray-600">Total Artikel</p>
                </div>
            </div>
        </div>

        <!-- Published Posts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-semibold text-gray-900">{{ $publishedPosts }}</p>
                    <p class="text-sm font-medium text-gray-600">Dipublikasi</p>
                </div>
            </div>
        </div>

        <!-- Draft Posts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-semibold text-gray-900">{{ $draftPosts }}</p>
                    <p class="text-sm font-medium text-gray-600">Draft</p>
                </div>
            </div>
        </div>

        <!-- Total Comments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalComments }}</p>
                    <p class="text-sm font-medium text-gray-600">Komentar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Posts Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Artikel (7 Hari Terakhir)</h3>
            <div class="h-64 flex items-end justify-between gap-2">
                @foreach($postsByDay as $day)
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-full bg-orange-100 rounded-t flex items-end justify-center relative" 
                             style="height: {{ $maxCount > 0 ? ($day['count'] / $maxCount * 200) : 0 }}px; min-height: 20px;">
                            @if($day['count'] > 0)
                                <span class="text-xs font-medium text-orange-700 mb-1">{{ $day['count'] }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-600 mt-2">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Categories -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Populer</h3>
            <div class="space-y-3">
                @forelse($topCategories as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-3"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $category['name'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">{{ $category['count'] }} artikel</span>
                            <span class="text-xs text-gray-400">({{ $category['pct'] }}%)</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada artikel dengan kategori</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Posts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Artikel Terbaru</h3>
                <a href="{{ route('author.posts.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentPosts as $post)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $post['title'] }}</h4>
                            <p class="text-xs text-gray-500">{{ $post['date'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post['status'] === 'Published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $post['status'] }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada artikel</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Komentar Terbaru</h3>
                <a href="#" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentComments as $comment)
                    <div class="py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $comment['name'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $comment['excerpt'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">pada "{{ $comment['post_title'] }}"</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $comment['date'] }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada komentar</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection