<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\AssessmentRubric;
use App\Models\AssessmentCriterion;
use App\Models\Rubric;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Services\Examiner\AssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AssessmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AssessmentService $service;
    protected User $examiner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
        ]);

        $this->examiner = User::factory()->create();
        $this->examiner->assignRole('dosen');
        $this->service = new AssessmentService();

        Auth::login($this->examiner);
    }

    public function test_get_examiner_assessments(): void
    {
        $submission1 = ThesisSubmission::factory()->create();
        $submission2 = ThesisSubmission::factory()->create();

        Assessment::create([
            'thesis_submission_id' => $submission1->id,
            'evaluator_id' => $this->examiner->id,
            'is_submitted' => false,
        ]);

        Assessment::create([
            'thesis_submission_id' => $submission2->id,
            'evaluator_id' => $this->examiner->id,
            'is_submitted' => true,
        ]);

        // Unrelated assessment
        $otherDosen = User::factory()->create();
        Assessment::create([
            'thesis_submission_id' => $submission1->id,
            'evaluator_id' => $otherDosen->id,
            'is_submitted' => false,
        ]);

        $assessments = $this->service->getExaminerAssessments(10);

        $this->assertEquals(2, $assessments->total());
    }

    public function test_get_criteria(): void
    {
        AssessmentCriterion::create([
            'name' => 'Criterion 1',
            'weight_percentage' => 40,
            'is_active' => true,
            'order' => 1,
        ]);
        AssessmentCriterion::create([
            'name' => 'Criterion 2',
            'weight_percentage' => 60,
            'is_active' => true,
            'order' => 2,
        ]);
        AssessmentCriterion::create([
            'name' => 'Inactive Criterion',
            'weight_percentage' => 100,
            'is_active' => false,
            'order' => 3,
        ]);

        $criteria = $this->service->getCriteria();

        $this->assertEquals(2, $criteria->count());
        $this->assertEquals('Criterion 1', $criteria->first()->name);
    }

    public function test_get_criteria_for_assessment_with_snapshot(): void
    {
        $submission = ThesisSubmission::factory()->create();
        $rubric = AssessmentRubric::create([
            'thesis_submission_id' => $submission->id,
            'name' => 'Rubrik TA',
            'description' => 'Rubrik Penilaian TA',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'description' => 'Desc 1', 'weight' => 50],
                ['id' => 'c2', 'name' => 'Kriteria 2', 'description' => 'Desc 2', 'weight' => 50],
            ],
        ]);

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->examiner->id,
            'assessment_rubric_id' => $rubric->id,
        ]);

        $criteria = $this->service->getCriteriaForAssessment($assessment);

        $this->assertEquals(2, $criteria->count());
        $this->assertEquals('Kriteria 1', $criteria->first()->name);
        $this->assertEquals(50, $criteria->first()->weight_percentage);
    }

    public function test_find_existing_assessment(): void
    {
        $submission = ThesisSubmission::factory()->create();

        $this->assertNull($this->service->findExistingAssessment($submission->id));

        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => $this->examiner->id,
        ]);

        $found = $this->service->findExistingAssessment($submission->id);
        $this->assertNotNull($found);
        $this->assertEquals($assessment->id, $found->id);
    }

    public function test_create_and_update_assessment(): void
    {
        $submission = ThesisSubmission::factory()->create();
        $rubric = Rubric::create([
            'name' => 'Rubrik Program',
            'criteria' => [
                ['id' => 'c1', 'name' => 'Kriteria 1', 'weight' => 30],
                ['id' => 'c2', 'name' => 'Kriteria 2', 'weight' => 70],
            ],
        ]);
        $submission->rubric_id = $rubric->id;
        $submission->save();

        $data = [
            'comments' => 'Catatan Utama',
            'strengths' => 'Kelebihan',
            'weaknesses' => 'Kekurangan',
            'recommendations' => 'Rekomendasi',
        ];

        // Scores array
        $scores = [
            'c1' => 80,
            'c2' => 90,
        ];

        // Mocks request parameters for notes
        request()->merge([
            'notes' => [
                'c1' => 'Notes 1',
                'c2' => 'Notes 2',
            ]
        ]);

        // Create
        $assessment = $this->service->create($submission, $data, $scores);

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
            'evaluator_id' => $this->examiner->id,
            'comments' => 'Catatan Utama',
            'total_score' => 87.00, // (80 * 0.3) + (90 * 0.7) = 24 + 63 = 87
        ]);

        $this->assertDatabaseHas('assessment_scores', [
            'assessment_id' => $assessment->id,
            'criterion_id' => 'c1',
            'score' => 80,
            'notes' => 'Notes 1',
        ]);

        // Update
        $updatedData = [
            'comments' => 'Catatan Utama Baru',
        ];
        $updatedScores = [
            'c1' => 100,
            'c2' => 80,
        ];

        $updated = $this->service->update($assessment, $updatedData, $updatedScores);

        $this->assertEquals('Catatan Utama Baru', $updated->comments);
        $this->assertEquals(86.00, $updated->total_score); // (100 * 0.3) + (80 * 0.7) = 30 + 56 = 86

        // Delete
        $this->service->delete($updated);
        $this->assertSoftDeleted('assessments', ['id' => $assessment->id]);
    }

    public function test_can_edit(): void
    {
        $submission1 = ThesisSubmission::factory()->create();
        $assessmentDraft = Assessment::create([
            'thesis_submission_id' => $submission1->id,
            'evaluator_id' => $this->examiner->id,
            'is_submitted' => false,
        ]);

        $submission2 = ThesisSubmission::factory()->create();
        $assessmentSubmitted = Assessment::create([
            'thesis_submission_id' => $submission2->id,
            'evaluator_id' => $this->examiner->id,
            'is_submitted' => true,
        ]);

        $submission3 = ThesisSubmission::factory()->create();
        $otherDosen = User::factory()->create();
        $otherAssessment = Assessment::create([
            'thesis_submission_id' => $submission3->id,
            'evaluator_id' => $otherDosen->id,
            'is_submitted' => false,
        ]);

        $this->assertTrue($this->service->canEdit($assessmentDraft));
        $this->assertFalse($this->service->canEdit($assessmentSubmitted));
        $this->assertFalse($this->service->canEdit($otherAssessment));
    }
}
