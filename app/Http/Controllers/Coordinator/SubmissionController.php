<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Coordinator\CoordinatorService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        protected CoordinatorService $coordinatorService
    ) {
    }

    public function index(Request $request)
    {
        $students = \App\Models\User::role('mahasiswa')
            ->withCount('thesisSubmissions')
            ->when($request->sort_by, function($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function($q) {
                $q->latest();
            })
            ->paginate(15);

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
