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
        
        // Get unique students who have submissions supervised by this lecturer
        $students = \App\Models\User::whereHas('thesisSubmissions', function($query) use ($lecturerId) {
            $query->where('supervisor_id', $lecturerId);
        })
        ->paginate(12);
            
        return view('dosen.students.index', compact('students'));
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
