<?php

namespace App\Services\Coordinator;

use App\Models\ThesisSubmission;

class CoordinatorService
{
    /**
     * Get all thesis submissions with related data.
     */
    public function getAllSubmissions(int $perPage = 10)
    {
        return ThesisSubmission::with(['student', 'supervisor'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a single submission with all related data.
     */
    public function getSubmission(ThesisSubmission $submission): ThesisSubmission
    {
        return $submission->load(['student', 'supervisor', 'files', 'assessments.evaluator', 'comments.user', 'statuses.changer']);
    }
}
