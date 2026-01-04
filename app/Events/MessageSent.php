<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.' . $this->message->apartment_id);
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'from_user_id' => $this->message->from_user_id,
            'to_user_id' => $this->message->to_user_id,
            'apartment_id' => $this->message->apartment_id,
            'body' => $this->message->body,
            'created_at' => $this->message->created_at,
            'from_user' => [
                'id' => $this->message->fromUser->id,
                'name' => $this->message->fromUser->name,
            ],
        ];
    }
}
