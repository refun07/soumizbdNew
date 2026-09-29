<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_brands'])->only(['index']);
        $this->middleware(['permission:create_brands'])->only(['store']);
        $this->middleware(['permission:edit_brands'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_brands'])->only(['destroy']);
    }

    # collections are listed on the brands page
    public function index()
    {
        return redirect()->route('admin.brands.index', ['tab' => 'collections']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:collections,name,NULL,id,deleted_at,NULL',
            'sort_order' => 'nullable|integer',
        ]);

        $collection             = new ProductCollection;
        $collection->name       = $request->name;
        $collection->slug       = ProductCollection::makeSlug($request->slug ?: $request->name);
        $collection->image      = $request->image;
        $collection->sort_order = $request->sort_order ?? 0;
        $collection->save();

        flash(translate('Collection has been added successfully'))->success();
        return redirect()->route('admin.brands.index', ['tab' => 'collections']);
    }

    # Show the form for editing the specified resource.
    public function edit($id)
    {
        $collection = ProductCollection::findOrFail($id);
        return view('backend.admin.collections.edit', compact('collection'));
    }

    # Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:collections,name,' . $id . ',id,deleted_at,NULL',
            'sort_order' => 'nullable|integer',
        ]);

        $collection             = ProductCollection::findOrFail($id);
        $collection->name       = $request->name;
        $collection->slug       = ProductCollection::makeSlug($request->slug ?: $request->name, $collection->id);
        $collection->image      = $request->image;
        $collection->sort_order = $request->sort_order ?? 0;
        $collection->save();

        flash(translate('Collection has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $collection = ProductCollection::findOrFail($id);

        Product::withTrashed()->where('collection_id', $collection->id)->update([
            'collection_id' => null
        ]);
        $collection->brands()->detach();

        $collection->delete();
        flash(translate('Collection has been deleted successfully'))->success();
        return redirect()->route('admin.brands.index', ['tab' => 'collections']);
    }
}
