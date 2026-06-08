<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitTranslation;
use Illuminate\Http\Request;
use Str;

class UnitController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_units'])->only(['index']);
        $this->middleware(['permission:create_units'])->only(['store']);
        $this->middleware(['permission:edit_units'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_units'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey = null;

        $units = Unit::latest();
        if ($request->search != null) {
            $units = $units->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $units = $units->paginate($limit);
        return view('backend.admin.units.index', compact('units', 'searchKey'));
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $unit = new Unit;
        $unit->name = $request->name;

        if ($request->slug != null) {
            $unit->slug = str_replace(' ', '-', $request->slug);
        } else {
            $unit->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name));
        }

        $unit->save();

        $unitTranslation = UnitTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'unit_id' => $unit->id]);
        $unitTranslation->name = $unit->name;
        $unitTranslation->save();

        if ($request->ajax()) {
            $units     = Unit::latest()->get();
            $product   = null;
            $langKey   = config('app.default_language');
            $selectedUnit = $unit->id;
            return view('components.backend.inc.products.unit-list', compact('units', 'product', 'langKey', 'selectedUnit'))->render();
        } else {

            flash(translate('Unit has been added successfully'))->success();
            return redirect()->route('admin.units.index');
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
            return redirect()->route('admin.units.index');
        }
        $unit = Unit::findOrFail($id);

        return view('backend.admin.units.edit', compact('unit', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $unit->name = $request->name;
            $unit->slug = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
        }

        $unitTranslation = UnitTranslation::firstOrNew(['lang_key' => $request->lang_key, 'unit_id' => $unit->id]);
        $unitTranslation->name = $request->name;

        $unit->save();
        $unitTranslation->save();
        flash(translate('Unit has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        try {
            Product::where('unit_id', $id)->update([
                'unit_id' => NULL
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $unit->delete();
        flash(translate('Unit has been deleted successfully'))->success();
        return redirect()->route('admin.units.index');
    }
}
