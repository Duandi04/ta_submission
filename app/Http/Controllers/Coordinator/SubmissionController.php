<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Coordinator\CoordinatorService;

class SubmissionController extends Controller
{
    public function __construct(
        protected CoordinatorService $coordinatorService
    ) {
    }

    public function index()
    {
        $students = \App\Models\User::role('mahasiswa')
            ->withCount('thesisSubmissions')
            ->latest()
            ->paginate(10);

        return view('coordinator.students.index', compact('students'));
    }

    public function show(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = \App\Models\ThesisSubmission::where('student_id', $studentId)
            ->with(['supervisor', 'assessments'])
            ->latest()
            ->get();

        return view('coordinator.students.show', compact('student', 'submissions'));
    }
}
