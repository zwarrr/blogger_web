<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();

        // Placeholder stats and data (replace with real queries when models exist)
        $stats = [
            'posts' => 120,
            'drafts' => 8,
            'comments' => 342,
            'views' => 15890,
        ];

        $recentPosts = [
            [
                'title' => 'Introducing Our New Feature',
                'date' => now()->subDays(1)->format('Y-m-d'),
                'status' => 'Published',
                'views' => 420,
            ],
            [
                'title' => 'Weekly Roundup: Tips & Tricks',
                'date' => now()->subDays(2)->format('Y-m-d'),
                'status' => 'Draft',
                'views' => 0,
            ],
            [
                'title' => 'Community Spotlight: September',
                'date' => now()->subDays(3)->format('Y-m-d'),
                'status' => 'Scheduled',
                'views' => 0,
            ],
        ];

        return view('admin.dashboard', compact('admin', 'stats', 'recentPosts'));
    }
}
