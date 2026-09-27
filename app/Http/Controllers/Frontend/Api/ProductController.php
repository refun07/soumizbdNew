<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RootCategoryResource;
use App\Http\Resources\VariationResource;
use App\Http\Services\ProductService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Variation;
use App\Traits\CategoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class ProductController extends Controller
{

    # get all products with pagination
    public function index(Request $request)
    {
        $limit      = $request->limit ?? perPage();
        $variations = Variation::isActive()->get();

        $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->with('variations');

        // discounted products
        if ($request->discounted) {
            $date = strtotime(date('d-m-Y H:i:s'));
            $products = $products->where('discount_start_date', '<=', $date)->where('discount_end_date', '>=', $date);
        }

        // pre-order products
        if ($request->preOrder) {
            if (getSetting('productSettingPreOrderEnabled', '1') === '0') {
                $products = $products->whereRaw('1 = 0');
            } else {
                $now = now();
                $products = $products->whereNotNull('pre_order')
                    ->where('pre_order->is_active', true)
                    ->where(function ($query) use ($now) {
                        $query->where('pre_order->start_date', '<=', $now)
                            ->orWhereNull('pre_order->start_date');
                    })
                    ->where(function ($query) use ($now) {
                        $query->where('pre_order->end_date', '>=', $now)
                            ->orWhereNull('pre_order->end_date');
                    });
            }
        }

        // admin-selected on sale products
        if ($request->onSaleProducts) {
            $onSaleProductIds = $this->themeOneOnSaleProductIds();

            if (count($onSaleProductIds) < 1) {
                $products = $products->whereRaw('1 = 0');
            } else {
                $products = $products->whereIn('id', $onSaleProductIds);
            }
        }

        $featuredProductIds = [];

        // admin-selected featured products
        if ($request->featuredProducts) {
            $featuredProductIds = $this->themeOneFeaturedProductIds();

            if (count($featuredProductIds) < 1) {
                $products = $products->whereRaw('1 = 0');
            } else {
                $products = $products->whereIn('id', $featuredProductIds);
            }
        }

        // by search keyword
        if ($request->searchKey != null) {
            $products = $products->where('name', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        // by shop
        if ($request->shopSlug != null) {
            $products = $products->whereHas('shopInfo', function ($q) use ($request) {
                $q->where('slug', $request->shopSlug);
            });
        }

        // filter by brands
        if ($request->brandIds != null) {
            $products = $products->whereIn('brand_id', $request->brandIds);
        } else {
            // [TODO::Advance search -> if request has search keyword, split the text and find brands, take brand ids, make an array]
        }

        // Brand wise product
        $brand = null;
        $brandCategories = collect();
        if ($request->brandSlug != null) {
            $brand = Brand::isActive()->withCount('products')->where('slug', $request->brandSlug)->first();
            if ($brand) {
                $products = $products->where('brand_id', $brand->id);
                $brandCategories = $this->brandCategories($brand);
            } else {
                $products = $products->whereRaw('1 = 0');
                $brandCategories = collect();
            }
        } else {
            // [TODO::Advance search -> if request has search keyword, split the text and find brands, take brand ids, make an array]
        }

        // Category wise product
        $category = null;
        if ($request->categorySlug != null) {
            $category = Category::with('childrenCategories')->where('slug', $request->categorySlug)->first();
            if ($category) {
                $isAllowedBrandCategory = !$brand || $brandCategories->contains('id', $category->id);

                if ($isAllowedBrandCategory) {
                    $categoryIds = array_merge([$category->id], CategoryTrait::childrenIds($category->id));
                    $products = $products->with('productCategories')->whereHas('productCategories', function ($query) use ($categoryIds) {
                        return $query->whereIn('category_id', $categoryIds);
                    });
                    $variations = $category->variations;
                } else {
                    $products = $products->whereRaw('1 = 0');
                    $category = null;
                }
            } else {
                $products = $products->whereRaw('1 = 0');
            }
        } else {
            // [TODO::Advance search -> if request has search keyword, split the text and find categories, take category ids, make an array]
        }

        // by tag
        if ($request->tag) {
            $products = $products->with('tags')->whereHas('tags', function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->tag . '%');
            });
        }

        // filter by price range
        if ($request->minPrice != null) {
            $products->where('min_price', '>=', $request->minPrice);
        }
        if ($request->maxPrice != null) {
            $products->where('min_price', '<=', $request->maxPrice);
        }

        // filter by variation values
        if ($request->variationValueIds) {
            $variationValueIds = $request->variationValueIds;
            $products->with('variationCombinations')->whereHas('variationCombinations', function ($query) use ($variationValueIds) {
                return $query->whereIn('variation_value_id', $variationValueIds);
            });
        }

        // by coupon
        if ($request->couponCode != null) {
            $coupon = Coupon::where('code', $request->couponCode)->first();
            if ($coupon) {
                $products = $products->whereHas('couponProducts', function ($query) use ($coupon) {
                    return $query->where('coupon_id', $coupon->id);
                });
            }
        }

        // sortBy
        switch ($request->sortBy) {
            case 'lowToHigh':
                $products = $this->orderByDiscountedPrice($products, 'ASC');
                break;

            case 'highToLow':
                $products = $this->orderByDiscountedPrice($products, 'DESC');
                break;

            case 'oldest':
                $products = $products->oldest();
                break;

            case 'newest':
                $products = $products->latest();
                break;

            default:
                if ($request->featuredProducts && count($featuredProductIds) > 0) {
                    $ids = implode(',', $featuredProductIds);
                    $products = $products->orderByRaw("FIELD(products.id, {$ids})");
                } else {
                    $products = $products->orderBy('created_at', 'DESC');
                }
                break;
        }

        $columns    = (new ProductService)->getSpecificColumns();
        $products   = $products->paginate($limit, $columns);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'         => ProductResource::collection($products)->response()->getData(true),
                'brands'           => BrandResource::collection(Brand::isActive()->get()),
                'selectedBrand'    => $request->brandSlug
                    ? ($brand ? BrandResource::make($brand) : null)
                    : null,
                'selectedCategory' => $request->categorySlug 
                    ? ($category ? CategoryResource::make($category) : null) 
                    : null,
                'rootCategories'   => RootCategoryResource::collection(
                    $brand ? collect() : Category::isRoot()->get()
                ),
                'brandCategories'  => RootCategoryResource::collection($brandCategories),
                'filterAttributes' => VariationResource::collection($variations),
                'priceFilter'      => [
                    'minPrice' => (float) $products->min('min_price') ?? 0,
                    'maxPrice' => (float) $products->max('max_price') ?? 0
                ]
            ]
        ];
    }

    private function brandCategories(Brand $brand)
    {
        if (!Schema::hasTable('brand_categories')) {
            return collect();
        }

        return $brand->categories()
            ->orderBy('sorting_order_level', 'ASC')
            ->get();
    }

    # get product details
    public function show($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $product = Product::fromPublishedShops()->isPublished()->hideOnEcom()->where('slug', $slug)->first();
        if (!is_null($product)) {
            $data['result'] = new ProductResource($product);
        } else {
            $data['message'] = translate('Product not found');
        }

        return $data;
    }

    private function orderByDiscountedPrice($products, string $direction)
    {
        $now = strtotime(date('d-m-Y H:i:s'));
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $discountedPriceSql = "
            COALESCE(
                (
                    SELECT MIN(
                        CASE
                            WHEN active_campaigns.product_variation_id IS NOT NULL THEN
                                CASE
                                    WHEN active_campaigns.discount_type = 'percentage'
                                        THEN pv.price - ((pv.price * active_campaigns.discount_value) / 100)
                                    ELSE pv.price - active_campaigns.discount_value
                                END
                            WHEN products.discount_start_date IS NOT NULL
                                AND products.discount_end_date IS NOT NULL
                                AND products.discount_start_date <= ?
                                AND products.discount_end_date >= ? THEN
                                CASE
                                    WHEN pv.discount_type = 'percentage'
                                        THEN pv.price - ((pv.price * pv.discount_value) / 100)
                                    ELSE pv.price - pv.discount_value
                                END
                            ELSE pv.price
                        END
                    )
                    FROM product_variations pv
                    LEFT JOIN (
                        SELECT
                            cp.product_variation_id,
                            cp.discount_type,
                            cp.discount_value
                        FROM campaign_products cp
                        INNER JOIN campaigns c ON c.id = cp.campaign_id
                            AND c.deleted_at IS NULL
                            AND c.is_published = 1
                            AND c.start_date <= ?
                            AND c.end_date >= ?
                        WHERE cp.deleted_at IS NULL
                    ) active_campaigns ON active_campaigns.product_variation_id = pv.id
                    WHERE pv.product_id = products.id
                        AND pv.deleted_at IS NULL
                ),
                products.min_price
            )
        ";

        return $products
            ->orderByRaw($discountedPriceSql . ' ' . $direction, [$now, $now, $now, $now])
            ->orderBy('products.id', $direction === 'ASC' ? 'ASC' : 'DESC');
    }

    private function themeOneOnSaleProductIds(): array
    {
        $products = getSetting('themeOneOnSaleProducts');
        if ($products == null) {
            return [];
        }

        $products = json_decode($products, true);
        if (!is_array($products)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $products))));
    }

    private function themeOneFeaturedProductIds(): array
    {
        $products = getSetting('themeOneTopProducts');
        if ($products == null) {
            return [];
        }

        $products = json_decode($products, true);
        if (!is_array($products)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $products))));
    }
}
