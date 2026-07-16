<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RbacControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            SettingSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }

    public function test_admin_can_access_rbac_index(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.rbac.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.rbac.index');
        $response->assertSee('admin');
        $response->assertSee('kaprodi');
    }

    public function test_non_admin_cannot_access_rbac_index(): void
    {
        /** @var User $student */
        $student = User::factory()->create();
        $student->assignRole('mahasiswa');

        $response = $this->actingAs($student)->get(route('admin.rbac.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_role(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.rbac.roles.store'), [
            'name' => 'koordinator_sidang',
            'permissions' => ['view submissions', 'approve submissions']
        ]);

        $response->assertRedirect(route('admin.rbac.index'));
        $this->assertDatabaseHas('roles', ['name' => 'koordinator_sidang']);
        
        $role = Role::findByName('koordinator_sidang');
        $this->assertTrue($role->hasPermissionTo('view submissions'));
        $this->assertTrue($role->hasPermissionTo('approve submissions'));
    }

    public function test_admin_can_update_role_permissions(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $role = Role::create(['name' => 'staf_jurusan']);
        $role->givePermissionTo('view submissions');

        $response = $this->actingAs($admin)->put(route('admin.rbac.roles.update', $role), [
            'name' => 'staf_jurusan',
            'permissions' => ['create submissions']
        ]);

        $response->assertRedirect(route('admin.rbac.index'));
        $role->refresh();
        
        $this->assertTrue($role->hasPermissionTo('create submissions'));
        $this->assertFalse($role->hasPermissionTo('view submissions'));
    }

    public function test_admin_cannot_rename_or_delete_core_roles(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $kaprodiRole = Role::findByName('kaprodi');

        // Renaming attempt
        $response1 = $this->actingAs($admin)->put(route('admin.rbac.roles.update', $kaprodiRole), [
            'name' => 'kaprodi_baru',
            'permissions' => []
        ]);
        $response1->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'kaprodi']);

        // Deleting attempt
        $response2 = $this->actingAs($admin)->delete(route('admin.rbac.roles.destroy', $kaprodiRole));
        $response2->assertRedirect(route('admin.rbac.index'));
        $response2->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'kaprodi']);
    }

    public function test_admin_can_create_permission(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.rbac.permissions.store'), [
            'name' => 'print pdf report'
        ]);

        $response->assertRedirect(route('admin.rbac.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'print pdf report']);
    }

    public function test_dynamic_permission_routing_works(): void
    {
        // Create custom user and custom role with specific menu visibility permission
        /** @var User $user */
        $user = User::factory()->create();
        $customRole = Role::create(['name' => 'custom_role']);
        $customRole->givePermissionTo('view menu: mahasiswa');
        $user->assignRole($customRole);

        // Access student dashboard which is protected by 'view menu: mahasiswa'
        $response = $this->actingAs($user)->get(route('student.submissions.index'));
        
        // Should succeed (200) instead of 403 since user has the permission
        $response->assertStatus(200);
    }
}
