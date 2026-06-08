<?php

namespace App\Http\Controllers\Backend\Admin\Blogs;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Language;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_blog_categories'])->only(['index']);
        $this->middleware(['permission:create_blog_categories'])->only(['store']);
        $this->middleware(['permission:edit_blog_categories'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_blog_categories'])->only(['destroy']);
    }

    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey      = null;
        $isPublished    = null;
        $limit          = $request->limit ?? perPage();

        $blogCategories = BlogCategory::latest();
        if ($request->search != null) {
            $blogCategories = $blogCategories->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $blogCategories = $blogCategories->paginate($limit);

        return view('backend.admin.blogs-categories.index', compact('blogCategories', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.blogs-categories.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $blogCategory           = new BlogCategory;
        $blogCategory->name     = $request->name;
        $blogCategory->save();

        $blogCategoryTranslation        = BlogCategoryTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'blog_category_id' => $blogCategory->id]);
        $blogCategoryTranslation->name  = $blogCategory->name;
        $blogCategoryTranslation->save();

        flash(translate('Blog category has been added successfully'))->success();
        return redirect()->route('admin.blogCategories.index');
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
            return redirect()->route('admin.blogs-categories.index');
        }
        $blogCategory = BlogCategory::find((int) $id);
        return view('backend.admin.blogs-categories.edit', compact('blogCategory', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {

        $blogCategory = BlogCategory::find((int) $id);
        if ($blogCategory) {
            if ($request->lang_key == config('app.default_language')) {
                $blogCategory->name = $request->name;
                $blogCategory->save();
            }
            $blogCategoryTranslation = BlogCategoryTranslation::firstOrNew(['lang_key' => $request->lang_key, 'blog_category_id' => $blogCategory->id]);
            $blogCategoryTranslation->name = $request->name;
            $blogCategoryTranslation->save();
        }

        flash(translate('Blog category has been updated successfully'))->success();
        return redirect()->route('admin.blogCategories.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $blogCategory = BlogCategory::find($id);

        if ($blogCategory) {
            try {
                $blogCategory->blogCategoryTranslations()->delete();
            } catch (\Throwable $th) {
                //throw $th;
            }

            $blogCategory->delete();
        }
        flash(translate('Blog category has been deleted successfully'))->success();
        return redirect()->route('admin.blogCategories.index');
    }
}
