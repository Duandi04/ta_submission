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

use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * Get the profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            return route('users.photo', $this->id);
        }

        return asset('images/default-avatar.png');
    }

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
        'angkatan',
        'can_exceed_submission_limit',
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
            'can_exceed_submission_limit' => 'boolean',
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
            ->dontSubmitEmptyLogs()
            ->useLogName('users');
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

    /**
     * Scopes
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('nim_nip', 'like', "%{$search}%");
        });
    }

    public function scopeByRole($query, $role)
    {
        return $query->role($role);
    }

    public function scopeByProgramStudi($query, $programStudiId)
    {
        return $query->where('program_studi_id', $programStudiId);
    }

    public function scopeFilterByRequest($query, $request)
    {
        return $query->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->role, fn($q) => $q->role($request->role))
            ->when($request->role_group == 'lecturer', fn($q) => $q->lecturers())
            ->when($request->program_studi_id, fn($q) => $q->byProgramStudi($request->program_studi_id));
    }

    public function scopeLecturers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->whereIn('name', ['dosen', 'kaprodi']);
        });
    }
}
