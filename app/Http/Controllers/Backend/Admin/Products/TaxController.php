<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProductTax;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_taxes'])->only(['index']);
        $this->middleware(['permission:create_taxes'])->only(['store']);
        $this->middleware(['permission:edit_taxes'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_taxes'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey = null;

        $taxes = Tax::latest();
        if ($request->search != null) {
            $taxes = $taxes->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $taxes = $taxes->paginate($limit);
        return view('backend.admin.taxes.index', compact('taxes', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.taxes.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $tax            = new Tax;
        $tax->name      = $request->name;
        $tax->is_active = 1;
        $tax->save();
        flash(translate('Tax has been added successfully'))->success();
        return redirect()->route('admin.taxes.index');
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
            return redirect()->route('admin.taxes.index');
        }
        $tax = Tax::findOrFail($id);

        return view('backend.admin.taxes.edit', compact('tax', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->name = $request->name;
        $tax->save();
        flash(translate('Tax has been updated successfully'))->success();
        return redirect()->route('admin.taxes.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        try {
            ProductTax::where('tax_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $tax->delete();
        flash(translate('Tax has been deleted successfully'))->success();
        return redirect()->route('admin.taxes.index');
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
        $tax = Tax::findOrFail($request->id);
        $tax->is_active = $request->isActive;
        $tax->save();

        return $data;
    }
}
