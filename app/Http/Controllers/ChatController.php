<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Apartment;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'apartment_id' => 'required|exists:apartments,id',
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $request->to_user_id,
            'apartment_id' => $request->apartment_id,
            'body' => $request->body,
        ]);

        $message->load('fromUser.avatar', 'toUser.avatar', 'apartment');

        // إرسال إشعار Firebase مع بيانات الرسالة
        try {
            $this->notificationService->sendToUser(
                $request->to_user_id,
                'رسالة جديدة من ' . $request->user()->name,
                $request->body,
                [
                    'type' => 'new_message',
                    'message_id' => $message->id,
                    'apartment_id' => $request->apartment_id,
                    'from_user_id' => $request->user()->id,
                    'from_user_name' => $request->user()->name,
                    'body' => $request->body,
                    'created_at' => $message->created_at->toISOString(),
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send message notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ], 201);
    }

    public function getMessages(Request $request, $apartmentId)
    {
        $userId = $request->user()->id;

        $messages = Message::where('apartment_id', $apartmentId)
            ->where(function ($query) use ($userId) {
                $query->where('from_user_id', $userId)
                    ->orWhere('to_user_id', $userId);
            })
            ->with(['fromUser.avatar', 'toUser.avatar'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function markAsRead(Request $request, $apartmentId)
    {
        $userId = $request->user()->id;

        Message::where('apartment_id', $apartmentId)
            ->where('to_user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Message::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->with(['fromUser.avatar', 'toUser.avatar', 'apartment.images'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('apartment_id')
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->from_user_id === $userId
                    ? $lastMessage->toUser
                    : $lastMessage->fromUser;

                if (!$otherUser) {
                    return null;
                }

                $unreadCount = $messages->where('to_user_id', $userId)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'apartment_id' => $lastMessage->apartment_id,
                    'apartment' => [
                        'id' => $lastMessage->apartment->id,
                        'title' => $lastMessage->apartment->title,
                        'images' => $lastMessage->apartment->images->map(fn($img) => ['url' => $img->url]),
                    ],
                    'other_user' => [
                        'id' => $otherUser->id,
                        'first_name' => $otherUser->first_name,
                        'last_name' => $otherUser->last_name,
                        'avatar' => $otherUser->avatar ? ['url' => $otherUser->avatar->url] : null,
                    ],
                    'last_message' => [
                        'id' => $lastMessage->id,
                        'body' => $lastMessage->body,
                        'created_at' => $lastMessage->created_at->toISOString(),
                        'is_mine' => $lastMessage->from_user_id === $userId,
                    ],
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter()
            ->values();

        return response()->json($conversations);
    }

    public function markAsRead(Request $request, $apartmentId)
    {
        $userId = $request->user()->id;

        Message::where('apartment_id', $apartmentId)
            ->where('to_user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markMessageAsRead(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->to_user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }
}
