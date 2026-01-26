<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Supervisor\SupervisorService;

class SubmissionController extends Controller
{
    public function __construct(
        protected SupervisorService $supervisorService
    ) {
    }

    public function index()
    {
        $students = $this->supervisorService->getSupervisedStudents();

        return view('supervisor.students.index', compact('students'));
    }

    public function studentDetails(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = $this->supervisorService->getStudentSubmissions($studentId);

        return view('supervisor.students.show', compact('student', 'submissions'));
    }

    public function show(ThesisSubmission $submission)
    {
        $submission = $this->supervisorService->getSubmission($submission);

        return view('supervisor.submissions.show', compact('submission'));
    }
}
