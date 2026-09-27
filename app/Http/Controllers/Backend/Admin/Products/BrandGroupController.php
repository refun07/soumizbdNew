<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandGroup;
use Illuminate\Http\Request;

class BrandGroupController extends Controller
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

        $brandGroups = BrandGroup::ordered();
        if ($request->search != null) {
            $brandGroups = $brandGroups->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $brandGroups = $brandGroups->withCount('brands')->paginate($limit);
        return view('backend.admin.brand-groups.index', compact('brandGroups', 'searchKey'));
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:brand_groups,name,NULL,id,deleted_at,NULL',
            'sort_order' => 'nullable|integer',
        ]);

        $brandGroup             = new BrandGroup;
        $brandGroup->name       = $request->name;
        $brandGroup->slug       = BrandGroup::makeSlug($request->slug ?: $request->name);
        $brandGroup->image      = $request->image;
        $brandGroup->sort_order = $request->sort_order ?? 0;
        $brandGroup->save();

        flash(translate('Brand group has been added successfully'))->success();
        return redirect()->route('admin.brand-groups.index');
    }

    # Show the form for editing the specified resource.
    public function edit($id)
    {
        $brandGroup = BrandGroup::findOrFail($id);
        return view('backend.admin.brand-groups.edit', compact('brandGroup'));
    }

    # Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:brand_groups,name,' . $id . ',id,deleted_at,NULL',
            'sort_order' => 'nullable|integer',
        ]);

        $brandGroup             = BrandGroup::findOrFail($id);
        $brandGroup->name       = $request->name;
        $brandGroup->slug       = BrandGroup::makeSlug($request->slug ?: $request->name, $brandGroup->id);
        $brandGroup->image      = $request->image;
        $brandGroup->sort_order = $request->sort_order ?? 0;
        $brandGroup->save();

        flash(translate('Brand group has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $brandGroup = BrandGroup::findOrFail($id);

        Brand::withTrashed()->where('brand_group_id', $brandGroup->id)->update([
            'brand_group_id' => null
        ]);

        $brandGroup->delete();
        flash(translate('Brand group has been deleted successfully'))->success();
        return redirect()->route('admin.brand-groups.index');
    }
}
