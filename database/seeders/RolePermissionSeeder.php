<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Thesis submission
            'view submissions',
            'create submissions',
            'edit submissions',
            'delete submissions',
            'approve submissions',
            'reject submissions',

            // Assessment
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',

            // Comments
            'view comments',
            'create comments',
            'edit comments',
            'delete comments',

            // Activity logs
            'view activity logs',

            // Reports
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $koordinator = Role::create(['name' => 'koordinator']);
        $koordinator->givePermissionTo([
            'view users',
            'view submissions',
            'approve submissions',
            'reject submissions',
            'view assessments',
            'view comments',
            'view activity logs',
            'view reports',
        ]);

        $dosenPembimbing = Role::create(['name' => 'dosen_pembimbing']);
        $dosenPembimbing->givePermissionTo([
            'view submissions',
            'edit submissions',
            'approve submissions',
            'view assessments',
            'create assessments',
            'edit assessments',
            'view comments',
            'create comments',
        ]);

        $dosenPenguji = Role::create(['name' => 'dosen_penguji']);
        $dosenPenguji->givePermissionTo([
            'view submissions',
            'view assessments',
            'create assessments',
            'edit assessments',
            'view comments',
            'create comments',
        ]);

        $mahasiswa = Role::create(['name' => 'mahasiswa']);
        $mahasiswa->givePermissionTo([
            'view submissions',
            'create submissions',
            'edit submissions',
            'view assessments',
            'view comments',
            'create comments',
        ]);
    }
}
