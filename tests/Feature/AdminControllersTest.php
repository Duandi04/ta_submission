<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\ProgramStudi;
use App\Models\Setting;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminControllersTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            \Database\Seeders\SettingSeeder::class,
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_activity_log_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.activity-logs.index'));
        $response->assertStatus(200);
    }

    public function test_configuration_index_and_update(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.configuration.index'));
        $response->assertStatus(200);

        $responseUpdate = $this->actingAs($this->admin)->post(route('admin.configuration.update'), [
            'settings' => [
                'attempts_per_batch' => '4',
                'max_batches' => '3',
            ]
        ]);
        $responseUpdate->assertRedirect();
        $responseUpdate->assertSessionHas('info', 'Pengaturan sistem saat ini bersifat statis.');
    }

    public function test_faculty_crud(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.faculties.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->admin)->get(route('admin.faculties.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('admin.faculties.store'), [
            'name' => 'Fakultas Teknik Baru',
            'code' => 'FTB',
            'description' => 'Deskripsi FT',
        ]);
        $response->assertRedirect(route('admin.faculties.index'));
        $this->assertDatabaseHas('faculties', ['name' => 'Fakultas Teknik Baru']);

        $faculty = Faculty::where('name', 'Fakultas Teknik Baru')->first();

        // Edit
        $response = $this->actingAs($this->admin)->get(route('admin.faculties.edit', $faculty->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.faculties.update', $faculty->id), [
            'name' => 'Fakultas Teknik Updated',
            'code' => 'FTB',
        ]);
        $response->assertRedirect(route('admin.faculties.index'));
        $this->assertDatabaseHas('faculties', ['name' => 'Fakultas Teknik Updated']);

        // Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.faculties.destroy', $faculty->id));
        $response->assertRedirect(route('admin.faculties.index'));
        $this->assertDatabaseMissing('faculties', ['id' => $faculty->id]);
    }

    public function test_program_studi_crud(): void
    {
        $faculty = Faculty::first();

        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.program-studis.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->admin)->get(route('admin.program-studis.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('admin.program-studis.store'), [
            'name' => 'Teknik Elektro',
            'code' => 'TE',
            'faculty_id' => $faculty->id,
            'description' => 'Deskripsi TE',
        ]);
        $response->assertRedirect(route('admin.program-studis.index'));
        $this->assertDatabaseHas('program_studis', ['name' => 'Teknik Elektro']);

        $prodi = ProgramStudi::where('name', 'Teknik Elektro')->first();

        // Edit
        $response = $this->actingAs($this->admin)->get(route('admin.program-studis.edit', $prodi->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.program-studis.update', $prodi->id), [
            'name' => 'Teknik Elektro Updated',
            'code' => 'TE',
            'faculty_id' => $faculty->id,
        ]);
        $response->assertRedirect(route('admin.program-studis.index'));
        $this->assertDatabaseHas('program_studis', ['name' => 'Teknik Elektro Updated']);

        // Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.program-studis.destroy', $prodi->id));
        $response->assertRedirect(route('admin.program-studis.index'));
        $this->assertDatabaseMissing('program_studis', ['id' => $prodi->id]);
    }

    public function test_admin_submissions_index_and_show(): void
    {
        $submission = ThesisSubmission::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.submissions.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.submissions.show', $submission->id));
        $response->assertStatus(200);
    }

    public function test_users_crud(): void
    {
        $prodi = ProgramStudi::first();

        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'User Baru',
            'email' => 'userbaru@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim_nip' => '12345678',
            'role' => 'mahasiswa',
            'program_studi_id' => $prodi->id,
            'is_active' => '1',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'userbaru@example.com']);

        $user = User::where('email', 'userbaru@example.com')->first();

        // Show
        $response = $this->actingAs($this->admin)->get(route('admin.users.show', $user->id));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $user->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user->id), [
            'name' => 'User Baru Updated',
            'email' => 'userbaru@example.com',
            'nim_nip' => '12345678',
            'role' => 'mahasiswa',
            'program_studi_id' => $prodi->id,
            'is_active' => '1',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['name' => 'User Baru Updated']);

        // Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user->id));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_users_export(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_students_crud(): void
    {
        $prodi = ProgramStudi::first();

        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.students.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->admin)->get(route('admin.students.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'name' => 'Student Baru',
            'email' => 'studentbaru@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim_nip' => 'S12345678',
            'program_studi_id' => $prodi->id,
            'angkatan' => '2023',
            'is_active' => '1',
            'role' => 'mahasiswa',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('users', ['email' => 'studentbaru@example.com']);

        $student = User::where('email', 'studentbaru@example.com')->first();

        // Show
        $response = $this->actingAs($this->admin)->get(route('admin.students.show', $student->id));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->admin)->get(route('admin.students.edit', $student->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.students.update', $student->id), [
            'name' => 'Student Baru Updated',
            'email' => 'studentbaru@example.com',
            'nim_nip' => 'S12345678',
            'program_studi_id' => $prodi->id,
            'angkatan' => '2023',
            'is_active' => '1',
            'role' => 'mahasiswa',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('users', ['name' => 'Student Baru Updated']);

        // Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.students.destroy', $student->id));
        $response->assertRedirect(route('admin.students.index'));
        $this->assertSoftDeleted('users', ['id' => $student->id]);
    }

    public function test_lecturers_crud(): void
    {
        $prodi = ProgramStudi::first();

        // Index
        $response = $this->actingAs($this->admin)->get(route('admin.lecturers.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->admin)->get(route('admin.lecturers.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('admin.lecturers.store'), [
            'name' => 'Lecturer Baru',
            'email' => 'lecturerbaru@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'nim_nip' => 'L12345678',
            'program_studi_id' => $prodi->id,
            'is_active' => '1',
            'role' => 'dosen',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.lecturers.index'));
        $this->assertDatabaseHas('users', ['email' => 'lecturerbaru@example.com']);

        $lecturer = User::where('email', 'lecturerbaru@example.com')->first();

        // Show
        $response = $this->actingAs($this->admin)->get(route('admin.lecturers.show', $lecturer->id));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->admin)->get(route('admin.lecturers.edit', $lecturer->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.lecturers.update', $lecturer->id), [
            'name' => 'Lecturer Baru Updated',
            'email' => 'lecturerbaru@example.com',
            'nim_nip' => 'L12345678',
            'program_studi_id' => $prodi->id,
            'is_active' => '1',
            'role' => 'dosen',
            'phone' => '08123456789',
            'address' => 'Jl. Baru No. 1',
        ]);
        $response->assertRedirect(route('admin.lecturers.index'));
        $this->assertDatabaseHas('users', ['name' => 'Lecturer Baru Updated']);

        // Destroy
        $response = $this->actingAs($this->admin)->delete(route('admin.lecturers.destroy', $lecturer->id));
        $response->assertRedirect(route('admin.lecturers.index'));
        $this->assertSoftDeleted('users', ['id' => $lecturer->id]);
    }
}
