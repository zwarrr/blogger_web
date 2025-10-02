<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 'USERBLOG001',
            'name' => 'Test Author',
            'email' => 'author@gmail.com',
            'password' => Hash::make('author123'),
            'role' => 'author',
        ]);
    }
}
