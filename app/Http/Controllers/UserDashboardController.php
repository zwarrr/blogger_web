<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class UserDashboardController extends Controller
{
    public function index() { return redirect()->route('user.views'); }

    // List posts for public view
    public function views()
    {
        $posts = Post::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9, ['id','title','description','cover_image','created_at']);

        // map to attributes used by blade (thumbnail, content excerpt handled in blade)
        $posts->getCollection()->transform(function($p){
            $p->thumbnail = $p->cover_image ? asset('storage/'.$p->cover_image) : null;
            // Use description as content source in current schema
            $p->content = $p->description ?? '';
            return $p;
        });

    return view('views', compact('posts'));
    }

    // Detail page for a single post
    public function detail(string $id)
    {
        $post = Post::where('id', $id)->where('is_published', true)->firstOrFail();
        $post->thumbnail = $post->cover_image ? asset('storage/'.$post->cover_image) : null;
        $post->content = $post->description ?? '';
    return view('detail', compact('post'));
    }
}
