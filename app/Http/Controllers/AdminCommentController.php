<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class AdminCommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('post:id,title')
            ->orderBy('created_at','desc')
            ->get(['id','code','post_id','name','email','body','is_visible','created_at']);

        $comments = $comments->map(function(Comment $c){
            return [
                'id' => $c->id,
                'code' => $c->code,
                'post_id' => $c->post_id,
                'post_title' => $c->post?->title,
                'name' => $c->name,
                'email' => $c->email,
                'body' => $c->body,
                'is_visible' => (bool)$c->is_visible,
                'created_at' => optional($c->created_at)->format('Y-m-d H:i'),
            ];
        });

        return view('admin.manage-comment', compact('comments'));
    }

    public function toggleVisibility(string $id)
    {
        $c = Comment::findOrFail($id);
        $c->is_visible = !$c->is_visible;
        $c->save();
        return back()->with('status', 'Comment visibility updated.');
    }

    public function destroy(string $id)
    {
        $c = Comment::findOrFail($id);
        $c->delete();
        return back()->with('status', 'Comment deleted.');
    }
}
