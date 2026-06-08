<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'            => $this->id,
            'status'        => $this->status,
            'note'          => $this->note,
            'createdDate'   => date('d M, Y', strtotime($this->created_at)),
            'createdTime'   => date('h:i A', strtotime($this->created_at)),
            'actorName'     => $this->getActorName() ?? 'System',
            'actorRole'     => $this->getActorRole(),
        ];
        return $data;
    }
}
