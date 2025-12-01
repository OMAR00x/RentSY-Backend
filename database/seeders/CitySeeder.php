<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'دمشق',
            'ريف دمشق',
            'حلب',
            'حمص',
            'حماة',
            'اللاذقية',
            'طرطوس',
            'إدلب',
            'درعا',
            'السويداء',
            'القنيطرة',
            'دير الزور',
            'الرقة',
            'الحسكة'
        ];

        foreach ($cities as $cityName) {
            $city = City::create(['name' => $cityName]);
        }
    }
}
