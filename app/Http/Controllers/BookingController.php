<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use Carbon\Carbon;

class BookingController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'payment_method' => 'required|in:cash,card',
            'payment_card' => 'required_if:payment_method,card|string'
        ]);

        $apartment = Apartment::findOrFail($validated['apartment_id']);

        if (!$apartment->isAvailable($validated['start_date'], $validated['end_date'])) {
            return $this->errorResponse('الشقة غير متاحة في هذه الفترة', 409);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $endDate->diffInDays($startDate);
        $totalPrice = $apartment->price * $days;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'apartment_id' => $validated['apartment_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'payment_method' => $validated['payment_method'],
            'payment_card' => $validated['payment_card'] ?? null,
            'status' => 'pending'
        ]);

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم إنشاء الحجز بنجاح',
            201
        );
    }

    public function myBookings(Request $request)
    {
        $status = $request->query('status');
        $query = Booking::with(['apartment.images', 'apartment.owner'])
            ->where('user_id', $request->user()->id);

        if ($status === 'upcoming') {
            $query->where('start_date', '>', now());
        } elseif ($status === 'past') {
            $query->where('end_date', '<', now());
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $bookings = $query->latest()->paginate(10);

        return response()->json($bookings);
    }

    public function apartmentBookings(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $apartment = Apartment::findOrFail($request->apartment_id);

        if ($apartment->owner_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك برؤية طلبات هذه الشقة', 403);
        }

        $bookings = Booking::with(['user.avatar', 'apartment'])
            ->where('apartment_id', $request->apartment_id)
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $booking = Booking::findOrFail($id);
        $apartment = $booking->apartment;

        if ($apartment->owner_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بتحديث هذا الحجز', 403);
        }

        $booking->update(['status' => $validated['status']]);

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم تحديث حالة الحجز'
        );
    }

    public function reschedule(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date'
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بتعديل هذا الحجز', 403);
        }

        if ($booking->status !== 'approved') {
            return $this->errorResponse('لا يمكن تعديل الحجز إلا إذا كان مقبولاً', 409);
        }

        $apartment = $booking->apartment;

        if (!$apartment->isAvailable($validated['start_date'], $validated['end_date'])) {
            return $this->errorResponse('الشقة غير متاحة في الفترة الجديدة', 409);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $endDate->diffInDays($startDate);
        $totalPrice = $apartment->price * $days;

        $booking->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'status' => 'modified'
        ]);

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم تعديل موعد الحجز'
        );
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بإلغاء هذا الحجز', 403);
        }

        if ($booking->status === 'cancelled') {
            return $this->errorResponse('الحجز ملغى بالفعل', 409);
        }

        $booking->update(['status' => 'cancelled']);

        return $this->successResponse(
            $booking,
            'تم إلغاء الحجز'
        );
    }
}
