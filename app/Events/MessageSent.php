<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    public int $chatId;

    /**
     * @var array<string, mixed>
     */
    public array $payload;

    public function __construct(ChatMessage $message)
    {
        $this->chatId = $message->chat_id;

        $this->payload = [
            'id' => $message->id,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'role' => $message->user->role,
            ],
            'content' => $message->content,
            'created_at' => $message->created_at?->toISOString(),
            'updated_at' => $message->updated_at?->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chats.'.$this->chatId)];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}


