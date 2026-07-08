<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KaprodiStudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $kaprodi;
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

        $this->kaprodi = User::factory()->create(['program_studi_id' => $this->prodi->id]);
        $this->kaprodi->assignRole('kaprodi');

        Storage::fake('local');
    }

    /**
     * Test StudentController index filters
     */
    public function test_student_controller_index_filters(): void
    {
        $studentSubmitted = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
            'name' => 'Student A',
        ]);
        $studentSubmitted->assignRole('mahasiswa');

        $studentNotSubmitted = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
            'name' => 'Student B',
        ]);
        $studentNotSubmitted->assignRole('mahasiswa');

        // Create submission for A (submitted)
        ThesisSubmission::factory()->submitted()->forStudent($studentSubmitted)->create();

        // Create draft submission for B (still considered not submitted in filters)
        ThesisSubmission::factory()->draft()->forStudent($studentNotSubmitted)->create();

        // Already submitted filter
        $response1 = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.index', [
            'filter_status' => 'sudah_mengumpulkan',
            'sort_by' => 'name',
            'sort_order' => 'asc'
        ]));
        $response1->assertStatus(200);
        $response1->assertSee('Student A');
        $response1->assertDontSee('Student B');

        // Not submitted filter
        $response2 = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.index', [
            'filter_status' => 'belum_mengumpulkan',
            'sort_by' => 'name',
            'sort_order' => 'desc'
        ]));
        $response2->assertStatus(200);
        $response2->assertSee('Student B');
        $response2->assertDontSee('Student A');
    }

    /**
     * Test StudentController create
     */
    public function test_student_controller_create(): void
    {
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.create'));
        $response->assertStatus(200);
    }

    /**
     * Test StudentController store
     */
    public function test_student_controller_store(): void
    {
        $photo = UploadedFile::fake()->image('profile.jpg');

        $payload = [
            'name' => 'Mahasiswa Baru',
            'email' => 'mhsbaru@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim_nip' => '1122339900',
            'phone' => '081211112222',
            'address' => 'Alamat baru',
            'program_studi_id' => $this->prodi->id,
            'angkatan' => '2023',
            'role' => 'mahasiswa',
            'is_active' => '1',
            'can_exceed_submission_limit' => '1',
            'profile_photo' => $photo,
        ];

        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.students.manage.store'), $payload);
        $response->assertRedirect(route('kaprodi.students.manage.index'));

        $this->assertDatabaseHas('users', ['email' => 'mhsbaru@example.com']);
        $student = User::where('email', 'mhsbaru@example.com')->first();
        $this->assertNotNull($student->profile_photo);
    }

    /**
     * Test StudentController show & edit aborts / permissions
     */
    public function test_student_controller_show_and_edit(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        $responseShow = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.show', $student));
        $responseShow->assertStatus(200);

        $responseEdit = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.edit', $student));
        $responseEdit->assertStatus(200);

        // Accessing other program studi's student -> abort 404
        $otherProdi = ProgramStudi::factory()->create();
        $otherStudent = User::factory()->mahasiswa()->create(['program_studi_id' => $otherProdi->id]);
        $otherStudent->assignRole('mahasiswa');

        $responseShowOther = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.show', $otherStudent));
        $responseShowOther->assertStatus(404);

        $responseEditOther = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.edit', $otherStudent));
        $responseEditOther->assertStatus(404);
    }

    /**
     * Test StudentController update
     */
    public function test_student_controller_update(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        $payload = [
            'name' => 'Updated Name',
            'email' => $student->email,
            'password' => 'Password123new!',
            'password_confirmation' => 'Password123new!',
            'nim_nip' => '1122339900',
            'phone' => '081211112222',
            'address' => 'Alamat baru',
            'program_studi_id' => $this->prodi->id,
            'angkatan' => '2023',
            'role' => 'mahasiswa',
            'is_active' => '1',
            'can_exceed_submission_limit' => '1',
        ];

        // Test normal update with new password
        $response = $this->actingAs($this->kaprodi)->put(route('kaprodi.students.manage.update', $student), $payload);
        $response->assertRedirect(route('kaprodi.students.manage.index'));
        $this->assertEquals('Updated Name', $student->fresh()->name);

        // Test update profile photo
        $photo = UploadedFile::fake()->image('profile_new.png');
        $payload['profile_photo'] = $photo;
        $this->actingAs($this->kaprodi)->put(route('kaprodi.students.manage.update', $student), $payload);
        $student->refresh();
        $this->assertNotNull($student->profile_photo);

        // Test remove profile photo
        $payload2 = $payload;
        unset($payload2['profile_photo']);
        $payload2['remove_photo'] = '1';
        $this->actingAs($this->kaprodi)->put(route('kaprodi.students.manage.update', $student), $payload2);
        $student->refresh();
        $this->assertNull($student->profile_photo);
    }

    /**
     * Test StudentController destroy
     */
    public function test_student_controller_destroy(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        $response = $this->actingAs($this->kaprodi)->delete(route('kaprodi.students.manage.destroy', $student));
        $response->assertRedirect(route('kaprodi.students.manage.index'));
        $this->assertSoftDeleted('users', ['id' => $student->id]);
    }

    /**
     * Test StudentController export
     */
    public function test_student_controller_export(): void
    {
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test StudentController import and update user path in UserImport
     */
    public function test_student_controller_import(): void
    {
        // Import CSV content (create and update)
        $csvContent = "nama,email,nim_nip,telepon,program_studi,alamat,angkatan,password\n";
        $csvContent .= "Imported Student,imported_student@example.com,77889900,081299998888,{$this->prodi->name},Alamat Import,2022,password123\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $csvContent);

        $response = $this->actingAs($this->kaprodi)->post(route('kaprodi.students.manage.import'), [
            'file' => $file
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'imported_student@example.com']);

        // Import again to test update user branch in UserImport
        $csvContentUpdate = "nama,email,nim_nip,telepon,program_studi,alamat,angkatan,password\n";
        $csvContentUpdate .= "Imported Student Updated,imported_student@example.com,77889900,081299998888,{$this->prodi->name},Alamat Import Updated,2022,password123new\n";
        
        $fileUpdate = UploadedFile::fake()->createWithContent('students.csv', $csvContentUpdate);

        $responseUpdate = $this->actingAs($this->kaprodi)->post(route('kaprodi.students.manage.import'), [
            'file' => $fileUpdate
        ]);

        $responseUpdate->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'imported_student@example.com',
            'name' => 'Imported Student Updated',
            'address' => 'Alamat Import Updated'
        ]);

        // Test ValidationException path
        $invalidCsv = "nama,email,nim_nip\n";
        $invalidCsv .= ",invalid_email,1234\n"; // missing name and invalid email

        $fileInvalid = UploadedFile::fake()->createWithContent('students_invalid.csv', $invalidCsv);
        $responseInvalid = $this->actingAs($this->kaprodi)->post(route('kaprodi.students.manage.import'), [
            'file' => $fileInvalid
        ]);
        $responseInvalid->assertRedirect();
        $responseInvalid->assertSessionHas('import_errors');
    }
}
