<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    # get all with pagination
    public function index(Request $request)
    {
        $limit      = $request->limit ?? 9;

        $blogs      = Blog::isPublished();

        // by search keyword
        if ($request->searchKey != null) {
            $blogs = $blogs->where('title', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        if ($request->blogCategoryId != null) {
            $blogs = $blogs->where('blog_category_id', $request->blogCategoryId);
        }

        $blogs          = $blogs->paginate($limit);
        $blogCategories = BlogCategory::all();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'blogs'             => BlogResource::collection($blogs)->response()->getData(true),
                'blogCategories'    => BlogCategoryResource::collection($blogCategories),
            ]
        ];
    }

    # get details
    public function show($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $blogCategories = BlogCategory::all();

        $blog = Blog::isPublished()->where('slug', $slug)->first();
        if (!is_null($blog)) {
            $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)->whereNot('id', $blog->id)->take(5)->get();
            $data['result']['blog']         = new BlogResource($blog);
            $data['result']['relatedBlogs'] = BlogResource::collection($relatedBlogs);
            $data['result']['blogCategories'] = BlogCategoryResource::collection($blogCategories);
        } else {
            $data['message'] = translate('Property not found');
        }

        return $data;
    }
}
