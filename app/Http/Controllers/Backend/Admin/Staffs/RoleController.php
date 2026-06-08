<?php

namespace App\Http\Controllers\Backend\Admin\Staffs;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // constructor
    public function __construct()
    {
        $this->middleware(['permission:view_roles_and_permissions'])->only('index');
        $this->middleware(['permission:create_roles_and_permissions'])->only(['create', 'store']);
        $this->middleware(['permission:edit_roles_and_permissions'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_roles_and_permissions'])->only('destroy');
    }


    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $roles = Role::oldest();
        if ($request->search != null) {
            $roles = $roles->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $roles = $roles->paginate($limit);
        return view('backend.admin.roles.index', compact('roles', 'searchKey'));
    }

    # create
    public function create()
    {
        $permissionGroups = Permission::all()->groupBy('group_name');

        $permissionGroups = $permissionGroups->sortByDesc(function ($permissions) {
            return $permissions->count();
        });

        return view('backend.admin.roles.create', compact('permissionGroups'));
    }

    # store
    public function store(Request $request)
    {
        $existing = Role::where('name', $request->name)->first();

        if (!is_null($existing)) {
            flash(translate('A role already exist with this name'))->error();
            return back();
        }

        $role = Role::create([
            'name'          => $request->name,
            'guard_name'    => 'web',
            'is_active'     => $request->is_active ? 1 : 0
        ]);

        try {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->givePermissionTo($permissions);
        } catch (\Throwable $th) {
            //throw $th;
        }
        flash(translate('Role has been added successfully'))->success();
        return redirect()->route('admin.roles.index');
    }

    # edit
    public function edit($id)
    {
        $role = Role::findOrFail((int)$id);
        $permissionGroups = Permission::all()->groupBy('group_name');

        $permissionGroups = $permissionGroups->sortByDesc(function ($permissions) {
            return $permissions->count();
        });

        return view('backend.admin.roles.edit', compact('permissionGroups', 'role'));
    }

    # update
    public function update(Request $request, $id)
    {
        $existing = Role::where('name', $request->name)->where('id', '!=', $id)->first();

        if (!is_null($existing)) {
            flash(translate('A role already exist with this name'))->error();
            return back();
        }

        $role = Role::findOrFail((int)$id);
        $role->name = $request->name;
        if ($request->is_active) {
            $role->is_active = 1;
        } else {
            $role->is_active = 0;
        }
        $role->save();

        try {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
            $role->syncPermissions($permissions);
        } catch (\Throwable $th) {
            //throw $th;
        }
        flash(translate('Role has been updated successfully'))->success();
        return back();
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
        $role = Role::findOrFail($request->id);
        $role->is_active = $request->isActive;
        $role->save();

        return $data;
    }

    # delete
    public function destroy($id)
    {
        $userCount = User::where('role_id', $id)->count();
        if ($userCount > 0) {
            flash(translate('This role has users, manage them first to delete the role'))->error();
            return redirect()->route('admin.roles.index');
        }

        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();
        return redirect()->route('admin.roles.index');
    }
}
