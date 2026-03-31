<?php

namespace Tests\Unit;

use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Services\Kaprodi;
use App\Services\Kaprodi\KaprodiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class KaprodiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KaprodiService $service;
    protected User $kaprodi;
    protected ProgramStudi $prodi;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->prodi = ProgramStudi::first();
        $this->kaprodi = User::factory()->create([
            'program_studi_id' => $this->prodi->id
        ]);
        $this->kaprodi->assignRole('kaprodi');
        
        $this->service = new KaprodiService();
        Auth::login($this->kaprodi);
    }

    /**
     * Test that acceptSubmission prevents multiple approved proposals for the same student.
     */
    public function test_accept_submission_prevents_multiple_approvals(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        // Create an already approved submission
        ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved'
        ]);

        // Create a new submitted proposal (must be under_review to be accepted)
        $newSubmission = ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'status' => 'under_review'
        ]);

        $lecturer = User::factory()->create(['program_studi_id' => $this->prodi->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mahasiswa ini sudah memiliki proposal yang diterima');

        $this->service->acceptSubmission($newSubmission->id, [
            'supervisor_id' => $lecturer->id
        ]);
    }

    /**
     * Test getStudentsQuery includes necessary relations and counts.
     */
    public function test_get_students_query_includes_required_data(): void
    {
        /** @var User $student */
        $student = User::factory()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        ThesisSubmission::factory()->count(3)->create([
            'student_id' => $student->id,
            'status' => 'submitted'
        ]);

        $request = new \Illuminate\Http\Request();
        $query = $this->service->getStudentsQuery($request);
        $result = $query->where('id', $student->id)->first();

        // Check if thesis_submissions_count is present
        $this->assertEquals(3, $result->thesis_submissions_count);
        
        // Check if thesisSubmissions relation is loaded and filtered to approved (should be empty here)
        $this->assertTrue($result->relationLoaded('thesisSubmissions'));
        $this->assertCount(0, $result->thesisSubmissions);

        // Add an approved one
        ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved'
        ]);

        $query = $this->service->getStudentsQuery($request);
        $result = $query->where('id', $student->id)->first();
        $this->assertCount(1, $result->thesisSubmissions);
        $this->assertEquals(4, $result->thesis_submissions_count);
    }
}
