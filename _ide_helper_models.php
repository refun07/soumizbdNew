<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $city_id
 * @property int|null $zone_id
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Zone|null $zone
 * @method static \Illuminate\Database\Eloquent\Builder|Area isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Area onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area whereZoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Area withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Area withoutTrashed()
 */
	class Area extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $bg_color
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BadgeTranslation> $badgeTranslations
 * @property-read int|null $badge_translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Badge isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge withoutTrashed()
 */
	class Badge extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $badge_id
 * @property string $lang_key
 * @property string $name
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BadgeTranslation whereUpdatedBy($value)
 */
	class BadgeTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $blog_category_id
 * @property string|null $short_description
 * @property string|null $description
 * @property string|null $thumbnail_image
 * @property string|null $banner
 * @property string $video_provider youtube / vimeo / ...
 * @property string|null $video_link
 * @property int $is_active
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\BlogCategory|null $blogCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BlogTranslation> $blogTranslations
 * @property-read int|null $blog_translations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Blog isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBlogCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereVideoLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereVideoProvider($value)
 */
	class Blog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategory whereUpdatedBy($value)
 */
	class BlogCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $blog_id
 * @property int $tag_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTag whereUpdatedBy($value)
 */
	class BlogTag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $blog_id
 * @property string $lang_key
 * @property string $title
 * @property string|null $short_description
 * @property string|null $description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogTranslation whereUpdatedBy($value)
 */
	class BlogTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $thumbnail_image
 * @property int $total_sale_count
 * @property float $total_sale_amount
 * @property int $is_active
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BrandTranslation> $brandTranslations
 * @property-read int|null $brand_translations_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Brand isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand query()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereTotalSaleAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereTotalSaleCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Brand withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Brand withoutTrashed()
 */
	class Brand extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $brand_id
 * @property string $lang_key
 * @property string $name
 * @property string|null $thumbnail_image
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandTranslation whereUpdatedBy($value)
 */
	class BrandTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $shop_id
 * @property string $type private/mega
 * @property string $name
 * @property string|null $slug
 * @property string|null $short_description
 * @property string|null $thumbnail_image
 * @property string|null $banner
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $default_discount_type flat/percentage
 * @property float $default_discount_value
 * @property int $is_published
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CampaignProduct> $campaignProducts
 * @property-read int|null $campaign_products_count
 * @property-read \App\Models\Shop|null $shopInfo
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign isPublished()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign shop()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereDefaultDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereDefaultDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereUpdatedBy($value)
 */
	class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $campaign_id
 * @property int $product_id
 * @property int $product_variation_id
 * @property string $discount_type flat/percentage
 * @property float $discount_value
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Campaign|null $campaign
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CampaignProduct whereUpdatedBy($value)
 */
	class CampaignProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $guest_user_id
 * @property int|null $warehouse_id
 * @property int $product_variation_id
 * @property int $qty
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGuestUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereWarehouseId($value)
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property int $shop_id
 * @property int|null $parent_id
 * @property int $level level of the category
 * @property int $sorting_order_level
 * @property string|null $thumbnail_image
 * @property string|null $icon
 * @property int $total_sale_count
 * @property float $total_sale_amount
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CategoryTranslation> $categoryTranslations
 * @property-read int|null $category_translations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $childrenCategories
 * @property-read int|null $children_categories_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read Category|null $parentCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductCategory> $productCategories
 * @property-read int|null $product_categories_count
 * @property-read \App\Models\User|null $updatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Variation> $variations
 * @property-read int|null $variations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category isRoot()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereSortingOrderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTotalSaleAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTotalSaleCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $category_id
 * @property string $lang_key
 * @property string $name
 * @property string|null $thumbnail_image
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryTranslation whereUpdatedBy($value)
 */
	class CategoryTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $category_id
 * @property int $variation_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryVariation whereVariationId($value)
 */
	class CategoryVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $state_id
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\State|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|City isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|City withoutTrashed()
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $shop_id
 * @property int|null $subscription_of_shop_id
 * @property float $admin_commission_percentage
 * @property float $amount
 * @property float $admin_earning_amount
 * @property float $shop_earning_amount
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Shop|null $shop
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory shopCommissions()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereAdminCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereAdminEarningAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereShopEarningAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereSubscriptionOfShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionHistory whereUpdatedBy($value)
 */
	class CommissionHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConversationMessage> $conversationMessages
 * @property-read int|null $conversation_messages_count
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUserId($value)
 */
	class Conversation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property string|null $message
 * @property string|null $attachment
 * @property int $is_seen_by_receiver
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Conversation|null $conversation
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereAttachment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereIsSeenByReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConversationMessage whereUserId($value)
 */
	class ConversationMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\State> $states
 * @property-read int|null $states_count
 * @method static \Illuminate\Database\Eloquent\Builder|Country isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereUpdatedBy($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property string|null $banner
 * @property string $code
 * @property string|null $info
 * @property string $discount_type flat/percentage
 * @property float $discount_value
 * @property int $is_free_shipping
 * @property string|null $start_date
 * @property string|null $end_date
 * @property float $min_spend
 * @property float $max_discount_value
 * @property int $total_usage_limit
 * @property int $customer_usage_limit
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponCondition> $conditions
 * @property-read int|null $conditions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponCategory> $couponCategories
 * @property-read int|null $coupon_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponProduct> $couponProducts
 * @property-read int|null $coupon_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponUsage> $couponUsages
 * @property-read int|null $coupon_usages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Shop|null $shopInfo
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon shop()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCustomerUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereIsFreeShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMaxDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMinSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereTotalUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon withoutTrashed()
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $coupon_id
 * @property int $category_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCategory whereUpdatedBy($value)
 */
	class CouponCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $coupon_id
 * @property string|null $text
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponCondition whereUpdatedBy($value)
 */
	class CouponCondition extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $coupon_id
 * @property int $product_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponProduct whereUpdatedBy($value)
 */
	class CouponProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property string $coupon_code
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouponUsage whereUserId($value)
 */
	class CouponUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $flag
 * @property string $code
 * @property int $is_rtl
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Language isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedBy($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $media_file
 * @property int|null $media_size
 * @property string|null $media_type video / image / pdf / ...
 * @property string|null $media_name
 * @property string|null $media_extension
 * @property string|null $alt_text
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereMediaExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereMediaFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereMediaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereMediaSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaFile withoutTrashed()
 */
	class MediaFile extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property string $for admin/shop
 * @property string|null $type
 * @property string|null $text
 * @property string|null $link_info
 * @property int $is_read
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification notRead()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereLinkInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedBy($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_group_id
 * @property int $order_code
 * @property int $shop_id
 * @property int|null $warehouse_id
 * @property float $amount
 * @property float $tax_amount
 * @property float $shipping_charge_amount
 * @property float $discount_amount
 * @property float $coupon_discount_amount
 * @property float $total_amount
 * @property int|null $coupon_id
 * @property string $pickup_or_delivery
 * @property string $delivery_status
 * @property string $payment_status
 * @property string|null $courier_name
 * @property string|null $tracking_number
 * @property string|null $tracking_url
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\CommissionHistory|null $commissionHistory
 * @property-read \App\Models\OrderGroup|null $orderGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderUpdate> $orderUpdates
 * @property-read int|null $order_updates_count
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order isCancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isConfirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isDelivered()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isPlaced()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isProcessing()
 * @method static \Illuminate\Database\Eloquent\Builder|Order isShipped()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order shopOrders()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCourierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePickupOrDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingChargeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTrackingUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order withoutTrashed()
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $guest_user_id
 * @property int $order_code
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $alternative_phone
 * @property string|null $note
 * @property int|null $shipping_address_id
 * @property int|null $billing_address_id
 * @property string|null $shipping_address_type
 * @property string|null $shipping_address
 * @property string|null $billing_address_type
 * @property string|null $billing_address
 * @property string|null $direction
 * @property int|null $transaction_id
 * @property int $is_pos_order
 * @property string|null $pos_order_address
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\UserAddress|null $billingAddress
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\UserAddress|null $shippingAddress
 * @property-read \App\Models\Transaction|null $transaction
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereAlternativePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereBillingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereBillingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereBillingAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereGuestUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereIsPosOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereOrderCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup wherePosOrderAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereShippingAddressType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGroup withoutTrashed()
 */
	class OrderGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_variation_id
 * @property int $qty
 * @property float $unit_price
 * @property float $total_tax
 * @property float $total_discount
 * @property float $total_price
 * @property int $reward_points
 * @property int $is_refunded
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereIsRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereRewardPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereTotalDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereTotalTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderItem whereUpdatedBy($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $type default/custom
 * @property string $status
 * @property string $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderUpdate whereUserId($value)
 */
	class OrderUpdate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $type default/custom/special
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property int $is_active
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PageTranslation> $pageTranslations
 * @property-read int|null $page_translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Page whereUpdatedBy($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $page_id
 * @property string $lang_key
 * @property string $title
 * @property string|null $content
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageTranslation whereUpdatedBy($value)
 */
	class PageTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @method static \Illuminate\Database\Eloquent\Builder|PosCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosCart query()
 */
	class PosCart extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PosCart> $posCarts
 * @property-read int|null $pos_carts_count
 * @method static \Illuminate\Database\Eloquent\Builder|PosCartGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosCartGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PosCartGroup query()
 */
	class PosCartGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property string $name
 * @property string $slug
 * @property int|null $brand_id
 * @property int|null $unit_id
 * @property string|null $thumbnail_image
 * @property string|null $gallery_images
 * @property string|null $product_tags
 * @property string|null $short_description
 * @property string|null $description
 * @property float $min_price
 * @property float $max_price
 * @property string|null $discount_info
 * @property int|null $discount_start_date
 * @property int|null $discount_end_date
 * @property int $stock_qty To check alert qty only, no other usage
 * @property int $alert_qty
 * @property int $is_published
 * @property int $min_purchase_qty
 * @property int $max_purchase_qty
 * @property string|null $est_delivery_time
 * @property int $has_emi
 * @property string|null $emi_info
 * @property int $has_variation
 * @property int $has_warranty
 * @property string|null $warranty_info
 * @property float $total_sale_count
 * @property string|null $size_guides
 * @property int $reward_points
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductBadge> $badges
 * @property-read int|null $badges_count
 * @property-read \App\Models\Brand|null $brand
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CampaignProduct> $campaignProducts
 * @property-read int|null $campaign_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CouponProduct> $couponProducts
 * @property-read int|null $coupon_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coupon> $coupons
 * @property-read int|null $coupons_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Badge> $productBadges
 * @property-read int|null $product_badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductCategory> $productCategories
 * @property-read int|null $product_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $productReviews
 * @property-read int|null $product_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductTag> $productTags
 * @property-read int|null $product_tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tax> $productTaxes
 * @property-read int|null $product_taxes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductTranslation> $productTranslations
 * @property-read int|null $product_translations_count
 * @property-read \App\Models\Shop|null $shopInfo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductTax> $taxes
 * @property-read int|null $taxes_count
 * @property-read \App\Models\Unit|null $unit
 * @property-read \App\Models\User|null $updatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariationCombination> $variationCombinations
 * @property-read int|null $variation_combinations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariation> $variations
 * @property-read int|null $variations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product fromPublishedShops()
 * @method static \Illuminate\Database\Eloquent\Builder|Product isPublished()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product shop()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAlertQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscountEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscountInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscountStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEmiInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereEstDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereGalleryImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereHasEmi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereHasVariation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereHasWarranty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMaxPurchaseQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinPurchaseQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereRewardPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSizeGuides($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStockQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTotalSaleCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereWarrantyInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $badge_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBadge whereUpdatedBy($value)
 */
	class ProductBadge extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategory whereUpdatedBy($value)
 */
	class ProductCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $shop_id
 * @property int $rating
 * @property string $description
 * @property string|null $images
 * @property int $is_active
 * @property int $is_viewed_by_shop
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereIsViewedByShop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductReview whereUserId($value)
 */
	class ProductReview extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereUpdatedBy($value)
 */
	class ProductTag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $tax_id
 * @property float $tax_value
 * @property string $tax_type amount / percentage
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Tax|null $tax
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereTaxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereTaxType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereTaxValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTax whereUpdatedBy($value)
 */
	class ProductTax extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $lang_key
 * @property int $product_id
 * @property string|null $name
 * @property string|null $short_description
 * @property string|null $description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTranslation whereUpdatedBy($value)
 */
	class ProductTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $sku
 * @property string|null $image
 * @property string|null $code
 * @property float $price
 * @property float $discount_value
 * @property string $discount_type
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CampaignProduct> $campaignProducts
 * @property-read int|null $campaign_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariationCombination> $combinations
 * @property-read int|null $combinations_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductVariationStock> $productVariationStocks
 * @property-read int|null $product_variation_stocks_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariation withoutTrashed()
 */
	class ProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $product_variation_id
 * @property int $variation_id
 * @property int $variation_value_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @property-read \App\Models\Variation|null $variation
 * @property-read \App\Models\VariationValue|null $variationValue
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination whereVariationValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationCombination withoutTrashed()
 */
	class ProductVariationCombination extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_variation_id
 * @property int|null $warehouse_id
 * @property int $stock_qty
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereStockQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariationStock withoutTrashed()
 */
	class ProductVariationStock extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $warehouse_id
 * @property int $supplier_id
 * @property string|null $reference_code
 * @property string|null $date
 * @property string $status pending/ordered/received/cancelled
 * @property float $sub_total
 * @property float $tax_percentage
 * @property float $tax_value
 * @property float $shipping
 * @property float $discount
 * @property float $grand_total
 * @property float $paid
 * @property float $due
 * @property string $payment_status paid/unpaid
 * @property string|null $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrderProductVariation> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrderPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\PurchaseReturnOrder|null $return
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder shop()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereReferenceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereTaxPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereTaxValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrder withoutTrashed()
 */
	class PurchaseOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
 * @property string|null $date
 * @property string $payment_method
 * @property float $paid_amount
 * @property float $return_amount
 * @property string|null $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $payable
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereReturnAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderPayment whereUpdatedBy($value)
 */
	class PurchaseOrderPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $purchase_order_id
 * @property int $product_variation_id
 * @property float $base_unit_price
 * @property float $unit_price
 * @property int $qty
 * @property float $sub_total
 * @property float $discount
 * @property float $tax
 * @property float $grand_total
 * @property int $prev_stock
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @property-read \App\Models\PurchaseOrder|null $purchaseOrder
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereBaseUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation wherePrevStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderProductVariation withoutTrashed()
 */
	class PurchaseOrderProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $purchase_order_id
 * @property int $shop_id
 * @property int $warehouse_id
 * @property int $supplier_id
 * @property string|null $reference_code
 * @property string|null $date
 * @property string $status pending/completed
 * @property float $sub_total
 * @property float $tax_percentage
 * @property float $tax_value
 * @property float $shipping
 * @property float $discount
 * @property float $grand_total
 * @property float $paid
 * @property float $due
 * @property string $payment_status paid/unpaid
 * @property string|null $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseReturnOrderProductVariation> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseOrderPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\PurchaseOrder|null $purchaseOrder
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder shop()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereReferenceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereShipping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereTaxPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereTaxValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrder whereWarehouseId($value)
 */
	class PurchaseReturnOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $purchase_return_order_id
 * @property int $product_id
 * @property int $purchase_order_id
 * @property int $product_variation_id
 * @property float $base_unit_price
 * @property float $unit_price
 * @property int $qty
 * @property int $returned_qty
 * @property float $sub_total
 * @property float $discount
 * @property float $tax
 * @property float $grand_total
 * @property int $prev_stock
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @property-read \App\Models\PurchaseOrder|null $purchaseOrder
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereBaseUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereGrandTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation wherePrevStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation wherePurchaseOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation wherePurchaseReturnOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereReturnedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereSubTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseReturnOrderProductVariation whereUpdatedBy($value)
 */
	class PurchaseReturnOrderProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role withoutPermission($permissions)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $is_approved
 * @property int $is_verified_by_admin
 * @property int $is_published
 * @property string|null $logo
 * @property string|null $banner
 * @property string|null $tagline
 * @property string $name
 * @property string $slug
 * @property string|null $info
 * @property float $rating
 * @property string|null $address
 * @property float $min_order_amount
 * @property float $admin_commission_percentage
 * @property float $current_balance
 * @property float $default_shipping_charge
 * @property string $manage_stock_by default/inventory
 * @property float $monthly_goal_amount
 * @property int $is_cash_payout
 * @property int $is_bank_payout
 * @property string|null $bank_name
 * @property string|null $bank_acc_name
 * @property string|null $bank_acc_no
 * @property string|null $bank_routing_no
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $meta_image
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommissionHistory> $commissionHistories
 * @property-read int|null $commission_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopImpression> $impressions
 * @property-read int|null $impressions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $productReviews
 * @property-read int|null $product_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopPayment> $shopPayments
 * @property-read int|null $shop_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShopSection> $shopSections
 * @property-read int|null $shop_sections_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Warehouse> $warehouses
 * @property-read int|null $warehouses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Shop isApproved()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop isPublished()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAdminCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBankAccName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBankAccNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBankRoutingNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDefaultShippingCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsBankPayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsCashPayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereIsVerifiedByAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereManageStockBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMinOrderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMonthlyGoalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUserId($value)
 */
	class Shop extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property string $impression positive/negative/neutral
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereImpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopImpression whereUserId($value)
 */
	class ShopImpression extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property float $demanded_amount
 * @property string|null $additional_info
 * @property float $given_amount
 * @property string|null $document_of_proof
 * @property string|null $payment_method
 * @property string|null $payment_details
 * @property string $status requested/paid/cancelled
 * @property string|null $reason_for_cancellation
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Shop|null $shopInfo
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment notPaid()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment paid()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment requested()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment shop()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereAdditionalInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereDemandedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereDocumentOfProof($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereGivenAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereReasonForCancellation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPayment whereUpdatedBy($value)
 */
	class ShopPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $order
 * @property string $type full-width-banner/box-width-banner/products
 * @property string|null $title
 * @property string|null $section_values
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection shop()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereSectionValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopSection whereUpdatedBy($value)
 */
	class ShopSection extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $cities
 * @property-read int|null $cities_count
 * @property-read \App\Models\Country|null $country
 * @method static \Illuminate\Database\Eloquent\Builder|State isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|State onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|State query()
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|State withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|State withoutTrashed()
 */
	class State extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property int $product_variation_id
 * @property int $qty
 * @property int|null $expiry_date
 * @property float $purchase_cost
 * @property float $selling_price
 * @property string|null $document
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock wherePurchaseCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereWarehouseId($value)
 */
	class Stock extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $warehouse_id
 * @property string|null $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockAdjustmentProductVariation> $productVariations
 * @property-read int|null $product_variations_count
 * @property-read \App\Models\Warehouse|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment shop()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustment whereWarehouseId($value)
 */
	class StockAdjustment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $stock_adjustment_id
 * @property int $product_id
 * @property int $product_variation_id
 * @property int $qty
 * @property string $action addition/deduction
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @property-read \App\Models\StockAdjustment|null $stockAdjustment
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereStockAdjustmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockAdjustmentProductVariation whereUpdatedBy($value)
 */
	class StockAdjustmentProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $from_warehouse_id
 * @property int $to_warehouse_id
 * @property string|null $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Warehouse|null $fromWarehouse
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockTransferProductVariation> $productVariations
 * @property-read int|null $product_variations_count
 * @property-read \App\Models\Warehouse|null $toWarehouse
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer shop()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereFromWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereToWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransfer whereUpdatedBy($value)
 */
	class StockTransfer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $stock_transfer_id
 * @property int $product_id
 * @property int $product_variation_id
 * @property int $qty
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\ProductVariation|null $productVariation
 * @property-read \App\Models\StockTransfer|null $stockTransfer
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereProductVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereStockTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockTransferProductVariation whereUpdatedBy($value)
 */
	class StockTransferProductVariation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $email
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscriber whereUpdatedBy($value)
 */
	class Subscriber extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $transaction_id
 * @property float $admin_commission_percentage
 * @property int $expiry_date
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereAdminCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionOfShop whereUpdatedBy($value)
 */
	class SubscriptionOfShop extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property float $admin_commission_percentage
 * @property int $has_cat_wise_commission
 * @property int $validity_in_days
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereAdminCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereHasCatWiseCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlan whereValidityInDays($value)
 */
	class SubscriptionPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $subscription_plan_id
 * @property int $category_id
 * @property float $admin_commission_percentage
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereAdminCommissionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPlanCategory whereUpdatedBy($value)
 */
	class SubscriptionPlanCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone_no
 * @property string|null $address
 * @property float $balance
 * @property string|null $payment_details
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier shop()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier wherePhoneNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Supplier withoutTrashed()
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $lang_key
 * @property string $type
 * @property string|null $value
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemSetting withoutTrashed()
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedBy($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Tax isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax withoutTrashed()
 */
	class Tax extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $for
 * @property float $amount
 * @property float $tax_amount
 * @property float $shipping_charge_amount
 * @property float $discount_amount
 * @property float $coupon_discount_amount
 * @property float $total_amount
 * @property string $status paid/unpaid/partial/failed/cancelled
 * @property string|null $payment_method
 * @property string|null $payment_details
 * @property int $is_manual_payment
 * @property string|null $manual_payment_details
 * @property string|null $manual_payment_document
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCouponDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereIsManualPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereManualPaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereManualPaymentDocument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereShippingChargeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedBy($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $lang_key
 * @property string $t_key
 * @property string $t_value
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereTKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereTValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereUpdatedBy($value)
 */
	class Translation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UnitTranslation> $unitTranslations
 * @property-read int|null $unit_translations_count
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Unit isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Unit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Unit withoutTrashed()
 */
	class Unit extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $unit_id
 * @property string $lang_key
 * @property string $name
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UnitTranslation withoutTrashed()
 */
	class UnitTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $role_id
 * @property string $user_type
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property int $email_or_otp_verified
 * @property string|null $verification_code
 * @property string|null $new_email_verification_code
 * @property mixed|null $password
 * @property string|null $remember_token
 * @property string|null $provider_id
 * @property string|null $avatar
 * @property string|null $postal_code
 * @property float $user_balance
 * @property int $is_banned
 * @property int|null $shop_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAddress> $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cart> $allCarts
 * @property-read int|null $all_carts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderGroup> $orderGroups
 * @property-read int|null $order_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $productReviews
 * @property-read int|null $product_reviews_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Shop|null $shop
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static \Illuminate\Database\Eloquent\Builder|User customers()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User sellers()
 * @method static \Illuminate\Database\Eloquent\Builder|User staffs()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailOrOtpVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNewEmailVerificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerificationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $country_id
 * @property int $state_id
 * @property int $city_id
 * @property int $area_id
 * @property string|null $postal_code
 * @property string $address
 * @property string|null $direction
 * @property string|null $type home/office
 * @property int $is_default only one can be default
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Area|null $area
 * @property-read \App\Models\City|null $city
 * @property-read \App\Models\Country|null $country
 * @property-read \App\Models\State|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserAddress withoutTrashed()
 */
	class UserAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cart> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VariationTranslation> $variationTranslations
 * @property-read int|null $variation_translations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VariationValue> $variationValues
 * @property-read int|null $variation_values_count
 * @method static \Illuminate\Database\Eloquent\Builder|Variation isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Variation withoutTrashed()
 */
	class Variation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $variation_id
 * @property string $name
 * @property string $lang_key
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationTranslation whereVariationId($value)
 */
	class VariationTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $variation_id
 * @property string $name
 * @property string|null $color_code only for colors
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Variation|null $variation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VariationValueTranslation> $variationValueTranslations
 * @property-read int|null $variation_value_translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue whereVariationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValue withoutTrashed()
 */
	class VariationValue extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $variation_value_id
 * @property string $name
 * @property string $lang_key
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereLangKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VariationValueTranslation whereVariationValueId($value)
 */
	class VariationValueTranslation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property string $name
 * @property string|null $address
 * @property string|null $description
 * @property string|null $thumbnail_image
 * @property int $is_active
 * @property int $is_default
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WarehouseZone> $warehouseZones
 * @property-read int|null $warehouse_zones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Zone> $zones
 * @property-read int|null $zones_count
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse shop()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereThumbnailImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Warehouse withoutTrashed()
 */
	class Warehouse extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $warehouse_id
 * @property int $zone_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Warehouse> $warehouses
 * @property-read int|null $warehouses_count
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone query()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone shop()
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WarehouseZone whereZoneId($value)
 */
	class WarehouseZone extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUserId($value)
 */
	class Wishlist extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $banner
 * @property int $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|Zone isActive()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereBanner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Zone withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Zone withoutTrashed()
 */
	class Zone extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $zone_id
 * @property float $charge
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge query()
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ZoneShippingCharge whereZoneId($value)
 */
	class ZoneShippingCharge extends \Eloquent {}
}

