<?php

namespace App\Http\Controllers\Backend;

use App\Models\Admin;
use App\Traits\FileManageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class StaffController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index'        => 'staff-list',
            'create|store' => 'staff-create',
            'edit|update'  => 'staff-edit',
        ];
    }

    /**
     * Display a listing of staff.
     */
    public function index(Request $request): View
    {
        $query = Admin::with('roles')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staffs = $query->paginate(10)->withQueryString();
        $roles  = Role::where('name', '!=', 'super-admin')->pluck('name', 'name');

        return view('backend.staff.index', compact('staffs', 'roles'));
    }

    /**
     * Store a new staff member.
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|confirmed',
            'role'     => 'required|string',
            'status'   => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $input['avatar'] = self::uploadImage($input['avatar']);
        }

        $input['password'] = Hash::make($input['password']);
        $input['status']   = $request->boolean('status');

        $staff = Admin::create($input);
        $staff->assignRole($input['role']);

        notifyEvs('success', __('Staff created successfully'));

        return redirect()->route('admin.staff.index');
    }

    /**
     * Show the form for editing a staff member.
     */
    public function edit(int $id): string
    {
        $staff     = Admin::findOrFail($id);
        $roles     = Role::pluck('name', 'name');
        $staffRole = $staff->roles->pluck('name')->first();

        return view('backend.staff.partials._update_data', compact('staff', 'roles', 'staffRole'))->render();
    }

    /**
     * Update a staff member.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $input = $request->validate([
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email,'.$id,
            'password' => 'nullable|confirmed',
            'role'     => 'required|string',
            'status'   => 'boolean',
        ]);

        $staff = Admin::findOrFail($id);

        if ($staff->id === 1 && $input['role'] !== 'super-admin') {
            notifyEvs('error', __('You cannot change the super-admin role'));

            return redirect()->route('admin.staff.index');
        }

        if ($request->filled('password')) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        if ($request->hasFile('avatar')) {
            $input['avatar'] = self::uploadImage($input['avatar'], $staff->avatar);
        }

        $input['status'] = $request->boolean('status');

        $staff->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $staff->assignRole($input['role']);

        notifyEvs('success', __('Staff updated successfully'));

        return redirect()->route('admin.staff.index');
    }
}
