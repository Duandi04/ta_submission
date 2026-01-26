<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use App\Http\Controllers\Supervisor\SubmissionController as SupervisorSubmissionController;
use App\Http\Controllers\Coordinator\SubmissionController as CoordinatorSubmissionController;
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
        Route::get('/students', [SupervisorSubmissionController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [SupervisorSubmissionController::class, 'studentDetails'])->name('students.show');
        Route::get('/submissions/{submission}', [SupervisorSubmissionController::class, 'show'])->name('submissions.show');
    });

    // Examiner routes
    Route::middleware('role:dosen_penguji')->prefix('examiner')->name('examiner.')->group(function () {
        Route::resource('assessments', AssessmentController::class);
    });

    // Coordinator routes
    Route::middleware('role:koordinator')->prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/students', [CoordinatorSubmissionController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [CoordinatorSubmissionController::class, 'show'])->name('submissions.show'); // Adjust this as needed

        Route::get('/reports', function () {
            return view('coordinator.reports.index');
        })->name('reports.index');
    });

    // Kaprodi routes
    Route::middleware('role:kaprodi')->prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/students', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'studentDetails'])->name('students.show');
        Route::get('/settings', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'updateSettings'])->name('settings.update');
        Route::get('/rubrics', [\App\Http\Controllers\Kaprodi\KaprodiController::class, 'rubrics'])->name('rubrics.index');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('faculties', \App\Http\Controllers\Admin\FacultyController::class);
        Route::resource('program-studis', \App\Http\Controllers\Admin\ProgramStudiController::class);
        Route::get('/configuration', [\App\Http\Controllers\Admin\ConfigurationController::class, 'index'])->name('configuration.index');
        Route::post('/configuration', [\App\Http\Controllers\Admin\ConfigurationController::class, 'update'])->name('configuration.update');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });


    // Profile routes (accessible to all authenticated users)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
