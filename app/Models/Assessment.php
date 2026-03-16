<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Assessment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'thesis_submission_id',
        'evaluator_id',
        'rubric_id',
        'total_score',
        'comments',
        'strengths',
        'weaknesses',
        'recommendations',
        'is_submitted',
        'submitted_at',
        'assessment_rubric_id',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'is_submitted' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['total_score', 'comments', 'strengths', 'weaknesses', 'recommendations', 'is_submitted', 'submitted_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('assessments');
    }

    /**
     * Relationships
     */
    public function thesisSubmission()
    {
        return $this->belongsTo(ThesisSubmission::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function assessmentRubric()
    {
        return $this->belongsTo(AssessmentRubric::class);
    }

    public function rubric()
    {
        return $this->belongsTo(Rubric::class);
    }

    /**
     * Scopes
     */
    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    public function scopeByEvaluator($query, $evaluatorId)
    {
        return $query->where('evaluator_id', $evaluatorId);
    }

    /**
     * Helper methods
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->evaluator_id === $user->id && !$this->is_submitted;
    }

    /**
     * Get anonymous label for student view.
     */
    public function getAnonymousLabel(): string
    {
        $submission = $this->thesisSubmission;
        
        if (!$submission) {
            return 'Dosen Penilai';
        }

        if ($this->evaluator_id === $submission->supervisor_id || $this->evaluator_id === $submission->supervisor_2_id) {
            return 'Pembimbing';
        }

        $examinerAssessments = $submission->assessments
            ->where('evaluator_id', '!=', $submission->supervisor_id)
            ->where('evaluator_id', '!=', $submission->supervisor_2_id)
            ->sortBy('id')
            ->values();

        foreach ($examinerAssessments as $index => $exam) {
            if ($exam->id === $this->id) {
                return 'Dosen ' . ($index + 1);
            }
        }

        return 'Dosen Penilai';
    }

}
