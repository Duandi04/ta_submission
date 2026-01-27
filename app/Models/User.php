<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim_nip',
        'phone',
        'address',
        'profile_photo',
        'is_active',
        'program_studi_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'nim_nip', 'phone', 'address', 'is_active', 'program_studi_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function thesisSubmissions()
    {
        return $this->hasMany(ThesisSubmission::class, 'student_id');
    }

    public function supervisedTheses()
    {
        return $this->hasMany(ThesisSubmission::class, 'supervisor_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'evaluator_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function uploadedFiles()
    {
        return $this->hasMany(SubmissionFile::class, 'uploaded_by');
    }

    /**
     * Helper methods
     */
    public function isStudent(): bool
    {
        return $this->hasRole('mahasiswa');
    }

    public function isLecturer(): bool
    {
        return $this->hasRole('dosen');
    }

    public function isSupervisor(): bool
    {
        return $this->hasRole('dosen');
    }

    public function isExaminer(): bool
    {
        return $this->hasRole('dosen');
    }

    public function isCoordinator(): bool
    {
        return $this->hasRole('koordinator');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
