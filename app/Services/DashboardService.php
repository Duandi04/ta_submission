<?php

namespace App\Services;

use App\Models\ThesisSubmission;
use App\Models\User;

class DashboardService
{
    /**
     * Get dashboard statistics based on user role.
     */
    public function getStats(User $user): array
    {
        $stats = [];

        if ($user->hasRole('admin')) {
            $stats = $this->getAdminStats();
        } elseif ($user->hasRole('kaprodi')) {
            $stats = $this->getKaprodiStats($user);
        } elseif ($user->hasRole('koordinator')) {
            $stats = $this->getCoordinatorStats();
        } elseif ($user->hasRole('dosen')) {
            $stats = $this->getDosenStats($user);
        } elseif ($user->hasRole('mahasiswa')) {
            $stats = $this->getStudentStats($user);
        }

        return $stats;
    }

    protected function getAdminStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_submissions' => ThesisSubmission::count(),
            'pending_submissions' => ThesisSubmission::where('status', 'pending')->count(),
            'approved_submissions' => ThesisSubmission::where('status', 'approved')->count(),
        ];
    }

    protected function getKaprodiStats(User $user): array
    {
        $prodiId = $user->program_studi_id;

        $studentQuery = User::role('mahasiswa');
        if ($prodiId) {
            $studentQuery->where('program_studi_id', $prodiId);
        }

        $allStudentsCount = $studentQuery->count();

        $submissionQuery = ThesisSubmission::query();
        if ($prodiId) {
            $submissionQuery->whereHas('student', function ($q) use ($prodiId) {
                $q->where('program_studi_id', $prodiId);
            });
        }

        // Students who have submitted (non-draft)
        $submittedStudents = User::role('mahasiswa')
            ->when($prodiId, fn($q) => $q->where('program_studi_id', $prodiId))
            ->whereHas('thesisSubmissions', function ($q) {
                $q->whereNotIn('status', ['draft', 'cancelled']);
            })
            ->with([
                'thesisSubmissions' => function ($q) {
                    $q->whereNotIn('status', ['draft', 'cancelled'])->latest()->limit(1);
                }
            ])
            ->latest()
            ->take(10)
            ->get();

        // Students who have NOT submitted (no submissions, or only draft/cancelled)
        $notSubmittedStudents = User::role('mahasiswa')
            ->when($prodiId, fn($q) => $q->where('program_studi_id', $prodiId))
            ->where(function ($q) {
                $q->whereDoesntHave('thesisSubmissions')
                    ->orWhereDoesntHave('thesisSubmissions', function ($sq) {
                        $sq->whereNotIn('status', ['draft', 'cancelled']);
                    });
            })
            ->latest()
            ->take(10)
            ->get();

        // Counts for the segmentation
        $submittedStudentsCount = User::role('mahasiswa')
            ->when($prodiId, fn($q) => $q->where('program_studi_id', $prodiId))
            ->whereHas('thesisSubmissions', function ($q) {
                $q->whereNotIn('status', ['draft', 'cancelled']);
            })
            ->count();

        $notSubmittedStudentsCount = $allStudentsCount - $submittedStudentsCount;

        return [
            'total_students' => $allStudentsCount,
            'total_submissions' => $submissionQuery->count(),
            'pending_submissions' => $submissionQuery->clone()->where('status', 'submitted')->count(),
            'approved_submissions' => $submissionQuery->clone()->where('status', 'approved')->count(),
            'submitted_students' => $submittedStudents,
            'submitted_students_count' => $submittedStudentsCount,
            'not_submitted_students' => $notSubmittedStudents,
            'not_submitted_students_count' => $notSubmittedStudentsCount,
        ];
    }

    protected function getCoordinatorStats(): array
    {
        return [
            'total_submissions' => ThesisSubmission::count(),
            'pending_review' => ThesisSubmission::where('status', 'pending')->count(),
            'in_progress' => ThesisSubmission::where('status', 'in_progress')->count(),
            'completed' => ThesisSubmission::where('status', 'completed')->count(),
        ];
    }

    protected function getDosenStats(User $user): array
    {
        $supervised = $user->supervisedTheses();
        $assessments = $user->assessments();

        return [
            'supervised_total'      => $supervised->count(),
            'supervised_ongoing'    => $supervised->whereIn('status', ['submitted', 'under_review'])->count(),
            'supervised_completed'  => $supervised->where('status', 'completed')->count(),
            'supervised_students'   => $user->supervisedTheses()->with('student')->latest()->take(10)->get(),
            'pending_assessments'   => $assessments->where('is_submitted', false)->count(),
            'submitted_assessments' => $assessments->where('is_submitted', true)->count(),
            'total_assessments'     => $assessments->count(),
        ];
    }

    protected function getSupervisorStats(User $user): array
    {
        return [
            'supervised_students' => $user->supervisedTheses()->count(),
            'pending_review' => $user->supervisedTheses()->where('status', 'pending')->count(),
            'in_progress' => $user->supervisedTheses()->where('status', 'in_progress')->count(),
        ];
    }

    protected function getExaminerStats(User $user): array
    {
        return [
            'total_assessments' => $user->assessments()->count(),
            'pending_assessments' => $user->assessments()->where('is_submitted', false)->count(),
            'submitted_assessments' => $user->assessments()->where('is_submitted', true)->count(),
        ];
    }

    protected function getStudentStats(User $user): array
    {
        return [
            'my_submissions' => $user->thesisSubmissions()->count(),
            'drafts' => $user->thesisSubmissions()->where('status', 'draft')->count(),
            'pending' => $user->thesisSubmissions()->where('status', 'pending')->count(),
            'approved' => $user->thesisSubmissions()->where('status', 'approved')->count(),
        ];
    }
}
