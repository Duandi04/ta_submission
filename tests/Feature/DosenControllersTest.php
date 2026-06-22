<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentRubric;
use App\Models\Rubric;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenControllersTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;
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

        $this->dosen = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        $this->dosen->assignRole('dosen');
    }

    /**
     * Test SubmissionController @submissions
     */
    public function test_submission_controller_submissions_index(): void
    {
        $student1 = User::factory()->mahasiswa()->create([
            'name' => 'Aditya Wijaya',
            'nim_nip' => '11223344',
            'program_studi_id' => $this->prodi->id,
        ]);

        $student2 = User::factory()->mahasiswa()->create([
            'name' => 'Citra Lestari',
            'nim_nip' => '55667788',
            'program_studi_id' => $this->prodi->id,
        ]);

        $submission1 = ThesisSubmission::factory()->approved()->forStudent($student1)->create([
            'supervisor_id' => $this->dosen->id,
        ]);
        $submission2 = ThesisSubmission::factory()->underReview()->forStudent($student2)->create([
            'supervisor_id' => $this->dosen->id,
        ]);

        // Response check - no filters
        $response = $this->actingAs($this->dosen)->get(route('dosen.submissions.index'));
        $response->assertStatus(200);
        $response->assertSee('Aditya Wijaya');
        $response->assertSee('Citra Lestari');

        // Response check - search by name
        $responseSearch = $this->actingAs($this->dosen)->get(route('dosen.submissions.index', ['search' => 'Aditya']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Aditya Wijaya');
        $responseSearch->assertDontSee('Citra Lestari');

        // Response check - filter by status
        $responseStatus = $this->actingAs($this->dosen)->get(route('dosen.submissions.index', ['status' => 'approved']));
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Aditya Wijaya');
        $responseStatus->assertDontSee('Citra Lestari');
    }

    /**
     * Test SubmissionController @index (Students index)
     */
    public function test_submission_controller_students_index(): void
    {
        $student1 = User::factory()->mahasiswa()->create([
            'name' => 'Aditya Wijaya',
            'nim_nip' => '11223344',
            'program_studi_id' => $this->prodi->id,
        ]);
        $student1->assignRole('mahasiswa');

        $student2 = User::factory()->mahasiswa()->create([
            'name' => 'Citra Lestari',
            'nim_nip' => '55667788',
            'program_studi_id' => $this->prodi->id,
        ]);
        $student2->assignRole('mahasiswa');

        // Approved submissions where dosen is primary or secondary supervisor
        $submission1 = ThesisSubmission::factory()->approved()->forStudent($student1)->create([
            'supervisor_id' => $this->dosen->id,
        ]);
        $submission2 = ThesisSubmission::factory()->approved()->forStudent($student2)->create([
            'supervisor_2_id' => $this->dosen->id,
        ]);

        // Access students list
        $response = $this->actingAs($this->dosen)->get(route('dosen.students.index'));
        $response->assertStatus(200);
        $response->assertSee('Aditya Wijaya');
        $response->assertSee('Citra Lestari');

        // Search by name
        $responseSearch = $this->actingAs($this->dosen)->get(route('dosen.students.index', ['search' => 'Citra']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Citra Lestari');
        $responseSearch->assertDontSee('Aditya Wijaya');
    }

    /**
     * Test SubmissionController @studentDetails
     */
    public function test_submission_controller_student_details(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);

        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create([
            'supervisor_id' => $this->dosen->id,
        ]);

        $response = $this->actingAs($this->dosen)->get(route('dosen.students.show', $student->id));
        $response->assertStatus(200);
        $response->assertSee($student->name);
    }

    /**
     * Test SubmissionController @show
     */
    public function test_submission_controller_show(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);

        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create([
            'supervisor_id' => $this->dosen->id,
        ]);

        $response = $this->actingAs($this->dosen)->get(route('dosen.submissions.show', $submission->id));
        $response->assertStatus(200);
        $response->assertSee($submission->title);
    }

    /**
     * Test AssessmentController @index
     */
    public function test_assessment_controller_index(): void
    {
        $student1 = User::factory()->mahasiswa()->create([
            'name' => 'Aditya Wijaya',
            'program_studi_id' => $this->prodi->id,
        ]);
        $student2 = User::factory()->mahasiswa()->create([
            'name' => 'Citra Lestari',
            'program_studi_id' => $this->prodi->id,
        ]);

        $submission1 = ThesisSubmission::factory()->approved()->forStudent($student1)->create();
        $submission2 = ThesisSubmission::factory()->approved()->forStudent($student2)->create();

        $assessment1 = Assessment::create([
            'thesis_submission_id' => $submission1->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => false,
        ]);

        $assessment2 = Assessment::create([
            'thesis_submission_id' => $submission2->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => true,
        ]);

        $response = $this->actingAs($this->dosen)->get(route('dosen.assessments.index'));
        $response->assertStatus(200);
        $response->assertSee('Aditya Wijaya');
        $response->assertSee('Citra Lestari');

        // Test search
        $responseSearch = $this->actingAs($this->dosen)->get(route('dosen.assessments.index', ['search' => 'Aditya']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Aditya Wijaya');
        $responseSearch->assertDontSee('Citra Lestari');

        // Test status draft filter
        $responseDraft = $this->actingAs($this->dosen)->get(route('dosen.assessments.index', ['status' => 'draft']));
        $responseDraft->assertStatus(200);
        $responseDraft->assertSee('Aditya Wijaya');
        $responseDraft->assertDontSee('Citra Lestari');

        // Test status submitted filter
        $responseSubmitted = $this->actingAs($this->dosen)->get(route('dosen.assessments.index', ['status' => 'submitted']));
        $responseSubmitted->assertStatus(200);
        $responseSubmitted->assertSee('Citra Lestari');
        $responseSubmitted->assertDontSee('Aditya Wijaya');
    }

    /**
     * Test AssessmentController @create
     */
    public function test_assessment_controller_create(): void
    {
        $rubric = Rubric::create([
            'name' => 'Rubrik TA Baru',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 50],
                ['id' => 'c2', 'name' => 'Kriteria 2', 'weight' => 50],
            ],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $submission->rubric_id = $rubric->id;
        $submission->save();

        // Access create page
        $response = $this->actingAs($this->dosen)->get(route('dosen.assessments.create', ['submission_id' => $submission->id]));
        $response->assertStatus(200);
        $response->assertSee('Kriteria 1');

        // Create an assessment to trigger redirection to edit
        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => false,
        ]);

        $responseRedirect = $this->actingAs($this->dosen)->get(route('dosen.assessments.create', ['submission_id' => $submission->id]));
        $responseRedirect->assertRedirect(route('dosen.assessments.edit', $assessment));
    }

    /**
     * Test AssessmentController @store
     */
    public function test_assessment_controller_store(): void
    {
        $rubric = Rubric::create([
            'name' => 'Rubrik TA Baru',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 50],
                ['id' => 'c2', 'name' => 'Kriteria 2', 'weight' => 50],
            ],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $submission->rubric_id = $rubric->id;
        $submission->save();

        $payload = [
            'thesis_submission_id' => $submission->id,
            'rubric_id' => $rubric->id,
            'comments' => 'Sangat bagus',
            'strengths' => 'Metode solid',
            'weaknesses' => 'Kurang data',
            'recommendations' => 'Tambah data',
            'scores' => [
                'c1' => 80,
                'c2' => 90,
            ],
            'notes' => [
                'c1' => 'Catatan kriteria 1',
                'c2' => 'Catatan kriteria 2',
            ],
        ];

        $response = $this->actingAs($this->dosen)->post(route('dosen.assessments.store'), $payload);

        $assessment = Assessment::where('thesis_submission_id', $submission->id)->where('evaluator_id', $this->dosen->id)->first();
        $this->assertNotNull($assessment);

        $response->assertRedirect(route('dosen.assessments.show', $assessment));
        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'comments' => 'Sangat bagus',
            'total_score' => 85.00, // (80 + 90) / 2
        ]);

        // Attempting duplicate store
        $responseDuplicate = $this->actingAs($this->dosen)->post(route('dosen.assessments.store'), $payload);
        $responseDuplicate->assertSessionHasErrors(['error']);
    }

    /**
     * Test AssessmentController @show
     */
    public function test_assessment_controller_show(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => false,
        ]);

        $response = $this->actingAs($this->dosen)->get(route('dosen.assessments.show', $assessment->id));
        $response->assertStatus(200);

        // Test unauthorized access
        $otherDosen = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        $otherDosen->assignRole('dosen');

        $response403 = $this->actingAs($otherDosen)->get(route('dosen.assessments.show', $assessment->id));
        $response403->assertStatus(403);
    }

    /**
     * Test AssessmentController @edit
     */
    public function test_assessment_controller_edit(): void
    {
        $rubric = Rubric::create([
            'name' => 'Rubrik TA Baru',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 100],
            ],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $submission->rubric_id = $rubric->id;
        $submission->save();

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->dosen->id,
            'rubric_id' => $rubric->id,
            'is_submitted' => false,
        ]);

        // Allowed to edit draft
        $response = $this->actingAs($this->dosen)->get(route('dosen.assessments.edit', $assessment->id));
        $response->assertStatus(200);

        // Change to submitted, not allowed to edit anymore
        $assessment->is_submitted = true;
        $assessment->save();

        $response403 = $this->actingAs($this->dosen)->get(route('dosen.assessments.edit', $assessment->id));
        $response403->assertStatus(403);
    }

    /**
     * Test AssessmentController @update
     */
    public function test_assessment_controller_update(): void
    {
        $rubric = Rubric::create([
            'name' => 'Rubrik TA Baru',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 100],
            ],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $submission->rubric_id = $rubric->id;
        $submission->save();

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->dosen->id,
            'rubric_id' => $rubric->id,
            'is_submitted' => false,
        ]);

        $payload = [
            'comments' => 'Update comment',
            'strengths' => 'Updated strengths',
            'weaknesses' => 'Updated weaknesses',
            'recommendations' => 'Updated recommendations',
            'scores' => [
                'c1' => 95,
            ],
        ];

        $response = $this->actingAs($this->dosen)->put(route('dosen.assessments.update', $assessment->id), $payload);
        $response->assertRedirect(route('dosen.assessments.show', $assessment->id));

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'comments' => 'Update comment',
            'total_score' => 95.00,
        ]);

        // Lock to submitted, assert 403 on update
        $assessment->is_submitted = true;
        $assessment->save();

        $response403 = $this->actingAs($this->dosen)->put(route('dosen.assessments.update', $assessment->id), $payload);
        $response403->assertStatus(403);
    }

    /**
     * Test AssessmentController @destroy
     */
    public function test_assessment_controller_destroy(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission1 = ThesisSubmission::factory()->approved()->forStudent($student)->create();

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission1->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => false,
        ]);

        // Destroy draft
        $response = $this->actingAs($this->dosen)->delete(route('dosen.assessments.destroy', $assessment->id));
        $response->assertRedirect(route('dosen.assessments.index'));
        $this->assertSoftDeleted('assessments', ['id' => $assessment->id]);

        // Destroying submitted assessment - create another submission to avoid unique constraint on (submission_id, evaluator_id)
        $submission2 = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $assessment2 = Assessment::create([
            'thesis_submission_id' => $submission2->id,
            'evaluator_id' => $this->dosen->id,
            'is_submitted' => true,
        ]);

        $response403 = $this->actingAs($this->dosen)->delete(route('dosen.assessments.destroy', $assessment2->id));
        $response403->assertStatus(403);
    }

    /**
     * Test AssessmentController @submit
     */
    public function test_assessment_controller_submit(): void
    {
        $rubric = Rubric::create([
            'name' => 'Rubrik TA Baru',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 100],
            ],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);

        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();
        $submission->rubric_id = $rubric->id;
        $submission->save();

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->dosen->id,
            'rubric_id' => $rubric->id,
            'is_submitted' => false,
        ]);

        // Submit with no scores first -> error
        $responseNoScores = $this->actingAs($this->dosen)->post(route('dosen.assessments.submit', $assessment->id));
        $responseNoScores->assertRedirect();
        $responseNoScores->assertSessionHas('error', 'Tidak dapat melakukan submit. Anda belum mengisi draft penilaian.');

        // Add scores
        $assessment->scores()->create([
            'criterion_id' => 'c1',
            'criterion_name' => 'Kriteria 1',
            'weight' => 100,
            'score' => 85,
        ]);

        $responseSubmit = $this->actingAs($this->dosen)->post(route('dosen.assessments.submit', $assessment->id));
        $responseSubmit->assertRedirect(route('dosen.assessments.show', $assessment->id));
        $this->assertTrue($assessment->fresh()->is_submitted);

        // Try submit again -> 403
        $responseAlreadySubmitted = $this->actingAs($this->dosen)->post(route('dosen.assessments.submit', $assessment->id));
        $responseAlreadySubmitted->assertStatus(403);
    }
}
