<?php

namespace App\Services\Supervisor;

use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;

class SupervisorService
{
    /**
     * Get unique students supervised by the current user.
     */
    public function getSupervisedStudents(int $perPage = 10)
    {
        return Auth::user()->supervisedTheses()
            ->with('student')
            ->get()
            ->pluck('student')
            ->unique('id');
    }

    /**
     * Get submissions for a specific student supervised by the current user.
     */
    public function getStudentSubmissions(int $studentId)
    {
        return Auth::user()->supervisedTheses()
            ->where('student_id', $studentId)
            ->latest()
            ->get();
    }

    /**
     * Get a single submission if supervised by the current user.
     */
    public function getSubmission(ThesisSubmission $submission): ThesisSubmission
    {
        abort_if($submission->supervisor_id !== Auth::id(), 403);

        return $submission->load(['student', 'files', 'assessments.evaluator', 'comments.user', 'statuses.changer']);
    }
}
