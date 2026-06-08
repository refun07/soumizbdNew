<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\VariationTranslation;
use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\Request;
use Str;

class VariationController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_variations'])->only(['index']);
        $this->middleware(['permission:create_variations'])->only(['store']);
        $this->middleware(['permission:edit_variations'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_variations'])->only(['destroy']);
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $variations = Variation::latest();
        if ($request->search != null) {
            $variations = $variations->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $variations = $variations->withCount('variationValues')->paginate($limit);
        return view('backend.admin.variations.index', compact('variations', 'searchKey'));
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $variation              = new Variation;
        $variation->name        = $request->name;
        $variation->is_color    = $request->is_color ?? 0;
        $variation->save();

        $variationTranslation = VariationTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'variation_id' => $variation->id]);
        $variationTranslation->name = $variation->name;
        $variationTranslation->save();

        flash(translate('Variation has been added successfully'))->success();
        return redirect()->route('admin.variations.index');
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
            return redirect()->route('admin.variations.index');
        }
        $variation = Variation::findOrFail($id);

        return view('backend.admin.variations.edit', compact('variation', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $variation = Variation::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $variation->name = $request->name;
        }

        $variation->is_color = $request->is_color ?? 0;

        $variationTranslation = VariationTranslation::firstOrNew(['lang_key' => $request->lang_key, 'variation_id' => $variation->id]);
        $variationTranslation->name = $request->name;

        $variation->save();
        $variationTranslation->save();
        flash(translate('Variation has been updated successfully'))->success();
        return back();
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
        $variation = Variation::findOrFail($request->id);
        $variation->is_active = $request->isActive;
        $variation->save();

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $variation          = Variation::findOrFail($id);
        $variationValueIds  = VariationValue::where('variation_id', $id)->pluck('id');

        try {
            ProductVariationCombination::whereIn('variation_value_id', $variationValueIds)->delete();
            VariationValue::where('variation_id', $id)->delete();

            $code = $id . ':';
            ProductVariation::where('code', 'like', '%' . $code . '%')->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }

        $variation->delete();
        flash(translate('Variation has been deleted successfully'))->success();
        return redirect()->route('admin.variations.index');
    }
}
