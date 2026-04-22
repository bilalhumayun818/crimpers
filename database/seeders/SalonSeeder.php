<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SalonSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Safiullah',
            'email' => 'admin@starline.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }
}
