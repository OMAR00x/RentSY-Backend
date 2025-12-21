<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Favorite;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;

class ApartmentController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Apartment::with(['images', 'city', 'owner.avatar'])
            ->where('status', 'active');

        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->area_id) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->rooms) {
            if ($request->rooms === '4+') {
                $query->where('rooms', '>=', 4);
            } else {
                $query->where('rooms', $request->rooms);
            }
        }

        if ($request->amenities) {
            $amenityIds = is_array($request->amenities) ? $request->amenities : explode(',', $request->amenities);
            foreach ($amenityIds as $amenityId) {
                $query->whereHas('amenities', function ($q) use ($amenityId) {
                    $q->where('amenities.id', $amenityId);
                });
            }
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%');
            });

            // حفظ البحث في السجل
            if ($request->user()) {
                SearchHistory::create([
                    'user_id' => $request->user()->id,
                    'query' => $request->search
                ]);
            }
        }

        $apartments = $query->latest()->paginate(10);

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
            'city',
            'owner.avatar',
            'amenities',
            'reviews.user.avatar'
        ])->findOrFail($id);

        if ($request->user()) {
            $apartment->is_favorite = Favorite::where('user_id', $request->user()->id)
                ->where('apartment_id', $id)
                ->exists();
        }

        return response()->json($apartment);
    }

    public function ownerApartments(Request $request)
    {
        $apartments = Apartment::with(['images', 'city'])
            ->where('owner_id', $request->user()->id)
            ->select('id', 'title', 'address', 'price', 'price_type', 'city_id', 'status', 'created_at')
            ->latest()
            ->get();
        return $this->successResponse($apartments, 'تم جلب الشقق بنجاح');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'rooms' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:daily,weekly,monthly',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'array|max:10',
            'images.*' => 'image|max:5120'
        ]);

        $apartment = Apartment::create([
            'owner_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'rooms' => $validated['rooms'],
            'price' => $validated['price'],
            'price_type' => $validated['price_type'],
            'status' => 'active'
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $apartment->images()->create([
                    'url' => $image->store('apartments', 'public'),
                    'type' => 'apartment',
                    'is_main' => $index === 0,
                    'order' => $index
                ]);
            }
        }

        if (!empty($validated['amenities'])) {
            $apartment->amenities()->attach($validated['amenities']);
        }

        return $this->successResponse(
            $apartment->load(['images', 'amenities']),
            'تم إضافة الشقة بنجاح',
            201
        );
    }

    public function update(Request $request, $id)
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->owner_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بتعديل هذه الشقة', 403);
        }

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'address' => 'string',
            'rooms' => 'integer|min:1',
            'price' => 'numeric|min:0',
            'price_type' => 'in:daily,weekly,monthly',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $apartment->update($validated);

        if (isset($validated['amenities'])) {
            $apartment->amenities()->sync($validated['amenities']);
        }

        return $this->successResponse(
            $apartment->load(['images', 'amenities']),
            'تم تحديث الشقة بنجاح'
        );
    }

    public function destroy(Request $request, $id)
    {
        $apartment = Apartment::findOrFail($id);

        if ($apartment->owner_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بحذف هذه الشقة', 403);
        }

        $apartment->delete();

        return $this->successResponse(null, 'تم حذف الشقة بنجاح');
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
        $favorites = Apartment::with(['images', 'city', 'owner.avatar'])
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
