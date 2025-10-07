<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use Illuminate\Support\Facades\Hash;

class AuthorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, delete existing sample authors to avoid conflicts
        Author::whereIn('id', ['AUTHOR001', 'AUTHOR002', 'AUTHOR003', 'AUTHOR004'])->delete();
        
        // Also clean up corresponding User records if they exist
        \App\Models\User::whereIn('email', ['author1@gmail.com', 'sarah.writer@gmail.com'])
                        ->orWhereIn('id', ['USER001', 'USER002'])
                        ->delete();

        // Create sample authors
        $authors = [
            [
                'id' => 'AUTHOR001',
                'name' => 'Author User',
                'email' => 'author1@gmail.com',
                'password' => Hash::make('author123'),
                'phone' => '08123456789',
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'bio' => 'Penulis artikel teknologi, programming, dan digital lifestyle.',
                'specialization' => 'Teknologi, Programming, Digital Lifestyle',
                'total_posts' => 0,
                'status' => 'active',
            ],
            [
                'id' => 'AUTHOR002',
                'name' => 'Sarah Writer',
                'email' => 'sarah.writer@gmail.com',
                'password' => Hash::make('sarah456'),
                'phone' => '08987654321',
                'address' => 'Jl. Sudirman No. 456, Bandung',
                'bio' => 'Spesialis artikel bisnis, keuangan, dan entrepreneurship.',
                'specialization' => 'Bisnis, Keuangan, Entrepreneurship',
                'total_posts' => 0,
                'status' => 'active',
            ],
        ];

        // Create authors and corresponding users
        foreach ($authors as $authorData) {
            // Create author
            Author::create($authorData);
            
            // Create corresponding user for authentication with custom ID
            $userId = str_replace('AUTHOR', 'USER', $authorData['id']);
            \App\Models\User::create([
                'id' => $userId,
                'name' => $authorData['name'],
                'email' => $authorData['email'],
                'password' => $authorData['password'],
                'role' => 'author',
                'is_active' => 1,
            ]);
        }

        echo "✅ Successfully created " . count($authors) . " authors in 'authors' table and corresponding users\n";
    }
}
