<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
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
        ]);
    }

    /**
     * Test comment can be created with factory
     */
    public function test_can_create_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => $comment->content,
        ]);
    }

    /**
     * Test comment belongs to thesis submission
     */
    public function test_comment_belongs_to_thesis(): void
    {
        $thesis = ThesisSubmission::factory()->create();
        $comment = Comment::factory()->forThesis($thesis)->create();

        $this->assertEquals($thesis->id, $comment->thesisSubmission->id);
    }

    /**
     * Test comment belongs to user
     */
    public function test_comment_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->fromUser($user)->create();

        $this->assertEquals($user->id, $comment->user->id);
    }

    /**
     * Test comment can have replies
     */
    public function test_comment_can_have_replies(): void
    {
        $parentComment = Comment::factory()->create();
        
        $reply1 = Comment::factory()->replyTo($parentComment)->create();
        $reply2 = Comment::factory()->replyTo($parentComment)->create();

        $this->assertCount(2, $parentComment->replies);
        $this->assertEquals($parentComment->id, $reply1->parent_id);
        $this->assertEquals($parentComment->id, $reply2->parent_id);
    }

    /**
     * Test reply has parent
     */
    public function test_reply_has_parent(): void
    {
        $parentComment = Comment::factory()->create();
        $reply = Comment::factory()->replyTo($parentComment)->create();

        $this->assertNotNull($reply->parent);
        $this->assertEquals($parentComment->id, $reply->parent->id);
    }

    /**
     * Test top level scope
     */
    public function test_top_level_scope(): void
    {
        $thesis = ThesisSubmission::factory()->create();
        
        // Create 3 top-level comments
        $topLevel1 = Comment::factory()->forThesis($thesis)->create();
        $topLevel2 = Comment::factory()->forThesis($thesis)->create();
        $topLevel3 = Comment::factory()->forThesis($thesis)->create();
        
        // Create 2 replies
        Comment::factory()->forThesis($thesis)->replyTo($topLevel1)->create();
        Comment::factory()->forThesis($thesis)->replyTo($topLevel2)->create();

        $topLevelComments = Comment::topLevel()->where('thesis_submission_id', $thesis->id)->get();

        $this->assertCount(3, $topLevelComments);
    }

    /**
     * Test supervisor comment factory state
     */
    public function test_supervisor_comment_state(): void
    {
        $comment = Comment::factory()->fromSupervisor()->create();

        $supervisorKeywords = [
            'Bagus', 'silakan', 'lanjutkan', 'perbaikan', 'metodologi',
            'referensi', 'BAB', 'format', 'hasil', 'diagram', 'revisi', 'disetujui'
        ];

        $hasKeyword = false;
        foreach ($supervisorKeywords as $keyword) {
            if (str_contains($comment->content, $keyword)) {
                $hasKeyword = true;
                break;
            }
        }

        $this->assertTrue($hasKeyword, 'Supervisor comment should contain relevant keywords');
    }

    /**
     * Test examiner comment factory state
     */
    public function test_examiner_comment_state(): void
    {
        $comment = Comment::factory()->fromExaminer()->create();

        $examinerKeywords = [
            'Bagaimana', 'keunggulan', 'jelaskan', 'arsitektur',
            'error', 'pengujian', 'waktu', 'rencana', 'keterbatasan'
        ];

        $hasKeyword = false;
        foreach ($examinerKeywords as $keyword) {
            if (str_contains($comment->content, $keyword)) {
                $hasKeyword = true;
                break;
            }
        }

        $this->assertTrue($hasKeyword, 'Examiner comment should contain question-like keywords');
    }

    /**
     * Test student reply factory state
     */
    public function test_student_reply_state(): void
    {
        $comment = Comment::factory()->studentReply()->create();

        $studentKeywords = [
            'Baik', 'Terima kasih', 'revisi', 'upload', 'penjelasan', 'tambahkan'
        ];

        $hasKeyword = false;
        foreach ($studentKeywords as $keyword) {
            if (str_contains($comment->content, $keyword)) {
                $hasKeyword = true;
                break;
            }
        }

        $this->assertTrue($hasKeyword, 'Student reply should contain appropriate keywords');
    }

    /**
     * Test comment soft delete
     */
    public function test_comment_soft_delete(): void
    {
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $comment->delete();

        $this->assertSoftDeleted('comments', ['id' => $commentId]);
    }

    /**
     * Test nested replies work correctly
     */
    public function test_nested_replies(): void
    {
        $thesis = ThesisSubmission::factory()->create();
        $supervisor = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();

        // Supervisor posts first comment
        $originalComment = Comment::factory()
            ->forThesis($thesis)
            ->fromUser($supervisor)
            ->fromSupervisor()
            ->create();

        // Student replies
        $studentReply = Comment::factory()
            ->forThesis($thesis)
            ->fromUser($student)
            ->studentReply()
            ->replyTo($originalComment)
            ->create();

        // Supervisor follows up
        $supervisorFollowUp = Comment::factory()
            ->forThesis($thesis)
            ->fromUser($supervisor)
            ->fromSupervisor()
            ->replyTo($studentReply)
            ->create();

        $this->assertCount(1, $originalComment->replies);
        $this->assertCount(1, $studentReply->replies);
        $this->assertEquals($student->id, $originalComment->replies->first()->user_id);
        $this->assertEquals($supervisor->id, $studentReply->replies->first()->user_id);
    }

    /**
     * Test comment factory generates Indonesian content
     */
    public function test_comment_factory_generates_indonesian_content(): void
    {
        // Create multiple comments to check diversity
        $comments = Comment::factory()->count(5)->create();

        $indonesianWords = [
            'Bagus', 'silakan', 'perbaikan', 'metodologi', 'referensi',
            'Terima kasih', 'revisi', 'Bagaimana', 'jelaskan', 'pengujian'
        ];

        $found = false;
        foreach ($comments as $comment) {
            foreach ($indonesianWords as $word) {
                if (str_contains($comment->content, $word)) {
                    $found = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($found, 'Comment factory should generate Indonesian content');
    }

    /**
     * Test comments are ordered by creation date
     */
    public function test_comments_ordered_by_date(): void
    {
        $thesis = ThesisSubmission::factory()->create();

        $oldComment = Comment::factory()->forThesis($thesis)->create([
            'created_at' => now()->subDays(2),
        ]);
        $newComment = Comment::factory()->forThesis($thesis)->create([
            'created_at' => now(),
        ]);
        $middleComment = Comment::factory()->forThesis($thesis)->create([
            'created_at' => now()->subDay(),
        ]);

        $orderedComments = $thesis->comments()->orderBy('created_at', 'desc')->get();

        $this->assertEquals($newComment->id, $orderedComments->first()->id);
        $this->assertEquals($oldComment->id, $orderedComments->last()->id);
    }
}
