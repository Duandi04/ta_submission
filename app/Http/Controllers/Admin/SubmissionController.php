<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use Illuminate\Http\Request;
use App\Helpers\NavigationHelper;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = ThesisSubmission::with(['student', 'student.programStudi', 'files'])
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate(15);

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(Request $request, int $id)
    {
        $submission = ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($id);

        // Contextual navigation
        $query = ThesisSubmission::filterByRequest($request);
        $navigation = NavigationHelper::getNavigation(
            $submission,
            $query,
            $request->sort_by ?: 'created_at',
            $request->sort_order ?: 'desc'
        );

        return view('admin.submissions.show', compact('submission', 'navigation'));
    }
}
