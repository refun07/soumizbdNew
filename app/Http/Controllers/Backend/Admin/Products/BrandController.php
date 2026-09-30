<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\ProductCollection;
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
        $searchKey = $request->search;
        $limit     = $request->limit ?? 15;
        $tab       = $request->tab === 'collections' ? 'collections' : 'brands';

        $brandsCount      = Brand::count();
        $collectionsCount = ProductCollection::count();
        $brands           = null;
        $collections      = null;

        # brands and collections share this page, one tab each
        if ($tab === 'collections') {
            $collections = ProductCollection::ordered()
                ->with('brands')
                ->withCount('products')
                ->when($searchKey, fn ($q) => $q->where('name', 'like', '%' . $searchKey . '%'))
                ->paginate($limit)
                ->withQueryString();
        } else {
            $brands = Brand::with('collections')
                ->withCount('products')
                ->latest()
                ->when($searchKey, fn ($q) => $q->where('name', 'like', '%' . $searchKey . '%'))
                ->paginate($limit)
                ->withQueryString();
        }

        return view('backend.admin.brands.index', compact('tab', 'brands', 'collections', 'brandsCount', 'collectionsCount', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.brands.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $brand = new Brand;
        $brand->name             = $request->name;
        $brand->thumbnail_image  = $request->thumbnail_image;
        $brand->banner_image     = $request->banner_image;
        $brand->meta_title       = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keywords    = $request->meta_keywords;
        $brand->meta_image       = $request->meta_image;

        $brand->slug = Str::slug($request->name, '-');

        $brand->save();
        $this->syncCollections($brand, $request);

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'brand_id' => $brand->id]);
        $brandTranslation->name             = $brand->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->banner_image     = $request->banner_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;
        $brandTranslation->save();

        if ($request->ajax()) {
            $brands     = Brand::isActive()->with('collections')->orderBy('name', 'ASC')->get();
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
        $lang_key = $request->lang_key ?? config('app.default_language');
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
        $langKey = $request->lang_key ?? config('app.default_language');

        if ($langKey == config("app.default_language")) {
            $brand->name = $request->name;
            $brand->thumbnail_image  = $request->thumbnail_image;
            $brand->banner_image     = $request->banner_image;
            $brand->meta_title       = $request->meta_title;
            $brand->meta_description = $request->meta_description;
            $brand->meta_keywords    = $request->meta_keywords;
            $brand->meta_image       = $request->meta_image;
            $brand->slug             = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
        }

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => $langKey, 'brand_id' => $brand->id]);
        $brandTranslation->name             = $request->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->banner_image     = $request->banner_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;

        $brand->save();
        if ($langKey == config("app.default_language")) {
            $this->syncCollections($brand, $request);
        }
        $brandTranslation->save();
        flash(translate('Brand has been updated successfully'))->success();
        return back();
    }

    # attach the picked collections; products of this brand drop a collection the brand no longer has
    private function syncCollections(Brand $brand, Request $request)
    {
        $collectionIds = ProductCollection::resolveIds($request->collection_ids);
        $brand->collections()->sync($collectionIds);

        Product::withTrashed()
            ->where('brand_id', $brand->id)
            ->whereNotNull('collection_id')
            ->whereNotIn('collection_id', $collectionIds)
            ->update(['collection_id' => null]);
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        try {
            Product::where('brand_id', $id)->update([
                'brand_id'      => NULL,
                'collection_id' => NULL,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $brand->delete();
        flash(translate('Brand has been deleted successfully'))->success();
        return redirect()->route('admin.brands.index');
    }
}
