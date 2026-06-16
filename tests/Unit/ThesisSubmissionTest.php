<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Comment;
use App\Models\ProgramStudi;
use App\Models\SubmissionFile;
use App\Models\ThesisSubmission;
use App\Models\ThesisStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisSubmissionTest extends TestCase
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

    /**
     * Test that a thesis submission can be created with factory
     */
    public function test_can_create_thesis_submission(): void
    {
        $submission = ThesisSubmission::factory()->create();

        $this->assertDatabaseHas('thesis_submissions', [
            'id' => $submission->id,
            'title' => $submission->title,
        ]);
    }

    /**
     * Test thesis submission belongs to a student
     */
    public function test_thesis_belongs_to_student(): void
    {
        $student = User::factory()->create();
        $submission = ThesisSubmission::factory()->forStudent($student)->create();

        $this->assertEquals($student->id, $submission->student->id);
        $this->assertInstanceOf(User::class, $submission->student);
    }

    /**
     * Test thesis submission belongs to a supervisor
     */
    public function test_thesis_belongs_to_supervisor(): void
    {
        $supervisor = User::factory()->create();
        $submission = ThesisSubmission::factory()->forSupervisor($supervisor)->create();

        $this->assertEquals($supervisor->id, $submission->supervisor->id);
        $this->assertInstanceOf(User::class, $submission->supervisor);
    }

    /**
     * Test thesis has many files
     */
    public function test_thesis_has_many_files(): void
    {
        $submission = ThesisSubmission::factory()->create();
        SubmissionFile::factory()->count(3)->forThesis($submission)->create();

        $this->assertCount(3, $submission->files);
        $this->assertInstanceOf(SubmissionFile::class, $submission->files->first());
    }

    /**
     * Test thesis has many assessments
     */
    public function test_thesis_has_many_assessments(): void
    {
        $submission = ThesisSubmission::factory()->create();
        Assessment::factory()->count(2)->forThesis($submission)->create();

        $this->assertCount(2, $submission->assessments);
        $this->assertInstanceOf(Assessment::class, $submission->assessments->first());
    }

    /**
     * Test thesis has many comments
     */
    public function test_thesis_has_many_comments(): void
    {
        $submission = ThesisSubmission::factory()->create();
        Comment::factory()->count(5)->forThesis($submission)->create();

        $this->assertCount(5, $submission->comments);
        $this->assertInstanceOf(Comment::class, $submission->comments->first());
    }

    /**
     * Test thesis has many status changes
     */
    public function test_thesis_has_many_statuses(): void
    {
        $submission = ThesisSubmission::factory()->create();
        ThesisStatus::factory()->count(3)->forThesis($submission)->create();

        $this->assertCount(3, $submission->statuses);
        $this->assertInstanceOf(ThesisStatus::class, $submission->statuses->first());
    }

    /**
     * Test getStatusBadgeClass returns correct class for each status
     */
    public function test_get_status_badge_class(): void
    {
        $statusClasses = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'warning',
            'revision_required' => 'danger',
            'approved' => 'success',
            'rejected' => 'dark',
            'scheduled_for_defense' => 'primary',
            'defense_in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        foreach ($statusClasses as $status => $expectedClass) {
            $submission = ThesisSubmission::factory()->make(['status' => $status]);
            $this->assertEquals($expectedClass, $submission->getStatusBadgeClass(), "Failed for status: {$status}");
        }
    }

    /**
     * Test getStatusLabel returns correct Indonesian label for each status
     */
    public function test_get_status_label(): void
    {
        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Sudah Diajukan',
            'under_review' => 'Sedang Ditinjau',
            'revision_required' => 'Perlu Revisi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'scheduled_for_defense' => 'Dijadwalkan Sidang',
            'defense_in_progress' => 'Sedang Sidang',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        foreach ($statusLabels as $status => $expectedLabel) {
            $submission = ThesisSubmission::factory()->make(['status' => $status]);
            $this->assertEquals($expectedLabel, $submission->getStatusLabel(), "Failed for status: {$status}");
        }
    }

    /**
     * Test canBeEditedByStudent returns true for draft and revision_required
     */
    public function test_can_be_edited_by_student(): void
    {
        $editableStatuses = ['draft', 'revision_required'];
        $nonEditableStatuses = ['submitted', 'under_review', 'approved', 'completed', 'cancelled'];

        foreach ($editableStatuses as $status) {
            $submission = ThesisSubmission::factory()->make(['status' => $status]);
            $this->assertTrue($submission->canBeEditedByStudent(), "Should be editable for status: {$status}");
        }

        foreach ($nonEditableStatuses as $status) {
            $submission = ThesisSubmission::factory()->make(['status' => $status]);
            $this->assertFalse($submission->canBeEditedByStudent(), "Should not be editable for status: {$status}");
        }
    }

    /**
     * Test scopeByStatus filters correctly
     */
    public function test_scope_by_status(): void
    {
        ThesisSubmission::factory()->draft()->create();
        ThesisSubmission::factory()->draft()->create();
        ThesisSubmission::factory()->submitted()->create();

        $drafts = ThesisSubmission::byStatus('draft')->get();
        $submitted = ThesisSubmission::byStatus('submitted')->get();

        $this->assertCount(2, $drafts);
        $this->assertCount(1, $submitted);
    }

    /**
     * Test scopeByStudent filters correctly
     */
    public function test_scope_by_student(): void
    {
        $student1 = User::factory()->create();
        $student2 = User::factory()->create();

        ThesisSubmission::factory()->forStudent($student1)->count(2)->create();
        ThesisSubmission::factory()->forStudent($student2)->create();

        $student1Submissions = ThesisSubmission::byStudent($student1->id)->get();
        $student2Submissions = ThesisSubmission::byStudent($student2->id)->get();

        $this->assertCount(2, $student1Submissions);
        $this->assertCount(1, $student2Submissions);
    }

    /**
     * Test scopeBySupervisor filters correctly
     */
    public function test_scope_by_supervisor(): void
    {
        $supervisor1 = User::factory()->create();
        $supervisor2 = User::factory()->create();

        ThesisSubmission::factory()->forSupervisor($supervisor1)->count(3)->create();
        ThesisSubmission::factory()->forSupervisor($supervisor2)->create();

        $supervisor1Submissions = ThesisSubmission::bySupervisor($supervisor1->id)->get();
        $supervisor2Submissions = ThesisSubmission::bySupervisor($supervisor2->id)->get();

        $this->assertCount(3, $supervisor1Submissions);
        $this->assertCount(1, $supervisor2Submissions);
    }

    /**
     * Test getLatestFile returns most recent file
     */
    public function test_get_latest_file(): void
    {
        $submission = ThesisSubmission::factory()->create();
        
        $oldFile = SubmissionFile::factory()->proposal()->forThesis($submission)->create([
            'created_at' => now()->subDays(5),
        ]);
        $newFile = SubmissionFile::factory()->finalDocument()->forThesis($submission)->create([
            'created_at' => now(),
        ]);

        $latestFile = $submission->getLatestFile();

        $this->assertEquals($newFile->id, $latestFile->id);
    }

    /**
     * Test soft delete works correctly
     */
    public function test_soft_delete_works(): void
    {
        $submission = ThesisSubmission::factory()->create();
        $submissionId = $submission->id;

        $submission->delete();

        $this->assertSoftDeleted('thesis_submissions', ['id' => $submissionId]);
        $this->assertNull(ThesisSubmission::find($submissionId));
        $this->assertNotNull(ThesisSubmission::withTrashed()->find($submissionId));
    }

    /**
     * Test factory states work correctly
     */
    public function test_factory_states(): void
    {
        $draft = ThesisSubmission::factory()->draft()->create();
        $submitted = ThesisSubmission::factory()->submitted()->create();
        $completed = ThesisSubmission::factory()->completed()->create();

        $this->assertEquals('draft', $draft->status);
        $this->assertEquals('submitted', $submitted->status);
        $this->assertEquals('completed', $completed->status);
        $this->assertNotNull($completed->final_score);
    }
}
