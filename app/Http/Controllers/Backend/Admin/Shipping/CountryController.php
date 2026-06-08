<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Cache;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_countries'])->only(['index']);
        $this->middleware(['permission:edit_countries'])->only(['updateStatus']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $countries = Country::orderBy('is_active', 'desc');

        if ($request->search != null) {
            $countries = $countries->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $countries = $countries->paginate($limit);
        return view('backend.admin.shipping.countries.index', compact('countries', 'searchKey'));
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
        $country = Country::findOrFail($request->id);
        $country->is_active = !$country->is_active;
        if ($country->save()) {

            $stateQuery = State::where('country_id', $country->id);
            $stateQuery->update([
                'is_active' => $country->is_active
            ]);
            $stateIds   = $stateQuery->where('is_active', 1)->pluck('id');

            $cityQuery  = City::whereIn('state_id', $stateIds);
            $cityQuery->update([
                'is_active' => $country->is_active
            ]);
            $cityIds   = $cityQuery->where('is_active', 1)->whereIn('state_id', $stateIds)->pluck('id');

            $areaQuery  = Area::whereIn('city_id', $cityIds);
            $areaQuery->update([
                'is_active' => $country->is_active
            ]);
        }

        return $data;
    }
}
