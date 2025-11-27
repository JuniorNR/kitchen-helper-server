<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatMessageController extends Controller
{
    public function index(Request $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $isMember = $request->user()->chats()->where('chats.id', $chat->id)->exists();
        abort_unless($isMember, Response::HTTP_FORBIDDEN);

        $messages = ChatMessage::where('chat_id', $chat->id)
            ->with('user:id,name,role')
            ->orderBy('created_at')
            ->get();

        foreach ($messages as $message) {
            $message->makeHidden(['user_id', 'chat_id']);
        }

        return response()->json($messages);
    }

    public function store(Request $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $isMember = $request->user()->chats()->where('chats.id', $chat->id)->exists();
        abort_unless($isMember, Response::HTTP_FORBIDDEN);

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $request->user()->id,
            'content' => $data['content'],
        ]);

        // preload user for payload
        $message->load('user:id,name,role');
        $message->makeHidden(['chat_id', 'user_id']);

        event(new MessageSent($message));

        return response()->json($message, Response::HTTP_CREATED);
    }
}


