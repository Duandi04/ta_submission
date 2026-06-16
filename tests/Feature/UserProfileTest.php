<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\FacultySeeder::class,
            \Database\Seeders\ProgramStudiSeeder::class,
        ]);

        $this->user = User::factory()->mahasiswa()->create();
        $this->user->assignRole('mahasiswa');
    }

    /**
     * Test user can view profile edit page
     */
    public function test_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));
        $response->assertStatus(200);
    }

    /**
     * Test user can update basic profile info
     */
    public function test_user_can_update_profile_info(): void
    {
        $profileData = [
            'email' => 'updated@test.com',
            'phone' => '08777777777',
        ];

        $response = $this->actingAs($this->user)->put(route('profile.update'), $profileData);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => 'updated@test.com',
            'phone' => '08777777777',
        ]);
    }

    /**
     * Test user can update password
     */
    public function test_user_can_update_password(): void
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->user)->put(route('profile.password.update'), $passwordData);
        $response->assertRedirect();
        
        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    /**
     * Test profile photo upload and delete
     */
    public function test_user_can_upload_and_delete_photo(): void
    {
        Storage::fake('local');
        
        $photo = UploadedFile::fake()->image('profile.jpg');

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'email' => $this->user->email,
            'profile_photo' => $photo
        ]);

        $response->assertRedirect();
        $this->user->refresh();
        $this->assertNotNull($this->user->profile_photo);

        // Delete photo
        $response = $this->actingAs($this->user)->delete(route('profile.photo.remove'));
        $response->assertRedirect();
        $this->assertNull($this->user->fresh()->profile_photo);
    }
}
