<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ProgramStudi $prodi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->prodi = ProgramStudi::first();
    }

    /**
     * Test admin dashboard
     */
    public function test_dashboard_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /**
     * Test kaprodi dashboard
     */
    public function test_dashboard_kaprodi(): void
    {
        $kaprodi = User::factory()->kaprodi()->create(['program_studi_id' => $this->prodi->id]);
        $kaprodi->assignRole('kaprodi');

        $response = $this->actingAs($kaprodi)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /**
     * Test coordinator dashboard
     */
    public function test_dashboard_coordinator(): void
    {
        Role::firstOrCreate(['name' => 'koordinator']);
        $coordinator = User::factory()->create();
        $coordinator->assignRole('koordinator');

        $response = $this->actingAs($coordinator)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /**
     * Test dosen dashboard
     */
    public function test_dashboard_dosen(): void
    {
        $dosen = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        $dosen->assignRole('dosen');

        $response = $this->actingAs($dosen)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    /**
     * Test student dashboard
     */
    public function test_dashboard_student(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }
}
