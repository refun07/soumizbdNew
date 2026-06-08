<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Request;

class CategoryController extends Controller
{
    # get all categories
    public function categories(Request $request)
    {
        $categories     = Category::with('childrenCategories')->orderBy('sorting_order_level', 'asc')->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories' => CategoryResource::collection($categories)
            ],
        ];
    }
}
