<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Language;
use Carbon\Carbon;
use Str;

class CampaignService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;
        $campaigns = Campaign::shop()->latest();

        // for seller campaign controller
        // if (user()->user_type == "seller") { $campaigns = $campaigns->orWhere('type', 'mega');}

        if ($request->search != null) {
            $campaigns = $campaigns->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $campaigns = $campaigns->paginate(perPage());

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'campaigns'   => $campaigns,
                'searchKey'   => $searchKey,
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
            $campaign                       = new Campaign;
            $campaign->name                 = $request->name;
            $campaign->slug                 = Str::slug($request->name, '-');
            $campaign->short_description    = $request->short_description;

            // if (user()->user_type != "seller") { $campaign->type = $request->type; }

            $campaign->shop_id          = shopId();
            $campaign->thumbnail_image  = $request->thumbnail_image;
            $campaign->banner           = $request->banner;
            $campaign->default_discount_type    = $request->default_discount_type;
            $campaign->default_discount_value   = $request->default_discount_value;

            if (Str::contains($request->date_range, '-')) {
                $date_var = explode(" - ", $request->date_range);
            } else {
                $date_var = [date("d-m-Y"), date("d-m-Y")];
            }
            $campaign->start_date = strtotime($date_var[0]);
            $campaign->end_date = strtotime($date_var[1]);

            $campaign->save();

            $data = [
                'status'    => 200,
                'message'   => translate('Campaign has been added successfully'),
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
                $data = [
                    'status'    => 403,
                    'message'   => translate('Language you are trying to translate is not available or not active'),
                    'result'    => [],
                ];
                return $data;
            }

            $campaign = Campaign::findOrFail($id);

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'campaign'      => $campaign,
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
            $campaign                     = Campaign::findOrFail($id);
            $campaign->name               = $request->name;
            $campaign->slug               = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
            $campaign->short_description  = $request->short_description;

            // if (user()->user_type != "seller") { $campaign->type = $request->type; }

            $campaign->shop_id          = shopId();
            $campaign->thumbnail_image  = $request->thumbnail_image;
            $campaign->banner           = $request->banner;
            $campaign->default_discount_type    = $request->default_discount_type;
            $campaign->default_discount_value   = $request->default_discount_value;

            if (Str::contains($request->date_range, '-')) {
                $date_var = explode(" - ", $request->date_range);
            } else {
                $date_var = [date("d-m-Y"), date("d-m-Y")];
            }

            $campaign->start_date = strtotime($date_var[0]);
            $campaign->end_date = strtotime($date_var[1]);

            $campaign->save();

            $data = [
                'status'    => 200,
                'message'   => translate('Campaign has been updated successfully'),
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
            $campaign   = Campaign::where('id', $id)->first();
            $today      = Carbon::now();

            if ($today->between(date('d-m-Y H:i:s', $campaign->start_date), date('d-m-Y H:i:s', $campaign->end_date))) {
                $data = [
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Active campaign can not be deleted'),
                    'result'    => [],
                ];
                return $data;
            }

            try {
                CampaignProduct::where('campaign_id', $campaign->id)->delete();
            } catch (\Throwable $th) {
            }
            $campaign->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Campaign has been deleted successfully'),
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

    # update status
    public static function updateStatus($request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];

        try {
            $campaign = Campaign::findOrFail($request->id);
            $campaign->is_published = $request->isActive;
            $campaign->save();

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
