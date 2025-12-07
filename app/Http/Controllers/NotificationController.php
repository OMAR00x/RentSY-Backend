<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;

class NotificationController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بتحديث هذا الإشعار', 403);
        }

        $notification->update(['read_at' => now()]);

        return $this->successResponse($notification, 'تم تحديث الإشعار');
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse(null, 'تم تحديث جميع الإشعارات');
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
