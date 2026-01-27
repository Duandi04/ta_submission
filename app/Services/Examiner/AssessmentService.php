<?php

namespace App\Services\Examiner;

use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;

class AssessmentService
{
    /**
     * Get assessments for the current examiner.
     */
    public function getExaminerAssessments(int $perPage = 10)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->assessments()
            ->with(['thesisSubmission.student', 'thesisSubmission'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all active assessment criteria.
     */
    public function getCriteria()
    {
        return AssessmentCriterion::active()->ordered()->get();
    }

    /**
     * Check if examiner already has an assessment for the submission.
     */
    public function findExistingAssessment(int $submissionId): ?Assessment
    {
        return Assessment::where('thesis_submission_id', $submissionId)
            ->where('evaluator_id', Auth::id())
            ->first();
    }

    /**
     * Create a new assessment.
     */
    public function create(array $data, array $scores): Assessment
    {
        $assessment = Assessment::create([
            'thesis_submission_id' => $data['thesis_submission_id'],
            'evaluator_id' => Auth::id(),
            'evaluator_type' => $data['evaluator_type'],
            'comments' => $data['comments'] ?? null,
            'strengths' => $data['strengths'] ?? null,
            'weaknesses' => $data['weaknesses'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'is_submitted' => false,
        ]);

        $totalScore = $this->saveScores($assessment, $scores);
        $assessment->update(['total_score' => round($totalScore, 2)]);

        activity()
            ->performedOn($assessment)
            ->log('Created assessment');

        return $assessment;
    }

    /**
     * Update an existing assessment.
     */
    public function update(Assessment $assessment, array $data, array $scores): Assessment
    {
        $assessment->update([
            'comments' => $data['comments'] ?? null,
            'strengths' => $data['strengths'] ?? null,
            'weaknesses' => $data['weaknesses'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
        ]);

        $totalScore = $this->saveScores($assessment, $scores, true);
        $assessment->update(['total_score' => round($totalScore, 2)]);

        activity()
            ->performedOn($assessment)
            ->log('Updated assessment');

        return $assessment;
    }

    /**
     * Delete an assessment.
     */
    public function delete(Assessment $assessment): void
    {
        activity()
            ->performedOn($assessment)
            ->log('Deleted assessment');

        $assessment->delete();
    }

    /**
     * Save scores and calculate total.
     */
    protected function saveScores(Assessment $assessment, array $scores, bool $update = false): float
    {
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($scores as $criterionId => $score) {
            $criterion = AssessmentCriterion::find($criterionId);

            if ($update) {
                $assessment->scores()->updateOrCreate(
                    ['criterion_id' => $criterionId],
                    ['score' => $score]
                );
            } else {
                $assessment->scores()->create([
                    'criterion_id' => $criterionId,
                    'score' => $score,
                ]);
            }

            $totalScore += ($score * $criterion->weight_percentage / 100);
            $totalWeight += $criterion->weight_percentage;
        }

        return $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0;
    }

    /**
     * Check if assessment can be edited.
     */
    public function canEdit(Assessment $assessment): bool
    {
        return $assessment->evaluator_id === Auth::id() && !$assessment->is_submitted;
    }
}
