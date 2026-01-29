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

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('local')->download($file->file_path, $customFilename);
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

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $path = Storage::disk('local')->path($file->file_path);
        $mimeType = $file->mime_type ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
