<?php

namespace App\Http\Controllers\Backend\Admin\Staffs;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class StaffController extends Controller
{
    // constructor
    public function __construct()
    {
        $this->middleware(['permission:view_staffs'])->only('index');
        $this->middleware(['permission:create_staffs'])->only(['create', 'store']);
        $this->middleware(['permission:edit_staffs'])->only(['edit', 'update', 'toggleBan']);
        $this->middleware(['permission:delete_staffs'])->only('destroy');
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $staffs = User::staffs();
        if ($request->search != null) {
            $staffs = $staffs->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        $staffs = $staffs->paginate($limit);
        return view('backend.admin.staffs.index', compact('staffs', 'searchKey'));
    }

    # return create form
    public function create()
    {
        $roles = Role::oldest()->where('id', '!=', 1)->isActive()->get();
        return view('backend.admin.staffs.create', compact('roles'));
    }

    # store data
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() == null) {
            $user             = new User;
            $user->name       = $request->name;
            $user->email      = $request->email;
            $user->phone      = $request->phone_no;
            $user->user_type  = "staff";
            $user->password   = Hash::make($request->password);
            $user->role_id    = $request->role_id;
            $user->shop_id    = shopId();
            $user->agent_commission_percentage = $request->agent_commission_percentage ?? 0;
            $user->agent_commission_fixed_amount = $request->agent_commission_fixed_amount ?? 0;
            $user->agent_commission_mode = $request->agent_commission_mode ?? 'percentage';
            $user->save();

            $user->assignRole(Role::findOrFail($request->role_id)->name);
            flash(translate('Staff has been added successfully'))->success();
            return redirect()->route('admin.staffs.index');
        }

        flash(translate('Email already taken'))->error();
        return back();
    }

    # edit data
    public function edit($id)
    {
        $staff  = User::where('user_type', 'staff')->where('id', $id)->first();
        if (is_null($staff)) {
            flash(translate('Something went wrong'))->error();
            return back();
        }
        $roles = Role::latest()->where('id', '!=', 1)->isActive()->get();
        return view('backend.admin.staffs.edit', compact('staff', 'roles'));
    }

    # update data
    public function update(Request $request, $id)
    {
        $user             = User::findOrFail($id);
        $old_role_id      = $user->role_id;
        try {
            $user->removeRole(Role::find((int)$old_role_id)->name);
        } catch (\Throwable $th) {
            //throw $th;
        }
        $user->name       = $request->name;
        $user->email      = $request->email;
        $user->phone      = $request->phone_no;
        $user->agent_commission_percentage = $request->agent_commission_percentage ?? 0;
        $user->agent_commission_fixed_amount = $request->agent_commission_fixed_amount ?? 0;
        $user->agent_commission_mode = $request->agent_commission_mode ?? 'percentage';

        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }

        $user->role_id    = $request->role_id;
        $user->save();

        $user->assignRole(Role::findOrFail($request->role_id)->name);

        flash(translate('Staff has been updated successfully'))->success();
        return redirect()->route('admin.staffs.index');
    }


    # toggle Ban 
    public function toggleBan(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $staff = User::findOrFail($request->id);
        $staff->is_banned = !$staff->is_banned;
        $staff->save();

        return $data;
    }


    # delete data
    public function destroy($id)
    {
        User::destroy($id);
        flash(translate('Staff has been deleted successfully'))->success();
        return redirect()->route('admin.staffs.index');
    }
}
