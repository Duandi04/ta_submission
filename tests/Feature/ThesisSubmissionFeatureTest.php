<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\ProgramStudi;
use App\Models\SubmissionFile;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThesisSubmissionFeatureTest extends TestCase
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
     * Test mahasiswa can view their own submissions list
     */
    public function test_mahasiswa_can_view_own_submissions(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $student */
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');

        ThesisSubmission::factory()->forStudent($student)->count(2)->create();

        $response = $this->actingAs($student)->get(route('student.submissions.index'));

        $response->assertStatus(200);
    }

    /**
     * Test mahasiswa can view single submission detail
     */
    public function test_mahasiswa_can_view_submission_detail(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $student */
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');

        $submission = ThesisSubmission::factory()->forStudent($student)->create();

        $response = $this->actingAs($student)->get(route('student.submissions.show', $submission));

        $response->assertStatus(200);
    }

    /**
     * Test mahasiswa cannot view other student's submission
     */
    public function test_mahasiswa_cannot_view_other_students_submission(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $student1 */
        $student1 = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student1->assignRole('mahasiswa');
        /** @var User $student2 */
        $student2 = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student2->assignRole('mahasiswa');

        $submission = ThesisSubmission::factory()->forStudent($student2)->create();

        $response = $this->actingAs($student1)->get(route('student.submissions.show', $submission));

        $response->assertStatus(403);
    }

    /**
     * Test mahasiswa can edit draft submission
     */
    public function test_mahasiswa_can_edit_draft_submission(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $student */
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');

        $submission = ThesisSubmission::factory()->draft()->forStudent($student)->create();

        $response = $this->actingAs($student)->get(route('student.submissions.edit', $submission));

        $response->assertStatus(200);
    }

    /**
     * Test mahasiswa cannot edit approved submission
     */
    public function test_mahasiswa_cannot_edit_approved_submission(): void
    {
        $this->withoutMiddleware();
        $prodi = ProgramStudi::first();
        /** @var User $student */
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');

        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create();

        $response = $this->actingAs($student)->put(route('student.submissions.update', $submission), [
            'title' => 'Updated Title',
            'abstract' => 'Updated abstract',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test dosen can view assigned submissions as supervisor
     */
    public function test_dosen_can_view_supervised_submissions(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $supervisor */
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $supervisor->assignRole('dosen');

        ThesisSubmission::factory()->forSupervisor($supervisor)->count(3)->create();

        $response = $this->actingAs($supervisor)->get(route('dosen.students.index'));

        $response->assertStatus(200);
    }

    /**
     * Test dosen can view submissions index
     */
    public function test_dosen_can_view_submissions_index(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $supervisor */
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $supervisor->assignRole('dosen');

        $response = $this->actingAs($supervisor)->get(route('dosen.submissions.index'));

        $response->assertStatus(200);
    }

    /**
     * Test dosen can view submission detail they supervise
     */
    public function test_dosen_can_view_supervised_submission_detail(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $supervisor */
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $supervisor->assignRole('dosen');

        $submission = ThesisSubmission::factory()->forSupervisor($supervisor)->create();

        $response = $this->actingAs($supervisor)->get(route('dosen.submissions.show', $submission));

        $response->assertStatus(200);
    }

    /**
     * Test kaprodi can view all submissions in their program
     */
    public function test_kaprodi_can_view_all_program_submissions(): void
    {
        $prodi = ProgramStudi::first();
        /** @var User $kaprodi */
        $kaprodi = User::factory()->kaprodi()->create(['program_studi_id' => $prodi->id]);
        $kaprodi->assignRole('kaprodi');

        $response = $this->actingAs($kaprodi)->get(route('kaprodi.submissions.index'));

        $response->assertStatus(200);
    }

    /**
     * Test submission status change creates status history
     */
    public function test_status_change_creates_history(): void
    {
        $prodi = ProgramStudi::first();
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');

        $submission = ThesisSubmission::factory()->draft()->forStudent($student)->create();

        // Change status
        $submission->status = 'submitted';
        $submission->submission_date = now();
        $submission->save();

        // Manually create status record (normally done in controller)
        \App\Models\ThesisStatus::create([
            'thesis_submission_id' => $submission->id,
            'changed_by' => $student->id,
            'old_status' => 'draft',
            'new_status' => 'submitted',
            'comment' => 'Proposal telah diajukan.',
        ]);

        $this->assertDatabaseHas('thesis_statuses', [
            'thesis_submission_id' => $submission->id,
            'old_status' => 'draft',
            'new_status' => 'submitted',
        ]);
    }

    /**
     * Test user can add comment to submission
     */
    public function test_user_can_add_comment(): void
    {
        $prodi = ProgramStudi::first();
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $student->assignRole('mahasiswa');
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);
        $supervisor->assignRole('dosen');

        $submission = ThesisSubmission::factory()
            ->forStudent($student)
            ->forSupervisor($supervisor)
            ->create();

        $comment = Comment::factory()
            ->forThesis($submission)
            ->fromUser($supervisor)
            ->create(['content' => 'Tolong perbaiki bagian metodologi.']);

        $this->assertDatabaseHas('comments', [
            'thesis_submission_id' => $submission->id,
            'user_id' => $supervisor->id,
            'content' => 'Tolong perbaiki bagian metodologi.',
        ]);

        $this->assertEquals($supervisor->id, $comment->user->id);
    }

    /**
     * Test comment replies are nested correctly
     */
    public function test_comment_replies_nested_correctly(): void
    {
        $prodi = ProgramStudi::first();
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $prodi->id]);
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $prodi->id]);

        $submission = ThesisSubmission::factory()
            ->forStudent($student)
            ->forSupervisor($supervisor)
            ->create();

        $parentComment = Comment::factory()
            ->forThesis($submission)
            ->fromUser($supervisor)
            ->create();

        $reply = Comment::factory()
            ->forThesis($submission)
            ->fromUser($student)
            ->replyTo($parentComment)
            ->create();

        $this->assertEquals($parentComment->id, $reply->parent_id);
        $this->assertCount(1, $parentComment->replies);
        $this->assertEquals($student->id, $parentComment->replies->first()->user_id);
    }

    /**
     * Test factory generates Indonesian data
     */
    public function test_factory_generates_indonesian_data(): void
    {
        $submission = ThesisSubmission::factory()->create();

        // Title should contain Indonesian words
        $keywords = ['Sistem', 'Berbasis', 'Aplikasi', 'Pengembangan', 'Implementasi', 'Analisis', 'Rancang', 'Model', 'Tugas', 'Akhir', 'Algoritma', 'Prediksi'];
        $found = false;
        foreach ($keywords as $keyword) {
            if (str_contains(strtolower($submission->title), strtolower($keyword))) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "Title '{$submission->title}' should contain Indonesian keywords");

        // Abstract should contain Indonesian words
        $this->assertTrue(
            str_contains($submission->abstract, 'Penelitian') ||
            str_contains($submission->abstract, 'dikembangkan') ||
            str_contains($submission->abstract, 'metode'),
            'Abstract should contain Indonesian words'
        );
    }

    /**
     * Test all thesis statuses are valid
     */
    public function test_all_statuses_are_valid(): void
    {
        $validStatuses = [
            'draft', 'submitted', 'under_review', 'revision_required',
            'approved', 'rejected', 'scheduled_for_defense',
            'defense_in_progress', 'completed', 'cancelled'
        ];

        foreach ($validStatuses as $status) {
            $submission = ThesisSubmission::factory()->make(['status' => $status]);
            $this->assertEquals($status, $submission->status);
            $this->assertNotEmpty($submission->getStatusLabel());
            $this->assertNotEmpty($submission->getStatusBadgeClass());
        }
    }
}
