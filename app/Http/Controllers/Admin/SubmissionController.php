<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = ThesisSubmission::with(['student', 'student.programStudi', 'files'])
            ->latest()
            ->paginate(15);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(int $id)
    {
        $submission = ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($id);
            
        // Reuse Kaprodi's view logic style or create a new one. 
        // For Admin, we don't necessarily need the complex navigation logic unless desired, 
        // but let's keep it simple for now or copy it if needed.
        // Let's copy the navigation logic to be nice.

        $prev = ThesisSubmission::where('id', '<', $id)->orderBy('id', 'desc')->first();
        $next = ThesisSubmission::where('id', '>', $id)->orderBy('id', 'asc')->first();
        $count = ThesisSubmission::count();
        $position = ThesisSubmission::where('id', '<=', $id)->count();

        $navigation = [
            'prev' => $prev?->id,
            'next' => $next?->id,
            'current' => $position,
            'total' => $count,
            'query' => []
        ];

        return view('admin.submissions.show', compact('submission', 'navigation'));
    }
}
