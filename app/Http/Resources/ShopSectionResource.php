<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class ShopSectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $values        = [];
        $sectionValues = $this->section_values ? json_decode($this->section_values) : null;


        // products
        if ($this->type == "products") {
            if (!is_null($sectionValues)) {
                $productIds = $sectionValues->products;
                $products   = Product::whereIn('id', $productIds)->get();
                $values     = ProductResource::collection($products);
            }
        }

        // boxed-width-banner
        if ($this->type == "boxed-width-banner") {
            if (!is_null($sectionValues)) {
                $box1Link    = $sectionValues->box_1_link;
                $box1Banners = [];

                $banners     = $sectionValues->box_1_banners;
                if (!is_null($banners)) {
                    $banners = explode(',', $banners);
                    foreach ($banners as $image) {
                        array_push($box1Banners, uploadedAsset($image));
                    }
                }

                $box2Link    = $sectionValues->box_2_link;
                $box2Banners = [];

                $banners     = $sectionValues->box_2_banners;
                if (!is_null($banners)) {
                    $banners = explode(',', $banners);
                    foreach ($banners as $image) {
                        array_push($box2Banners, uploadedAsset($image));
                    }
                }

                $values = [
                    'box1Link'      => $box1Link,
                    'box1Banners'   => $box1Banners,
                    'box2Link'      => $box2Link,
                    'box2Banners'   => $box2Banners,
                ];
            } else {
                $values = [
                    'box1Link'      => "",
                    'box1Banners'   => [],
                    'box2Link'      => "",
                    'box2Banners'   => [],
                ];
            }
        }


        // full-width-banner
        if ($this->type == "full-width-banner") {
            if (!is_null($sectionValues)) {

                $sectionLink    = $sectionValues->link;
                $sectionBanners = [];
                $banners = $sectionValues->banners;
                if (!is_null($banners)) {
                    $banners = explode(',', $banners);
                    foreach ($banners as $image) {
                        array_push($sectionBanners, uploadedAsset($image));
                    }
                }
                $values = [
                    'link'      => $sectionLink,
                    'banners'   => $sectionBanners
                ];
            } else {
                $values = [
                    'link'      => "",
                    'banners'   => []
                ];
            }
        }


        $data =  [
            'order'     => $this->order,
            'type'      => $this->type,
            'title'     => $this->title ? translate($this->title) : null,
            'values'    => $values,
        ];

        return $data;
    }
}
