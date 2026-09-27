<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\Models\Product;
use App\Models\BrandTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Str;

class BrandController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_brands'])->only(['index']);
        $this->middleware(['permission:view_brands'])->only(['categories']);
        $this->middleware(['permission:create_brands'])->only(['store']);
        $this->middleware(['permission:edit_brands'])->only(['edit', 'update', 'updateCategories']);
        $this->middleware(['permission:delete_brands'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;
        $categories = Category::isRoot()->orderBy('sorting_order_level', 'ASC')->get();

        $brands = Brand::latest();
        if ($request->search != null) {
            $brands = $brands->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $brands = $brands->withCount('products')->paginate($limit);
        return view('backend.admin.brands.index', compact('brands', 'searchKey', 'categories'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $categories = Category::isRoot()->orderBy('sorting_order_level', 'ASC')->get();
        return view('backend.admin.brands.create', compact('categories'));
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
        $brand->meta_title       = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        $brand->meta_keywords    = $request->meta_keywords;
        $brand->meta_image       = $request->meta_image;

        if (Schema::hasColumn('brands', 'banner_image')) {
            $brand->banner_image = $request->banner_image;
        }

        $brand->slug = Str::slug($request->name, '-');

        $brand->save();
        $this->syncCategories($brand, $request);

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'brand_id' => $brand->id]);
        $brandTranslation->name             = $brand->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;

        if (Schema::hasColumn('brand_translations', 'banner_image')) {
            $brandTranslation->banner_image = $request->banner_image;
        }

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
        $lang_key = $request->lang_key ?? config('app.default_language');
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.brands.index');
        }
        $brand = Brand::findOrFail($id);
        $categories = Category::isRoot()->orderBy('sorting_order_level', 'ASC')->get();

        return view('backend.admin.brands.edit', compact('brand', 'lang_key', 'categories'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $langKey = $request->lang_key ?? config('app.default_language');

        if ($langKey == config("app.default_language")) {
            $brand->name = $request->name;
            $brand->thumbnail_image  = $request->thumbnail_image;
            $brand->meta_title       = $request->meta_title;
            $brand->meta_description = $request->meta_description;
            $brand->meta_keywords    = $request->meta_keywords;
            $brand->meta_image       = $request->meta_image;
            $brand->slug             = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');

            if (Schema::hasColumn('brands', 'banner_image')) {
                $brand->banner_image = $request->banner_image;
            }
        }

        $brandTranslation = BrandTranslation::firstOrNew(['lang_key' => $langKey, 'brand_id' => $brand->id]);
        $brandTranslation->name             = $request->name;
        $brandTranslation->thumbnail_image  = $request->thumbnail_image;
        $brandTranslation->meta_title       = $request->meta_title;
        $brandTranslation->meta_description = $request->meta_description;
        $brandTranslation->meta_keywords    = $request->meta_keywords;
        $brandTranslation->meta_image       = $request->meta_image;

        if (Schema::hasColumn('brand_translations', 'banner_image')) {
            $brandTranslation->banner_image = $request->banner_image;
        }

        $brand->save();
        if ($langKey == config("app.default_language")) {
            $this->syncCategories($brand, $request);
        }
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

    public function categories(Request $request)
    {
        $brands = Brand::isActive()->orderBy('name', 'ASC')->get();
        $selectedBrand = $request->brand_id
            ? Brand::isActive()->where('id', $request->brand_id)->first()
            : $brands->first();

        $categories = Category::isRoot()->orderBy('sorting_order_level', 'ASC')->get();
        $selectedCategories = $selectedBrand && Schema::hasTable('brand_categories')
            ? $selectedBrand->categories()->pluck('categories.id')->all()
            : [];

        return view('backend.admin.brands.categories', compact(
            'brands',
            'selectedBrand',
            'categories',
            'selectedCategories'
        ));
    }

    public function updateCategories(Request $request)
    {
        $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $brand = Brand::findOrFail($request->brand_id);
        $this->syncCategories($brand, $request);

        flash(translate('Brand categories have been updated successfully'))->success();
        return redirect()->route('admin.brands.categories.index', ['brand_id' => $brand->id]);
    }

    private function categoryIds(Request $request): array
    {
        return collect($request->input('category_ids', []))
            ->filter()
            ->map(function ($value) {
                if (is_numeric($value)) {
                    return Category::whereKey((int) $value)->value('id');
                }

                if (!is_string($value) || !str_starts_with($value, '__new__:')) {
                    return null;
                }

                $name = trim(substr($value, strlen('__new__:')));
                if ($name === '') {
                    return null;
                }

                $category = Category::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
                if (!$category) {
                    $category = Category::create([
                        'name' => $name,
                        'slug' => $this->uniqueCategorySlug($name),
                        'parent_id' => 0,
                        'level' => 0,
                        'sorting_order_level' => 0,
                    ]);

                    CategoryTranslation::create([
                        'category_id' => $category->id,
                        'lang_key' => config('app.default_language'),
                        'name' => $name,
                    ]);
                }

                return $category->id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function uniqueCategorySlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'category';
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function syncCategories(Brand $brand, Request $request): void
    {
        if (!Schema::hasTable('brand_categories')) {
            return;
        }

        $brand->categories()->sync($this->categoryIds($request));
    }
}
