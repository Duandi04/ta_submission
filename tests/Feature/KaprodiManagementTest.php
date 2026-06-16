<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\Rubric;
use App\Models\Setting;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KaprodiManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $kaprodi;
    protected ProgramStudi $prodi;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
            \Database\Seeders\SettingSeeder::class,
        ]);

        $this->prodi = ProgramStudi::first();
        $this->kaprodi = User::factory()->create([
            'program_studi_id' => $this->prodi->id
        ]);
        $this->kaprodi->assignRole('kaprodi');
    }

    /**
     * Test Kaprodi can CRUD Rubrics
     */
    public function test_kaprodi_can_manage_rubrics(): void
    {
        // View rubrics list
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.rubrics.index'));
        $response->assertStatus(200);

        // View create form
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.rubrics.create'));
        $response->assertStatus(200);

        // Store Rubric
        $rubricData = [
            'name' => 'Rubrik Ujian Baru',
            'description' => 'Deskripsi rubrik ujian baru',
            'is_active' => 1,
            'criteria' => [
                ['name' => 'Kerapian', 'weight' => 40, 'description' => 'Deskripsi kerapian'],
                ['name' => 'Penguasaan', 'weight' => 60, 'description' => 'Deskripsi penguasaan'],
            ]
        ];
        
        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.rubrics.store'), $rubricData);
        $response->assertRedirect(route('kaprodi.rubrics.index'));
        $this->assertDatabaseHas('rubrics', ['name' => 'Rubrik Ujian Baru']);

        $rubric = Rubric::where('name', 'Rubrik Ujian Baru')->first();

        // View edit form
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.rubrics.edit', $rubric->id));
        $response->assertStatus(200);

        // Update Rubric
        $updatedData = [
            'name' => 'Rubrik Ujian Edit',
            'description' => 'Deskripsi edit',
            'is_active' => 1,
            'criteria' => [
                ['name' => 'Kerapian', 'weight' => 50, 'description' => 'Deskripsi kerapian'],
                ['name' => 'Penguasaan', 'weight' => 50, 'description' => 'Deskripsi penguasaan'],
            ]
        ];
        $response = $this->actingAs($this->kaprodi)->put(route('kaprodi.rubrics.update', $rubric->id), $updatedData);
        $response->assertRedirect(route('kaprodi.rubrics.index'));
        $this->assertDatabaseHas('rubrics', ['name' => 'Rubrik Ujian Edit']);

        // Delete Rubric
        $response = $this->actingAs($this->kaprodi)->delete(route('kaprodi.rubrics.destroy', $rubric->id));
        $response->assertRedirect(route('kaprodi.rubrics.index'));
        $this->assertDatabaseMissing('rubrics', ['id' => $rubric->id]);
    }

    /**
     * Test Kaprodi can update settings
     */
    public function test_kaprodi_can_update_settings(): void
    {
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.settings.index'));
        $response->assertStatus(200);

        $settingsData = [
            'max_batches' => 3,
            'attempts_per_batch' => 3,
            'submission_start' => '2026-06-01 00:00:00',
            'submission_end' => '2026-06-30 00:00:00',
        ];

        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.settings.update'), $settingsData);
        $response->assertRedirect();
        
        $this->assertEquals(3, $this->prodi->fresh()->max_batches);
    }

    /**
     * Test Kaprodi can CRUD Lecturer profiles
     */
    public function test_kaprodi_can_manage_lecturers(): void
    {
        // View lecturer management index
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.lecturers.manage.index'));
        $response->assertStatus(200);

        // View create form
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.lecturers.manage.create'));
        $response->assertStatus(200);

        // Store new Lecturer
        $lecturerData = [
            'name' => 'Dosen Baru',
            'email' => 'dosenbaru@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nim_nip' => '1990111222',
            'phone' => '08123456789',
            'role' => 'dosen',
            'program_studi_id' => $this->prodi->id,
            'is_active' => '1',
            'address' => 'Test address',
        ];

        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.lecturers.manage.store'), $lecturerData);
        $response->assertRedirect(route('kaprodi.lecturers.manage.index'));
        $this->assertDatabaseHas('users', ['email' => 'dosenbaru@test.com']);

        $lecturer = User::where('email', 'dosenbaru@test.com')->first();

        // View detail
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.lecturers.manage.show', $lecturer));
        $response->assertStatus(200);

        // Edit form
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.lecturers.manage.edit', $lecturer));
        $response->assertStatus(200);

        // Update Lecturer
        $updatedData = [
            'name' => 'Dosen Edit Name',
            'email' => 'dosenbaru@test.com',
            'nim_nip' => '1990111222',
            'phone' => '0899999999',
            'role' => 'dosen',
            'program_studi_id' => $this->prodi->id,
            'is_active' => '1',
            'address' => 'Test address updated',
        ];
        $response = $this->actingAs($this->kaprodi)->put(route('kaprodi.lecturers.manage.update', $lecturer), $updatedData);
        $response->assertRedirect(route('kaprodi.lecturers.manage.index'));
        $this->assertDatabaseHas('users', ['name' => 'Dosen Edit Name']);

        // Delete Lecturer
        $response = $this->actingAs($this->kaprodi)->delete(route('kaprodi.lecturers.manage.destroy', $lecturer));
        $response->assertRedirect(route('kaprodi.lecturers.manage.index'));
        $this->assertSoftDeleted('users', ['id' => $lecturer->id]);
    }

    /**
     * Test Lecturer Import/Export
     */
    public function test_kaprodi_can_export_import_lecturers(): void
    {
        // Export
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.lecturers.manage.export'));
        $response->assertStatus(200);

        // Import CSV structure mock
        $csvContent = "nama,email,nim_nip,telepon,program_studi,alamat\n";
        $csvContent .= "Dosen CSV,dosencsv@test.com,19955555,0812341234,{$this->prodi->name},Alamat Dosen\n";

        $file = UploadedFile::fake()->createWithContent('lecturers.csv', $csvContent);

        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.lecturers.manage.import'), [
            'file' => $file
        ]);

        $response->assertRedirect(); // Should redirect back instead of index
        $this->assertDatabaseHas('users', ['email' => 'dosencsv@test.com']);
    }

    /**
     * Test assign lecturers/evaluators and accept/reject submission
     */
    public function test_kaprodi_can_assign_lecturer_and_process_submission(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $submission = ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted'
        ]);

        $dosen1 = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        $dosen2 = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);

        // Assign lecturers (assessors)
        $rubric = Rubric::create([
            'name' => 'Rubrik Penilaian TA',
            'program_studi_id' => $this->prodi->id,
            'description' => 'Description test',
            'criteria' => [
                ['name' => 'Latar Belakang', 'weight' => 50, 'description' => 'Test'],
                ['name' => 'Metodologi', 'weight' => 50, 'description' => 'Test'],
            ],
            'is_active' => true,
        ]);
        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.submissions.assign-lecturers', $submission), [
            'assessor_ids' => [$dosen1->id, $dosen2->id],
            'rubric_id' => $rubric->id,
        ]);
        $response->assertRedirect();
        $this->assertEquals('under_review', $submission->fresh()->status);

        // Mock submitted assessments
        foreach ($submission->assessments as $assessment) {
            $assessment->update([
                'is_submitted' => true,
                'total_score' => 85
            ]);
        }

        // Accept submission
        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.submissions.accept', $submission), [
            'supervisor_id' => $dosen1->id,
            'supervisor_2_id' => $dosen2->id,
        ]);
        $response->assertRedirect();
        $this->assertEquals('approved', $submission->fresh()->status);

        // Reject submission (using a fresh separate submission that isn't approved)
        $submissionForReject = ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted'
        ]);
        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.submissions.reject', $submissionForReject->id), [
            'rejection_reason' => 'Perbaiki rumusan masalah'
        ]);
        $response->assertRedirect();
        $this->assertEquals('rejected', $submissionForReject->fresh()->status);
    }
}
