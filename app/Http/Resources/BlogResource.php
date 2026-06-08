<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'                => (int) $this->id,
            'title'             => $this->collectTranslation('title'),
            'userName'          => $this->name,
            'slug'              => $this->slug,
            'thumbnailImage'    => uploadedAsset($this->thumbnail_image),
            'blogCategoryId'    => (int) $this->blog_category_id,
            'blogCategoryName'  => $this->blogCategory?->collectTranslation('name') ?? "",
            'isPublished'       => (int) $this->is_published,
            'createdAt'         => $this->created_at ? date('d M, Y', strtotime($this->created_at)) : null,
        ];

        $additionalData = [];

        if (Route::currentRouteName() == 'api.blogs.show') { // todo:: && frontend listing route
            $additionalData = [
                'shortDescription'  => $this->collectTranslation('short_description'),
                'description'       => $this->collectTranslation('description'),
                'banner'            => uploadedAsset($this->banner),
                'metaTitle'         => $this->meta_title,
                'metaKeywords'      => $this->meta_keywords,
                'metaDescription'   => $this->meta_description,
                'metaImage'         => $this->meta_image,
                'metaImageUrl'      => uploadedAsset($this->meta_image),
            ];
        }

        $data = array_merge($data, $additionalData);
        return $data;
    }
}
