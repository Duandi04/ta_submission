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

            // Settings & Rubrics
            'manage settings',
            'manage rubrics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $koordinator = Role::firstOrCreate(['name' => 'koordinator']);
        $koordinator->syncPermissions([
            'view users',
            'view submissions',
            'approve submissions',
            'reject submissions',
            'view assessments',
            'view comments',
            'view activity logs',
            'view reports',
        ]);

        $kaprodi = Role::firstOrCreate(['name' => 'kaprodi']);
        $kaprodi->syncPermissions([
            'view users',
            'view submissions',
            'approve submissions',
            'reject submissions',
            'view assessments',
            'view comments',
            'view activity logs',
            'view reports',
            'manage settings',
            'manage rubrics',
        ]);

        $dosen = Role::firstOrCreate(['name' => 'dosen']);
        $dosen->syncPermissions([
            'view submissions',
            'edit submissions',
            'approve submissions',
            'view assessments',
            'create assessments',
            'edit assessments',
            'view comments',
            'create comments',
        ]);

        $mahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);
        $mahasiswa->syncPermissions([
            'view submissions',
            'create submissions',
            'edit submissions',
            'view assessments',
            'view comments',
            'create comments',
        ]);
    }
}
