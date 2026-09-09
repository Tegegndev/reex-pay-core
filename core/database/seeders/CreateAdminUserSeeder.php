<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // Clear existing admin records

        DB::table('admins')->truncate();

        // Create a super admin user
        $superAdmin = Admin::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@coevs.com',
            'password' => bcrypt('12345678'),
        ]);

        // Assign the super admin role to the super admin user
        $superAdmin->assignRole('super-admin');
    }
}
