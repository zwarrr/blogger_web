<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Auditor;
use Illuminate\Support\Facades\Hash;

class UserAuditorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users that correspond to auditors for foreign key relationship
        $auditors = Auditor::all();
        
        foreach ($auditors as $auditor) {
            // Check if user with same email already exists
            $existingUser = User::where('email', $auditor->email)->first();
            
            if (!$existingUser) {
                User::create([
                    'id' => 'USER' . substr($auditor->id, 7), // Convert AUDITOR001 to USER001
                    'name' => $auditor->name,
                    'email' => $auditor->email,
                    'password' => Hash::make('password'), // Default password
                    'role' => 'auditor',
                    'email_verified_at' => now(),
                ]);
                
                echo "Created user for auditor: {$auditor->name} ({$auditor->email})" . PHP_EOL;
            }
        }
        
        // If no auditors exist, create at least one default auditor user
        if ($auditors->isEmpty()) {
            User::create([
                'id' => 'USER001',
                'name' => 'Default Auditor',
                'email' => 'auditor@example.com',
                'password' => Hash::make('password'),
                'role' => 'auditor',
                'email_verified_at' => now(),
            ]);
            
            echo "Created default auditor user" . PHP_EOL;
        }
    }
}