<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;
        $data = [
            'id'                => $this->id,
            'conversationId'    => $this->conversation_id,
            'message'           => $this->message,
            'isSeenByReceiver'  => $this->is_seen_by_receiver,
            'userId'            => $user->id,
            'userName'          => $user->name,
            'userAvatar'        => uploadedAsset($user->avatar),
            'messageAt'         => date('d M, Y g:i:s A', strtotime($this->created_at)),
        ];

        return $data;
    }
}
