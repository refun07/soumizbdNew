<?php

namespace App\Http\Controllers\Backend\Admin\Resources;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProductTag;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_tags'])->only(['index']);
        $this->middleware(['permission:create_tags'])->only(['create', 'store']);
        $this->middleware(['permission:edit_tags'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_tags'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey = null;

        $tags = Tag::latest();
        if ($request->search != null) {
            $tags = $tags->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $tags = $tags->paginate($limit);
        return view('backend.admin.resources.tags.index', compact('tags', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.resources.tags.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $tag            = new Tag;
        $tag->name      = $request->name;
        $tag->save();
        if ($request->ajax()) {
            $tags       = Tag::orderBy('name', 'ASC')->get();
            $productTags = collect();
            return view('components.backend.inc.products.tag-list', compact('productTags', 'tags'))->render();
        } else {
            flash(translate('Tag has been added successfully'))->success();
            return redirect()->route('admin.tags.index');
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
            return redirect()->route('admin.tags.index');
        }
        $tag = Tag::findOrFail($id);

        return view('backend.admin.resources.tags.edit', compact('tag', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->name = $request->name;
        $tag->save();
        flash(translate('Tag has been updated successfully'))->success();
        return redirect()->route('admin.tags.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        try {
            ProductTag::where('tag_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $tag->delete();
        flash(translate('Tag has been deleted successfully'))->success();
        return redirect()->route('admin.tags.index');
    }
}
