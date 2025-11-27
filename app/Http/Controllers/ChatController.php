<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = $request->user()
            ->chats()
            ->with([
                'users:id,name,role',
                'lastMessage',
                'lastMessage.user:id,name,role',
                'creator:id,name,role',
            ])
            ->withMax('messages as last_message_created_at', 'created_at')
            ->withCount('users')
            ->orderByDesc('last_message_created_at')
            ->get();

        // скрыть pivot'ы из ответа
        foreach ($chats as $chat) {
            $chat->makeHidden('pivot');
            $chat->makeHidden('created_by');
            if ($chat->relationLoaded('users')) {
                foreach ($chat->users as $user) {
                    $user->makeHidden('pivot');
                }
            }
            if ($chat->relationLoaded('lastMessage') && $chat->lastMessage) {
                $chat->lastMessage->makeHidden(['chat_id', 'user_id']);
            }
        }

        return response()->json($chats);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $chat = Chat::create([
            'name' => $data['name'],
            'created_by' => $request->user()->id,
        ]);

        $chat->users()->attach($request->user()->id);

        return response()->json($chat, Response::HTTP_CREATED);
    }

    public function join(Request $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if (!$request->user()->chats()->where('chats.id', $chat->id)->exists()) {
            $chat->users()->attach($request->user()->id);
        }

        return response()->noContent();
    }

    public function leave(Request $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $chat->users()->detach($request->user()->id);

        return response()->noContent();
    }
}



