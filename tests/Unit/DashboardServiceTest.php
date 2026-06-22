<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->service = new DashboardService();
    }

    public function test_get_stats_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        User::factory()->count(5)->create(); // Create some other users
        ThesisSubmission::factory()->create(['status' => 'pending']);
        ThesisSubmission::factory()->create(['status' => 'approved']);
        ThesisSubmission::factory()->create(['status' => 'draft']);

        $stats = $this->service->getStats($admin);

        $this->assertArrayHasKey('total_users', $stats);
        $this->assertEquals(ThesisSubmission::count(), $stats['total_submissions']);
        $this->assertEquals(1, $stats['pending_submissions']);
        $this->assertEquals(1, $stats['approved_submissions']);
    }

    public function test_get_stats_for_kaprodi(): void
    {
        $prodi = ProgramStudi::first();
        $kaprodi = User::factory()->create(['program_studi_id' => $prodi->id]);
        $kaprodi->assignRole('kaprodi');

        // Create 2 students in same prodi
        $student1 = User::factory()->create(['program_studi_id' => $prodi->id]);
        $student1->assignRole('mahasiswa');
        $student2 = User::factory()->create(['program_studi_id' => $prodi->id]);
        $student2->assignRole('mahasiswa');

        // Student in another prodi
        $otherProdi = ProgramStudi::factory()->create();
        $studentOther = User::factory()->create(['program_studi_id' => $otherProdi->id]);
        $studentOther->assignRole('mahasiswa');

        // Student 1 submits an approved proposal
        ThesisSubmission::factory()->create([
            'student_id' => $student1->id,
            'status' => 'approved',
        ]);

        // Student 2 only has a draft proposal (considered "not submitted")
        ThesisSubmission::factory()->create([
            'student_id' => $student2->id,
            'status' => 'draft',
        ]);

        $stats = $this->service->getStats($kaprodi);

        $this->assertEquals(2, $stats['total_students']); // only student1 and student2 (mahasiswa in prodi)
        $this->assertEquals(2, $stats['total_submissions']); // total submissions of students of this prodi
        $this->assertEquals(1, $stats['approved_submissions']);
        $this->assertEquals(1, $stats['submitted_students_count']);
        $this->assertEquals(1, $stats['not_submitted_students_count']);
    }

    public function test_get_stats_for_coordinator(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'koordinator']);
        $coordinator = User::factory()->create();
        $coordinator->assignRole('koordinator');

        ThesisSubmission::factory()->create(['status' => 'pending']);
        ThesisSubmission::factory()->create(['status' => 'in_progress']);
        ThesisSubmission::factory()->create(['status' => 'completed']);

        $stats = $this->service->getStats($coordinator);

        $this->assertEquals(3, $stats['total_submissions']);
        $this->assertEquals(1, $stats['pending_review']);
        $this->assertEquals(1, $stats['in_progress']);
        $this->assertEquals(1, $stats['completed']);
    }

    public function test_get_stats_for_dosen(): void
    {
        $dosen = User::factory()->create();
        $dosen->assignRole('dosen');

        // Supervised theses
        ThesisSubmission::factory()->create([
            'supervisor_id' => $dosen->id,
            'status' => 'submitted',
        ]);
        ThesisSubmission::factory()->create([
            'supervisor_id' => $dosen->id,
            'status' => 'completed',
        ]);

        // Assessments assigned to Dosen using factory
        $submission = ThesisSubmission::factory()->create();
        Assessment::factory()->forThesis($submission)->forEvaluator($dosen)->draft()->create();

        $submission2 = ThesisSubmission::factory()->create();
        Assessment::factory()->forThesis($submission2)->forEvaluator($dosen)->submitted()->create();

        $dosen->refresh();
        $stats = $this->service->getStats($dosen);

        $this->assertEquals(2, $stats['supervised_total']);
        $this->assertEquals(1, $stats['supervised_ongoing']);
        $this->assertEquals(1, $stats['supervised_completed']);
        $this->assertEquals(1, $stats['pending_assessments']);
        $this->assertEquals(1, $stats['submitted_assessments']);
        $this->assertEquals(2, $stats['total_assessments']);
    }

    public function test_get_stats_for_student(): void
    {
        $student = User::factory()->create();
        $student->assignRole('mahasiswa');

        ThesisSubmission::factory()->create(['student_id' => $student->id, 'status' => 'draft']);
        ThesisSubmission::factory()->create(['student_id' => $student->id, 'status' => 'pending']);
        ThesisSubmission::factory()->create(['student_id' => $student->id, 'status' => 'approved']);

        $stats = $this->service->getStats($student);

        $this->assertEquals(3, $stats['my_submissions']);
        $this->assertEquals(1, $stats['drafts']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['approved']);
    }
}
