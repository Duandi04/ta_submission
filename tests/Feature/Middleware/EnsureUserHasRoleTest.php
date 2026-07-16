<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureUserHasRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        // Register a temporary route for testing the middleware
        Route::middleware(['web', EnsureUserHasRole::class . ':admin,dosen'])
            ->get('/_test_middleware_role', fn() => 'passed');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/_test_middleware_role');

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_role_is_blocked(): void
    {
        $user = User::factory()->create();
        $user->assignRole('mahasiswa');

        $response = $this->actingAs($user)->get('/_test_middleware_role');

        $response->assertStatus(403);
        $response->assertSee('tidak memiliki izin');
    }

    public function test_authorized_role_is_allowed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('dosen');

        $response = $this->actingAs($user)->get('/_test_middleware_role');

        $response->assertStatus(200);
        $response->assertSee('passed');
    }
}
