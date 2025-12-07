<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            'واي فاي',
            'بلكون',
            'مكيف',
            'مصعد',
            'موقف سيارة',
            'حديقة',

        ];

        foreach ($amenities as $amenity) {
            Amenity::create(['name' => $amenity]);
        }
    }
}
