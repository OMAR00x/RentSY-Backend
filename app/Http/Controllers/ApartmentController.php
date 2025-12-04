<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Favorite;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::with(['images', 'mainImage', 'city', 'owner.avatar'])
            ->where('status', 'active');

        // فلتر حسب المدينة
        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        // فلتر حسب المنطقة
        if ($request->area_id) {
            $query->where('area_id', $request->area_id);
        }

        // فلتر حسب السعر
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // فلتر حسب عدد الغرف
        if ($request->rooms) {
            if ($request->rooms === '4+') {
                $query->where('rooms', '>=', 4);
            } else {
                $query->where('rooms', $request->rooms);
            }
        }

        // فلتر حسب الميزات
        if ($request->amenities) {
            $amenityIds = is_array($request->amenities) ? $request->amenities : explode(',', $request->amenities);
            foreach ($amenityIds as $amenityId) {
                $query->whereHas('amenities', function ($q) use ($amenityId) {
                    $q->where('amenities.id', $amenityId);
                });
            }
        }

        // البحث
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        $apartments = $query->latest()->paginate(10);

        // إضافة حالة المفضلة للمستخدم المسجل
        if ($request->user()) {
            $favoriteIds = Favorite::where('user_id', $request->user()->id)
                ->pluck('apartment_id')
                ->toArray();

            $apartments->getCollection()->transform(function ($apartment) use ($favoriteIds) {
                $apartment->is_favorite = in_array($apartment->id, $favoriteIds);
                return $apartment;
            });
        }

        return response()->json($apartments);
    }

    public function show($id, Request $request)
    {
        $apartment = Apartment::with([
            'images',
            'city.areas',
            'owner.avatar',
            'amenities',
            'reviews.user.avatar'
        ])->findOrFail($id);

        // التحقق من المفضلة
        if ($request->user()) {
            $apartment->is_favorite = Favorite::where('user_id', $request->user()->id)
                ->where('apartment_id', $id)
                ->exists();
        }
        /*
        // حساب متوسط التقييم
        $apartment->average_rating = $apartment->reviews()->avg('rating');
        $apartment->reviews_count = $apartment->reviews()->count();
*/
        return response()->json($apartment);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('apartment_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'تم الإزالة من المفضلة', 'is_favorite' => false]);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'apartment_id' => $id
        ]);

        return response()->json(['message' => 'تم الإضافة للمفضلة', 'is_favorite' => true]);
    }

    public function favorites(Request $request)
    {
        $favorites = Apartment::with(['images', 'mainImage', 'city', 'owner.avatar'])
            ->whereHas('favorites', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->paginate(10);

        $favorites->getCollection()->transform(function ($apartment) {
            $apartment->is_favorite = true;
            return $apartment;
        });

        return response()->json($favorites);
    }
}
