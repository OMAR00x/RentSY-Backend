<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Http\Traits\ResponseTrait;
use App\Services\NotificationService;

class BookingController extends Controller
{
    use ResponseTrait;

    // المستأجر ينشئ حجز جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $apartment = Apartment::findOrFail($validated['apartment_id']);

        if (!$apartment->isAvailable($validated['start_date'], $validated['end_date'])) {
            return $this->errorResponse('الشقة غير متاحة في هذه الفترة', 409);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate);
        $totalPrice = $apartment->price * $days;

        $renter = $request->user();

        if ($renter->wallet < $totalPrice) {
            return $this->errorResponse('رصيد المحفظة غير كافي', 400);
        }

        $booking = Booking::create([
            'user_id' => $renter->id,
            'apartment_id' => $validated['apartment_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_method' => 'wallet',
        ]);

        $renter->decrement('wallet', $totalPrice);

        // إرسال إشعار للمالك
        try {
            $notificationService = new NotificationService(new FirebaseService());
            $notificationService->sendToUser(
                $apartment->owner_id,
                'طلب حجز جديد',
                "لديك طلب حجز جديد من {$renter->name} للشقة {$apartment->title}",
                [
                    'type' => 'new_booking',
                    'booking_id' => $booking->id,
                    'apartment_id' => $apartment->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send reschedule notification: ' . $e->getMessage());
        }

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم طلب الحجز بنجاح',
            201
        );
    }

    // المستأجر يعرض حجوزاته (حالية، ملغية، سابقة)
    public function myBookings(Request $request)
    {
        $type = $request->query('type'); // current, cancelled, past
        $now = now();

        $query = Booking::with(['apartment.images', 'apartment.owner'])
            ->where('user_id', $request->user()->id);

        if ($type === 'current') {
            $query->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('status', 'approved');
        } elseif ($type === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($type === 'past') {
            $query->where('end_date', '<', $now);
        }

        $bookings = $query->latest()->get();

        return response()->json($bookings);
    }

    // المؤجر يعرض طلبات الحجز على شقته
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
            ->get();

        return response()->json($bookings);
    }

    // المؤجر يقبل أو يرفض الحجز
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

        // إضافة المبلغ للمؤجر عند الموافقة أو إرجاعه للمستأجر عند الرفض
        if ($validated['status'] === 'approved') {
            $apartment->owner->increment('wallet', $booking->total_price);
        } elseif ($validated['status'] === 'rejected') {
            $booking->user->increment('wallet', $booking->total_price);
        }

        // إرسال إشعار للمستأجر
        try {
            $notificationService = new NotificationService(new FirebaseService());
            $message = $validated['status'] === 'approved' ? 'تم قبول حجزك' : 'تم رفض حجزك';
            $notificationService->sendToUser(
                $booking->user_id,
                $message,
                "حجزك للشقة {$apartment->title}",
                [
                    'type' => 'booking_status',
                    'booking_id' => $booking->id,
                    'status' => $validated['status'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send booking status notification: ' . $e->getMessage());
        }

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم تحديث حالة الحجز'
        );
    }

    // المستأجر يعدل موعد الحجز
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

        $apartment = $booking->apartment;

        if (!$apartment->isAvailable($validated['start_date'], $validated['end_date'], $booking->id)) {
            return $this->errorResponse('الشقة غير متاحة في الفترة الجديدة', 409);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate);
        $newTotalPrice = $apartment->price * $days;

        $booking->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $newTotalPrice,
            'status' => 'pending'
        ]);

        // إرسال إشعار للمالك
        try {
            $notificationService = new NotificationService(new FirebaseService());
            $notificationService->sendToUser(
                $apartment->owner_id,
                'طلب تعديل حجز',
                "لديك طلب تعديل حجز من {$request->user()->name} للشقة {$apartment->title}",
                [
                    'type' => 'booking_reschedule',
                    'booking_id' => $booking->id,
                    'apartment_id' => $apartment->id,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send reschedule notification: ' . $e->getMessage());
        }

        return $this->successResponse(
            $booking->load(['apartment', 'user']),
            'تم تعديل موعد الحجز ويحتاج موافقة المؤجر'
        );
    }

    // المستأجر يلغي الحجز
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بإلغاء هذا الحجز', 403);
        }

        if ($booking->status === 'cancelled') {
            return $this->errorResponse('الحجز ملغى بالفعل', 409);
        }

        // إرجاع المبلغ للمستأجر
        if ($booking->status === 'pending') {
            $booking->user->increment('wallet', $booking->total_price);
        }

        $booking->update(['status' => 'cancelled']);

        return $this->successResponse($booking, 'تم إلغاء الحجز');
    }
}
