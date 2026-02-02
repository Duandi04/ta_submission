<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Supervisor\SupervisorService;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function __construct(
        protected SupervisorService $supervisorService
    ) {
    }

    public function index()
    {
        $lecturerId = Auth::id();
        $submissions = ThesisSubmission::where('supervisor_id', $lecturerId)
            ->with(['student'])
            ->latest()
            ->paginate(10);
            
        return view('dosen.students.index', compact('submissions'));
    }

    public function studentDetails(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = ThesisSubmission::where('student_id', $studentId)
            ->with(['supervisor', 'files', 'assessments'])
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
