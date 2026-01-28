<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request)
    {
        $submissions = ThesisSubmission::with(['student', 'supervisor'])
            ->when($request->sort_by, function($q) use ($request) {
                if ($request->sort_by === 'student') {
                    $q->join('users as students', 'thesis_submissions.student_id', '=', 'students.id')
                        ->orderBy('students.name', $request->sort_order ?: 'asc')
                        ->select('thesis_submissions.*');
                } else {
                    $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
                }
            }, function($q) {
                $q->latest();
            })
            ->paginate(15);

        return view('coordinator.reports.index', compact('submissions'));
    }
}
