<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Examiner\AssessmentController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Student routes
    Route::middleware('role:mahasiswa')->prefix('student')->name('student.')->group(function () {
        Route::resource('submissions', StudentSubmissionController::class);
    });

    // Supervisor routes
    Route::middleware('role:dosen_pembimbing')->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/submissions', function () {
            return view('supervisor.submissions.index', [
                'submissions' => auth()->user()->supervisedTheses()->latest()->paginate(10)
            ]);
        })->name('submissions.index');

        Route::get('/submissions/{submission}', function (\App\Models\ThesisSubmission $submission) {
            abort_if($submission->supervisor_id !== auth()->id(), 403);
            return view('supervisor.submissions.show', compact('submission'));
        })->name('submissions.show');
    });

    // Examiner routes
    Route::middleware('role:dosen_penguji')->prefix('examiner')->name('examiner.')->group(function () {
        Route::resource('assessments', AssessmentController::class);
    });

    // Coordinator routes
    Route::middleware('role:koordinator')->prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/submissions', function () {
            return view('coordinator.submissions.index', [
                'submissions' => \App\Models\ThesisSubmission::with(['student', 'supervisor'])->latest()->paginate(10)
            ]);
        })->name('submissions.index');

        Route::get('/submissions/{submission}', function (\App\Models\ThesisSubmission $submission) {
            return view('coordinator.submissions.show', compact('submission'));
        })->name('submissions.show');

        Route::get('/reports', function () {
            return view('coordinator.reports.index');
        })->name('reports.index');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });


    // Profile routes (accessible to all authenticated users)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
