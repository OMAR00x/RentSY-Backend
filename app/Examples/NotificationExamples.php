<?php



namespace App\Examples;

use App\Services\NotificationService;
use App\Models\Booking;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Message;

class NotificationExamples
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


    public function notifyOwnerNewBooking(Booking $booking)
    {
        $apartment = $booking->apartment;
        $renter = $booking->user;
        $owner = $apartment->owner;

        $this->notificationService->sendToUser(
            $owner->id,
            'حجز جديد 🎉',
            "لديك طلب حجز جديد من {$renter->name} للشقة {$apartment->title}",
            [
                'type' => 'new_booking',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'renter_id' => $renter->id,
                'action' => 'view_booking'
            ]
        );
    }


    public function notifyRenterBookingApproved(Booking $booking)
    {
        $apartment = $booking->apartment;

        $this->notificationService->sendToUser(
            $booking->user_id,
            'تم قبول حجزك ✅',
            "تم قبول حجزك للشقة {$apartment->title}",
            [
                'type' => 'booking_approved',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'action' => 'view_booking_details'
            ]
        );
    }


    public function notifyRenterBookingRejected(Booking $booking, string $reason = null)
    {
        $apartment = $booking->apartment;
        $message = "تم رفض حجزك للشقة {$apartment->title}";

        if ($reason) {
            $message .= "\nالسبب: {$reason}";
        }

        $this->notificationService->sendToUser(
            $booking->user_id,
            'تم رفض حجزك ❌',
            $message,
            [
                'type' => 'booking_rejected',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'reason' => $reason,
                'action' => 'view_other_apartments'
            ]
        );
    }


    public function notifyNewMessage(Message $message)
    {
        $sender = $message->sender;
        $receiver = $message->receiver;

        $this->notificationService->sendToUser(
            $receiver->id,
            'رسالة جديدة 💬',
            "{$sender->name}: {$this->truncateMessage($message->content)}",
            [
                'type' => 'new_message',
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'conversation_id' => $message->conversation_id,
                'action' => 'open_chat'
            ]
        );
    }


    public function notifyBookingReminder(Booking $booking)
    {
        $apartment = $booking->apartment;
        $checkIn = $booking->check_in->format('Y-m-d');

        $this->notificationService->sendToUser(
            $booking->user_id,
            'تذكير بموعد الحجز ⏰',
            "تذكير: حجزك للشقة {$apartment->title} سيبدأ غداً في {$checkIn}",
            [
                'type' => 'booking_reminder',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'check_in' => $checkIn,
                'action' => 'view_booking_details'
            ]
        );
    }


    public function notifyOwnerBookingCancelled(Booking $booking)
    {
        $apartment = $booking->apartment;
        $renter = $booking->user;

        $this->notificationService->sendToUser(
            $apartment->owner_id,
            'تم إلغاء حجز',
            "قام {$renter->name} بإلغاء حجز الشقة {$apartment->title}",
            [
                'type' => 'booking_cancelled',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'renter_id' => $renter->id,
                'action' => 'view_apartment'
            ]
        );
    }


    public function notifyOwnerNewReview($review)
    {
        $apartment = $review->apartment;
        $reviewer = $review->user;

        $this->notificationService->sendToUser(
            $apartment->owner_id,
            'مراجعة جديدة ⭐',
            "قام {$reviewer->name} بإضافة مراجعة للشقة {$apartment->title} - التقييم: {$review->rating}/5",
            [
                'type' => 'new_review',
                'review_id' => $review->id,
                'apartment_id' => $apartment->id,
                'rating' => $review->rating,
                'action' => 'view_review'
            ]
        );
    }


    public function notifyUserAccountApproved(User $user)
    {
        $this->notificationService->sendToUser(
            $user->id,
            'تم قبول حسابك ✅',
            "مرحباً {$user->first_name}! تم قبول حسابك في RentSY. يمكنك الآن استخدام جميع ميزات التطبيق",
            [
                'type' => 'account_approved',
                'action' => 'explore_app'
            ]
        );
    }


    public function notifyUserAccountRejected(User $user, string $reason = null)
    {
        $message = "نأسف لإبلاغك بأنه تم رفض حسابك في RentSY";

        if ($reason) {
            $message .= "\nالسبب: {$reason}";
        }

        $this->notificationService->sendToUser(
            $user->id,
            'تم رفض حسابك ❌',
            $message,
            [
                'type' => 'account_rejected',
                'reason' => $reason,
                'action' => 'contact_support'
            ]
        );
    }


    public function broadcastAnnouncement(string $title, string $message, array $extraData = [])
    {
        $this->notificationService->sendToAllUsers(
            $title,
            $message,
            array_merge([
                'type' => 'announcement',
                'action' => 'view_announcement'
            ], $extraData)
        );
    }


    public function notifyOwnerBookingEnding(Booking $booking)
    {
        $apartment = $booking->apartment;
        $checkOut = $booking->check_out->format('Y-m-d');

        $this->notificationService->sendToUser(
            $apartment->owner_id,
            'انتهاء حجز قريب',
            "حجز الشقة {$apartment->title} سينتهي في {$checkOut}",
            [
                'type' => 'booking_ending',
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'check_out' => $checkOut,
                'action' => 'prepare_apartment'
            ]
        );
    }


    public function notifySpecialOffer(User $user, Apartment $apartment, $discountPercentage)
    {
        $this->notificationService->sendToUser(
            $user->id,
            "عرض خاص 🎁 - خصم {$discountPercentage}%",
            "خصم خاص على الشقة {$apartment->title}. احجز الآن!",
            [
                'type' => 'special_offer',
                'apartment_id' => $apartment->id,
                'discount' => $discountPercentage,
                'action' => 'view_apartment'
            ]
        );
    }


    private function truncateMessage(string $text, int $length = 50): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }


    public function notifyAllParties(Booking $booking, string $action)
    {
        $apartment = $booking->apartment;
        $renter = $booking->user;
        $owner = $apartment->owner;

        switch ($action) {
            case 'created':
                $this->notificationService->sendToUser(
                    $renter->id,
                    'تم إنشاء حجزك',
                    "تم إنشاء حجزك للشقة {$apartment->title} بنجاح. في انتظار موافقة المالك",
                    ['type' => 'booking_created', 'booking_id' => $booking->id]
                );

                $this->notifyOwnerNewBooking($booking);
                break;

            case 'approved':
                $this->notifyRenterBookingApproved($booking);
                break;

            case 'rejected':
                $this->notifyRenterBookingRejected($booking);
                break;

            case 'cancelled':
                $this->notifyOwnerBookingCancelled($booking);
                break;
        }
    }
}
