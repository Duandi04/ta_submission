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

        // Get unique students who have supervised submissions (Primary or Secondary Supervisor)
        // Using a nested closure to ensure OR condition is properly scoped inside whereHas
        $query = \App\Models\User::whereHas('thesisSubmissions', function ($q) use ($lecturerId) {
            $q->where('status', 'approved')
              ->where(function($sq) use ($lecturerId) {
                $sq->where('supervisor_id', $lecturerId)
                  ->orWhere('supervisor_2_id', $lecturerId);
            });
        });

        // Search by student name or NIM
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        // Eager load the latest supervised submission and its assessment for this lecturer
        $students = $query->with(['thesisSubmissions' => function($q) use ($lecturerId) {
            $q->where('status', 'approved')
              ->where(function($sq) use ($lecturerId) {
                $sq->where('supervisor_id', $lecturerId)
                  ->orWhere('supervisor_2_id', $lecturerId);
            })
            ->latest()
            ->with(['assessments' => function($aq) use ($lecturerId) {
                $aq->where('evaluator_id', $lecturerId);
            }]);
        }])->paginate(12)->withQueryString();

        return view('dosen.students.index', compact('students'));
    }

    public function submissions(Request $request)
    {
        // Use the refined comprehensive supervisedTheses method from the User model
        $query = Auth::user()->supervisedTheses()
            ->with(['student', 'files', 'assessments.evaluator']);

        // Search by student name or NIM
        if ($search = $request->query('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('dosen.submissions.index', compact('submissions'));
    }

    public function studentDetails(int $studentId)
    {
        $lecturerId = Auth::id();
        $student = \App\Models\User::findOrFail($studentId);

        // Fetch student's supervised submissions (Supervisor 1 or 2)
        $submissions = ThesisSubmission::where('student_id', $studentId)
            ->where(function($q) use ($lecturerId) {
                $q->where('supervisor_id', $lecturerId)
                  ->orWhere('supervisor_2_id', $lecturerId);
            })
            ->with([
                'supervisor',
                'supervisor2',
                'files',
                'assessments' => function($aq) use ($lecturerId) {
                    $aq->where('evaluator_id', $lecturerId);
                }
            ])
            ->latest()
            ->get();

        // Identify the "Official" supervision record (the approved one)
        $officialSupervision = $submissions->where('status', 'approved')->first();

        return view('dosen.students.show', compact('student', 'submissions', 'officialSupervision'));
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
