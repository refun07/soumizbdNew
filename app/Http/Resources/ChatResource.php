<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $shop = $this->shop;
        $user = $this->user;
        $data = [
            'id'                    => $this->id,
            'shopId'                => $shop->id,
            'userId'                => $user->id,
            'shopName'              => $shop->name,
            'shopSlug'              => $shop->slug,
            'shopLogo'              => uploadedAsset($shop->logo),
            'userName'              => $user->name,
            'userAvatar'            => uploadedAsset($user->avatar),
            'lastMessageAt'         => date('d M, Y', strtotime($this->updated_at)),
            'unseenMessageCounter'  => $this->conversationMessages()->where('user_id', '!=', $user->id)->where('is_seen_by_receiver', 0)->count(),
        ];

        return $data;
    }
}
