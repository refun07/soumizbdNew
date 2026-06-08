<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_states'])->only(['index']);
        $this->middleware(['permission:create_states'])->only(['store']);
        $this->middleware(['permission:edit_states'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_states'])->only(['destroy']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey = null;
        $countryId = null;
        $sortBy = $request->get('sort_by', 'is_active');
        $sortOrder = $request->get('sort_order', 'desc');
        $limit     = $request->limit ?? perPage();
        $countries = Country::orderBy('is_active', 'desc')->get();

        $states = State::query();

        if ($request->search != null) {
            $states = $states->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->countryId) {
            $states->where('country_id', $request->countryId);
            $countryId = $request->countryId;
        }

        // Apply sorting
        if ($sortBy === 'name') {
            $states->orderBy('name', $sortOrder);
        } else {
            $states->orderBy('is_active', 'desc');
        }

        $states = $states->paginate($limit)->appends($request->query());
        return view('backend.admin.shipping.states.index', compact('states', 'searchKey', 'countryId', 'countries', 'sortBy', 'sortOrder'));
    }

    # add new data
    public function store(Request $request)
    {
        $state = new State;
        $state->name        = $request->name;
        $state->country_id  = $request->country_id;
        $state->is_active   = 1;
        $state->save();
        flash(translate('State has been added successfully'))->success();
        return redirect()->route('admin.states.index');
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $countries  = Country::isActive()->get();
        $state      = State::findOrFail($id);
        return view('backend.admin.shipping.states.edit', compact('countries', 'state', 'lang_key'));
    }

    # update state
    public function update(Request $request, $id)
    {
        $state              = State::findOrFail((int) $id);
        $state->name        = $request->name;
        $state->country_id  = $request->country_id;
        $state->save();
        flash(translate('State has been updated successfully'))->success();
        return redirect()->route('admin.states.index');
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
        $state = State::findOrFail($request->id);
        $state->is_active = !$state->is_active;
        if ($state->save()) {

            $cityQuery  = City::where('state_id', $state->id);
            $cityQuery->update([
                'is_active' => $state->is_active
            ]);
            $cityIds   = $cityQuery->where('is_active', 1)->pluck('id');

            $areaQuery  = Area::whereIn('city_id', $cityIds);
            $areaQuery->update([
                'is_active' => $state->is_active
            ]);
        }

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $state->delete();
        flash(translate('State has been deleted successfully'))->success();
        return redirect()->route('admin.states.index');
    }
}
