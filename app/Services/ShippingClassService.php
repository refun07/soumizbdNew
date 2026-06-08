<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShippingClass;
use Illuminate\Validation\ValidationException;

class ShippingClassService
{
    # get data
    public function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey     = null;
        $limit         = $request->limit ?? perPage();

        $shippingClasses = ShippingClass::shop()->withCount('products')->latest();




        $shippingClasses   = $shippingClasses->paginate($limit);


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'shippingClasses'     => $shippingClasses,
                'searchKey'    => $searchKey,
                'route' => routePrefix()
            ],
        ];

        return $data;
    }



    /**
     * Create a new ShippingClass
     *
     * @param array $data
     * @return ShippingClass
     */
    public function store(array $attributes)
    {
        // Create the shipping class
        $shippingClass = ShippingClass::create([
            'name'        => $attributes['name'],
            'description' => $attributes['description'] ?? null,
            'shop_id'     => shop()->id,
        ]);
        // Assign shipping_class_id to selected products
        if (!empty($attributes['selectedProductIds']) && is_array($attributes['selectedProductIds'])) {
            Product::whereIn('id', $attributes['selectedProductIds'])
                ->update(['shipping_class_id' => $shippingClass->id]);
        }

        $data = [
            'status'    => 200,
            'message'   => translate('Shipping class has been added successfully'),
            'result'    => [],
        ];
        return $data;
    }


    public function getById($id)
    {
        $shippingClass = ShippingClass::with('products')->findOrFail($id);
        return $shippingClass;
    }

    public function update(array $attributes, int $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $shippingClass = $this->getById($id);
        $shippingClass->name = $attributes['name'];
        $shippingClass->description = $attributes['description'];
        $shippingClass->save();

        if (!empty($attributes['selectedProductIds']) && is_array($attributes['selectedProductIds'])) {
            // Remove this shipping class from all previously associated products
            Product::where('shipping_class_id', $shippingClass->id)
                ->whereNotIn('id', $attributes['selectedProductIds'])
                ->update(['shipping_class_id' => null]);

            // Assign this shipping class to selected products
            Product::whereIn('id', $attributes['selectedProductIds'])
                ->update(['shipping_class_id' => $shippingClass->id]);
        } else {
            Product::where('shipping_class_id', $shippingClass->id)
                ->update(['shipping_class_id' => null]);
        }
        $data = [
            'status'    => 200,
            'message'   => translate('Shipping Class has been updated successfully'),
            'result'    => [],
        ];
        return $data;
    }

    public function destroy(int $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];
        $shippingClass = $this->getById($id);

        // Detach shipping class from associated product products
        Product::where('shipping_class_id', $shippingClass->id)
            ->update(['shipping_class_id' => null]);

        // Delete the shipping class
        $shippingClass->delete();

        $data = [
            'status'        => 200,
            'message'       => translate('Shipping class has been deleted successfully'),
            'result'        => [],
        ];
        return $data;
    }

    public function getProducts($request)
    {
        $searchKey = $request->searchKey;
        $products = Product::shop()->select('id', 'shop_id', 'name')->where('name', 'like', '%' . $searchKey . '%')->paginate(perPage(20));
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'products'     => $products,
            ],
        ];

        return $data;
    }
}
