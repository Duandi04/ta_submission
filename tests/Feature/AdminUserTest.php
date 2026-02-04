<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\RolePermissionSeeder::class,
        ]);
    }

    public function test_admin_can_view_users_index(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
    }

    public function test_admin_can_create_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $prodi = \App\Models\ProgramStudi::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'mahasiswa',
            'nim_nip' => '123456789',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'program_studi_id' => $prodi->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_admin_can_update_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $prodi = \App\Models\ProgramStudi::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['program_studi_id' => $prodi->id]);
        $user->assignRole('mahasiswa');

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'mahasiswa',
            'nim_nip' => $user->nim_nip,
            'phone' => '08123456789',
            'address' => 'Updated Address',
            'program_studi_id' => $prodi->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }
}
