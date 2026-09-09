<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name'       => 'payment-gateway-configure',
            'guard_name' => 'admin',
        ]);

        $role = Role::firstOrCreate([
            'name'       => 'super-admin',
            'guard_name' => 'admin',
        ]);

        if (! $role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep the existing permission and role intact when rolling back.
    }
};
