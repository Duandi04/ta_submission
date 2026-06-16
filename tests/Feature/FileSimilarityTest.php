<?php

namespace Tests\Feature;

use App\Models\SubmissionFile;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileSimilarityTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected User $dosen;
    protected ThesisSubmission $submission;
    protected SubmissionFile $submissionFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->student = User::factory()->mahasiswa()->create();
        $this->student->assignRole('mahasiswa');

        $this->otherStudent = User::factory()->mahasiswa()->create();
        $this->otherStudent->assignRole('mahasiswa');

        $this->dosen = User::factory()->dosen()->create();
        $this->dosen->assignRole('dosen');

        $this->submission = ThesisSubmission::factory()->create([
            'student_id' => $this->student->id,
            'title' => 'Proposal Rancang Bangun Website TA'
        ]);

        Storage::fake('local');
        $filePath = 'submissions/test_file.pdf';
        Storage::disk('local')->put($filePath, 'Fake PDF Content');

        // Note: The FileDownloadController uses Storage::disk('local')->path() and response()->download()
        // In fake storage local, the path is simulated, but we need to ensure the physical path exists
        // during testing, or at least exists on the fake local disk.
        // Let's create it in storage_path('app/submissions/test_file.pdf') just in case download() checks physical filesystem
        @mkdir(storage_path('app/submissions'), 0777, true);
        file_put_contents(storage_path('app/submissions/test_file.pdf'), 'Fake PDF Content');

        $this->submissionFile = SubmissionFile::create([
            'thesis_submission_id' => $this->submission->id,
            'file_path' => $filePath,
            'file_name' => 'test_file.pdf',
            'file_type' => 'proposal',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'storage_disk' => 'local',
            'uploaded_by' => $this->student->id
        ]);
    }

    /**
     * Test student can download and preview their own file.
     */
    public function test_student_can_download_and_preview_own_file(): void
    {
        $downloadRoute = route('files.download', $this->submissionFile);
        $previewRoute = route('files.preview', $this->submissionFile);

        $response = $this->actingAs($this->student)->get($downloadRoute);
        $response->assertStatus(200);

        $response = $this->actingAs($this->student)->get($previewRoute);
        $response->assertStatus(200);
    }

    /**
     * Test other student cannot download or preview file.
     */
    public function test_other_student_cannot_download_or_preview_file(): void
    {
        $downloadRoute = route('files.download', $this->submissionFile);
        $previewRoute = route('files.preview', $this->submissionFile);

        $response = $this->actingAs($this->otherStudent)->get($downloadRoute);
        $response->assertStatus(403);

        $response = $this->actingAs($this->otherStudent)->get($previewRoute);
        $response->assertStatus(403);
    }

    /**
     * Test dosen can download and preview any file.
     */
    public function test_dosen_can_download_and_preview_file(): void
    {
        $downloadRoute = route('files.download', $this->submissionFile);
        $previewRoute = route('files.preview', $this->submissionFile);

        $response = $this->actingAs($this->dosen)->get($downloadRoute);
        $response->assertStatus(200);

        $response = $this->actingAs($this->dosen)->get($previewRoute);
        $response->assertStatus(200);
    }

    /**
     * Test Similarity check API
     */
    public function test_similarity_check_api(): void
    {
        // Create another submission with similar title
        ThesisSubmission::factory()->create([
            'title' => 'Proposal Rancang Bangun Website Tugas Akhir'
        ]);

        $response = $this->actingAs($this->student)->postJson(route('similarity.check'), [
            'title' => 'Proposal Rancang Bangun Website TA'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'count',
            'data'
        ]);
    }
}
