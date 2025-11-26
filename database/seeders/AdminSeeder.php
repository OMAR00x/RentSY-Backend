<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'phone' => '0900000000',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'approved',
        ]);
    }
}
