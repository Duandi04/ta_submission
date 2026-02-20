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
            return $assessment->scores->map(function ($score) {
                $obj = new AssessmentCriterion();
                $obj->forceFill([
                    'id' => $score->criterion_id,
                    'name' => $score->criterion_name,
                    'description' => $score->notes, // Or keep notes separate
                    'weight_percentage' => $score->weight,
                ]);
                $obj->id = $score->criterion_id;
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
    public function create(ThesisSubmission $submission, array $data, array $scores): Assessment
    {
        $assessment = Assessment::create([
            'thesis_submission_id' => $submission->id,
            'evaluator_id' => Auth::id(),
            'evaluator_type' => $data['evaluator_type'],
            'rubric_id' => $submission->rubric_id,
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

        $rubric = $assessment->rubric;
        $criteria = $rubric ? collect($rubric->criteria) : collect();
        $criteriaMap = $criteria->keyBy(fn($c, $idx) => $c['id'] ?? $idx);

        foreach ($scores as $criterionKey => $score) {
            $criterionData = $criteriaMap->get($criterionKey);
            $name = $criterionData['name'] ?? 'Kriteria';
            $description = $criterionData['description'] ?? null;
            $weight = $criterionData['weight'] ?? 0;

            $assessment->scores()->updateOrCreate(
                ['criterion_id' => $criterionKey],
                [
                    'criterion_name' => $name,
                    'criterion_description' => $description,
                    'weight' => $weight,
                    'score' => $score,
                    'notes' => $data['notes'][$criterionKey] ?? null
                ]
            );

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
