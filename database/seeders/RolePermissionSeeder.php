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
            // Menu Visibility (UI)
            'view menu: mahasiswa',
            'view menu: dosen',
            'view menu: kaprodi',
            'view menu: admin',
            'view menu: reports',

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
            'assign lecturers',

            // Assessment
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',

            // Activity logs
            'view activity logs',

            // Reports
            'view reports',

            // Settings & Rubrics
            'manage settings',
            'manage rubrics',

            // System Management (Admin only)
            'manage faculties',
            'manage program-studis',
            'manage configuration',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $kaprodi = Role::firstOrCreate(['name' => 'kaprodi']);
        $kaprodi->syncPermissions([
            'view menu: dosen', // Kaprodi can see Dosen menu too
            'view menu: kaprodi',
            'view menu: reports',
            'view users',
            'view submissions',
            'approve submissions',
            'reject submissions',
            'assign lecturers',
            'view assessments',
            'view activity logs',
            'view reports',
            'manage settings',
            'manage rubrics',
        ]);

        $dosen = Role::firstOrCreate(['name' => 'dosen']);
        $dosen->syncPermissions([
            'view menu: dosen',
            'view submissions',
            'edit submissions',
            'approve submissions',
            'view assessments',
            'create assessments',
            'edit assessments',
        ]);

        $mahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);
        $mahasiswa->syncPermissions([
            'view menu: mahasiswa',
            'view submissions',
            'create submissions',
            'edit submissions',
        ]);
    }
}
