<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TestUserSeeder::class,
            CitySeeder::class,
            AmenitySeeder::class,
            AdminSeeder::class,
            AreasSeeder::class,
            ApartmentSeeder::class,
        ]);
    }
}
