<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        // Placeholder data; replace with Category::latest()->paginate() when model exists
        $categories = [
            ['name' => 'General', 'slug' => 'general', 'posts' => 42],
            ['name' => 'Tutorials', 'slug' => 'tutorials', 'posts' => 16],
            ['name' => 'News', 'slug' => 'news', 'posts' => 9],
        ];

        return view('admin.categories.index', compact('categories'));
    }
}
