<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\SubmissionController as AdminSubmissionController;
use App\Http\Controllers\Dosen\AssessmentController;
use App\Http\Controllers\Dosen\SubmissionController as DosenSubmissionController;
use App\Http\Controllers\Kaprodi\KaprodiController;
use App\Http\Controllers\Kaprodi\StudentController as KaprodiStudentController;
use App\Http\Controllers\Kaprodi\LecturerController as KaprodiLecturerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Default redirect
Route::redirect('/', '/login');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Similarity check (AJAX)
    Route::post('/similarity/check', [\App\Http\Controllers\SimilarityController::class, 'check'])->name('similarity.check');

    // Student routes
    Route::middleware('role:mahasiswa')->prefix('student')->name('student.')->group(function () {
        Route::post('submissions/{submission}/revision', [StudentSubmissionController::class, 'storeRevision'])->name('submissions.revision');
        Route::patch('submissions/{submission}/cancel', [StudentSubmissionController::class, 'cancel'])->name('submissions.cancel');
        Route::patch('submissions/{submission}/submit', [StudentSubmissionController::class, 'submit'])->name('submissions.submit');
        Route::resource('submissions', StudentSubmissionController::class);
    });

    // Dosen routes (merges Supervisor & Examiner)
    Route::middleware('role:dosen|kaprodi')->prefix('dosen')->name('dosen.')->group(function () {
        // Supervision Routes
        Route::get('/submissions', [DosenSubmissionController::class, 'submissions'])->name('submissions.index');
        Route::get('/students', [DosenSubmissionController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [DosenSubmissionController::class, 'studentDetails'])->name('students.show');
        Route::get('/submissions/{submission}', [DosenSubmissionController::class, 'show'])->name('submissions.show');

        // Assessment Routes
        Route::resource('assessments', AssessmentController::class);
        Route::post('assessments/{assessment}/submit', [AssessmentController::class, 'submit'])->name('assessments.submit');
    });

    // Kaprodi routes
    Route::middleware('role:kaprodi')->prefix('kaprodi')->name('kaprodi.')->group(function () {
        // Management Routes (Students & Lecturers)
        Route::resource('students/manage', KaprodiStudentController::class)->names([
            'index' => 'students.manage.index',
            'create' => 'students.manage.create',
            'store' => 'students.manage.store',
            'show' => 'students.manage.show',
            'edit' => 'students.manage.edit',
            'update' => 'students.manage.update',
            'destroy' => 'students.manage.destroy',
        ]);
        Route::resource('lecturers/manage', KaprodiLecturerController::class)->names([
            'index' => 'lecturers.manage.index',
            'create' => 'lecturers.manage.create',
            'store' => 'lecturers.manage.store',
            'show' => 'lecturers.manage.show',
            'edit' => 'lecturers.manage.edit',
            'update' => 'lecturers.manage.update',
            'destroy' => 'lecturers.manage.destroy',
        ]);

        Route::get('/students', [KaprodiController::class, 'index'])->name('students.index');
        Route::get('/submissions', [KaprodiController::class, 'submissions'])->name('submissions.index');
        Route::get('/students/{student}', [KaprodiController::class, 'studentDetails'])->name('students.show');
        Route::get('/submissions/{submission}', [KaprodiController::class, 'submissionShow'])->name('submissions.show');
        Route::get('/assessments/{assessment}', [KaprodiController::class, 'assessmentShow'])->name('assessments.show');
        Route::post('/submissions/{submission}/assign-lecturers', [KaprodiController::class, 'assignLecturers'])->name('submissions.assign-lecturers');
        Route::post('/submissions/batch-assign', [KaprodiController::class, 'batchAssignLecturers'])->name('submissions.batch-assign');
        Route::post('/submissions/{submission}/accept', [KaprodiController::class, 'acceptSubmission'])->name('submissions.accept');
        Route::post('/submissions/{submission}/reject', [KaprodiController::class, 'rejectSubmission'])->name('submissions.reject');
        Route::get('/settings', [KaprodiController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [KaprodiController::class, 'updateSettings'])->name('settings.update');
        Route::get('/rubrics', [KaprodiController::class, 'rubrics'])->name('rubrics.index');
        Route::get('/rubrics/create', [KaprodiController::class, 'createRubric'])->name('rubrics.create');
        Route::post('/rubrics', [KaprodiController::class, 'storeRubric'])->name('rubrics.store');
        Route::get('/rubrics/{id}/edit', [KaprodiController::class, 'editRubric'])->name('rubrics.edit');
        Route::put('/rubrics/{id}', [KaprodiController::class, 'updateRubric'])->name('rubrics.update');
        Route::delete('/rubrics/{id}', [KaprodiController::class, 'destroyRubric'])->name('rubrics.destroy');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
        Route::resource('students', StudentController::class);
        Route::resource('lecturers', LecturerController::class);
        Route::resource('faculties', FacultyController::class);
        Route::resource('program-studis', ProgramStudiController::class);
        Route::get('/configuration', [ConfigurationController::class, 'index'])->name('configuration.index');
        Route::post('/configuration', [ConfigurationController::class, 'update'])->name('configuration.update');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });


    // Profile routes (accessible to all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');

    // Custom Download and Preview routes
    Route::get('/files/{file}/download', [\App\Http\Controllers\FileDownloadController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/preview', [\App\Http\Controllers\FileDownloadController::class, 'preview'])->name('files.preview');

    // Secure Profile Photo Route
    Route::get('/users/{user}/photo', [\App\Http\Controllers\FileDownloadController::class, 'profilePhoto'])->name('users.photo');
});
