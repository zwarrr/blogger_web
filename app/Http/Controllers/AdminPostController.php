<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('created_at','desc')->get()
            ->map(function(Post $p){
                return [
                    'id' => $p->id,
                    'cover_image' => $p->cover_image,
                    'title' => $p->title,
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

        return view('admin.manage-posts', compact('posts'));
    }

    public function store(Request $request)
    {
        // Validate request
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'cover' => ['nullable','image','max:5120'], // up to ~5MB
            'location' => ['nullable','string','max:255'],
            'published_at' => ['nullable','date'],
            'allow_comments' => ['nullable','boolean'],
            'is_pinned' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'is_published' => ['nullable','boolean'],
        ]);

        // Generate custom incremental ID: POST001
        $prefix = 'POST';
        $last = Post::where('id', 'like', $prefix.'%')
            ->orderBy('id', 'desc')
            ->value('id');
        $num = 0;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'([0-9]{3,})$/', $last, $m)) {
            $num = intval($m[1]);
        }
        $next = $prefix . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);

        $coverPath = null;

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            // Slug from title for filename base
            $base = preg_replace('/[^a-z0-9]+/i', '-', strtolower($data['title']));
            $base = trim($base, '-');
            $ext = $file->getClientOriginalExtension();
            $filename = $base ? ($base.'-'.uniqid().'.'.$ext) : ('post-'.uniqid().'.'.$ext);
            // store in public disk under posts/
            $coverPath = $file->storeAs('posts', $filename, 'public');
        }

        Post::create([
            'id' => $next,
            'cover_image' => $coverPath,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'allow_comments' => (bool)($data['allow_comments'] ?? false),
            'is_pinned' => (bool)($data['is_pinned'] ?? false),
            'is_featured' => (bool)($data['is_featured'] ?? false),
            'is_published' => (bool)($data['is_published'] ?? false),
        ]);

        // Note: Media upload handling can be added later. Left out for now.

        return back()->with('status', 'Post created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'cover' => ['nullable','image','max:5120'],
            'location' => ['nullable','string','max:255'],
            'published_at' => ['nullable','date'],
            'allow_comments' => ['nullable','boolean'],
            'is_pinned' => ['nullable','boolean'],
            'is_featured' => ['nullable','boolean'],
            'is_published' => ['nullable','boolean'],
        ]);

        $post = Post::findOrFail($id);

        $coverPath = $post->cover_image;
        if ($request->hasFile('cover')) {
            // delete old cover if exists
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
        $post = Post::findOrFail($id);
        // delete cover file if exists
        if ($post->cover_image && \Storage::disk('public')->exists($post->cover_image)) {
            \Storage::disk('public')->delete($post->cover_image);
        }
        $post->delete();

        // Catatan: ID tetap tidak di-reuse; format POST001 akan tetap berurutan pada pembuatan berikutnya.
        return back()->with('status', 'Post deleted successfully.');
    }
}
