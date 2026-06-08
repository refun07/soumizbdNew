<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Language;
use App\Models\Product;
use App\Models\BrandTranslation;
use Illuminate\Http\Request;
use Str;

class BrandController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_brands'])->only(['index']);
        $this->middleware(['permission:create_brands'])->only(['store']);
        $this->middleware(['permission:edit_brands'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_brands'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $brands = Brand::latest();
        if ($request->search != null) {
            $brands = $brands->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $brands = $brands->withCount('products')->paginate($limit);
        return view('backend.admin.brands.index', compact('brands', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.brands.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->name             = $request->name;
        $brand->thumbnail_image  = $request->thumbnail_image;
        $brand->meta_title       = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keywords    = $request->meta_keywords;
        $brand->meta_image       = $request->meta_image;

        $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name));

        $brand->save();

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'brand_id' => $brand->id]);
        $brandTranslation->name             = $brand->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;
        $brandTranslation->save();

        if ($request->ajax()) {
            $brands     = Brand::isActive()->orderBy('name', 'ASC')->get();
            $product = null;
            $langKey = config('app.default_language');
            $selectedBrand = $brand->id;
            return view('components.backend.inc.products.brand-list', compact('brands', 'product', 'langKey', 'selectedBrand'))->render();
        } else {

            flash(translate('Brand has been added successfully'))->success();
            return redirect()->route('admin.brands.index');
        }
    }

    # Display the specified resource.
    public function show($id)
    {
        //
    }

    # Show the form for editing the specified resource.
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.brands.index');
        }
        $brand = Brand::findOrFail($id);

        return view('backend.admin.brands.edit', compact('brand', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $brand->name = $request->name;
            $brand->thumbnail_image  = $request->thumbnail_image;
            $brand->meta_title       = $request->meta_title;
            $brand->meta_description = $request->meta_description;
            $brand->meta_keywords    = $request->meta_keywords;
            $brand->meta_image       = $request->meta_image;
            $brand->slug             = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
        }

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => $request->lang_key, 'brand_id' => $brand->id]);
        $brandTranslation->name             = $request->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;

        $brand->save();
        $brandTranslation->save();
        flash(translate('Brand has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        try {
            Product::where('brand_id', $id)->update([
                'brand_id' => NULL
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $brand->delete();
        flash(translate('Brand has been deleted successfully'))->success();
        return redirect()->route('admin.brands.index');
    }
}
