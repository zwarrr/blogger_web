<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthorDashboardController extends Controller
{
    public function index()
    {
        $authorName = Auth::user()->name;

        // KPI metrics
        $totalPosts = Post::where('author', $authorName)->count();
        $publishedPosts = Post::where('author', $authorName)->where('is_published', true)->count();
        $draftPosts = $totalPosts - $publishedPosts;
        $totalComments = Comment::whereHas('post', function($q) use ($authorName){
            $q->where('author', $authorName);
        })->count();

        // Posts by day for last 7 days (including today)
        $start = Carbon::today()->subDays(6)->startOfDay();
        $raw = Post::where('author', $authorName)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c','d');
        $days = collect(range(6,0))->map(function($i){ return Carbon::today()->subDays($i); });
        $postsByDay = $days->map(function(Carbon $day) use ($raw){
            $key = $day->toDateString();
            return [
                'label' => $day->format('D'),
                'count' => (int)($raw[$key] ?? 0),
            ];
        })->values();
        $maxCount = max(1, $postsByDay->max('count'));

        // Top categories used by this author
        $categoryCounts = Post::where('author', $authorName)
            ->selectRaw('category_id, COUNT(*) as c')
            ->groupBy('category_id')
            ->orderByDesc('c')
            ->limit(5)
            ->get();
        $categoriesMap = Category::whereIn('id', $categoryCounts->pluck('category_id')->filter())
            ->pluck('name','id');
        $topCategories = $categoryCounts->map(function($row) use ($categoriesMap, $totalPosts){
            $name = $row->category_id ? ($categoriesMap[$row->category_id] ?? 'Unknown') : 'Tidak Berkategori';
            $count = (int) $row->c;
            $pct = $totalPosts > 0 ? round($count * 100 / $totalPosts) : 0;
            return [ 'name' => $name, 'count' => $count, 'pct' => $pct ];
        });

        // Recent posts
        $recentPosts = Post::where('author', $authorName)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id','title','is_published','created_at'])
            ->map(function(Post $p){
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'date' => optional($p->created_at)->format('M d, Y'),
                    'status' => $p->is_published ? 'Published' : 'Draft',
                ];
            });

        // Recent comments on author's posts
        $recentComments = Comment::with(['post:id,title'])
            ->whereHas('post', function($q) use ($authorName){ $q->where('author', $authorName); })
            ->orderBy('created_at','desc')
            ->take(5)
            ->get(['id','name','body','post_id','created_at'])
            ->map(function(Comment $c){
                return [
                    'name' => $c->name,
                    'excerpt' => Str::limit($c->body, 80),
                    'post_title' => $c->post?->title ?? 'Unknown',
                    'date' => optional($c->created_at)->format('M d'),
                ];
            });

        return view('author.dashboard', compact(
            'totalPosts', 'publishedPosts', 'draftPosts', 'totalComments',
            'postsByDay', 'maxCount', 'topCategories', 'recentPosts', 'recentComments'
        ));
    }
}
