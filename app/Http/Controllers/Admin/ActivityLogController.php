<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by causer (user)
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by log name/event
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        $activities = $query->paginate(20);

        // Get unique log names and subject types for filters
        $logNames = Activity::distinct()->pluck('log_name');
        $subjectTypes = Activity::distinct()->pluck('subject_type');
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.activity-logs.index', compact('activities', 'logNames', 'subjectTypes', 'users'));
    }
}
