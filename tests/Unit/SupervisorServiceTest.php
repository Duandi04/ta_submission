<?php

namespace Tests\Unit;

use App\Models\ThesisSubmission;
use App\Models\User;
use App\Services\Supervisor\SupervisorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SupervisorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupervisorService $service;
    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->supervisor = User::factory()->create();
        $this->supervisor->assignRole('dosen');
        $this->service = new SupervisorService();

        Auth::login($this->supervisor);
    }

    public function test_get_supervised_students(): void
    {
        $student1 = User::factory()->create();
        $student2 = User::factory()->create();

        // Create theses supervised by current supervisor
        ThesisSubmission::factory()->create([
            'student_id' => $student1->id,
            'supervisor_id' => $this->supervisor->id,
        ]);
        // Duplicate student thesis (should return unique student)
        ThesisSubmission::factory()->create([
            'student_id' => $student1->id,
            'supervisor_id' => $this->supervisor->id,
        ]);
        ThesisSubmission::factory()->create([
            'student_id' => $student2->id,
            'supervisor_id' => $this->supervisor->id,
        ]);

        // Unrelated thesis
        $otherDosen = User::factory()->create();
        ThesisSubmission::factory()->create([
            'supervisor_id' => $otherDosen->id,
        ]);

        $students = $this->service->getSupervisedStudents();

        $this->assertEquals(2, $students->count());
        $this->assertTrue($students->contains(fn($u) => $u->id === $student1->id));
        $this->assertTrue($students->contains(fn($u) => $u->id === $student2->id));
    }

    public function test_get_student_submissions(): void
    {
        $student = User::factory()->create();

        ThesisSubmission::factory()->count(3)->create([
            'student_id' => $student->id,
            'supervisor_id' => $this->supervisor->id,
        ]);

        // Unrelated submission for another student
        ThesisSubmission::factory()->create([
            'supervisor_id' => $this->supervisor->id,
        ]);

        $submissions = $this->service->getStudentSubmissions($student->id);

        $this->assertEquals(3, $submissions->count());
    }

    public function test_get_submission_success(): void
    {
        $submission = ThesisSubmission::factory()->create([
            'supervisor_id' => $this->supervisor->id,
        ]);

        $result = $this->service->getSubmission($submission);

        $this->assertEquals($submission->id, $result->id);
        $this->assertTrue($result->relationLoaded('student'));
        $this->assertTrue($result->relationLoaded('files'));
    }

    public function test_get_submission_denied_for_non_supervisor(): void
    {
        $otherDosen = User::factory()->create();
        $submission = ThesisSubmission::factory()->create([
            'supervisor_id' => $otherDosen->id,
        ]);

        try {
            $this->service->getSubmission($submission);
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }
}
