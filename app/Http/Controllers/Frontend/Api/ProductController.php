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
        if ($request->brandSlug != null) {
            $brand = Brand::where('slug', $request->brandSlug)->first();
            $products = $products->where('brand_id', $brand->id);
        } else {
            // [TODO::Advance search -> if request has search keyword, split the text and find brands, take brand ids, make an array]
        }

        // Category wise product
        $category = null;
        if ($request->categorySlug != null) {
            $category = Category::with('childrenCategories')->where('slug', $request->categorySlug)->first();
            if ($category) {
                $categoryIds = array_merge([$category->id], CategoryTrait::childrenIds($category->id));
                $products = $products->with('productCategories')->whereHas('productCategories', function ($query) use ($categoryIds) {
                    return $query->whereIn('category_id', $categoryIds);
                });
                $variations = $category->variations;
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
            case 'highToLow':
                $products = $products->orderBy('min_price', 'DESC');
                break;

            case 'oldest':
                $products = $products->oldest();
                break;

            case 'newest':
                $products = $products->latest();
                break;

            default:
                $products = $products->orderBy('min_price', 'ASC');
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
                'selectedCategory' => $request->categorySlug 
                    ? ($category ? CategoryResource::make($category) : null) 
                    : null,
                'rootCategories'   => RootCategoryResource::collection(Category::isRoot()->get()),
                'filterAttributes' => VariationResource::collection($variations),
                'priceFilter'      => [
                    'minPrice' => (float) $products->min('min_price') ?? 0,
                    'maxPrice' => (float) $products->max('max_price') ?? 0
                ]
            ]
        ];
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
}
