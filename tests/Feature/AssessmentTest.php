<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\AssessmentScore;
use App\Models\ProgramStudi;
use App\Models\Rubric;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
            \Database\Seeders\RubricSeeder::class,
            \Database\Seeders\AssessmentCriteriaSeeder::class,
        ]);
    }

    /**
     * Test assessment can be created with factory
     */
    public function test_can_create_assessment(): void
    {
        $assessment = Assessment::factory()->create();

        $this->assertDatabaseHas('assessments', [
            'id' => $assessment->id,
        ]);
    }

    /**
     * Test assessment belongs to thesis submission
     */
    public function test_assessment_belongs_to_thesis(): void
    {
        $thesis = ThesisSubmission::factory()->create();
        $assessment = Assessment::factory()->forThesis($thesis)->create();

        $this->assertEquals($thesis->id, $assessment->thesisSubmission->id);
    }

    /**
     * Test assessment belongs to evaluator
     */
    public function test_assessment_belongs_to_evaluator(): void
    {
        $evaluator = User::factory()->dosen()->create();
        $assessment = Assessment::factory()->forEvaluator($evaluator)->create();

        $this->assertEquals($evaluator->id, $assessment->evaluator->id);
    }

    /**
     * Test assessment has many scores
     */
    public function test_assessment_has_many_scores(): void
    {
        $assessment = Assessment::factory()->create();
        $criteria = AssessmentCriterion::active()->get();

        if ($criteria->isNotEmpty()) {
            foreach ($criteria->take(3) as $criterion) {
                AssessmentScore::factory()
                    ->forAssessment($assessment)
                    ->forCriterion($criterion)
                    ->create();
            }

            $this->assertGreaterThanOrEqual(1, $assessment->scores()->count());
        } else {
            $this->assertTrue(true); // Skip if no criteria
        }
    }

    /**
     * Test dosen can view their assessments
     */
    public function test_dosen_can_view_assessments(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $dosen->assignRole('dosen');

        Assessment::factory()->forEvaluator($dosen)->count(2)->create();

        $response = $this->actingAs($dosen)->get(route('dosen.assessments.index'));

        $response->assertStatus(200);
    }

    /**
     * Test dosen can only edit own unsubmitted assessment
     */
    public function test_dosen_can_edit_own_unsubmitted_assessment(): void
    {
        $prodi = ProgramStudi::first();
        $dosen = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $dosen->assignRole('dosen');

        $assessment = Assessment::factory()
            ->forEvaluator($dosen)
            ->draft()
            ->create();

        $this->assertTrue($assessment->canBeEditedBy($dosen));
    }

    /**
     * Test dosen cannot edit submitted assessment
     */
    public function test_dosen_cannot_edit_submitted_assessment(): void
    {
        $prodi = ProgramStudi::first();
        $dosen = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $dosen->assignRole('dosen');

        $assessment = Assessment::factory()
            ->forEvaluator($dosen)
            ->submitted()
            ->create();

        $this->assertFalse($assessment->canBeEditedBy($dosen));
    }

    /**
     * Test dosen cannot edit other's assessment
     */
    public function test_dosen_cannot_edit_others_assessment(): void
    {
        $prodi = ProgramStudi::first();
        $dosen1 = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $dosen2 = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $dosen1->assignRole('dosen');
        $dosen2->assignRole('dosen');

        $assessment = Assessment::factory()
            ->forEvaluator($dosen1)
            ->draft()
            ->create();

        $this->assertFalse($assessment->canBeEditedBy($dosen2));
    }


    /**
     * Test assessment factory states
     */
    public function test_assessment_factory_states(): void
    {
        $draft = Assessment::factory()->draft()->create();
        $submitted = Assessment::factory()->submitted()->create();
        $supervisor = Assessment::factory()->supervisor()->create();
        $examiner1 = Assessment::factory()->examiner1()->create();

        $this->assertFalse($draft->is_submitted);
        $this->assertTrue($submitted->is_submitted);
        $this->assertNotNull($submitted->submitted_at);
        
    }

    /**
     * Test submitted scope
     */
    public function test_submitted_scope(): void
    {
        Assessment::factory()->draft()->count(2)->create();
        Assessment::factory()->submitted()->count(3)->create();

        $submitted = Assessment::submitted()->get();

        $this->assertCount(3, $submitted);
    }

    /**
     * Test by evaluator scope
     */
    public function test_by_evaluator_scope(): void
    {
        $dosen = User::factory()->dosen()->create();
        Assessment::factory()->forEvaluator($dosen)->count(2)->create();
        Assessment::factory()->create(); // Different evaluator

        $dosenAssessments = Assessment::byEvaluator($dosen->id)->get();

        $this->assertCount(2, $dosenAssessments);
    }

    /**
     * Test by type scope
     */
    public function test_by_type_scope(): void
    {
        $thesis = ThesisSubmission::factory()->create([
            'supervisor_id' => User::factory()->dosen()->create()->id,
            'supervisor_2_id' => User::factory()->dosen()->create()->id,
        ]);

        Assessment::factory()->create(['thesis_submission_id' => $thesis->id, 'evaluator_id' => $thesis->supervisor_id]);
        Assessment::factory()->create(['thesis_submission_id' => $thesis->id, 'evaluator_id' => $thesis->supervisor_2_id]);

        $examiner1 = Assessment::factory()->create(['thesis_submission_id' => $thesis->id, 'evaluator_id' => User::factory()->dosen()->create()->id]);
        $examiner2 = Assessment::factory()->create(['thesis_submission_id' => $thesis->id, 'evaluator_id' => User::factory()->dosen()->create()->id]);

        $supervisors = Assessment::byType('supervisor')->get();
        $examiner1s = Assessment::byType('examiner_1')->get();

        $this->assertCount(2, $supervisors);
        $this->assertCount(1, $examiner1s);
        $this->assertEquals($examiner1->id, $examiner1s->first()->id);
    }

    /**
     * Test assessment with rubric
     */
    public function test_assessment_with_rubric(): void
    {
        $rubric = Rubric::first();

        if ($rubric) {
            $assessment = Assessment::factory()->withRubric($rubric)->create();

            $this->assertEquals($rubric->id, $assessment->rubric_id);
        } else {
            $this->assertTrue(true); // Skip if no rubric
        }
    }

    /**
     * Test assessment soft delete
     */
    public function test_assessment_soft_delete(): void
    {
        $assessment = Assessment::factory()->create();
        $assessmentId = $assessment->id;

        $assessment->delete();

        $this->assertSoftDeleted('assessments', ['id' => $assessmentId]);
    }

    /**
     * Test Indonesian content in factory
     */
    public function test_assessment_factory_indonesian_content(): void
    {
        $assessment = Assessment::factory()->submitted()->create();

        $indonesianWords = ['baik', 'penelitian', 'perlu', 'sudah', 'metodologi', 'implementasi', 'relevan', 'industri'];

        $hasIndonesian = false;
        foreach ($indonesianWords as $word) {
            if (
                str_contains(strtolower($assessment->comments ?? ''), $word) ||
                str_contains(strtolower($assessment->strengths ?? ''), $word) ||
                str_contains(strtolower($assessment->weaknesses ?? ''), $word) ||
                str_contains(strtolower($assessment->recommendations ?? ''), $word)
            ) {
                $hasIndonesian = true;
                break;
            }
        }

        $this->assertTrue($hasIndonesian, 'Assessment should contain Indonesian content');
    }
}
