<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'admin_id' => 'ADMN1',
            'name' => 'AdminSuper',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin987'),  // ganti password sesuai kebutuhan
        ]);
    }
}
