<?php

use App\Http\Controllers\Api\StorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes use token-based auth via session (same as web) or Bearer tokens
| if Sanctum is installed. All endpoints are protected by throttle middleware.
|
| Rate Limits:
|   - General API:  api         -> throttle:api        (60/min per user or IP)
|   - File Uploads: api.upload  -> throttle:api.upload (10/min per user or IP)
|
*/

Route::middleware(['auth', 'throttle:api'])->prefix('storage')->name('api.storage.')->group(function () {

    // Get file metadata (name, size, MIME type, disk, URL)
    Route::get('/info/{file}', [StorageController::class, 'info'])
        ->name('info');

    // Generate a temporary pre-signed URL for a file (15-minute expiry)
    Route::get('/signed-url/{file}', [StorageController::class, 'signedUrl'])
        ->name('signed-url');

    // Upload a file to R2 (or configured disk)
    Route::post('/upload', [StorageController::class, 'upload'])
        ->middleware('throttle:api.upload')
        ->name('upload');

    // Delete a file (admin only)
    Route::delete('/{file}', [StorageController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('destroy');

    // Switch storage disk for a file (admin only — migrate local -> r2 or vice versa)
    Route::patch('/{file}/migrate-disk', [StorageController::class, 'migrateDisk'])
        ->middleware('role:admin')
        ->name('migrate-disk');
});
