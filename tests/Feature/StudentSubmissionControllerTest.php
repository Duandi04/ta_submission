<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentRubric;
use App\Models\Rubric;
use App\Models\SubmissionFile;
use App\Models\ThesisStatus;
use App\Models\ThesisSubmission;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

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

        $this->student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $this->student->assignRole('mahasiswa');

        Storage::fake('local');
    }

    /**
     * Test SubmissionController @index
     */
    public function test_student_submissions_index(): void
    {
        ThesisSubmission::factory()->count(2)->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->get(route('student.submissions.index'));
        $response->assertStatus(200);
    }

    /**
     * Test SubmissionController @create
     */
    public function test_student_submissions_create(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.submissions.create'));
        $response->assertStatus(200);
    }

    /**
     * Test SubmissionController @store validation and success
     */
    public function test_student_submissions_store_validation_and_success(): void
    {
        // Validation failure
        $response = $this->actingAs($this->student)->post(route('student.submissions.store'), []);
        $response->assertSessionHasErrors(['title', 'abstract', 'proposal_file']);

        // Successful store
        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');
        $payload = [
            'title' => 'Sistem Pendeteksi Objek Baru',
            'abstract' => 'Penelitian ini mendeteksi objek dengan model baru.',
            'research_field' => 'Artificial Intelligence',
            'proposal_file' => $file,
        ];

        $responseSuccess = $this->actingAs($this->student)->post(route('student.submissions.store'), $payload);

        $submission = ThesisSubmission::where('title', 'Sistem Pendeteksi Objek Baru')->first();
        $this->assertNotNull($submission);
        $responseSuccess->assertRedirect(route('student.submissions.show', $submission));
    }

    /**
     * Test SubmissionController @show
     */
    public function test_student_submissions_show(): void
    {
        $submission = ThesisSubmission::factory()->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->get(route('student.submissions.show', $submission));
        $response->assertStatus(200);

        // Accessing other student's submission -> 403
        $otherStudent = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $otherStudent->assignRole('mahasiswa');

        $response403 = $this->actingAs($otherStudent)->get(route('student.submissions.show', $submission));
        $response403->assertStatus(403);
    }

    /**
     * Test SubmissionController @edit
     */
    public function test_student_submissions_edit(): void
    {
        $submission = ThesisSubmission::factory()->draft()->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->get(route('student.submissions.edit', $submission));
        $response->assertStatus(200);

        // Edit approved submission -> 403
        $approvedSubmission = ThesisSubmission::factory()->approved()->forStudent($this->student)->create();
        $responseApproved = $this->actingAs($this->student)->get(route('student.submissions.edit', $approvedSubmission));
        $responseApproved->assertStatus(403);
    }

    /**
     * Test SubmissionController @update
     */
    public function test_student_submissions_update(): void
    {
        $submission = ThesisSubmission::factory()->draft()->forStudent($this->student)->create();

        $payload = [
            'title' => 'Updated Title',
            'abstract' => 'Updated abstract description.',
            'research_field' => 'Software Engineering',
        ];

        $response = $this->actingAs($this->student)->put(route('student.submissions.update', $submission), $payload);
        $response->assertRedirect(route('student.submissions.show', $submission));
        $this->assertEquals('Updated Title', $submission->fresh()->title);

        // Cannot update approved submission
        $approvedSubmission = ThesisSubmission::factory()->approved()->forStudent($this->student)->create();
        $response403 = $this->actingAs($this->student)->put(route('student.submissions.update', $approvedSubmission), $payload);
        $response403->assertStatus(403);
    }

    /**
     * Test SubmissionController @destroy
     */
    public function test_student_submissions_destroy(): void
    {
        $submission = ThesisSubmission::factory()->draft()->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->delete(route('student.submissions.destroy', $submission));
        $response->assertRedirect(route('student.submissions.index'));
        $this->assertSoftDeleted('thesis_submissions', ['id' => $submission->id]);

        // Cannot destroy approved submission
        $approvedSubmission = ThesisSubmission::factory()->approved()->forStudent($this->student)->create();
        $response403 = $this->actingAs($this->student)->delete(route('student.submissions.destroy', $approvedSubmission));
        $response403->assertStatus(403);
    }

    /**
     * Test SubmissionController @cancel
     */
    public function test_student_submissions_cancel(): void
    {
        $submission = ThesisSubmission::factory()->submitted()->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->patch(route('student.submissions.cancel', $submission));
        $response->assertRedirect(route('student.submissions.index'));
        $this->assertEquals('cancelled', $submission->fresh()->status);

        // Cancel approved submission -> 403
        $approvedSubmission = ThesisSubmission::factory()->approved()->forStudent($this->student)->create();
        $response403 = $this->actingAs($this->student)->patch(route('student.submissions.cancel', $approvedSubmission));
        $response403->assertStatus(403);
    }

    /**
     * Test SubmissionController @submit
     */
    public function test_student_submissions_submit(): void
    {
        $submission = ThesisSubmission::factory()->draft()->forStudent($this->student)->create();

        $response = $this->actingAs($this->student)->patch(route('student.submissions.submit', $submission));
        $response->assertRedirect(route('student.submissions.show', $submission));
        $this->assertEquals('submitted', $submission->fresh()->status);

        // Submit non-draft submission -> 403
        $approvedSubmission = ThesisSubmission::factory()->approved()->forStudent($this->student)->create();
        $response403 = $this->actingAs($this->student)->patch(route('student.submissions.submit', $approvedSubmission));
        $response403->assertStatus(403);
    }

    /**
     * Test FileDownloadController @download and @preview
     */
    public function test_file_download_and_preview(): void
    {
        $submission = ThesisSubmission::factory()->forStudent($this->student)->create([
            'title' => 'Sistem IoT Keren',
        ]);

        $file = SubmissionFile::create([
            'thesis_submission_id' => $submission->id,
            'file_name' => 'proposal.pdf',
            'file_path' => 'submissions/proposal.pdf',
            'file_type' => 'proposal',
            'file_size' => 500,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->student->id,
        ]);

        // Put fake file on disk
        Storage::disk('local')->put('submissions/proposal.pdf', 'dummy content');

        // Test download success
        $responseDownload = $this->actingAs($this->student)->get(route('files.download', $file));
        $responseDownload->assertStatus(200);
        $responseDownload->assertHeader('Content-Disposition', 'attachment; filename="[' . $this->student->name . '] sistem iot keren.pdf"');

        // Test preview success
        $responsePreview = $this->actingAs($this->student)->get(route('files.preview', $file));
        $responsePreview->assertStatus(200);

        // Test other student blocked
        $otherStudent = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $otherStudent->assignRole('mahasiswa');

        $responseBlocked = $this->actingAs($otherStudent)->get(route('files.download', $file));
        $responseBlocked->assertStatus(403);

        $responsePreviewBlocked = $this->actingAs($otherStudent)->get(route('files.preview', $file));
        $responsePreviewBlocked->assertStatus(403);

        // Test elevated access (Dosen can access)
        $dosen = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        $dosen->assignRole('dosen');

        $responseDosen = $this->actingAs($dosen)->get(route('files.download', $file));
        $responseDosen->assertStatus(200);

        // Test file not found on server
        Storage::disk('local')->delete('submissions/proposal.pdf');
        $response404 = $this->actingAs($this->student)->get(route('files.download', $file));
        $response404->assertStatus(404);

        $responsePreview404 = $this->actingAs($this->student)->get(route('files.preview', $file));
        $responsePreview404->assertStatus(404);
    }

    /**
     * Test FileDownloadController profile photo logic
     */
    public function test_profile_photo_route(): void
    {
        // Default photo (null)
        $responseDefault = $this->actingAs($this->student)->get(route('users.photo', $this->student));
        $responseDefault->assertRedirect(); // Should redirect to default asset path

        // Uploaded photo not exists on server
        $this->student->profile_photo = 'photos/avatar.png';
        $this->student->save();

        $responseMissing = $this->actingAs($this->student)->get(route('users.photo', $this->student));
        $responseMissing->assertRedirect();

        // Photo exists on server
        Storage::disk('local')->put('photos/avatar.png', 'fake image binary data');
        $responseSuccess = $this->actingAs($this->student)->get(route('users.photo', $this->student));
        $responseSuccess->assertStatus(200);
    }

    /**
     * Test AssessmentRubric Model and Rubric programStudi relationship
     */
    public function test_rubric_models_and_relationships(): void
    {
        $submission = ThesisSubmission::factory()->create();
        
        $assessmentRubric = AssessmentRubric::create([
            'thesis_submission_id' => $submission->id,
            'name' => 'Rubrik Test',
            'description' => 'Desc',
            'criteria' => [['name' => 'C1', 'weight' => 100]],
        ]);
        
        $this->assertEquals($submission->id, $assessmentRubric->thesisSubmission->id);

        $rubric = Rubric::create([
            'name' => 'Rubrik Test 2',
            'criteria' => [],
            'is_active' => true,
            'program_studi_id' => $this->prodi->id,
        ]);
        
        $this->assertEquals($this->prodi->id, $rubric->programStudi->id);
    }

    /**
     * Test SubmissionFile helper methods
     */
    public function test_submission_file_helpers(): void
    {
        $submission = ThesisSubmission::factory()->create();
        $file = SubmissionFile::create([
            'thesis_submission_id' => $submission->id,
            'file_name' => 'proposal.pdf',
            'file_path' => 'submissions/proposal.pdf',
            'file_type' => 'proposal',
            'file_size' => 1500,
            'mime_type' => 'application/pdf',
            'uploaded_by' => $this->student->id,
        ]);

        $this->assertEquals($submission->id, $file->thesisSubmission->id);
        $this->assertEquals($this->student->id, $file->uploader->id);
        
        $this->assertEquals('1.46 KB', $file->getFormattedFileSize());
        $this->assertEquals('Proposal', $file->getFileTypeLabel());

        $file->file_type = 'final_document';
        $this->assertEquals('Dokumen Akhir', $file->getFileTypeLabel());

        $file->file_type = 'presentation';
        $this->assertEquals('Presentasi', $file->getFileTypeLabel());

        $file->file_type = 'unknown';
        $this->assertEquals('Lainnya', $file->getFileTypeLabel());

        // Test format for larger files
        $file->file_size = 1024 * 1024 * 3.5; // 3.5 MB
        $this->assertEquals('3.5 MB', $file->getFormattedFileSize());

        $file->file_size = 500; // 500 B
        $this->assertEquals('500 B', $file->getFormattedFileSize());
    }

    /**
     * Test ThesisStatus relationships
     */
    public function test_thesis_status_relationships(): void
    {
        $submission = ThesisSubmission::factory()->create();
        $status = ThesisStatus::create([
            'thesis_submission_id' => $submission->id,
            'changed_by' => $this->student->id,
            'old_status' => 'draft',
            'new_status' => 'submitted',
            'comment' => 'Test comment',
        ]);

        $this->assertEquals($submission->id, $status->thesisSubmission->id);
        $this->assertEquals($this->student->id, $status->changer->id);
    }

    /**
     * Test SimilarityHelper
     */
    public function test_similarity_helper(): void
    {
        $student = User::factory()->mahasiswa()->create(['name' => 'John Doe']);
        $student->assignRole('mahasiswa');
        $submission = ThesisSubmission::factory()->approved()->forStudent($student)->create([
            'title' => 'Sistem Informasi Akademik Sekolah Dasar Berbasis Web',
        ]);

        $results = \App\Helpers\SimilarityHelper::findSimilarSubmissions(
            'Sistem Informasi Akademik Sekolah Dasar Berbasis Web',
            50,
            $submission->id + 1
        );

        $this->assertTrue($results->count() > 0);
        $first = $results->first();
        $this->assertEquals($submission->id, $first->id);
        $this->assertEquals('John Doe', $first->student->name);
        $this->assertEquals('approved', $first->status);
    }
}
