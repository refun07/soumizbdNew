<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class PageResource extends JsonResource
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
            'id'                => $this->id,
            'title'             => $this->collectTranslation('title'),
            'slug'              => $this->slug,
        ];

        $additionalData = [];
        if (Route::currentRouteName() == 'api.pages.show') {
            $additionalData = [
                'content'           => $this->collectTranslation('content'),
                'metaTile'          => $this->meta_title,
                'metaDescription'   => $this->meta_description,
                'metaKeywords'      => $this->meta_keywords,
                'metaImage'         => uploadedAsset($this->meta_image),
            ];
        }

        $data = array_merge($data, $additionalData);
        return $data;
    }
}
