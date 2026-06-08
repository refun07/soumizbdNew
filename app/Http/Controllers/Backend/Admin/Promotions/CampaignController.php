<?php

namespace App\Http\Controllers\Backend\Admin\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_campaigns'])->only(['index']);
        $this->middleware(['permission:create_campaigns'])->only(['create', 'store']);
        $this->middleware(['permission:edit_campaigns'])->only(['edit', 'show', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_campaigns'])->only(['destroy']);
    }

    # resource list
    public function index(Request $request)
    {
        $response = CampaignService::index($request);


        if ($response['status'] == 200) {
            return view('backend.admin.campaigns.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # return view of create form
    public function create()
    {
        return view('backend.admin.campaigns.create');
    }

    # add new data
    public function store(Request $request)
    {
        $response = CampaignService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.campaigns.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = CampaignService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.admin.campaigns.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.campaigns.index');
    }

    # return show page
    public function show(Request $request, $id)
    {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],
        ];

        $campaign = Campaign::findOrFail($id);
        return view('backend.admin.campaigns.show', compact('campaign', 'settings'));
    }

    # update category
    public function update(Request $request, $id)
    {
        $response = CampaignService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.campaigns.index');
    }

    # delete category
    public function destroy($id)
    {
        $response = CampaignService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.campaigns.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.campaigns.index');
    }

    # update status
    public function updateStatus(Request $request)
    {
        $response = CampaignService::updateStatus($request);
        return $response;
    }
}
