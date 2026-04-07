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

    public function scopeByType($query, $type)
    {
        if ($type === 'supervisor') {
            return $query->whereHas('thesisSubmission', function ($q) {
                $q->whereColumn('assessments.evaluator_id', 'thesis_submissions.supervisor_id')
                  ->orWhereColumn('assessments.evaluator_id', 'thesis_submissions.supervisor_2_id');
            });
        } elseif ($type === 'examiner_1') {
            $thesisIds = self::whereHas('thesisSubmission', function ($q) {
                $q->whereColumn('assessments.evaluator_id', '!=', 'thesis_submissions.supervisor_id')
                  ->where(function ($query) {
                      $query->whereColumn('assessments.evaluator_id', '!=', 'thesis_submissions.supervisor_2_id')
                            ->orWhereNull('thesis_submissions.supervisor_2_id');
                  });
            })->selectRaw('MIN(id) as first_id')->groupBy('thesis_submission_id')->pluck('first_id');
            
            return $query->whereIn('id', $thesisIds);
        }
        return $query;
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

        // Get all assessments for this submission, ordered by ID or creation date
        $allAssessments = $submission->assessments()
            ->orderBy('id')
            ->get();

        foreach ($allAssessments as $index => $assessment) {
            if ($assessment->id === $this->id) {
                return 'Dosen ' . ($index + 1);
            }
        }

        return 'Dosen Penilai';
    }

}
