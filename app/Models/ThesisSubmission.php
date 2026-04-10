<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ThesisSubmission extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'supervisor_2_id',
        'title',
        'abstract',
        'research_field',
        'status',
        'submission_date',
        'defense_date',
        'notes',
        'final_score',
        'is_historical',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'defense_date' => 'date',
            'final_score' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'abstract', 'research_field', 'status', 'submission_date', 'defense_date', 'notes', 'final_score', 'supervisor_id', 'supervisor_2_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('submissions');
    }

    /**
     * Relationships
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function supervisor2()
    {
        return $this->belongsTo(User::class, 'supervisor_2_id');
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function statuses()
    {
        return $this->hasMany(ThesisStatus::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    /**
     * Scopes
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('research_field', 'like', "%{$search}%")
                ->orWhereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('nim_nip', 'like', "%{$search}%");
                });
        });
    }

    public function scopeFilterByRequest($query, $request)
    {
        return $query->when($request->status, fn($q) => $q->byStatus($request->status))
            ->when($request->student_id, fn($q) => $q->byStudent($request->student_id))
            ->when($request->supervisor_id, fn($q) => $q->bySupervisor($request->supervisor_id))
            ->when($request->search, fn($q) => $q->search($request->search));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Helper methods
     */
    public function canBeEditedByStudent(): bool
    {
        return in_array($this->status, ['draft', 'revision_required']);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'warning',
            'revision_required' => 'danger',
            'approved' => 'success',
            'rejected' => 'dark',
            'scheduled_for_defense' => 'primary',
            'defense_in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Sudah Diajukan',
            'under_review' => 'Sedang Ditinjau',
            'revision_required' => 'Perlu Revisi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'scheduled_for_defense' => 'Dijadwalkan Sidang',
            'defense_in_progress' => 'Sedang Sidang',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get the latest (most recent) file for this submission.
     * Prioritizes revision files, falls back to proposal.
     */
    public function getLatestFile(): ?SubmissionFile
    {
        return $this->files()->latest('created_at')->first();
    }

    /**
     * Get activity logs related to this submission.
     */
    public function getActivityLogs()
    {
        return \Spatie\Activitylog\Models\Activity::where('subject_type', self::class)
            ->where('subject_id', $this->id)
            ->with('causer')
            ->latest()
            ->get();
    }

    /**
     * Get the rejection reason from status history.
     */
    public function getRejectionReason(): ?string
    {
        if ($this->status !== 'rejected') {
            return null;
        }

        $status = $this->statuses()
            ->where('new_status', 'rejected')
            ->latest()
            ->first();

        return $status ? $status->comment : null;
    }
}
