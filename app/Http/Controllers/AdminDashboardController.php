<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Auditor;
use App\Models\Author;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();

        // KPI metrics from DB
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $totalCategories = Category::count();
        $totalUsers = Admin::count() + Auditor::count() + Author::count();

        // Posts by day for last 7 days
        $start = Carbon::today()->subDays(6)->startOfDay();
        $raw = Post::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c','d');
        $days = collect(range(6,0))->map(fn($i) => Carbon::today()->subDays($i));
        $postsByDay = $days->map(function(Carbon $day) use ($raw){
            $key = $day->toDateString();
            return [ 'label' => $day->format('D'), 'count' => (int)($raw[$key] ?? 0) ];
        })->values();
        $maxCount = max(1, $postsByDay->max('count'));

        // Top categories across all posts
        $categoryCounts = Post::selectRaw('category_id, COUNT(*) as c')
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

        // Recent posts (global)
        $recentPosts = Post::orderBy('created_at','desc')
            ->take(5)
            ->get(['id','title','is_published','created_at'])
            ->map(fn(Post $p) => [
                'title' => $p->title,
                'date' => optional($p->created_at)->format('M d, Y'),
                'status' => $p->is_published ? 'Published' : 'Draft',
            ]);

        // Recent comments (global)
        $recentComments = Comment::with(['post:id,title'])
            ->orderBy('created_at','desc')
            ->take(5)
            ->get(['id','name','body','post_id','created_at'])
            ->map(function($c){
                return [
                    'name' => $c->name,
                    'excerpt' => Str::limit($c->body, 80),
                    'post_title' => $c->post?->title ?? 'Unknown',
                    'date' => optional($c->created_at)->format('M d'),
                ];
            });

        return view('admin.dashboard', compact(
            'admin',
            'totalPosts','totalComments','totalCategories','totalUsers',
            'postsByDay','maxCount','topCategories','recentPosts','recentComments'
        ));
    }
}
