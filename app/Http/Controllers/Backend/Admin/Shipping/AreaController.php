<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Http\Request;

class AreaController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view_areas'])->only(['index']);
        $this->middleware(['permission:create_areas'])->only(['create', 'store']);
        $this->middleware(['permission:edit_areas'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_areas'])->only(['destroy']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey  = null;
        $zoneId     = null;
        $limit      = $request->limit ?? perPage();
        $areas      = Area::latest();

        if ($request->search != null) {
            $areas = $areas->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->zoneId) {
            $areas->where('zone_id', $request->zoneId);
            $zoneId = $request->zoneId;
        }

        $areas = $areas->paginate($limit);

        $zones  = Zone::isActive()->orderBy('name', 'asc')->get();
        $cities = City::isActive()->orderBy('name', 'asc')->get();

        return view('backend.admin.shipping.areas.index', compact('areas', 'searchKey', 'zoneId', 'zones', 'cities'));
    }

    # create form
    public function create()
    {
        $zones  = Zone::isActive()->orderBy('name', 'asc')->get();
        $cities = City::isActive()->orderBy('name', 'asc')->get();
        return view('backend.admin.shipping.areas.create', compact('zones', 'cities'));
    }

    # add new data
    public function store(Request $request)
    {
        $area = new Area;
        $area->name        = $request->name;
        $area->city_id     = $request->city_id;
        $area->zone_id     = $request->zone_id;
        $area->is_active   = 1;
        $area->save();
        flash(translate('Area has been added successfully'))->success();
        return redirect()->route('admin.areas.index');
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key   = $request->lang_key;
        $zones      = Zone::isActive()->orderBy('name', 'asc')->get();
        $cities     = City::isActive()->orderBy('name', 'asc')->get();
        $area       = Area::findOrFail($id);
        return view('backend.admin.shipping.areas.edit', compact('zones', 'cities', 'area', 'lang_key'));
    }

    # update state
    public function update(Request $request, $id)
    {
        $area              = Area::findOrFail((int) $id);
        $area->name        = $request->name;
        $area->city_id     = $request->city_id;
        $area->zone_id     = $request->zone_id;
        $area->save();
        flash(translate('Area has been updated successfully'))->success();
        return redirect()->route('admin.areas.index');
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
        $area = Area::findOrFail($request->id);
        $area->is_active = !$area->is_active;
        $area->save();

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        flash(translate('Area has been deleted successfully'))->success();
        return redirect()->route('admin.areas.index');
    }
}
