<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;

class ReviewController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بتقييم هذا الحجز', 403);
        }

        if ($booking->status !== 'approved') {
            return $this->errorResponse('لا يمكن تقييم حجز غير مقبول', 409);
        }

        $review = Review::updateOrCreate(
            [
                'booking_id' => $validated['booking_id'],
                'user_id' => $request->user()->id,
                'apartment_id' => $booking->apartment_id
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return $this->successResponse(
            $review->load(['user.avatar', 'apartment']),
            'تم حفظ التقييم بنجاح',
            201
        );
    }

    public function apartmentReviews($apartmentId)
    {
        $reviews = Review::with(['user.avatar'])
            ->where('apartment_id', $apartmentId)
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }
}
