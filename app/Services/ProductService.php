<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Brand;
use App\Models\CampaignProduct;
use App\Models\Category;
use App\Models\Language;
use App\Models\Notification;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBadge;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\ProductTag;
use App\Models\ProductTax;
use App\Models\ProductTranslation;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\ProductVariationStock;
use App\Models\Tag;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValue;
use App\Models\Warehouse;
use App\Models\ShippingClass;
use Str;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class ProductService
{
    private const DISCOUNT_ACTIVE_START_TIMESTAMP = 1;
    private const DISCOUNT_ACTIVE_END_TIMESTAMP = 2147483647;

    private static function toBoolean($value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'published', 'active'], true);
    }

    private static function productSettingEnabled(string $key): bool
    {
        return getSetting($key, '1') !== '0';
    }

    private static function syncDiscountEnabled(Product $product, bool $isEnabled): void
    {
        if (!$isEnabled) {
            $product->discount_start_date = null;
            $product->discount_end_date   = null;
            return;
        }

        $product->discount_start_date = self::DISCOUNT_ACTIVE_START_TIMESTAMP;
        $product->discount_end_date   = self::DISCOUNT_ACTIVE_END_TIMESTAMP;
    }

    private static function requestHasDiscountValue($request): bool
    {
        if ($request->has('is_variant') && $request->has('variations')) {
            foreach ($request->variations as $variation) {
                if (self::normalizeDiscountValue($variation['discount_value'] ?? 0) > 0) {
                    return true;
                }
            }

            return false;
        }

        return self::normalizeDiscountValue($request->discount_value ?? 0) > 0;
    }

    private static function normalizeDiscountValue($value): float
    {
        return is_numeric($value) ? max(0, (float) $value) : 0;
    }

    private static function normalizeDiscountType($type): string
    {
        return $type === 'percentage' ? 'percentage' : 'amount';
    }

    private static function resolveMinPurchaseQtyValue($value = null): int
    {
        if (!self::productSettingEnabled('productSettingMinPurchaseQtyEnabled')) {
            return 1;
        }

        return is_numeric($value) ? max(1, (int) $value) : 1;
    }

    private static function resolveMaxPurchaseQtyValue($value = null): int
    {
        if (!self::productSettingEnabled('productSettingMaxPurchaseQtyEnabled')) {
            return 100;
        }

        return is_numeric($value) ? max(1, (int) $value) : 100;
    }

    private static function resolveMinPurchaseQty($request): int
    {
        return self::resolveMinPurchaseQtyValue($request->input('min_purchase_qty'));
    }

    private static function resolveMaxPurchaseQty($request): int
    {
        return self::resolveMaxPurchaseQtyValue($request->input('max_purchase_qty'));
    }

    private static function resolveVideoLink($request): ?string
    {
        if (!self::productSettingEnabled('productSettingVideoLinkEnabled')) {
            return null;
        }

        return $request->filled('video_link') ? $request->input('video_link') : null;
    }

    private static function resolveAlertQty($request): int
    {
        if (!self::productSettingEnabled('productSettingAlertQtyEnabled')) {
            return 0;
        }

        return is_numeric($request->input('alert_qty')) ? max(0, (int) $request->input('alert_qty')) : 0;
    }

    private static function resolveEstimatedDeliveryTime($request): ?string
    {
        if (!self::productSettingEnabled('productSettingEstimatedDeliveryTimeEnabled')) {
            return null;
        }

        return $request->filled('est_delivery_time') ? $request->input('est_delivery_time') : null;
    }

    private static function resolveHasEmi($request): int
    {
        if (!self::productSettingEnabled('productSettingEmiEnabled')) {
            return 0;
        }

        return $request->has('has_emi') ? 1 : 0;
    }

    private static function resolveEmiInfo($request): ?string
    {
        if (!self::productSettingEnabled('productSettingEmiEnabled')) {
            return null;
        }

        return $request->filled('emi_info') ? $request->input('emi_info') : null;
    }

    private static function resolveHasWarranty($request): int
    {
        if (!self::productSettingEnabled('productSettingWarrantyEnabled')) {
            return 0;
        }

        return $request->has('has_warranty') ? 1 : 0;
    }

    private static function resolveWarrantyInfo($request): ?string
    {
        if (!self::productSettingEnabled('productSettingWarrantyEnabled')) {
            return null;
        }

        return $request->filled('warranty_info') ? $request->input('warranty_info') : null;
    }

    private static function syncProductTaxes(Product $product, $request): void
    {
        if (!self::productSettingEnabled('productSettingTaxesEnabled') || !$request->has('tax_values')) {
            $product->productTaxes()->sync([]);
            return;
        }

        $taxes = [];
        foreach ($request->input('tax_values', []) as $key => $taxValue) {
            $taxId = $request->input("tax_ids.{$key}");
            if (!$taxId) {
                continue;
            }

            $taxes[$taxId] = [
                'tax_value' => is_numeric($taxValue) ? $taxValue : 0,
                'tax_type'  => $request->input("tax_types.{$key}", 'amount'),
            ];
        }

        $product->productTaxes()->sync($taxes);
    }

    private static function splitCsvValues($value): array
    {
        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[|,]/', (string) $value))));
    }

    private static function resolveModelId(string $modelClass, $value, string $nameColumn = 'name'): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return $modelClass::where('id', (int) $value)->value('id');
        }

        return $modelClass::where($nameColumn, $value)->value('id');
    }

    private static function resolveModelIds(string $modelClass, $value, string $nameColumn = 'name'): array
    {
        $ids = [];

        foreach (self::splitCsvValues($value) as $item) {
            $id = self::resolveModelId($modelClass, $item, $nameColumn);
            if ($id) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private static function makeUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name, '-');
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'product';
        $slug = $baseSlug;
        $counter = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private static function normalizeSizeChartState($sizeChartState): ?array
    {
        if (blank($sizeChartState)) {
            return null;
        }

        if (is_string($sizeChartState)) {
            $sizeChartState = json_decode($sizeChartState, true);
        }

        if (!is_array($sizeChartState)) {
            return null;
        }

        $rawColumns = is_array($sizeChartState['columns'] ?? null) ? array_values($sizeChartState['columns']) : [];
        $rawRows = is_array($sizeChartState['rows'] ?? null) ? array_values($sizeChartState['rows']) : [];

        $columnCount = count($rawColumns);
        foreach ($rawRows as $row) {
            if (is_array($row)) {
                $columnCount = max($columnCount, count($row));
            }
        }

        if ($columnCount === 0) {
            return null;
        }

        $hasContent = false;
        $columns = [];
        for ($index = 0; $index < $columnCount; $index++) {
            $label = trim((string) ($rawColumns[$index] ?? ''));
            if ($label !== '') {
                $hasContent = true;
            }

            $columns[] = $label !== '' ? $label : 'Column ' . ($index + 1);
        }

        $rows = [];
        foreach ($rawRows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalizedRow = [];
            $rowHasContent = false;

            for ($index = 0; $index < $columnCount; $index++) {
                $value = trim((string) ($row[$index] ?? ''));
                if ($value !== '') {
                    $rowHasContent = true;
                    $hasContent = true;
                }

                $normalizedRow[] = $value;
            }

            if ($rowHasContent) {
                $rows[] = $normalizedRow;
            }
        }

        if (!$hasContent) {
            return null;
        }

        return [
            'columns' => $columns,
            'rows' => $rows,
        ];
    }

    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey     = null;
        $isPublished   = null;
        $limit         = $request->limit ?? perPage();
        $warehouseIds  = Warehouse::pluck('id')->toArray();

        $products = Product::shop()
            ->with([
                'categories',
                'brand',
                'unit',
                'variations' => fn($query) => $query->withSum('productVariationStocks as stock_total', 'stock_qty'),
            ])
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');

        if ($request->isPublished != null) {
            $products = $products->where('is_published', $request->isPublished);
            $isPublished    = $request->isPublished;
        }

        if ($request->isPreOrder != null && $request->isPreOrder == 1) {
            $products = $products->whereNotNull('pre_order');
        }

        if ($request->categoryId != null) {
            $products = $products->with('productCategories')->whereHas('productCategories', function ($query) use ($request) {
                return $query->where('category_id', $request->categoryId);
            });
        }

        if ($request->tagId != null) {
            $products = $products->with('productTags')->whereHas('productTags', function ($query) use ($request) {
                return $query->where('tag_id', $request->tagId);
            });
        }

        if ($request->brandId != null) {
            $products = $products->where('brand_id', $request->brandId);
        }

        if ($request->hideOnEcom != null) {
            if ($request->hideOnEcom === 'showing') {
                $products = $products->where('hide_on_ecom', false);
            } elseif ($request->hideOnEcom === 'hidden') {
                $products = $products->where('hide_on_ecom', true);
            }
        }

        $inStockVariationIds = null;
        if ($request->stockStatus != null) {
            $inStockVariationIds = ProductVariationStock::whereIn('warehouse_id', $warehouseIds)
                ->selectRaw('product_variation_id')
                ->groupBy('product_variation_id')
                ->havingRaw('SUM(stock_qty) > 0')
                ->pluck('product_variation_id')
                ->toArray();

            if ($request->stockStatus === 'in_stock') {
                $products = $products->whereHas('variations', function ($query) use ($inStockVariationIds) {
                    $query->whereIn('id', $inStockVariationIds);
                });
            } elseif ($request->stockStatus === 'out_of_stock') {
                $products = $products->whereDoesntHave('variations', function ($query) use ($inStockVariationIds) {
                    $query->whereIn('id', $inStockVariationIds);
                });
            }
        }

        if ($request->search != null) {
            $searchKey  = $request->search;

            $directMatchIds = Product::shop()
                ->where('name', 'like', '%' . $request->search . '%')
                ->when($request->isPublished != null, function ($q) use ($request) {
                    return $q->where('is_published', $request->isPublished);
                })
                ->when($request->isPreOrder != null && $request->isPreOrder == 1, function ($q) {
                    return $q->whereNotNull('pre_order');
                })
                ->when($request->hideOnEcom === 'showing', function ($q) {
                    return $q->where('hide_on_ecom', false);
                })
                ->when($request->hideOnEcom === 'hidden', function ($q) {
                    return $q->where('hide_on_ecom', true);
                })
                ->when($request->stockStatus === 'in_stock', function ($q) use ($inStockVariationIds) {
                    $q->whereHas('variations', function ($query) use ($inStockVariationIds) {
                        $query->whereIn('id', $inStockVariationIds);
                    });
                })
                ->when($request->stockStatus === 'out_of_stock', function ($q) use ($inStockVariationIds) {
                    $q->whereDoesntHave('variations', function ($query) use ($inStockVariationIds) {
                        $query->whereIn('id', $inStockVariationIds);
                    });
                })
                ->pluck('id')
                ->toArray();

            if (!empty($directMatchIds)) {
                $products = $products->distinct()->whereIn('id', $directMatchIds);

                $order = implode(',', $directMatchIds);
                $products = $products->orderByRaw("FIELD(id, {$order})");
            } else {
                $translationMatchIds = Product::shop()
                    ->whereHas('productTranslations', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->when($request->isPublished != null, function ($q) use ($request) {
                        return $q->where('is_published', $request->isPublished);
                    })
                    ->when($request->isPreOrder != null && $request->isPreOrder == 1, function ($q) {
                        return $q->whereNotNull('pre_order');
                    })
                    ->when($request->hideOnEcom === 'showing', function ($q) {
                        return $q->where('hide_on_ecom', false);
                    })
                    ->when($request->hideOnEcom === 'hidden', function ($q) {
                        return $q->where('hide_on_ecom', true);
                    })
                    ->when($request->stockStatus === 'in_stock', function ($q) use ($inStockVariationIds) {
                        $q->whereHas('variations', function ($query) use ($inStockVariationIds) {
                            $query->whereIn('id', $inStockVariationIds);
                        });
                    })
                    ->when($request->stockStatus === 'out_of_stock', function ($q) use ($inStockVariationIds) {
                        $q->whereDoesntHave('variations', function ($query) use ($inStockVariationIds) {
                            $query->whereIn('id', $inStockVariationIds);
                        });
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($translationMatchIds)) {
                    $products = $products->distinct()->whereIn('id', $translationMatchIds);

                    $order = implode(',', $translationMatchIds);
                    $products = $products->orderByRaw("FIELD(id, {$order})");
                } else {
                    $products = $products->whereNull('id');
                }
            }
        }

        $products   = $products->paginate($limit);

        $categories = Category::where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->with('childrenCategories')
            ->get();

        $brands   = Brand::isActive()->orderBy('name', 'ASC')->get();
        $tags     = Tag::orderBy('name', 'ASC')->get();


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'     => $products,
                'isPublished'  => $isPublished,
                'categories'   => $categories,
                'brands'       => $brands,
                'tags'         => $tags,
                'searchKey'    => $searchKey,
            ],
        ];

        return $data;
    }

    # return view of create form
    public static function create()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->with('childrenCategories')
            ->get();

        $brands              = Brand::isActive()->orderBy('name', 'ASC')->get();
        $units               = Unit::isActive()->orderBy('name', 'ASC')->get();
        $variations          = Variation::isActive()->get();
        $taxes               = Tax::isActive()->orderBy('name', 'ASC')->get();
        $badges              = Badge::isActive()->orderBy('name', 'ASC')->get();
        $tags                = Tag::orderBy('name', 'ASC')->get();
        $shippingClasses     = ShippingClass::shop()->isActive()
            ->select('id', 'shop_id', 'name')
            ->with([
                'shippingCharges' => fn($q) =>
                $q->select('id', 'shipping_class_id', 'zone_id', 'charge'),

                'shippingCharges.zone:id,name'
            ])
            ->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'categories'    => $categories,
                'brands'        => $brands,
                'units'         => $units,
                'variations'    => $variations,
                'taxes'         => $taxes,
                'badges'        => $badges,
                'tags'          => $tags,
                'shippingClasses' => $shippingClasses
            ],
        ];

        return $data;
    }

    # generate variation combinations
    public static function generateVariationCombinations($request)
    {
        $variations_and_values = array();

        if ($request->has('chosen_variations')) {
            $chosen_variations = $request->chosen_variations;
            sort($chosen_variations, SORT_NUMERIC);

            foreach ($chosen_variations as $key => $option) {

                $option_name = 'option_' . $option . '_choices'; # $option = variation_id
                $value_ids = array();

                if ($request->has($option_name)) {

                    $variation_option_values = $request[$option_name];
                    sort($variation_option_values, SORT_NUMERIC);

                    foreach ($variation_option_values as $item) {
                        array_push($value_ids, $item);
                    }
                    $variations_and_values[$option] =  $value_ids;
                }
            }
        }

        $combinations = array(array());
        foreach ($variations_and_values as $variation => $variation_values) {
            $tempArray = array();
            foreach ($combinations as $combination_item) {
                foreach ($variation_values as $variation_value) {
                    $tempArray[] = $combination_item + array($variation => $variation_value);
                }
            }
            $combinations = $tempArray;
        }

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'combinations' => $combinations
            ],
        ];

        return $data;
    }

    # get variation values to add new product
    public static function getVariationValues($request)
    {
        $variation_id = $request->variation_id;
        $variation = Variation::find($variation_id);
        $variation_values = VariationValue::isActive()->where('variation_id', $variation_id)->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'variation'           => $variation,
                'variation_values'    => $variation_values,
                'variation_id'        => $variation_id
            ],
        ];
        return $data;
    }

    # new chosen variation
    public static function getNewVariation($request)
    {
        $variations = Variation::isActive();

        foreach ($request->chosen_variations as $key => $value) {
            if ($value == NULL) {
                $variations = $variations->get();
                $data = [
                    'status'        => 200,
                    'message'       => '',
                    'result'    => [
                        'variations'    => $variations,
                        'count'         => -1
                    ],
                ];

                return $data;
            }
        }

        $variations = $variations->whereNotIn('id', $request->chosen_variations)->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'variations'    => $variations,
                'count'         => count($variations),
                'chosenCounter' => count($request->chosen_variations) + 1
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        if ($request->has('is_variant') && !$request->has('variations')) {
            $data = [
                'status'    => 403,
                'message'   => translate('Invalid product variations, please check again'),
                'result'    => [],
            ];
            return $data;
        }

        try {
            $product                    = new Product;
            $product->shop_id           = shop()->id;
            $product->name              = $request->name;
            $product->slug              = Str::slug($request->name, '-');
            $product->brand_id          = $request->brand_id;
            $product->unit_id           = $request->unit_id;

            $product->thumbnail_image   = $request->thumbnail_image;
            $product->gallery_images    = $request->gallery_images;
            $product->video_link        = self::resolveVideoLink($request);
            $hasSizeChart               = self::productSettingEnabled('productSettingSizeChartEnabled') && $request->has('isSizeChart');
            $product->isSizeChart       = $hasSizeChart ? 1 : 0;
            $product->size_guides       = $hasSizeChart ? self::normalizeSizeChartState($request->input('size_chart_state')) : null;
            $product->chart_image       = $hasSizeChart && $request->filled('chart_image') ? $request->chart_image : null;
            if (self::productSettingEnabled('productSettingQrCodeEnabled')) {
                $product->show_recipe_details = $request->has('show_recipe_details') ? 1 : 0;
                $product->recipe_details    = $request->recipe_details;
                $product->recipe_image      = $request->has('show_recipe_details') && $request->filled('recipe_image') ? $request->recipe_image : null;
            }

            $product->description       = $request->description;

            # min-max price
            if ($request->has('is_variant') && $request->has('variations')) {
                $product->min_price =  min(array_column($request->variations, 'price'));
                $product->max_price =  max(array_column($request->variations, 'price'));
            } else {
                $product->min_price =  $request->price;
                $product->max_price =  $request->price;
            }

            # discount
            self::syncDiscountEnabled(
                $product,
                $request->boolean('discount_schedule_enabled') || self::requestHasDiscountValue($request)
            );
            // $product->discount_info   = $request->discount_info;

            # stock qty based on all variations / no variation 
            if (useInventory()) {
                $product->stock_qty = 0;
            } else {
                $product->stock_qty   = ($request->has('is_variant') && $request->has('variations')) ? array_sum(array_column($request->variations, 'stock_qty')) : $request->stock_qty;
            }
            $product->alert_qty   = self::resolveAlertQty($request);


            $product->is_published         = $request->is_published ?  $request->is_published : 0;
            $product->hide_on_ecom         = $request->has('hide_on_ecom') ? 1 : 0;
            $product->min_purchase_qty     = self::resolveMinPurchaseQty($request);
            $product->max_purchase_qty     = self::resolveMaxPurchaseQty($request);
            $product->est_delivery_time    = self::resolveEstimatedDeliveryTime($request);
            $product->emi_info             = self::resolveEmiInfo($request);

            $product->has_variation        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

            $product->has_warranty         = self::resolveHasWarranty($request);
            $product->has_emi              = self::resolveHasEmi($request);
            $product->warranty_info        = self::resolveWarrantyInfo($request);

            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_image        = $request->meta_image;
            $product->shipping_class_id = $request->filled('shipping_class_id') ? $request->shipping_class_id : null;

            $product->save();

            # Pre-Order settings
            if (self::productSettingEnabled('productSettingPreOrderEnabled') && $request->has('pre_order_is_active')) {
                $preOrderData = [
                    'is_active' => $request->pre_order_is_active ? true : false,
                    'order_limit' => $request->pre_order_order_limit ?? 0,
                    'total_order_limit' => $request->pre_order_total_order_limit ?? 0,
                    'start_date' => $request->pre_order_start_date ?? null,
                    'end_date' => $request->pre_order_end_date ?? null,
                    'expected_release_date' => $request->pre_order_expected_release_date ?? null,
                    'description' => $request->pre_order_description ?? '',
                ];
                $product->pre_order = $preOrderData;
                $product->save();
            }

            # Product translation
            $ProductTranslation = ProductTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'product_id' => $product->id]);
            $ProductTranslation->name        = $request->name;
            $ProductTranslation->description = $request->description;
            $ProductTranslation->save();

            # tags
            if (self::productSettingEnabled('productSettingTagsEnabled')) {
                $product->tags()->sync($request->tag_ids ?? []);
            }

            # badges
            if (self::productSettingEnabled('productSettingBadgesEnabled')) {
                $product->productBadges()->sync($request->badge_ids ?? []);
            }

            # category
            $product->categories()->sync($request->category_ids);

            # taxes
            self::syncProductTaxes($product, $request);

            # variations, combinations & stocks of warehouses
            $warehouses = shop()->warehouses;

            if ($request->has('is_variant') && $request->has('variations')) {
                foreach ($request->variations as $variation) {
                    $product_variation                  = new ProductVariation();
                    $product_variation->product_id      = $product->id;
                    $product_variation->code            = $variation['code'];
                    $product_variation->price           = $variation['price'];
                    $product_variation->image           = $variation['image'];
                    $product_variation->sku             = $variation['sku'];
                    $product_variation->discount_value  = self::normalizeDiscountValue($variation['discount_value'] ?? 0);
                    $product_variation->discount_type   = self::normalizeDiscountType($variation['discount_type'] ?? null);
                    $product_variation->save();

                    foreach ($warehouses as $warehouse) {
                        $product_variation_stock                        = new ProductVariationStock;
                        $product_variation_stock->product_variation_id  = $product_variation->id;
                        $product_variation_stock->warehouse_id          = $warehouse->id;

                        if (!useInventory() && $warehouse->is_default == 1) {
                            $product_variation_stock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $product_variation_stock->stock_qty  = 0;
                        }

                        $product_variation_stock->save();
                    }

                    foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                        $product_variation_combination                         = new ProductVariationCombination;
                        $product_variation_combination->product_id             = $product->id;
                        $product_variation_combination->product_variation_id   = $product_variation->id;
                        $product_variation_combination->variation_id           = explode(":", $combination)[0];
                        $product_variation_combination->variation_value_id     = explode(":", $combination)[1];

                        // Save combination image if provided (color swatch image)
                        if (isset($variation['combination_image']) && $variation['combination_image']) {
                            $product_variation_combination->image = $variation['combination_image'];
                        }

                        $product_variation_combination->save();
                    }
                }
            } else {
                $variation                  = new ProductVariation;
                $variation->product_id      = $product->id;
                $variation->price           = $request->price;
                $variation->image           = $request->image;
                $variation->sku             = $request->sku;
                $variation->discount_value  = self::normalizeDiscountValue($request->discount_value ?? 0);
                $variation->discount_type   = self::normalizeDiscountType($request->discount_type);
                $variation->save();

                foreach ($warehouses as $warehouse) {
                    $product_variation_stock                        = new ProductVariationStock;
                    $product_variation_stock->product_variation_id  = $variation->id;
                    $product_variation_stock->warehouse_id          = $warehouse->id;

                    if (!useInventory() && $warehouse->is_default == 1) {
                        $product_variation_stock->stock_qty  = $request->stock_qty;
                    } else {
                        $product_variation_stock->stock_qty  = 0;
                    }

                    $product_variation_stock->save();
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Product has been added successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            \Log::error('Product store error', [
                'message' => $th->getMessage()
            ]);
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # import simple products from CSV
    public static function import($request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:10240'
            ], [
                'csv_file.required' => translate('Please upload a CSV file'),
                'csv_file.mimes' => translate('File must be a CSV file'),
                'csv_file.max' => translate('File size must be less than 10MB')
            ]);

            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), 'r');

            if (!$handle) {
                return [
                    'status' => 403,
                    'message' => translate('Unable to read uploaded CSV file'),
                    'result' => [],
                ];
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return [
                    'status' => 403,
                    'message' => translate('CSV file is empty'),
                    'result' => [],
                ];
            }

            $header = array_map(fn($column) => Str::snake(strtolower(trim((string) $column))), $header);
            $requiredColumns = ['name', 'price'];
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $header)) {
                    fclose($handle);
                    return [
                        'status' => 403,
                        'message' => translate('CSV must contain name and price columns'),
                        'result' => [],
                    ];
                }
            }

            $created = 0;
            $errors = [];
            $errorCount = 0;
            $rowNumber = 1;
            $warehouses = shop()->warehouses;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if (count(array_filter($row, fn($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                if (count($row) !== count($header)) {
                    if ($errorCount < 10) {
                        $errors[] = "Row {$rowNumber}: Column count does not match the header";
                    }
                    $errorCount++;
                    continue;
                }

                $rowData = array_combine($header, $row);
                $name = trim((string) ($rowData['name'] ?? ''));
                $price = trim((string) ($rowData['price'] ?? ''));

                if ($name === '') {
                    if ($errorCount < 10) {
                        $errors[] = "Row {$rowNumber}: Name is required";
                    }
                    $errorCount++;
                    continue;
                }

                if ($price === '' || !is_numeric($price) || (float) $price < 0) {
                    if ($errorCount < 10) {
                        $errors[] = "Row {$rowNumber}: Price must be a valid number";
                    }
                    $errorCount++;
                    continue;
                }

                try {
                    DB::transaction(function () use ($rowData, $name, $price, $warehouses) {
                        $stockQty = isset($rowData['stock_qty']) && is_numeric($rowData['stock_qty']) ? (int) $rowData['stock_qty'] : 0;
                        $discountType = in_array(($rowData['discount_type'] ?? 'flat'), ['flat', 'amount', 'percentage'], true)
                            ? $rowData['discount_type']
                            : 'flat';

                        if ($discountType === 'amount') {
                            $discountType = 'flat';
                        }

                        $product = new Product;
                        $product->shop_id = shop()->id;
                        $product->name = $name;
                        $product->slug = self::makeUniqueSlug($name);
                        $product->brand_id = self::resolveModelId(Brand::class, $rowData['brand'] ?? ($rowData['brand_id'] ?? ''));
                        $product->unit_id = self::resolveModelId(Unit::class, $rowData['unit'] ?? ($rowData['unit_id'] ?? ''));
                        $product->thumbnail_image = $rowData['thumbnail_image'] ?? null;
                        $product->gallery_images = $rowData['gallery_images'] ?? null;
                        $product->description = $rowData['description'] ?? null;
                        $product->min_price = (float) $price;
                        $product->max_price = (float) $price;
                        $product->stock_qty = useInventory() ? 0 : $stockQty;
                        $product->alert_qty = isset($rowData['alert_qty']) && is_numeric($rowData['alert_qty']) ? (int) $rowData['alert_qty'] : 0;
                        $product->is_published = self::toBoolean($rowData['is_published'] ?? null, false) ? 1 : 0;
                        $product->hide_on_ecom = self::toBoolean($rowData['hide_on_ecom'] ?? null) ? 1 : 0;
                        $product->min_purchase_qty = self::resolveMinPurchaseQtyValue($rowData['min_purchase_qty'] ?? null);
                        $product->max_purchase_qty = self::resolveMaxPurchaseQtyValue($rowData['max_purchase_qty'] ?? null);
                        $product->has_variation = 0;
                        $product->has_warranty = 0;
                        $product->has_emi = 0;
                        $product->meta_title = $rowData['meta_title'] ?? null;
                        $product->meta_description = $rowData['meta_description'] ?? null;
                        $product->meta_keywords = $rowData['meta_keywords'] ?? null;
                        $product->shipping_class_id = self::resolveModelId(ShippingClass::class, $rowData['shipping_class'] ?? ($rowData['shipping_class_id'] ?? ''));
                        $product->save();

                        $productTranslation = ProductTranslation::firstOrNew([
                            'lang_key' => config('app.default_language'),
                            'product_id' => $product->id,
                        ]);
                        $productTranslation->name = $name;
                        $productTranslation->description = $product->description;
                        $productTranslation->save();

                        $product->categories()->sync(self::resolveModelIds(Category::class, $rowData['categories'] ?? ($rowData['category_ids'] ?? '')));
                        $product->tags()->sync(self::resolveModelIds(Tag::class, $rowData['tags'] ?? ($rowData['tag_ids'] ?? '')));
                        $product->productBadges()->sync(self::resolveModelIds(Badge::class, $rowData['badges'] ?? ($rowData['badge_ids'] ?? '')));

                        $variation = new ProductVariation;
                        $variation->product_id = $product->id;
                        $variation->price = (float) $price;
                        $variation->sku = $rowData['sku'] ?? null;
                        $variation->discount_value = isset($rowData['discount_value']) && is_numeric($rowData['discount_value']) ? (float) $rowData['discount_value'] : 0;
                        $variation->discount_type = $discountType;
                        $variation->save();

                        foreach ($warehouses as $warehouse) {
                            $productVariationStock = new ProductVariationStock;
                            $productVariationStock->product_variation_id = $variation->id;
                            $productVariationStock->warehouse_id = $warehouse->id;
                            $productVariationStock->stock_qty = (!useInventory() && $warehouse->is_default == 1) ? $stockQty : 0;
                            $productVariationStock->save();
                        }
                    });

                    $created++;
                } catch (\Throwable $th) {
                    \Log::error('Product import row error', [
                        'row' => $rowNumber,
                        'message' => $th->getMessage(),
                    ]);

                    if ($errorCount < 10) {
                        $errors[] = "Row {$rowNumber}: Could not import product";
                    }
                    $errorCount++;
                }
            }

            fclose($handle);

            $message = translate('Product import completed') . ". {$created} " . translate('created') . ".";
            if ($errorCount > 0) {
                $message .= " {$errorCount} " . translate('rows failed') . ". " . implode(' ', $errors);
            }

            return [
                'status' => 200,
                'message' => $message,
                'result' => [
                    'created' => $created,
                    'errors' => $errors,
                    'total_errors' => $errorCount,
                ],
            ];
        } catch (\Throwable $th) {
            \Log::error('Product import error', [
                'message' => $th->getMessage(),
            ]);

            return [
                'status' => 403,
                'message' => translate('Product import failed'),
                'result' => [],
            ];
        }
    }

    # download sample CSV for product import
    public static function sampleCsv()
    {
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'name',
            'price',
            'sku',
            'stock_qty',
            'description',
            'categories',
            'brand',
            'unit',
            'thumbnail_image',
            'gallery_images',
            'discount_value',
            'discount_type',
            'is_published',
            'hide_on_ecom',
            'min_purchase_qty',
            'max_purchase_qty',
            'alert_qty',
            'tags',
            'badges',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'shipping_class',
        ]);

        $csv->insertOne([
            'Classic Cotton T-Shirt',
            '19.99',
            'TSHIRT-CLASSIC-001',
            '100',
            'Soft everyday cotton t-shirt.',
            'Men,T-Shirts',
            'Acme',
            'Piece',
            '',
            '',
            '0',
            'flat',
            '1',
            '0',
            '1',
            '5',
            '10',
            'summer|cotton',
            'new',
            'Classic Cotton T-Shirt',
            'Soft everyday cotton t-shirt.',
            't-shirt,cotton,classic',
            '',
        ]);

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="product_import_sample.csv"');
    }

    # return view of edit form
    public static function edit($request, $id)
    {
        try {
            Notification::where('link_info', $id)->where('type', 'stock')->where('shop_id', shopId())->update([
                'is_read' => 1
            ]);
        } catch (\Throwable $th) {
        }


        try {
            $lang_key = $request->lang_key;
            $language = Language::where('is_active', 1)->where('code', $lang_key)->first();
            if (!$language) {
                if (!$language) {
                    $data = [
                        'status'    => 403,
                        'message'   => translate('Language you are trying to translate is not available or not active'),
                        'result'    => [],
                    ];
                    return $data;
                }
            }
            $product = Product::shop()->findOrFail($id);
            $categories = Category::where('parent_id', 0)
                ->orderBy('name', 'ASC')
                ->with('childrenCategories')
                ->get();

            $brands     = Brand::isActive()->orderBy('name', 'ASC')->get();
            $units      = Unit::isActive()->orderBy('name', 'ASC')->get();
            $variations = Variation::isActive()->get();
            $taxes      = Tax::isActive()->orderBy('name', 'ASC')->get();
            $badges     = Badge::isActive()->orderBy('name', 'ASC')->get();
            $tags       = Tag::orderBy('name', 'ASC')->get();
            $shippingClasses     = ShippingClass::shop()->isActive()
                ->select('id', 'shop_id', 'name')
                ->with([
                    'shippingCharges' => fn($q) =>
                    $q->select('id', 'shipping_class_id', 'zone_id', 'charge'),

                    'shippingCharges.zone:id,name'
                ])
                ->get();

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'product'       => $product,
                    'categories'    => $categories,
                    'brands'        => $brands,
                    'units'         => $units,
                    'variations'    => $variations,
                    'taxes'         => $taxes,
                    'badges'        => $badges,
                    'lang_key'      => $lang_key,
                    'tags'          => $tags,
                    'shippingClasses' => $shippingClasses
                ],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        if ($request->has('is_variant') && !$request->has('variations')) {
            $data = [
                'status'    => 403,
                'message'   => translate('Invalid product variations, please check again'),
                'result'    => [],
            ];
            return $data;
        }

        // try {
        $product                    = Product::where('id', $id)->first();
        $oldProduct                 = clone $product;

        if ($product->shop_id != shop()->id) {
            abort(403);
        }

        if ($request->lang_key == config("app.default_language")) {
            $product->name              = $request->name;
            $product->slug              = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
            $product->brand_id          = $request->brand_id;
            $product->unit_id           = $request->unit_id;

            $product->thumbnail_image   = $request->thumbnail_image;
            $product->gallery_images    = $request->gallery_images;
            if (self::productSettingEnabled('productSettingVideoLinkEnabled')) {
                $product->video_link    = self::resolveVideoLink($request);
            }
            $hasSizeChart               = self::productSettingEnabled('productSettingSizeChartEnabled') && $request->has('isSizeChart');
            $product->isSizeChart       = $hasSizeChart ? 1 : 0;
            $product->size_guides       = $hasSizeChart ? self::normalizeSizeChartState($request->input('size_chart_state')) : null;
            $product->chart_image       = $hasSizeChart && $request->filled('chart_image') ? $request->chart_image : null;
            if (self::productSettingEnabled('productSettingQrCodeEnabled')) {
                $product->show_recipe_details = $request->has('show_recipe_details') ? 1 : 0;
                $product->recipe_details    = $request->recipe_details;
                $product->recipe_image      = $request->has('show_recipe_details') && $request->filled('recipe_image') ? $request->recipe_image : null;
            }

            $product->description       = $request->description;

            # min-max price
            if ($request->has('is_variant') && $request->has('variations')) {
                $product->min_price =  min(array_column($request->variations, 'price'));
                $product->max_price =  max(array_column($request->variations, 'price'));
            } else {
                $product->min_price =  $request->price;
                $product->max_price =  $request->price;
            }

            # discount 
            self::syncDiscountEnabled(
                $product,
                $request->boolean('discount_schedule_enabled') || self::requestHasDiscountValue($request)
            );
            // $product->discount_info   = $request->discount_info;

            # stock qty based on all variations / no variation 
            if (!useInventory()) {
                $product->stock_qty   = ($request->has('is_variant') && $request->has('variations')) ? array_sum(array_column($request->variations, 'stock_qty')) : $request->stock_qty;
            }

            $product->alert_qty   = self::resolveAlertQty($request);

            $product->is_published         = $request->is_published ? $request->is_published : 0;
            $product->hide_on_ecom         = $request->has('hide_on_ecom') ? 1 : 0;
            $product->min_purchase_qty     = self::resolveMinPurchaseQty($request);
            $product->max_purchase_qty     = self::resolveMaxPurchaseQty($request);
            $product->est_delivery_time    = self::resolveEstimatedDeliveryTime($request);
            $product->emi_info             = self::resolveEmiInfo($request);

            $product->has_warranty         = self::resolveHasWarranty($request);
            $product->has_emi              = self::resolveHasEmi($request);

            $product->warranty_info        = self::resolveWarrantyInfo($request);

            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_image        = $request->meta_image;
            $product->shipping_class_id = $request->filled('shipping_class_id') ? $request->shipping_class_id : null;

            $product->save();

            # Pre-Order settings
            if (self::productSettingEnabled('productSettingPreOrderEnabled') && $request->has('pre_order_is_active')) {
                $preOrderData = [
                    'is_active' => $request->pre_order_is_active ? true : false,
                    'order_limit' => $request->pre_order_order_limit ?? 0,
                    'total_order_limit' => $request->pre_order_total_order_limit ?? 0,
                    'start_date' => $request->pre_order_start_date ?? null,
                    'end_date' => $request->pre_order_end_date ?? null,
                    'expected_release_date' => $request->pre_order_expected_release_date ?? null,
                    'description' => $request->pre_order_description ?? '',
                ];
                $product->pre_order = $preOrderData;
                $product->save();
            } else {
                $product->pre_order = null;
                $product->save();
            }

            if (self::productSettingEnabled('productSettingQrCodeEnabled')) {
                # QR Data settings
                $qrData = $product->qr_data ?? [];

                # Update color if provided
                if ($request->has('qr_color')) {
                    $qrData['color'] = $request->qr_color;
                }

                # Update links if provided
                if ($request->has('qr_links')) {
                    $qrLinks = [];
                    foreach ($request->qr_links as $link) {
                        if (!empty($link['url'])) {
                            $qrLinks[] = [
                                'url' => $link['url'],
                                'title' => $link['title'] ?? null,
                                'icon' => $link['icon'] ?? null,
                            ];
                        }
                    }
                    $qrData['links'] = $qrLinks;
                }

                # Save qr_data as object with color and links
                if (!empty($qrData['links']) || !empty($qrData['color'])) {
                    $product->qr_data = $qrData;
                    $product->save();
                } else {
                    $product->qr_data = null;
                    $product->save();
                }
            }

            # tags
            if (self::productSettingEnabled('productSettingTagsEnabled')) {
                $product->tags()->sync($request->tag_ids ?? []);
            }

            # badges
            if (self::productSettingEnabled('productSettingBadgesEnabled')) {
                $product->productBadges()->sync($request->badge_ids ?? []);
            }

            # category
            $product->categories()->sync($request->category_ids);

            # taxes
            self::syncProductTaxes($product, $request);


            # variations, combinations & stocks of warehouses
            $warehouses = shop()->warehouses;

            if ($request->has('is_variant') && $request->has('variations')) {
                $product->has_variation        = 1;
                $product->save();

                $requested_variations           = collect($request->variations);

                $requested_variation_codes      = $requested_variations->pluck('code')->toArray();
                $old_variations_codes           = $product->variations()->pluck('code')->toArray();

                $old_matched_variations         = $requested_variations->whereIn('code', $old_variations_codes);
                $new_variations                 = $requested_variations->whereNotIn('code', $old_variations_codes);

                # delete old variations that isn't requested
                // $product->variations()->whereNotIn('code', $requested_variation_codes)->orWhere('code', null)->each(function ($variation) {
                //     foreach ($variation->combinations as $comb) {
                //         $comb->delete();
                //     }
                //     $variation->productVariationStocks()->delete();
                //     // $variation->cartItems()->delete();
                //     $variation->delete();
                // });

                # delete old variations that isn't requested
                $product->variations()->where(function ($query) use ($requested_variation_codes) {
                    $query->whereNotIn('code', $requested_variation_codes)
                        ->orWhereNull('code');
                })->each(function ($variation) {
                    foreach ($variation->combinations as $comb) {
                        $comb->delete();
                    }
                    $variation->productVariationStocks()->delete();
                    // $variation->cartItems()->delete();
                    $variation->delete();
                });

                # update old matched variations
                foreach ($old_matched_variations as $variation) {
                    $pVariation                  = ProductVariation::where('product_id', $product->id)->where('code', $variation['code'])->first();
                    $pVariation->price           = $variation['price'];
                    $pVariation->image           = $variation['image'];
                    $pVariation->sku             = $variation['sku'];
                    $pVariation->discount_value  = self::normalizeDiscountValue($variation['discount_value'] ?? 0);
                    $pVariation->discount_type   = self::normalizeDiscountType($variation['discount_type'] ?? null);
                    $pVariation->save();

                    # update stock of this variation
                    foreach ($warehouses as $warehouse) {
                        $productVariationStock = $pVariation->productVariationStocks()->where('warehouse_id', $warehouse->id)->first();
                        if (is_null($productVariationStock)) {
                            $productVariationStock                          = new ProductVariationStock;
                            $productVariationStock->product_variation_id    = $pVariation->id;
                            $productVariationStock->warehouse_id            = $warehouse->id;
                        }
                        if (!useInventory() && $warehouse->is_default == 1) {
                            $productVariationStock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $productVariationStock->stock_qty  = $productVariationStock?->stock_qty ?? 0;
                        }
                        $productVariationStock->save();
                    }

                    # update combination image (color swatch) for existing variations
                    if (isset($variation['combination_image']) && $variation['combination_image']) {
                        $combinations = $pVariation->combinations;
                        foreach ($combinations as $combination) {
                            $combination->image = $variation['combination_image'];
                            $combination->save();
                        }
                    }
                }

                # store new requested variations
                foreach ($new_variations as $variation) {
                    $product_variation                  = new ProductVariation;
                    $product_variation->product_id      = $product->id;
                    $product_variation->code            = $variation['code'];
                    $product_variation->price           = $variation['price'];
                    $product_variation->image           = $variation['image'];
                    $product_variation->sku             = $variation['sku'];
                    $product_variation->discount_value  = self::normalizeDiscountValue($variation['discount_value'] ?? 0);
                    $product_variation->discount_type   = self::normalizeDiscountType($variation['discount_type'] ?? null);
                    $product_variation->save();

                    foreach ($warehouses as $warehouse) {
                        $product_variation_stock                        = new ProductVariationStock;
                        $product_variation_stock->product_variation_id  = $product_variation->id;
                        $product_variation_stock->warehouse_id          = $warehouse->id;

                        if (!useInventory() && $warehouse->is_default == 1) {
                            $product_variation_stock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $product_variation_stock->stock_qty  = 0;
                        }
                        $product_variation_stock->save();
                    }

                    foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                        $product_variation_combination                         = new ProductVariationCombination;
                        $product_variation_combination->product_id             = $product->id;
                        $product_variation_combination->product_variation_id   = $product_variation->id;
                        $product_variation_combination->variation_id           = explode(":", $combination)[0];
                        $product_variation_combination->variation_value_id     = explode(":", $combination)[1];

                        // Save combination image if provided (color swatch image)
                        if (isset($variation['combination_image']) && $variation['combination_image']) {
                            $product_variation_combination->image = $variation['combination_image'];
                        }

                        $product_variation_combination->save();
                    }
                }
            } else {
                $product->has_variation        = 0;
                $product->save();
                # check if old product is variant then delete all old variation & combinations
                if ($oldProduct->has_variation) {
                    foreach ($product->variations as $variation) {
                        foreach ($variation->combinations as $comb) {
                            $comb->delete();
                        }
                        $variation->productVariationStocks()->delete();
                        $variation->delete();
                    }
                    $variation = new ProductVariation;
                } else {
                    $variation = $oldProduct->variations()->first();
                    if (is_null($variation)) {
                        $variation = new ProductVariation;
                    }
                }

                $variation->product_id      = $product->id;
                $variation->price           = $request->price;
                $variation->image           = $request->image;
                $variation->sku             = $request->sku;
                $variation->discount_value  = self::normalizeDiscountValue($request->discount_value ?? 0);
                $variation->discount_type   = self::normalizeDiscountType($request->discount_type);
                $variation->save();

                foreach ($warehouses as $warehouse) {
                    $productVariationStock = $variation->productVariationStocks()->where('warehouse_id', $warehouse->id)->first();
                    if (is_null($productVariationStock)) {
                        $productVariationStock  = new ProductVariationStock;
                    }
                    $productVariationStock->product_variation_id  = $variation->id;
                    $productVariationStock->warehouse_id          = $warehouse->id;

                    if (!useInventory() && $warehouse->is_default == 1) {
                        $productVariationStock->stock_qty  = $request->stock_qty;
                    } else {
                        $productVariationStock->stock_qty  = $productVariationStock?->stock_qty ?? 0;
                    }

                    $productVariationStock->save();
                }
            }
        }

        # Product translation
        $ProductTranslation = ProductTranslation::firstOrNew(['lang_key' => $request->lang_key, 'product_id' => $product->id]);
        $ProductTranslation->name = $request->name;
        $ProductTranslation->description = $request->description;
        $ProductTranslation->save();

        $data = [
            'status'    => 200,
            'message'   => translate('Product has been updated successfully'),
            'result'    => [],
        ];
        return $data;
        // } catch (\Throwable $th) {
        //     $data = [
        //         'status'    => 403,
        //         'message'   => translate('Something went wrong'),
        //         'result'    => [],
        //     ];
        //     return $data;
        // }
    }

    # update status
    public static function updateStatus($request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        try {
            $product = Product::shop()->findOrFail($request->id);
            $product->is_published = $request->isActive;
            $product->save();
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    public static function duplicate($id)
    {
        $product                        = Product::find($id);
        $productNew                     = $product->replicate();
        $productNew->stock_qty          = 0;
        $productNew->total_sale_count   = 0;
        $productNew->slug               = Str::slug($productNew->name, '-') . '-' . strtolower(Str::random(5));

        if ($productNew->save()) {

            //categories duplicate
            foreach ($product->productCategories as $key => $category) {
                $pCategory                 = new ProductCategory;
                $pCategory->product_id     = $productNew->id;
                $pCategory->category_id    = $category->category_id;
                $pCategory->save();
            }

            //badges duplicate
            foreach ($product->badges as $key => $badge) {
                $pBadge                 = new ProductBadge;
                $pBadge->product_id     = $productNew->id;
                $pBadge->badge_id    = $badge->badge_id;
                $pBadge->save();
            }

            //tags duplicate
            foreach ($product->productTags as $key => $tag) {
                $pTag                 = new ProductTag;
                $pTag->product_id     = $productNew->id;
                $pTag->tag_id    = $tag->tag_id;
                $pTag->save();
            }

            // taxes duplicate
            foreach ($product->taxes as $key => $tax) {
                $pTax                = new ProductTax;
                $pTax->product_id    = $productNew->id;
                $pTax->tax_id        = $tax->tax_id;
                $pTax->tax_value     = $tax->tax_value;
                $pTax->tax_type      = $tax->tax_type;
                $pTax->save();
            }

            // translation duplicate
            foreach ($product->productTranslations as $key => $translation) {
                $productTranslation                    = new ProductTranslation;
                $productTranslation->lang_key          = $translation->lang_key;
                $productTranslation->product_id        = $productNew->id;
                $productTranslation->name              = $translation->name;
                $productTranslation->short_description = $translation->short_description;
                $productTranslation->description   = $translation->description;
                $productTranslation->save();
            }

            // variation duplicate
            foreach ($product->variations()->withoutTrashed()->get() as $key => $variation) {
                $pVariation                 = new ProductVariation;
                $pVariation->product_id     = $productNew->id;
                $pVariation->sku            = $variation->sku;
                $pVariation->image          = $variation->image;
                $pVariation->code           = $variation->code;
                $pVariation->price          = $variation->price;
                $pVariation->discount_value = $variation->discount_value;
                $pVariation->discount_type  = $variation->discount_type;
                $pVariation->save();

                // variation combination duplicate
                foreach ($variation->combinations as $key => $combination) {
                    $pVariationComb                         = new ProductVariationCombination;
                    $pVariationComb->product_id             = $productNew->id;
                    $pVariationComb->product_variation_id   = $pVariation->id;
                    $pVariationComb->variation_id           = $combination->variation_id;
                    $pVariationComb->variation_value_id     = $combination->variation_value_id;
                    $pVariationComb->save();
                }
            }

            // todo:: duplicate stock but set 0

            $data = [
                'status'    => 200,
                'message'   => translate('Product has been duplicated successfully'),
                'result'    => [],
            ];
            return $data;
        } else {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # delete data
    public static function destroy($id)
    {
        try {
            $product    = Product::shop()->where('id', $id)->first();

            ProductCategory::where('product_id', $product->id)->delete();
            ProductBadge::where('product_id', $product->id)->delete();
            ProductReview::where('product_id', $product->id)->delete();
            ProductTag::where('product_id', $product->id)->delete();
            ProductTax::where('product_id', $product->id)->delete();
            ProductTranslation::where('product_id', $product->id)->delete();
            ProductVariation::where('product_id', $product->id)->delete();
            ProductVariationCombination::where('product_id', $product->id)->delete();
            CampaignProduct::where('product_id', $product->id)->delete();
            $product->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Product deleted successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # Pre-Order Methods
    public static function updatePreOrderSettings($request, $productId)
    {
        if (!self::productSettingEnabled('productSettingPreOrderEnabled')) {
            return [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Pre-order settings are disabled'),
                'result'    => [],
            ];
        }

        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Pre-order settings updated successfully'),
            'result'    => null
        ];

        try {
            $product = Product::shop()->findOrFail($productId);

            $preOrderData = [
                'is_active' => $request->is_active ? true : false,
                'order_limit' => $request->order_limit ?? 0,
                'total_order_limit' => $request->total_order_limit ?? 0,
                'start_date' => $request->start_date ?? null,
                'end_date' => $request->end_date ?? null,
                'expected_release_date' => $request->expected_release_date ?? null,
                'description' => $request->description ?? '',
            ];

            $product->pre_order = $preOrderData;
            $product->save();

            return $data;
        } catch (\Throwable $th) {
            \Log::error('Pre-order settings update error', [
                'message' => $th->getMessage()
            ]);
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    public static function updateVariationPreOrderSettings($request, $variationId)
    {
        if (!self::productSettingEnabled('productSettingPreOrderEnabled')) {
            return [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Pre-order settings are disabled'),
                'result'    => [],
            ];
        }

        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Variation pre-order settings updated successfully'),
            'result'    => null
        ];

        try {
            $variation = ProductVariation::whereHas('product', function ($query) {
                $query->where('shop_id', shop()->id);
            })->findOrFail($variationId);

            if ($request->enabled === 'inherit') {
                $variation->pre_order = null;
            } elseif ($request->enabled === 'enable') {
                $variation->pre_order = ['enabled' => true];
            } elseif ($request->enabled === 'disable') {
                $variation->pre_order = ['enabled' => false];
            }

            $variation->save();

            return $data;
        } catch (\Throwable $th) {
            \Log::error('Variation pre-order settings update error', [
                'message' => $th->getMessage()
            ]);
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    public static function getCurrentOrdersCount($variationId): int
    {
        return OrderItem::where('product_variation_id', $variationId)
            ->whereHas('order', function ($query) {
                $query->whereIn('delivery_status', ['pending', 'confirmed', 'processing', 'shipped']);
            })
            ->whereNotNull('pre_order_info')
            ->sum('qty');
    }

    public static function validatePreOrder($variation, $customerId = null, $quantity = 1)
    {
        $result = [
            'valid' => true,
            'message' => '',
        ];

        if (!$variation->isPreOrderEnabled()) {
            $result['valid'] = false;
            $result['message'] = translate('This variation is not available for pre-order');
            return $result;
        }

        if ($variation->isSoldOut()) {
            $result['valid'] = false;
            $result['message'] = translate('Pre-order limit reached for this item');
            return $result;
        }

        $product = $variation->product;
        $orderLimit = $variation->getPreOrderOrderLimit();

        if ($orderLimit > 0 && $customerId) {
            $customerOrderCount = OrderItem::whereHas('order', function ($query) use ($customerId) {
                $query->where('user_id', $customerId)
                    ->whereIn('delivery_status', ['pending', 'confirmed', 'processing', 'shipped']);
            })->where('product_variation_id', $variation->id)
            ->whereNotNull('pre_order_info')
            ->sum('qty');

            if ($customerOrderCount + $quantity > $orderLimit) {
                $result['valid'] = false;
                $result['message'] = translate('You have reached the pre-order limit for this item');
                return $result;
            }
        }

        $remaining = $variation->remainingOrders();
        if ($remaining < $quantity) {
            $result['valid'] = false;
            $result['message'] = translate('Only :count pre-orders remaining', ['count' => $remaining]);
            return $result;
        }

        return $result;
    }

    public static function reservePreOrder($variation, $quantity = 1)
    {
        $totalLimit = $variation->getPreOrderTotalLimit();
        $currentOrders = self::getCurrentOrdersCount($variation->id);

        if ($totalLimit > 0 && $currentOrders + $quantity > $totalLimit) {
            return [
                'success' => false,
                'message' => translate('Pre-order limit reached'),
            ];
        }

        return [
            'success' => true,
            'currentOrders' => $currentOrders + $quantity,
        ];
    }

    public static function getPreOrderProducts($request = null)
    {
        $query = Product::shop()
            ->whereNotNull('pre_order')
            ->where('pre_order->is_active', true)
            ->with(['variations']);

        if ($request && $request->status) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('pre_order->start_date', '<=', $now)
                    ->where('pre_order->end_date', '>=', $now);
            } elseif ($request->status === 'ended') {
                $query->where('pre_order->end_date', '<', $now);
            } elseif ($request->status === 'sold_out') {
                // This would need subquery or having clause
            }
        }

        return $query;
    }

    # get quick edit data for modal
    public static function getQuickEditData($productId)
    {
        $product = Product::with(['productTranslations', 'variations.productVariationStocks'])->find($productId);
        
        if (!$product) {
            return [
                'status' => 404,
                'message' => translate('Product not found'),
            ];
        }

        $langKey = app()->getLocale();
        $translation = $product->productTranslations->where('lang_key', $langKey)->first();
        $defaultWarehouse = Warehouse::where('shop_id', $product->shop_id)
            ->orderByDesc('is_default')
            ->first();
        
        $data = [
            'id' => $product->id,
            'name' => $translation ? $translation->name : $product->name,
            'slug' => $product->slug,
            'has_variation' => $product->has_variation,
            'use_inventory' => useInventory(),
        ];

        // Get variations if product has variations
        if ($product->has_variation) {
            $variations = $product->variations->map(function ($variation) use ($defaultWarehouse) {
                $stockQty = useInventory()
                    ? $variation->productVariationStocks->sum('stock_qty')
                    : $variation->productVariationStocks
                        ->where('warehouse_id', $defaultWarehouse?->id)
                        ->sum('stock_qty');
                
                return [
                    'id' => $variation->id,
                    'code' => $variation->code,
                    'price' => $variation->price,
                    'stock_qty' => $stockQty,
                ];
            });
            $data['variations'] = $variations;
            
            // Set default to first variation
            if ($variations->isNotEmpty()) {
                $firstVariation = $variations->first();
                $data['price'] = $firstVariation['price'];
                $data['stock_qty'] = $firstVariation['stock_qty'];
                $data['variation_id'] = $firstVariation['id'];
            }
        } else {
            // Simple product
            $data['price'] = $product->min_price;
            
            $data['stock_qty'] = useInventory()
                ? $product->getStockQuantity()
                : $product->variations
                    ->flatMap->productVariationStocks
                    ->where('warehouse_id', $defaultWarehouse?->id)
                    ->sum('stock_qty');
        }

        return [
            'status' => 200,
            'data' => $data,
        ];
    }

    # quick update product data
    public static function quickUpdate($request, $productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return [
                'status' => 404,
                'success' => false,
                'message' => translate('Product not found'),
            ];
        }

        // Verify shop ownership - prevent editing products from other shops
        if ($product->shop_id != shop()->id) {
            return [
                'status' => 403,
                'success' => false,
                'message' => translate('Unauthorized to edit this product'),
            ];
        }

        // Validate outside try-catch to properly return validation errors
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug,' . $productId,
                'price' => 'required|numeric|min:0',
                'stock' => 'nullable|integer',
                'variation_id' => 'nullable|integer|exists:product_variations,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return [
                'status' => 422,
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ];
        }

        // Check stock validation before transaction
        if (!useInventory() && isset($validated['stock'])) {
            $allowBackorder = getSetting('allowBackorder') == '1';
            
            if (!$allowBackorder && $validated['stock'] < 0) {
                return [
                    'status' => 422,
                    'success' => false,
                    'message' => translate('Stock cannot be negative when backorder is not allowed'),
                ];
            }
        }

        // Wrap all database operations in a transaction
        try {
            DB::beginTransaction();
            
            $langKey = app()->getLocale();
            
            // Update name in translations
            $translation = ProductTranslation::where('product_id', $productId)
                ->where('lang_key', $langKey)
                ->first();
            
            if ($translation) {
                $translation->name = $validated['name'];
                $translation->save();
            } else {
                // Create translation if doesn't exist
                ProductTranslation::create([
                    'product_id' => $productId,
                    'lang_key' => $langKey,
                    'name' => $validated['name'],
                ]);
            }

            // Update slug
            $product->slug = $validated['slug'];

            // Update price - always update product_variations table
            if (!empty($validated['variation_id'])) {
                // Variant product: Update the selected specific variation
                $variation = ProductVariation::where('id', $validated['variation_id'])
                    ->where('product_id', $productId)
                    ->first();
                
                if ($variation) {
                    $variation->price = $validated['price'];
                    $variation->save();
                }
            } else {
                // Simple product: Find and update the single variation
                $variation = ProductVariation::where('product_id', $productId)->first();
                
                if ($variation) {
                    $variation->price = $validated['price'];
                    $variation->save();
                }
            }
            
            // Recalculate product min/max prices from all variations
            $prices = $product->variations->pluck('price')->toArray();
            if (!empty($prices)) {
                $product->min_price = min($prices);
                $product->max_price = max($prices);
            }

            // Without inventory, stock is stored against the shop's default warehouse.
            if (!useInventory() && isset($validated['stock'])) {
                $defaultWarehouse = Warehouse::where('shop_id', $product->shop_id)
                    ->orderByDesc('is_default')
                    ->first();

                if (!$defaultWarehouse) {
                    throw new \RuntimeException(translate('No warehouse is configured for this shop'));
                }

                if (!empty($validated['variation_id'])) {
                    $variation = ProductVariation::where('id', $validated['variation_id'])
                        ->where('product_id', $productId)
                        ->first();
                } else {
                    $variation = ProductVariation::where('product_id', $productId)->first();
                }

                if ($variation) {
                    $variationStock = $variation->productVariationStocks()
                        ->where('warehouse_id', $defaultWarehouse->id)
                        ->first() ?? new ProductVariationStock();

                    $variationStock->product_variation_id = $variation->id;
                    $variationStock->warehouse_id = $defaultWarehouse->id;
                    $variationStock->stock_qty = $validated['stock'];
                    $variationStock->save();
                }

                $product->stock_qty = ProductVariationStock::whereHas('productVariation', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                    ->where('warehouse_id', $defaultWarehouse->id)
                    ->sum('stock_qty');
            }

            $product->save();
            
            DB::commit();

            return [
                'status' => 200,
                'success' => true,
                'message' => translate('Product updated successfully'),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Quick update failed', ['error' => $e->getMessage(), 'product_id' => $productId]);
            return [
                'status' => 500,
                'success' => false,
                'message' => translate('Failed to update product: ') . $e->getMessage(),
            ];
        }
    }
}
