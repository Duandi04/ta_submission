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
use App\Http\Controllers\Admin\RolePermissionController;
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
    Route::middleware('permission:view menu: mahasiswa')->prefix('student')->name('student.')->group(function () {
        Route::patch('submissions/{submission}/cancel', [StudentSubmissionController::class, 'cancel'])->name('submissions.cancel');
        Route::patch('submissions/{submission}/submit', [StudentSubmissionController::class, 'submit'])->name('submissions.submit');
        Route::resource('submissions', StudentSubmissionController::class);
    });

    // Dosen routes (merges Supervisor & Examiner)
    Route::middleware('permission:view menu: dosen')->prefix('dosen')->name('dosen.')->group(function () {
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
    Route::middleware('permission:view menu: kaprodi')->prefix('kaprodi')->name('kaprodi.')->group(function () {
        // Management Routes (Students & Lecturers)
        Route::get('students/manage/export', [KaprodiStudentController::class, 'export'])->name('students.manage.export');
        Route::post('students/manage/import', [KaprodiStudentController::class, 'import'])->name('students.manage.import');
        Route::resource('students/manage', KaprodiStudentController::class)->names([
            'index' => 'students.manage.index',
            'create' => 'students.manage.create',
            'store' => 'students.manage.store',
            'show' => 'students.manage.show',
            'edit' => 'students.manage.edit',
            'update' => 'students.manage.update',
            'destroy' => 'students.manage.destroy',
        ]);
        Route::get('lecturers/manage/export', [KaprodiLecturerController::class, 'export'])->name('lecturers.manage.export');
        Route::post('lecturers/manage/import', [KaprodiLecturerController::class, 'import'])->name('lecturers.manage.import');
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
        Route::get('/reports', [KaprodiController::class, 'reportIndex'])->name('reports.index');
        Route::get('/reports/print', [KaprodiController::class, 'reportPrint'])->name('reports.print');
        Route::get('/submissions', [KaprodiController::class, 'submissions'])->name('submissions.index');
        Route::get('/submissions/create', [KaprodiController::class, 'create'])->name('submissions.create');
        Route::post('/submissions', [KaprodiController::class, 'store'])->name('submissions.store');
        Route::get('/submissions/{id}/edit-historical', [KaprodiController::class, 'editHistorical'])->name('submissions.edit-historical');
        Route::put('/submissions/{id}/historical', [KaprodiController::class, 'updateHistorical'])->name('submissions.update-historical');
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
    Route::middleware('permission:view menu: admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
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

        // RBAC Management
        Route::get('/rbac', [RolePermissionController::class, 'index'])->name('rbac.index');
        
        Route::get('/rbac/roles/create', [RolePermissionController::class, 'createRole'])->name('rbac.roles.create');
        Route::post('/rbac/roles', [RolePermissionController::class, 'storeRole'])->name('rbac.roles.store');
        Route::get('/rbac/roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('rbac.roles.edit');
        Route::put('/rbac/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('rbac.roles.update');
        Route::delete('/rbac/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('rbac.roles.destroy');
        
        Route::get('/rbac/permissions/create', [RolePermissionController::class, 'createPermission'])->name('rbac.permissions.create');
        Route::post('/rbac/permissions', [RolePermissionController::class, 'storePermission'])->name('rbac.permissions.store');
        Route::get('/rbac/permissions/{permission}/edit', [RolePermissionController::class, 'editPermission'])->name('rbac.permissions.edit');
        Route::put('/rbac/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('rbac.permissions.update');
        Route::delete('/rbac/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('rbac.permissions.destroy');
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
