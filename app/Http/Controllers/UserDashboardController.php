<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\VisitorStat;

class UserDashboardController extends Controller
{
    public function index() { return redirect()->route('user.views'); }

    // List posts for public view
    public function views()
    {
        // Real statistics
        $totalPosts = Post::where('is_published', true)->count();
        $totalUsers = VisitorStat::getActiveCount(); // Active visitors in last 5 minutes
        
        $posts = Post::with(['category:id,name,icon,color'])
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9, ['id','title','description','cover_image','created_at','category_id']);

        // map to attributes used by blade (thumbnail, content excerpt handled in blade)
        $collection = method_exists($posts, 'getCollection') ? $posts->getCollection() : collect($posts->items());
        $collection->transform(function($p){
            $p->thumbnail = $p->cover_image ? asset('storage/'.$p->cover_image) : null;
            // Use description as content source in current schema
            $p->content = $p->description ?? '';
            return $p;
        });
        if (method_exists($posts, 'setCollection')) {
            $posts->setCollection($collection);
        }

        return view('views', compact('posts', 'totalPosts', 'totalUsers'));
    }

    // Detail page for a single post
    public function detail(string $id)
    {
        $post = Post::with([
                'category:id,name,icon,color',
                // Load only top-level comments with likes
                'comments' => function($q){
                    $q->where('is_visible', true)
                      ->whereNull('parent_id')
                      ->select('id','post_id','parent_id','name','email','body','created_at','likes');
                },
                // Load visible replies with likes
                'comments.replies' => function($q){
                    $q->where('is_visible', true)
                      ->select('id','post_id','parent_id','name','email','body','created_at','likes');
                },
            ])
            ->where('id', $id)
            ->where('is_published', true)
            ->firstOrFail();
        $post->thumbnail = $post->cover_image ? asset('storage/'.$post->cover_image) : null;
        $post->content = $post->description ?? '';
        $comments = $post->comments;
    return view('detail', compact('post','comments'));
    }

    // Track visitor activity
    public function tracker(Request $request)
    {
        $sessionId = session()->getId();
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        VisitorStat::trackVisitor($sessionId, $ip, $userAgent);

        return response()->json(['success' => true]);
    }
}
