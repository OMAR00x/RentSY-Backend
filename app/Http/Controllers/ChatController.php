<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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

        $message->load('fromUser', 'apartment');
        broadcast(new MessageSent($message))->toOthers();

        // إرسال إشعار Firebase
        $this->notificationService->sendToUser(
            $request->to_user_id,
            'رسالة جديدة',
            $request->user()->name . ': ' . $request->body,
            [
                'type' => 'new_message',
                'apartment_id' => $request->apartment_id,
                'from_user_id' => $request->user()->id,
            ]
        );

        return response()->json($message, 201);
    }

    public function getMessages(Request $request, $apartmentId)
    {
        $userId = $request->user()->id;

        $messages = Message::where('apartment_id', $apartmentId)
            ->where(function ($query) use ($userId) {
                $query->where('from_user_id', $userId)
                    ->orWhere('to_user_id', $userId);
            })
            ->with(['fromUser', 'toUser'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Message::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->with(['fromUser', 'toUser', 'apartment'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('apartment_id')
            ->map(function ($messages) use ($userId) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->from_user_id === $userId
                    ? $lastMessage->toUser
                    : $lastMessage->fromUser;

                return [
                    'apartment' => $lastMessage->apartment,
                    'other_user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $messages->where('to_user_id', $userId)
                        ->whereNull('read_at')
                        ->count(),
                ];
            })
            ->values();

        return response()->json($conversations);
    }

    public function markAsRead(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->to_user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }
}
