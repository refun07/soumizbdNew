<?php

namespace App\Http\Controllers\Backend\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatResource;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    # all chats
    public function index(Request $request)
    {
        $chats = Conversation::where('shop_id', shopId())->latest();

        // by search keyword
        if ($request->searchKey != null) {
            $chats = $chats->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like',  '%' . $request->searchKey . '%');
            });
        }

        $chats   = $chats->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ChatResource::collection($chats),
        ];
    }

    # destroy chat
    public function destroy(Request $request)
    {
        $chat = Conversation::find((int) $request->id);
        if ($chat) {
            $chat->conversationMessages()->delete();
            $chat->delete();
            return $this->index($request);
        } else {
            return response()->json(
                [
                    'success'   => false,
                    'status'    => 404,
                    'message'   => translate('Address not found'),
                    'result'    => null
                ],
                404
            );
        }
    }

    # all messages
    public function indexMessages($chatId)
    {
        $userId     = userId();
        $chat       = Conversation::where('id', $chatId)->where('shop_id', shopId())->first();
        $messages   = collect();

        if (!is_null($chat)) {
            $chat->conversationMessages()->where('user_id', '!=', $userId)->update([
                'is_seen_by_receiver'   => 1
            ]);
            $messages   = $chat->conversationMessages()->latest()->get();
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ChatMessageResource::collection($messages),
        ];
    }

    # store chat
    public function storeMessages(Request $request)
    {
        $userId  = userId();
        $message = new ConversationMessage;
        $message->conversation_id   = $request->chatId;
        $message->user_id           = $userId;
        $message->message           = $request->message;
        $message->save();
        return $this->indexMessages($request->chatId);
    }
}
