<?php

namespace Tests\Unit;

use App\Models\ProgramStudi;
use App\Models\Setting;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Services\Student\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubmissionService $service;
    protected User $student;
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
        $this->student = User::factory()->create([
            'program_studi_id' => $this->prodi->id,
        ]);
        $this->student->assignRole('mahasiswa');

        $this->service = new SubmissionService();
        Auth::login($this->student);
        Storage::fake('local');
    }

    public function test_get_student_submissions(): void
    {
        ThesisSubmission::factory()->count(15)->forStudent($this->student)->create();

        $result = $this->service->getStudentSubmissions(10);

        $this->assertEquals(15, $result->total());
        $this->assertCount(10, $result->items());
    }

    public function test_create_submission_success(): void
    {
        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');
        $data = [
            'title' => 'Judul Baru',
            'abstract' => 'Abstrak baru',
        ];

        $submission = $this->service->create($data, $file);

        $this->assertDatabaseHas('thesis_submissions', [
            'id' => $submission->id,
            'title' => 'Judul Baru',
            'status' => 'draft',
        ]);

        $this->assertEquals(1, $submission->files->count());
        $this->assertEquals('proposal.pdf', $submission->files->first()->file_name);
        $this->assertTrue(Storage::disk('local')->exists($submission->files->first()->file_path));
    }

    public function test_create_submission_fails_when_before_start_deadline(): void
    {
        $this->prodi->update([
            'submission_start' => now()->addDays(2),
            'submission_end' => now()->addDays(5),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Masa pengajuan belum dimulai');

        $this->service->create(['title' => 'Test', 'abstract' => 'Abstrak']);
    }

    public function test_create_submission_fails_when_after_end_deadline(): void
    {
        $this->prodi->update([
            'submission_start' => now()->subDays(5),
            'submission_end' => now()->subDays(2),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Masa pengajuan telah berakhir');

        $this->service->create(['title' => 'Test', 'abstract' => 'Abstrak']);
    }

    public function test_create_submission_fails_when_has_approved_submission(): void
    {
        ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'approved',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Anda sudah memiliki pengajuan yang disetujui');

        $this->service->create(['title' => 'Test', 'abstract' => 'Abstrak']);
    }

    public function test_create_submission_fails_when_max_total_limit_reached(): void
    {
        // Limit is attempts_per_batch * max_batches. From SettingSeeder, it defaults to 3 * 2 = 6.
        // Let's create 6 rejected/cancelled submissions
        ThesisSubmission::factory()->count(6)->create([
            'student_id' => $this->student->id,
            'status' => 'rejected',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Anda telah mencapai batas maksimal total pengajuan');

        $this->service->create(['title' => 'Test', 'abstract' => 'Abstrak']);
    }

    public function test_create_submission_fails_when_current_batch_not_all_finished(): void
    {
        // Create 3 submissions, where one is still draft/submitted/under_review
        ThesisSubmission::factory()->count(2)->create([
            'student_id' => $this->student->id,
            'status' => 'rejected',
        ]);
        ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'submitted',
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Batch pengajuan Anda saat ini (3 judul) belum selesai diproses');

        $this->service->create(['title' => 'Test', 'abstract' => 'Abstrak']);
    }

    public function test_create_submission_allows_exceeding_limit(): void
    {
        $this->student->update(['can_exceed_submission_limit' => true]);
        ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'approved',
        ]);

        $submission = $this->service->create(['title' => 'Test Over Limit', 'abstract' => 'Abstrak']);
        $this->assertDatabaseHas('thesis_submissions', ['id' => $submission->id, 'title' => 'Test Over Limit']);
    }

    public function test_update_submission_success(): void
    {
        $submission = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'draft',
        ]);

        $oldFile = UploadedFile::fake()->create('old.pdf', 500, 'application/pdf');
        $this->service->update($submission, [], $oldFile);
        $this->assertEquals(1, $submission->files->count());
        $oldPath = $submission->files->first()->file_path;

        $newFile = UploadedFile::fake()->create('new.pdf', 500, 'application/pdf');
        $updated = $this->service->update($submission, [
            'title' => 'Updated Title',
        ], $newFile);

        $updated->refresh();

        $this->assertEquals('Updated Title', $updated->title);
        $this->assertEquals(1, $updated->files->count());
        $this->assertEquals('new.pdf', $updated->files->first()->file_name);

        $this->assertFalse(Storage::disk('local')->exists($oldPath));
        $this->assertTrue(Storage::disk('local')->exists($updated->files->first()->file_path));
    }

    public function test_delete_submission(): void
    {
        $submission = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'draft',
        ]);

        $this->service->delete($submission);

        $this->assertSoftDeleted('thesis_submissions', ['id' => $submission->id]);
    }

    public function test_can_edit(): void
    {
        $draft = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'draft',
        ]);
        $approved = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'approved',
        ]);

        $otherStudent = User::factory()->create();
        $otherSubmission = ThesisSubmission::factory()->create([
            'student_id' => $otherStudent->id,
            'status' => 'draft',
        ]);

        $this->assertTrue($this->service->canEdit($draft));
        $this->assertFalse($this->service->canEdit($approved));
        $this->assertFalse($this->service->canEdit($otherSubmission));
    }

    public function test_can_delete(): void
    {
        $draft = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'draft',
        ]);
        $submitted = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'submitted',
        ]);

        $otherStudent = User::factory()->create();
        $otherSubmission = ThesisSubmission::factory()->create([
            'student_id' => $otherStudent->id,
            'status' => 'draft',
        ]);

        $this->assertTrue($this->service->canDelete($draft));
        $this->assertFalse($this->service->canDelete($submitted));
        $this->assertFalse($this->service->canDelete($otherSubmission));
    }
}
