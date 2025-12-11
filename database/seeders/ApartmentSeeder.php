<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;
use App\Models\City;
use App\Models\Area;
use App\Models\Image;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first() ?? User::first();
        $city = City::first();
        $area = Area::first();

        $apartments = [
            [
                'title' => 'شقة فاخرة في وسط المدينة',
                'description' => 'شقة واسعة ومجهزة بالكامل في موقع مميز',
                'price' => 150,
                'price_type' => 'daily',
                'rooms' => 3,
                'status' => 'active',
                'address' => 'شارع بغداد، دمشق',
            ],
            [
                'title' => 'شقة عصرية مطلة على البحر',
                'description' => 'شقة حديثة مع إطلالة رائعة',
                'price' => 200,
                'price_type' => 'daily',
                'rooms' => 4,
                'status' => 'active',
                'address' => 'كورنيش المزة، دمشق',
            ],
            [
                'title' => 'شقة مفروشة للإيجار اليومي',
                'description' => 'شقة مفروشة بالكامل مناسبة للعائلات',
                'price' => 500,
                'price_type' => 'daily',
                'rooms' => 2,
                'status' => 'active',
                'address' => 'المالكي، دمشق',
            ],
            [
                'title' => 'شقة اقتصادية قريبة من الجامعة',
                'description' => 'شقة مناسبة للطلاب والموظفين',
                'price' => 800,
                'price_type' => 'daily',
                'rooms' => 2,
                'status' => 'active',
                'address' => 'المزة، دمشق',
            ],
        ];

        foreach ($apartments as $index => $apartmentData) {
            $apartment = Apartment::create([
                'owner_id' => $owner->id,
                'city_id' => $city->id,
                'area_id' => $area->id,
                ...$apartmentData
            ]);

            $imageNumber = ($index % 4) + 1;
            Image::create([
                'imageable_type' => Apartment::class,
                'imageable_id' => $apartment->id,
                'url' => "apartments/{$imageNumber}.jpg",
                'is_main' => true,
                'type' => 'apartment'
            ]);
        }
    }
}
