<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Supervisor\SupervisorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(
        protected SupervisorService $supervisorService
    ) {
    }

    public function index(Request $request)
    {
        $lecturerId = Auth::id();

        // Get unique students who have submissions where this lecturer is an evaluator (assessor)
        $query = \App\Models\User::whereHas('thesisSubmissions', function ($q) use ($lecturerId) {
            $q->whereHas('assessments', function ($aq) use ($lecturerId) {
                $aq->where('evaluator_id', $lecturerId);
            });
        });

        // Search by student name or NIM
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(12)->withQueryString();

        return view('dosen.students.index', compact('students'));
    }

    public function studentDetails(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = ThesisSubmission::where('student_id', $studentId)
            ->whereHas('assessments', function ($q) {
                $q->where('evaluator_id', Auth::id());
            })
            ->with([
                'supervisor',
                'files',
                'assessments' => function ($q) {
                    $q->where('evaluator_id', Auth::id());
                }
            ])
            ->latest()
            ->get();

        return view('dosen.students.show', compact('student', 'submissions'));
    }

    public function show(int $submissionId)
    {
        $submission = ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($submissionId);

        // Ensure the authenticated user is the supervisor
        // if ($submission->supervisor_id !== auth()->id()) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('dosen.submissions.show', compact('submission'));
    }
}
