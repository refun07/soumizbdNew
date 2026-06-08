<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view_cities'])->only(['index']);
        $this->middleware(['permission:create_cities'])->only(['store', 'resetToDefault']);
        $this->middleware(['permission:edit_cities'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_cities'])->only(['destroy', 'resetToDefault']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey = null;
        $stateId   = null;
        $zoneId   = null;
        $sortBy = $request->get('sort_by', 'is_active');
        $sortOrder = $request->get('sort_order', 'desc');
        $limit     = $request->limit ?? perPage();
        $cities    = City::query();

        if ($request->search != null) {
            $cities     = $cities->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }

        if ($request->stateId != null) {
            $cities->where('state_id', $request->stateId);
            $stateId = $request->stateId;
        }

        if ($request->zoneId != null) {
            $cities->where('zone_id', $request->zoneId);
            $zoneId = $request->zoneId;
        }

        // Apply sorting
        if ($sortBy === 'name') {
            $cities->orderBy('name', $sortOrder);
        } else {
            $cities->orderBy('is_active', 'desc');
        }

        $cities = $cities->paginate($limit)->appends($request->query());
        $states = State::isActive()->get();
        $zones = Zone::isActive()->get();

        return view('backend.admin.shipping.cities.index', compact('cities', 'searchKey', 'stateId', 'states', 'zones', 'sortBy', 'sortOrder'));
    }

    # add new data
    public function store(Request $request)
    {
        $city = new City;
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->zone_id    = $request->zone_id;
        $city->is_active   = 1;
        $city->save();
        flash(translate('State has been added successfully'))->success();
        return redirect()->route('admin.cities.index');
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key   = $request->lang_key;
        $states     = State::isActive()->get();
        $city       = City::findOrFail($id);
        $zones = Zone::isActive()->get();
        return view('backend.admin.shipping.cities.edit', compact('states', 'city', 'lang_key', 'zones'));
    }

    # update state
    public function update(Request $request, $id)
    {
        $city              = City::findOrFail((int) $id);
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->zone_id    = $request->zone_id;
        $city->save();
        flash(translate('State has been updated successfully'))->success();
        return redirect()->route('admin.cities.index');
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
        $city = City::findOrFail($request->id);
        $city->is_active = !$city->is_active;
        if ($city->save()) {
            // Cache::forget('countries'); 
            Area::where('city_id', $city->id)->update([
                'is_active' => $city->is_active
            ]);
        }

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        flash(translate('State has been deleted successfully'))->success();
        return redirect()->route('admin.cities.index');
    }

    # reset cities to default from Bangladesh districts CSV
    public function resetToDefault()
    {
        $zone = Zone::where('name', 'default')->first();
        if (!$zone) {
            $zone = Zone::create(['name' => 'default', 'is_active' => 1]);
        }

        $country = Country::where('name', 'Bangladesh')->first();
        if (!$country) {
            Log::error('Bangladesh country not found');
            flash(translate('Bangladesh country not found'))->error();
            return redirect()->back();
        }

        $csvPath = public_path('Bangladesh_Upazila_List_EN.csv');
        if (!file_exists($csvPath)) {
            Log::error('CSV file not found', ['path' => $csvPath]);
            flash(translate('CSV file not found'))->error();
            return redirect()->back();
        }

        City::query()->delete();

        $districts = [];
        if (($handle = fopen($csvPath, 'r')) !== FALSE) {
            fgetcsv($handle);
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $district = trim($data[1]);
                if (!empty($district)) {
                    $districts[$district] = $district;
                }
            }
            fclose($handle);
        }
        sort($districts);

        $created = 0;
        foreach ($districts as $districtName) {
            $state = State::where('name', $districtName)
                ->where('country_id', $country->id)
                ->first();

            if (!$state) {
                $state = State::create([
                    'name' => $districtName,
                    'country_id' => $country->id,
                    'is_active' => 1
                ]);
            }

            $city = City::where('name', $districtName)
                ->where('state_id', $state->id)
                ->first();

            if (!$city) {
                City::create([
                    'name' => $districtName,
                    'state_id' => $state->id,
                    'zone_id' => $zone->id,
                    'is_active' => 1
                ]);
                $created++;
            }
        }

        Log::info('Cities reset to default', ['created' => $created, 'total_districts' => count($districts)]);

        flash(translate('Cities reset complete. Created ' . $created . ' cities from ' . count($districts) . ' unique districts.'))->success();
        return redirect()->back();
    }
}
