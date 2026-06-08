<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:bizana_view_products'])->only(['priceCalculator']);
    }

    public function priceCalculator(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $brandId = $request->get('brand_id', '');
        $purchaseOrderId = $request->get('purchase_order_id', '');

        $products = Product::query()
            ->with(['productTranslations', 'categories', 'brand', 'purchaseOrderProductVariations' => function ($query) use ($purchaseOrderId) {
                if ($purchaseOrderId) {
                    $query->where('purchase_order_id', $purchaseOrderId);
                }
            }])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('productTranslations', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->where('brand_id', $brandId);
            })
              ->paginate($perPage);

        $products->getCollection()->transform(function ($product) use ($purchaseOrderId) {
            $purchaseCost = 0;

            if ($purchaseOrderId) {
                // Get exact cost from specific purchase order
                $poVariation = $product->purchaseOrderProductVariations->first();
                if ($poVariation) {
                    $purchaseCost = $poVariation->unit_price;
                }
            } else {
                // Use average cost as before
                $purchaseCost = $product->getAveragePurchaseCost();
            }

            $totalCost = $purchaseCost + (float)($product->packaging_cost ?? 0) + (float)($product->additional_costs ?? 0);

            return [
                'id' => $product->id,
                'name' => $product->collectTranslation('name'),
                'purchase_cost' => $purchaseCost,
                'packaging_cost' => (float)($product->packaging_cost ?? 0),
                'additional_costs' => (float)($product->additional_costs ?? 0),
                'total_cost' => $totalCost,
            ];
        });

        // Get filter options
        $categories = Category::with(['categoryTranslations'])
            ->where('level', 0) // Root categories
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->collectTranslation('name'),
                ];
            });

        $brands = Brand::with(['brandTranslations'])
            ->isActive()
            ->orderBy('name')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->collectTranslation('name'),
                ];
            });

        $purchaseOrders = PurchaseOrder::with(['supplier'])
            ->whereNull('deleted_at')
            ->orderBy('reference_code', 'desc')
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'reference_code' => 'PO-' . str_pad($po->reference_code, 6, '0', STR_PAD_LEFT),
                    'supplier_name' => $po->supplier ? $po->supplier->name : 'N/A',
                ];
            });

        return Inertia::render('products/PriceCalculator', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_order_id' => $purchaseOrderId,
            ],
            'filterOptions' => [
                'categories' => $categories,
                'brands' => $brands,
                'purchase_orders' => $purchaseOrders,
            ]
        ]);
    }

    public function updatePackagingCost(Request $request, $id)
    {
        $request->validate([
            'packaging_cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        $product = Product::findOrFail($id);
        $product->packaging_cost = $request->packaging_cost;
        $product->save();

        return response()->json(['success' => true]);
    }

    public function bulkUpdatePackagingCost(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'packaging_cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        $productIds = $request->product_ids;
        $packagingCost = $request->packaging_cost;

        // Update all products at once for better performance
        $updatedCount = Product::whereIn('id', $productIds)
            ->update(['packaging_cost' => $packagingCost]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
        ]);
    }

    public function updateAdditionalCost(Request $request, $id)
    {
        $request->validate([
            'additional_costs' => 'required|numeric|min:0|max:999999.99',
        ]);

        $product = Product::findOrFail($id);
        $product->additional_costs = $request->additional_costs;
        $product->save();

        return response()->json(['success' => true]);
    }

    public function bulkUpdateAdditionalCost(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'additional_costs' => 'required|numeric|min:0|max:999999.99',
        ]);

        $productIds = $request->product_ids;
        $additionalCosts = $request->additional_costs;

        // Update all products at once for better performance
        $updatedCount = Product::whereIn('id', $productIds)
            ->update(['additional_costs' => $additionalCosts]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Get products for price calculator (API endpoint for admin panel)
     */
    public function getProductsForPriceCalculator(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $brandId = $request->get('brand_id', '');
        $purchaseOrderId = $request->get('purchase_order_id', '');
        $page = $request->get('page', 1);

        $products = Product::query()
            ->with(['productTranslations', 'categories', 'brand', 'purchaseOrderProductVariations' => function ($query) use ($purchaseOrderId) {
                if ($purchaseOrderId) {
                    $query->where('purchase_order_id', $purchaseOrderId);
                }
            }])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('productTranslations', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->where('brand_id', $brandId);
            })
            ->paginate($perPage, ['*'], 'page', $page);

        $products->getCollection()->transform(function ($product) use ($purchaseOrderId) {
            $purchaseCost = 0;

            if ($purchaseOrderId) {
                $poVariation = $product->purchaseOrderProductVariations->first();
                if ($poVariation) {
                    $purchaseCost = $poVariation->unit_price;
                }
            } else {
                $purchaseCost = $product->getAveragePurchaseCost();
            }

            return [
                'id' => $product->id,
                'name' => $product->collectTranslation('name'),
                'purchase_cost' => $purchaseCost,
                'packaging_cost' => (float)($product->packaging_cost ?? 0),
                'additional_costs' => (float)($product->additional_costs ?? 0),
            ];
        });

        return response()->json($products);
    }

    /**
     * Get filter options for price calculator (API endpoint for admin panel)
     */
    public function getFilterOptions(Request $request)
    {
        // Get categories
        $categories = Category::with(['categoryTranslations'])
            ->where('level', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->collectTranslation('name'),
                ];
            });

        // Get brands
        $brands = Brand::with(['brandTranslations'])
            ->isActive()
            ->orderBy('name')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->collectTranslation('name'),
                ];
            });

        // Get purchase orders
        $purchaseOrders = PurchaseOrder::with(['supplier'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'reference_code' => $po->reference_code,
                    'supplier_name' => $po->supplier ? $po->supplier->name : 'Unknown',
                ];
            });

        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
            'purchase_orders' => $purchaseOrders,
        ]);
    }
}
