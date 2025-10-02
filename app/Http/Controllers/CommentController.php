<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, string $id)
    {
        $post = Post::where('id', $id)->where('is_published', true)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150'],
            'comment' => ['required','string','max:5000'],
        ]);

        $comment = new Comment([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'body' => $validated['comment'],
        ]);
        $comment->post()->associate($post);
        $comment->save();

        return redirect()->route('user.detail', $post->id)
            ->with('success', 'Komentar berhasil dikirim.');
    }

    public function reply(Request $request, string $id, int $commentId)
    {
        $post = Post::where('id', $id)->where('is_published', true)->firstOrFail();
        $parent = $post->comments()->where('id', $commentId)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:150'],
            'comment' => ['required','string','max:5000'],
        ]);

        $reply = new Comment([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'body' => $validated['comment'],
            'parent_id' => $parent->id,
        ]);
        $reply->post()->associate($post);
        $reply->save();

        return redirect()->route('user.detail', $post->id)
            ->with('success', 'Balasan komentar berhasil dikirim.');
    }

    public function like(Request $request, string $id, int $commentId)
    {
        $post = Post::where('id', $id)->where('is_published', true)->firstOrFail();
        $comment = $post->comments()->where('id', $commentId)->firstOrFail();
        // Basic like increment without user tracking. Can be improved with cookie/IP throttle.
        $comment->increment('likes');
        return redirect()->route('user.detail', $post->id);
    }
}
