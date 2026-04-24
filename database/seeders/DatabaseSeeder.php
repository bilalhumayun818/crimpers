<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Delete the old admin user to ensure only the new one works
        User::where('email', 'safullahzafar@gmail.com')->delete();

        // Create or update the new admin user
        User::updateOrCreate(
            ['email' => 'Sa40560@gmail.com'],
            [
                'name' => 'Sajid',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );
    }
}
