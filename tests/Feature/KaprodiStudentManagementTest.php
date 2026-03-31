<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\ThesisSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaprodiStudentManagementTest extends TestCase
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
        ]);

        $this->prodi = ProgramStudi::first();
        $this->kaprodi = User::factory()->create([
            'program_studi_id' => $this->prodi->id
        ]);
        $this->kaprodi->assignRole('kaprodi');
    }

    /**
     * Test Kaprodi can see student list with submission count and status.
     */
    public function test_kaprodi_can_view_student_manage_list_with_metrics(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
            'name' => 'Student Target'
        ]);
        $student->assignRole('mahasiswa');

        // Create 2 submitted proposals
        ThesisSubmission::factory()->count(2)->create([
            'student_id' => $student->id,
            'status' => 'submitted'
        ]);

        // Create 1 approved proposal
        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.index'));

        $response->assertStatus(200);
        $response->assertSee('Student Target');
        $response->assertSee('Total Pengajuan');
        $response->assertSee('Status Proposal');
        // Check submission count badge
        $response->assertSee('3'); 
        // Check status badge
        $response->assertSee('Diterima');
        $response->assertSee($supervisor->name);
    }

    /**
     * Test Student Detail displays Angkatan and supervisor in history.
     */
    public function test_student_detail_shows_angkatan_and_supervisor_history(): void
    {
        $student = User::factory()->mahasiswa()->create([
            'program_studi_id' => $this->prodi->id,
            'angkatan' => '2022'
        ]);
        $student->assignRole('mahasiswa');

        $supervisor = User::factory()->dosen()->create(['program_studi_id' => $this->prodi->id]);
        ThesisSubmission::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Proposal History Title',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.students.manage.show', $student));

        $response->assertStatus(200);
        $response->assertSee('2022');
        $response->assertSee('Riwayat Pengajuan');
        $response->assertSee('Proposal History Title');
        $response->assertSee('Pembimbing:');
        $response->assertSee($supervisor->name);
    }

    /**
     * Test Reports page filter and NIM sorting.
     */
    public function test_reports_page_filters_approved_students_and_sorts_by_nim(): void
    {
        // Student A: Approved, NIM 20220002
        $studentA = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id, 'nim_nip' => '20220002', 'name' => 'Alice']);
        $studentA->assignRole('mahasiswa');
        ThesisSubmission::factory()->create(['student_id' => $studentA->id, 'status' => 'approved']);

        // Student B: Approved, NIM 20220001
        $studentB = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id, 'nim_nip' => '20220001', 'name' => 'Bob']);
        $studentB->assignRole('mahasiswa');
        ThesisSubmission::factory()->create(['student_id' => $studentB->id, 'status' => 'approved']);

        // Student C: Submitted (NOT approved)
        $studentC = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id, 'nim_nip' => '20220003']);
        $studentC->assignRole('mahasiswa');
        ThesisSubmission::factory()->create(['student_id' => $studentC->id, 'status' => 'submitted']);

        // Check Preview page
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Alice');
        $response->assertSee('Bob');
        $response->assertDontSee('20220003');

        // Check Printing page logic for NIM Sort (Bob with 20220001 should appear before Alice with 20220002)
        $printResponse = $this->actingAs($this->kaprodi)->get(route('kaprodi.reports.print'));
        $printResponse->assertStatus(200);
        
        $content = $printResponse->getContent();
        $posBob = strpos($content, 'Bob');
        $posAlice = strpos($content, 'Alice');
        
        $this->assertTrue($posBob < $posAlice, "Bob (NIM 20220001) should appear before Alice (NIM 20220002)");
    }

    /**
     * Test security: Non-kaprodi cannot access reports.
     */
    public function test_non_kaprodi_cannot_access_reports(): void
    {
        $student = User::factory()->mahasiswa()->create(['program_studi_id' => $this->prodi->id]);
        $student->assignRole('mahasiswa');

        $response = $this->actingAs($student)->get(route('kaprodi.reports.index'));
        $response->assertStatus(403);
    }
}
