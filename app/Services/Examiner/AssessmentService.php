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
     * @deprecated Use getCriteriaForAssessment instead whenever possible.
     */
    public function getCriteria()
    {
        return AssessmentCriterion::active()->ordered()->get();
    }

    /**
     * Get criteria for a specific assessment.
     * Prioritizes the snapshot if available, otherwise falls back to active criteria.
     */
    public function getCriteriaForAssessment(Assessment $assessment)
    {
        if ($assessment->rubric_snapshot) {
            // Convert snapshot array to a format compatible with the view
            // Assuming snapshot structure is list of criteria objects
            return collect($assessment->rubric_snapshot)->map(function ($item, $index) {
                // Determine if it looks like an object or array
                $data = (array) $item;
                $obj = new AssessmentCriterion(); // Using model as a DTO mostly
                
                // Use ID from snapshot if available, otherwise use index (0, 1, 2...)
                $id = $data['id'] ?? $index;
                
                $obj->forceFill([
                    'id' => $id, 
                    'name' => $data['name'] ?? '',
                    'description' => $data['description'] ?? '',
                    'weight_percentage' => $data['weight'] ?? 0,
                    // Map other fields if necessary
                ]);
                $obj->id = $id; // Force set ID
                return $obj;
            });
        }
        
        // Fallback for legacy assessments without snapshot
        return $this->getCriteria();
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

        $criteria = $this->getCriteriaForAssessment($assessment);
        $criteriaMap = $criteria->keyBy('id');

        foreach ($scores as $criterionKey => $score) {
            // Find weight from the criteria snapshot/list
            $weight = 0;
            if ($criteriaMap->has($criterionKey)) {
                $weight = $criteriaMap->get($criterionKey)->weight_percentage;
            } else {
                 // Fallback look up if using global ID and we are in legacy mode
                 $criterion = AssessmentCriterion::find($criterionKey);
                 if ($criterion) $weight = $criterion->weight_percentage;
            }

            if ($update) {
                $assessment->scores()->updateOrCreate(
                    ['criterion_id' => $criterionKey],
                    ['score' => $score]
                );
            } else {
                $assessment->scores()->create([
                    'criterion_id' => $criterionKey,
                    'score' => $score,
                ]);
            }

            $totalScore += ($score * $weight / 100);
            $totalWeight += $weight;
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
