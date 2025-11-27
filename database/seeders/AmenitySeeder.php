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
            'تدفئة',
            'مصعد',
            'موقف سيارة',
            'حديقة',
            'مسبح',


        ];

        foreach ($amenities as $amenity) {
            Amenity::create(['name' => $amenity]);
        }
    }
}
