<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\ProductCategory;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\CategoryTrait;

class CategoryController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_categories'])->only(['index']);
        $this->middleware(['permission:create_categories'])->only(['create', 'store']);
        $this->middleware(['permission:edit_categories'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_categories'])->only(['destroy']);
    }

    # category list
    public function index(Request $request)
    {
        $searchKey = null;
        $parentCategory = null;
        $categories = Category::orderBy('sorting_order_level', 'asc');
        if ($request->search != null) {
            $categories = $categories->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->parent_id) {
            $parentCategory = Category::findOrFail($request->parent_id);
            $categories = $categories->where('parent_id', $request->parent_id);
        } else {
            $categories = $categories->where('parent_id', 0);
        }

        $categories = $categories->withCount('childrenCategories')->paginate(perPage());
        return view('backend.admin.categories.index', compact('categories', 'searchKey', 'parentCategory'));
    }

    # return view of create form
    public function create()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('sorting_order_level', 'desc')
            ->with('childrenCategories')
            ->get();
        $variations = Variation::isActive()->latest()->get();
        return view('backend.admin.categories.create', compact('categories', 'variations'));
    }

    # return view of create form in modal
    public function initCreateModal()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('sorting_order_level', 'desc')
            ->with('childrenCategories')
            ->get();
        $variations = Variation::isActive()->latest()->get();
        return view('components.backend.forms.category-form-modal', compact('categories', 'variations'))->render();
    }

    # add new data
    public function store(Request $request)
    {
        $category                       = new Category;
        $category->name                 = $request->name;
        $category->sorting_order_level  = 0;
        $category->thumbnail_image      = $request->thumbnail_image;

        if ($request->sorting_order_level != null) {
            $category->sorting_order_level = $request->sorting_order_level;
        }

        if ($request->parent_id != "0") {
            $category->parent_id    = $request->parent_id;
            $parent                 = Category::find($request->parent_id);
            $category->level        = $parent->level + 1;
        } else {
            $category->parent_id    = $request->parent_id;
            $category->level        = 0;
        }

        if ($request->slug != null) {
            $category->slug = Str::slug($request->slug);
        } else {
            $category->slug = Str::slug($request->name);
        }

        $category->meta_title         = $request->meta_title;
        $category->meta_description   = $request->meta_description;
        $category->meta_keywords      = $request->meta_keywords;
        $category->meta_image         = $request->meta_image;

        $category->save();

        # variations
        $category->variations()->sync($request->variation_ids);

        $categoryTranslation                    = CategoryTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'category_id' => $category->id]);
        $categoryTranslation->name              = $category->name;
        $categoryTranslation->thumbnail_image   = $request->thumbnail_image;
        $categoryTranslation->meta_title        = $category->meta_title;
        $categoryTranslation->meta_description  = $category->meta_description;
        $categoryTranslation->meta_keywords     = $category->meta_keywords;
        $categoryTranslation->meta_image        = $request->meta_image;

        $category->save();
        $categoryTranslation->save();

        if ($request->ajax()) {
            $categories = Category::where('parent_id', 0)
                ->orderBy('name', 'ASC')
                ->with('childrenCategories')
                ->get();
            $productCategories = collect();
            $langKey = config('app.default_language');
            $selectedCategories = [];
            if ($request->selected_categories != "") {
                $selectedCategories = explode(',', $request->selected_categories);
                array_push($selectedCategories, $category->id);
            }
            return view('components.backend.inc.products.category-list', compact('categories', 'productCategories', 'langKey', 'selectedCategories'))->render();
        } else {
            flash(translate('Category has been added successfully'))->success();
            return redirect()->route('admin.categories.index');
        }
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::where('is_active', 1)->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.categories.index');
        }

        $category = Category::findOrFail($id);
        $categories = Category::where('parent_id', 0)
            ->where('id', '!=', $category->id)
            ->orderBy('sorting_order_level', 'desc')
            ->with('childrenCategories')
            ->whereNotIn('id', CategoryTrait::childrenIds($category->id, true))
            ->where('level', '<=', $category->level)
            ->orderBy('name', 'asc')
            ->get();

        $variations = Variation::isActive()->latest()->get();
        return view('backend.admin.categories.edit', compact('category', 'categories', 'variations', 'lang_key'));
    }

    # update category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $category->name             = $request->name;
            $category->thumbnail_image  = $request->thumbnail_image;

            $category->slug = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
            if ($request->sorting_order_level != null) {
                $category->sorting_order_level = $request->sorting_order_level;
            }

            $oldLevel = $category->level;

            if ((int) $request->parent_id != 0) {
                $category->parent_id    = $request->parent_id;
                $parent                 = Category::find((int) $request->parent_id);
                $category->level        = $parent->level + 1;
            } else {
                $category->parent_id    = 0;
                $category->level        = 0;
            }

            if ($category->level > $oldLevel) {
                CategoryTrait::downLevelOneStep($category->id);
            } elseif ($category->level < $oldLevel) {
                CategoryTrait::upLevelOneStep($category->id);
            }

            $category->meta_title       = $request->meta_title;
            $category->meta_description = $request->meta_description;
            $category->meta_keywords    = $request->meta_keywords;
            $category->meta_image       = $request->meta_image;
            $category->save();
        }


        $categoryTranslation = CategoryTranslation::firstOrNew(['lang_key' => $request->lang_key, 'category_id' => $category->id]);
        $categoryTranslation->name              = $request->name;
        $categoryTranslation->thumbnail_image   = $request->thumbnail_image;
        $categoryTranslation->meta_title        = $request->meta_title;
        $categoryTranslation->meta_description  = $request->meta_description;
        $categoryTranslation->meta_keywords     = $request->meta_keywords;
        $categoryTranslation->meta_image        = $request->meta_image;

        $category->save();
        $categoryTranslation->save();

        # variations
        $category->variations()->sync($request->variation_ids);
        flash(translate('Category has been updated successfully'))->success();
        return back();
    }


    # delete category
    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();
        if (!is_null($category)) {
            CategoryTrait::moveChildrenToParent($category->id);

            try {
                ProductCategory::where('category_id', $category->id)->delete();
            } catch (\Throwable $th) {
            }

            $category->delete();
        }
        flash(translate('Category has been deleted successfully'))->success();
        return back();
    }
}
