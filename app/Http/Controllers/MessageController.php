<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use App\Services\NotificationService;

class MessageController extends Controller
{
    use ResponseTrait;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function conversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Message::where(function ($q) use ($userId) {
            $q->where('from_user_id', $userId)
                ->orWhere('to_user_id', $userId);
        })
            ->with(['fromUser.avatar', 'toUser.avatar'])
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->from_user_id === $userId 
                    ? $message->to_user_id 
                    : $message->from_user_id;
            })
            ->map(function ($messages) {
                return $messages->first();
            })
            ->values();

        return response()->json($conversations);
    }

    public function chat(Request $request, $userId)
    {
        $messages = Message::where(function ($q) use ($request, $userId) {
            $q->where('from_user_id', $request->user()->id)
                ->where('to_user_id', $userId);
        })
            ->orWhere(function ($q) use ($request, $userId) {
                $q->where('from_user_id', $userId)
                    ->where('to_user_id', $request->user()->id);
            })
            ->with(['fromUser.avatar', 'toUser.avatar'])
            ->latest()
            ->get();

        // Mark as read
        Message::where('to_user_id', $request->user()->id)
            ->where('from_user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id|different:from_user_id',
            'body' => 'required|string|max:1000',
            'apartment_id' => 'nullable|exists:apartments,id'
        ]);

        $message = Message::create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $validated['to_user_id'],
            'body' => $validated['body'],
            'apartment_id' => $validated['apartment_id'] ?? null
        ]);

        $message->load(['fromUser.avatar', 'toUser.avatar']);

        // إرسال إشعار للمستخدم المستقبل
        $this->notificationService->sendToUser(
            $validated['to_user_id'],
            'رسالة جديدة من ' . $request->user()->name,
            $validated['body'],
            [
                'type' => 'new_message',
                'message_id' => $message->id,
                'from_user_id' => $request->user()->id,
                'apartment_id' => $validated['apartment_id'] ?? null
            ]
        );

        return $this->successResponse(
            $message,
            'تم إرسال الرسالة',
            201
        );
    }
}
