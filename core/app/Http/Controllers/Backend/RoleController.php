<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'        => 'role-list',
            'create|store' => 'role-create',
            'edit|update'  => 'role-edit',
            'destroy'      => 'role-delete',
        ];
    }

    public function index(): View
    {
        $roles = Role::orderBy('id')->paginate(10);

        return view('backend.role.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all()->groupBy('category');

        return view('backend.role.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name'   => 'required|unique:roles,name',
            'description' => 'required',
            'permission'  => 'required|array',
        ]);

        $role = Role::create([
            'name'        => $validated['role_name'],
            'description' => $validated['description'],
        ]);

        $role->syncPermissions(array_map('intval', $validated['permission']));

        notifyEvs('success', 'Role created successfully');

        return redirect()->route('admin.role.index');
    }

    public function edit(int $id): View
    {
        $role            = Role::findOrFail($id);
        $permissions     = Permission::all()->groupBy('category');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('backend.role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'role_name'   => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'permission'  => 'required|array',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name'        => $validated['role_name'],
            'description' => $validated['description'],
        ]);

        $role->syncPermissions(array_map('intval', $validated['permission']));

        notifyEvs('success', 'Role updated successfully');

        return redirect()->route('admin.role.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super-admin') {
            notifyEvs('error', 'The super-admin role cannot be deleted');
        } else {

            // Check if the role is assigned to any users
            $userCount = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->count();
            if ($userCount > 0) {
                notifyEvs('error', 'This role is assigned to users and cannot be deleted');

                return redirect()->route('admin.role.index');
            }

            $role->delete();
            notifyEvs('success', 'Role deleted successfully');
        }

        return redirect()->route('admin.role.index');
    }
}
