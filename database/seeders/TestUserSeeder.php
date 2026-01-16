<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'phone' => '0911111111',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'birthdate' => '1990-01-01',
            'status' => 'approved',
            'fcm_token' => null,

            'wallet' => 0
        ]);

        User::create([
            'first_name' => 'سارة',
            'last_name' => 'علي',
            'phone' => '0922222222',
            'password' => Hash::make('password123'),
            'role' => 'renter',
            'birthdate' => '1995-05-15',
            'status' => 'approved',
            'fcm_token' => null,

            'wallet' => 10000
        ]);

        User::create([
            'first_name' => 'خالد',
            'last_name' => 'أحمد',
            'phone' => '0933333333',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'birthdate' => '1988-08-20',
            'status' => 'approved',
            'fcm_token' => null,
            'wallet' => 0
        ]);

        $this->command->info(' تم إنشاء 3 مستخدمين تجريبيين بنجاح!');
    }
}
