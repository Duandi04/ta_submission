<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileDownloadController extends Controller
{
    /**
     * Download a submission file with a custom filename format: [Student Name] Submission Title.ext
     */
    public function download(SubmissionFile $file)
    {
        $submission = $file->thesisSubmission;

        // Basic security check (Student can download their own, Dosen/Kaprodi/Admin/Koordinator can download based on roles)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has elevated privileges
        $hasElevatedAccess = $user->hasAnyRole(['dosen', 'admin', 'kaprodi', 'koordinator']);

        // If not elevated and is a student, enforce ownership
        if (!$hasElevatedAccess && $user->hasRole('mahasiswa')) {
            if ((int) $submission->student_id !== (int) $user->id) {
                abort(403);
            }
        }

        // Generate custom filename
        $studentName = $submission->student->name;
        $title = $submission->title;
        $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);

        // Sanitize title for filename
        $safeTitle = Str::limit(Str::slug($title, ' '), 50);
        $customFilename = sprintf('[%s] %s.%s', $studentName, $safeTitle, $extension);

        $disk = $file->storage_disk ?? 'local';

        // If file lives on R2/S3, redirect via a temporary signed URL
        if ($disk !== 'local') {
            $diskInstance = Storage::disk($disk);
            try {
                if (method_exists($diskInstance, 'temporaryUrl')) {
                    $tempUrl = $diskInstance->temporaryUrl($file->file_path, now()->addMinutes(15));
                    return redirect()->away($tempUrl);
                }
                // If temporaryUrl is not supported, fall through to local processing if possible
                // or just fail if it's strictly expected to be cloud.
            } catch (\Throwable $e) {
                activity()->log("Error generating signed URL for download [{$disk}]: " . $e->getMessage());
                abort(500, 'Gagal membuat tautan unduhan.');
            }
        }

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $path = Storage::disk('local')->path($file->file_path);

        return response()->download($path, $customFilename);
    }

    /**
     * Preview (inline display) a submission file (useful for PDFs).
     */
    public function preview(SubmissionFile $file)
    {
        $submission = $file->thesisSubmission;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user has elevated privileges
        $hasElevatedAccess = $user->hasAnyRole(['dosen', 'admin', 'kaprodi', 'koordinator']);

        // If not elevated and is a student, enforce ownership
        if (!$hasElevatedAccess && $user->hasRole('mahasiswa')) {
            if ((int) $submission->student_id !== (int) $user->id) {
                abort(403);
            }
        }

        $disk = $file->storage_disk ?? 'local';

        // If file lives on R2/S3, redirect via a temporary signed URL (inline open)
        if ($disk !== 'local') {
            $diskInstance = Storage::disk($disk);
            try {
                if (method_exists($diskInstance, 'temporaryUrl')) {
                    $tempUrl = $diskInstance->temporaryUrl($file->file_path, now()->addMinutes(15));
                    return redirect()->away($tempUrl);
                }
            } catch (\Throwable $e) {
                activity()->log("Error generating signed URL for preview [{$disk}]: " . $e->getMessage());
                abort(500, 'Gagal membuat pratinjau file.');
            }
        }

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $path = Storage::disk('local')->path($file->file_path);
        $mimeType = $file->mime_type ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }

    /**
     * Download or Preview user profile photo.
     */
    public function profilePhoto(\App\Models\User $user)
    {
        // Any authenticated user can see other users' profile photos
        // We rely on the 'auth' middleware on the route

        if (!$user->profile_photo) {
            return redirect(asset('images/default-avatar.png'));
        }

        if (!Storage::disk('local')->exists($user->profile_photo)) {
            return redirect(asset('images/default-avatar.png'));
        }

        $path = Storage::disk('local')->path($user->profile_photo);
        // Guess mime type based on extension
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
        ]);
    }
}
