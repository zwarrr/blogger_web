<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Category;

class AuthorPostController extends Controller
{
    public function index()
    {
        try {
            \Log::info('AuthorPostController::index - Starting');
            
            $user = auth('web')->user();
            if (!$user) {
                \Log::error('AuthorPostController::index - User not authenticated');
                return redirect()->route('auth.login');
            }
            
            $authorName = $user->name;
            \Log::info('AuthorPostController::index - Author: ' . $authorName);
            
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
                            'icon' => $p->category->icon ?? 'fa-folder',
                            'color' => $p->category->color ?? '#6b7280',
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

            \Log::info('AuthorPostController::index - Posts count: ' . $posts->count());

            $categories = Category::select('id','name','icon','color')
                ->orderBy('name')
                ->get()
                ->map(function(Category $c){
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'icon' => $c->icon ?? 'fa-folder',
                        'color' => $c->color ?? '#6b7280',
                    ];
                });

            \Log::info('AuthorPostController::index - Categories count: ' . $categories->count());

            // Calculate statistics
            $totalPosts = $posts->count();
            $publishedPosts = $posts->where('is_published', true)->count();
            $draftPosts = $posts->where('is_published', false)->count();
            $featuredPosts = $posts->where('is_featured', true)->count();

            $statistics = [
                'total' => $totalPosts,
                'published' => $publishedPosts,
                'draft' => $draftPosts,
                'featured' => $featuredPosts,
            ];

            return view('author.posts.index', compact('posts', 'categories', 'statistics'));
        } catch (\Exception $e) {
            \Log::error('AuthorPostController::index - Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman posts.');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => ['required','string','max:255'],
                'category_id' => ['nullable','exists:categories,id'],
                'description' => ['nullable','string'],
                'cover' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:5120'],
                'location' => ['nullable','string','max:255'],
                'published_at' => ['nullable','date'],
                'allow_comments' => ['nullable'],
                'is_pinned' => ['nullable'],
                'is_featured' => ['nullable'],
                'is_published' => ['nullable'],
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
                $coverPath = $file->storeAs('photos', $filename, 'public');
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
                'author' => auth('web')->user()->name,
            ]);

            return response()->json(['success' => true, 'message' => 'Post created successfully.']);
            
        } catch (\Exception $e) {
            \Log::error('AuthorPostController::store - Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan post.'], 422);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'title' => ['required','string','max:255'],
                'category_id' => ['nullable','exists:categories,id'],
                'description' => ['nullable','string'],
                'cover' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:5120'],
                'location' => ['nullable','string','max:255'],
                'published_at' => ['nullable','date'],
                'allow_comments' => ['nullable'],
                'is_pinned' => ['nullable'],
                'is_featured' => ['nullable'],
                'is_published' => ['nullable'],
            ]);

            $post = Post::where('id',$id)->where('author', auth('web')->user()->name)->firstOrFail();

            $coverPath = $post->cover_image;
            if ($request->hasFile('cover')) {
                if ($coverPath && Storage::disk('public')->exists($coverPath)) {
                    Storage::disk('public')->delete($coverPath);
                }
                $file = $request->file('cover');
                $base = preg_replace('/[^a-z0-9]+/i', '-', strtolower($data['title']));
                $base = trim($base, '-');
                $ext = $file->getClientOriginalExtension();
                $filename = $base ? ($base.'-'.uniqid().'.'.$ext) : ('post-'.uniqid().'.'.$ext);
                $coverPath = $file->storeAs('photos', $filename, 'public');
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

            return response()->json(['success' => true, 'message' => 'Post updated successfully.']);
            
        } catch (\Exception $e) {
            \Log::error('AuthorPostController::update - Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui post.'], 422);
        }
    }

    public function destroy(string $id)
    {
        try {
            $post = Post::where('id',$id)->where('author', auth('web')->user()->name)->firstOrFail();
            if ($post->cover_image && Storage::disk('public')->exists($post->cover_image)) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $post->delete();
            
            return response()->json(['success' => true, 'message' => 'Post deleted successfully.']);
            
        } catch (\Exception $e) {
            \Log::error('AuthorPostController::destroy - Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus post.'], 422);
        }
    }
}
