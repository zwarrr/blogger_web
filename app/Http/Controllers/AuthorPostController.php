<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Category;

class AuthorPostController extends Controller
{
    public function index()
    {
        $authorName = Auth::user()->name;
        $posts = Post::with('category')
            ->where('author', $authorName)
            ->orderBy('created_at','desc')
            ->get()
            ->map(function(Post $p){
                return [
                    'id' => $p->id,
                    'cover_image' => $p->cover_image,
                    'title' => $p->title,
                    'category_id' => $p->category_id,
                    'category' => $p->category ? [
                        'id' => $p->category->id,
                        'name' => $p->category->name,
                        'icon' => $p->category->icon,
                        'color' => $p->category->color,
                    ] : null,
                    'description' => $p->description,
                    'location' => $p->location,
                    'published_at' => optional($p->published_at)->format('Y-m-d H:i'),
                    'date' => $p->created_at?->format('Y-m-d'),
                    'status' => $p->is_published ? 'Published' : 'Draft',
                    'allow_comments' => (bool)$p->allow_comments,
                    'is_pinned' => (bool)$p->is_pinned,
                    'is_featured' => (bool)$p->is_featured,
                    'is_published' => (bool)$p->is_published,
                ];
            });

        $categories = Category::select('id','name','icon','color')
            ->orderBy('name')
            ->get()
            ->map(function(Category $c){
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'icon' => $c->icon,
                    'color' => $c->color,
                ];
            });
        return view('author.manage-posts', compact('posts','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'category_id' => ['required','exists:categories,id'],
            'description' => ['nullable','string'],
            'cover' => ['nullable','image','max:5120'],
            'location' => ['nullable','string','max:255'],
            'published_at' => ['nullable','date'],
            'allow_comments' => ['nullable','boolean'],
            'is_pinned' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'is_published' => ['nullable','boolean'],
        ]);

        $prefix = 'POST';
        $last = Post::where('id', 'like', $prefix.'%')->orderBy('id', 'desc')->value('id');
        $num = 0;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'([0-9]{3,})$/', $last, $m)) {
            $num = intval($m[1]);
        }
        $next = $prefix . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $base = preg_replace('/[^a-z0-9]+/i', '-', strtolower($data['title']));
            $base = trim($base, '-');
            $ext = $file->getClientOriginalExtension();
            $filename = $base ? ($base.'-'.uniqid().'.'.$ext) : ('post-'.uniqid().'.'.$ext);
            $coverPath = $file->storeAs('posts', $filename, 'public');
        }

        Post::create([
            'id' => $next,
            'cover_image' => $coverPath,
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'allow_comments' => (bool)($data['allow_comments'] ?? false),
            'is_pinned' => (bool)($data['is_pinned'] ?? false),
            'is_featured' => (bool)($data['is_featured'] ?? false),
            'is_published' => (bool)($data['is_published'] ?? false),
            'author' => Auth::user()->name,
        ]);

        return back()->with('status', 'Post created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'category_id' => ['required','exists:categories,id'],
            'description' => ['nullable','string'],
            'cover' => ['nullable','image','max:5120'],
            'location' => ['nullable','string','max:255'],
            'published_at' => ['nullable','date'],
            'allow_comments' => ['nullable','boolean'],
            'is_pinned' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'is_published' => ['nullable','boolean'],
        ]);

        $post = Post::where('id',$id)->where('author', Auth::user()->name)->firstOrFail();

        $coverPath = $post->cover_image;
        if ($request->hasFile('cover')) {
            if ($coverPath && \Storage::disk('public')->exists($coverPath)) {
                \Storage::disk('public')->delete($coverPath);
            }
            $file = $request->file('cover');
            $base = preg_replace('/[^a-z0-9]+/i', '-', strtolower($data['title']));
            $base = trim($base, '-');
            $ext = $file->getClientOriginalExtension();
            $filename = $base ? ($base.'-'.uniqid().'.'.$ext) : ('post-'.uniqid().'.'.$ext);
            $coverPath = $file->storeAs('posts', $filename, 'public');
        }

        $post->update([
            'title' => $data['title'],
            'category_id' => $data['category_id'],
            'cover_image' => $coverPath,
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'allow_comments' => (bool)($data['allow_comments'] ?? false),
            'is_pinned' => (bool)($data['is_pinned'] ?? false),
            'is_featured' => (bool)($data['is_featured'] ?? false),
            'is_published' => (bool)($data['is_published'] ?? false),
        ]);

        return back()->with('status', 'Post updated successfully.');
    }

    public function destroy(string $id)
    {
        $post = Post::where('id',$id)->where('author', Auth::user()->name)->firstOrFail();
        if ($post->cover_image && \Storage::disk('public')->exists($post->cover_image)) {
            \Storage::disk('public')->delete($post->cover_image);
        }
        $post->delete();
        return back()->with('status', 'Post deleted successfully.');
    }
}
