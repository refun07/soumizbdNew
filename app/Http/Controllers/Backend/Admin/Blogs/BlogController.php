<?php

namespace App\Http\Controllers\Backend\Admin\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Str;

class BlogController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_blogs'])->only(['index']);
        $this->middleware(['permission:create_blogs'])->only(['store']);
        $this->middleware(['permission:edit_blogs'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_blogs'])->only(['destroy']);
    }

    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey      = null;
        $isPublished    = null;
        $limit          = $request->limit ?? perPage();

        $blogs = Blog::latest();
        if ($request->search != null) {
            $blogs = $blogs->where('title', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }
        if ($request->isPublished != null) {
            $blogs     = $blogs->where('is_published', $request->isPublished);
            $isPublished    = $request->isPublished;
        }

        $blogs = $blogs->paginate($limit);
        return view('backend.admin.blogs.index', compact('blogs', 'searchKey', 'isPublished'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $blogCategories = BlogCategory::orderBy('name', 'ASC')->get();
        return view('backend.admin.blogs.create', compact('blogCategories'));
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $blog   = new Blog;
        $this->__insertDataToDb($blog, $request);
        flash(translate('Blog has been added successfully'))->success();
        return redirect()->route('admin.blogs.index');
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
            return redirect()->route('admin.blogs.index');
        }
        $blog           = Blog::findOrFail((int) $id);
        $blogCategories = BlogCategory::orderBy('name', 'ASC')->get();
        return view('backend.admin.blogs.edit', compact('blog', 'lang_key', 'blogCategories'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail((int) $id);
        $this->__insertDataToDb($blog, $request);
        flash(translate('Blog has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $blog = Blog::findOrFail((int) $id);
        try {
            $blog->blogTranslations()->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $blog->delete();
        flash(translate('Blog has been deleted successfully'))->success();
        return redirect()->route('admin.blogs.index');
    }

    # update status
    public function updateStatus(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $blog = Blog::findOrFail((int) $request->id);
        $blog->is_published = $request->isActive;
        $blog->save();

        return $data;
    }

    # insert data to db
    private function __insertDataToDb($blog, $request)
    {

        $langKey = $request->lang_key ?? config('app.default_language');

        if ($langKey == config('app.default_language')) {
            $blog->title                = $request->title;
            $blog->blog_category_id     = $request->blog_category_id;
            $blog->slug                 = $request->slug ? Str::slug($request->slug) :  Str::slug($request->title);
            $blog->short_description    = $request->short_description;
            $blog->description          = $request->description;
            $blog->thumbnail_image      = $request->thumbnail_image;
            $blog->banner               = $request->banner;

            $blog->meta_title           = $request->meta_title;
            $blog->meta_keywords        = $request->meta_keywords;
            $blog->meta_description     = $request->meta_description;
            $blog->meta_image           = $request->meta_image;
        }

        $blog->save();

        // translation
        $blogTranslation                    = BlogTranslation::firstOrNew(['lang_key' => $langKey, 'blog_id' => $blog->id]);
        $blogTranslation->title             = $request->title;
        $blogTranslation->short_description = $request->short_description;
        $blogTranslation->description       = $request->description;
        $blogTranslation->save();
    }
}
