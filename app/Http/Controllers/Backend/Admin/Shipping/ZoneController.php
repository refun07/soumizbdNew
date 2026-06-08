<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Zone;
use Illuminate\Http\Request;
use Str;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_zones'])->only(['index']);
        $this->middleware(['permission:create_zones'])->only(['store']);
        $this->middleware(['permission:edit_zones'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_zones'])->only(['destroy']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey = null;
        $stateId   = null;
        $limit     = $request->limit ?? perPage();
        $zones     = Zone::latest();

        if ($request->search != null) {
            $zones     = $zones->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }
        $zones = $zones->paginate($limit);

        $areas = Area::isActive()->get();

        return view('backend.admin.shipping.zones.index', compact('zones', 'searchKey', 'areas'));
    }

    # add new data
    public function store(Request $request)
    {
        $zone = new Zone;
        $zone->name        = $request->name;
        $zone->banner      = $request->banner;
        $zone->slug        = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name));
        $zone->is_active   = 1;
        $zone->save();

        if ($request->area_ids && count($request->area_ids) > 0) {
            Area::whereIn('id', $request->area_ids)->update([
                'zone_id'   => $zone->id
            ]);
        }
        flash(translate('Zone has been added successfully'))->success();
        return redirect()->route('admin.zones.index');
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key   = $request->lang_key;
        $areas      = Area::isActive()->get();
        $zone       = Zone::findOrFail($id);
        return view('backend.admin.shipping.zones.edit', compact('areas', 'zone', 'lang_key'));
    }

    # update state
    public function update(Request $request, $id)
    {
        $zone              = Zone::findOrFail((int) $id);
        $zone->name        = $request->name;
        $zone->banner      = $request->banner;
        $zone->slug        = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');

        $zone->save();


        if ($request->area_ids && count($request->area_ids) > 0) {

            Area::where('zone_id', $zone->id)->update([
                'zone_id'   => null
            ]);

            Area::whereIn('id', $request->area_ids)->update([
                'zone_id'   => $zone->id
            ]);
        }

        flash(translate('Zone has been updated successfully'))->success();
        return redirect()->route('admin.zones.index');
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
        $zone = Zone::findOrFail($request->id);
        $zone->is_active = !$zone->is_active;
        if ($zone->save()) {
        }

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();
        flash(translate('Zone has been deleted successfully'))->success();
        return redirect()->route('admin.zones.index');
    }
}
