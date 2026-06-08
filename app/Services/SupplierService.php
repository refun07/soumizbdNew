<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Supplier;

class SupplierService
{
    # get data
    public static function index($request, $limit)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;

        $suppliers = Supplier::shop()->latest();
        if ($request->search != null) {
            $suppliers = $suppliers->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $suppliers = $suppliers->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'suppliers'   => $suppliers,
                'searchKey' => $searchKey,
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {

            $supplier           = new Supplier;
            $supplier->shop_id  = shop()->id;
            $supplier->name     = $request->name;
            $supplier->email    = $request->email;
            $supplier->phone_no = $request->phone_no;
            $supplier->address  = $request->address;

            $supplier->payment_details = $request->payment_details;
            $supplier->save();

            $data = [
                'status'    => 200,
                'message'   => translate('Supplier has been added successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # return view of edit form
    public static function edit($request, $id)
    {
        try {
            $lang_key = $request->lang_key;
            $language = Language::where('is_active', 1)->where('code', $lang_key)->first();

            if (!$language) {
                if (!$language) {
                    $data = [
                        'status'    => 403,
                        'message'   => translate('Language you are trying to translate is not available or not active'),
                        'result'    => [],
                    ];
                    return $data;
                }
            }

            $supplier = Supplier::findOrFail($id);

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'supplier'      => $supplier,
                    'lang_key'      => $lang_key,
                ],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->name = $request->name;
            $supplier->save();
            $data = [
                'status'    => 200,
                'message'   => translate('Supplier has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # delete data
    public static function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Supplier has been deleted successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
