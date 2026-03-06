<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubmissionFile;
use App\Models\ThesisSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageController extends Controller
{
    /**
     * Return metadata for a submission file.
     *
     * GET /api/storage/info/{file}
     */
    public function info(SubmissionFile $file): JsonResponse
    {
        $this->authorizeFileAccess($file);

        $disk = $file->storage_disk ?? config('filesystems.default');

        return response()->json([
            'id'           => $file->id,
            'name'         => $file->file_name,
            'type'         => $file->file_type,
            'type_label'   => $file->getFileTypeLabel(),
            'size_bytes'   => $file->file_size,
            'size_human'   => $file->getFormattedFileSize(),
            'mime_type'    => $file->mime_type,
            'storage_disk' => $disk,
            'uploaded_at'  => $file->created_at->toIso8601String(),
        ]);
    }

    /**
     * Generate a temporary pre-signed URL for downloading a file from R2.
     *
     * GET /api/storage/signed-url/{file}
     *
     * Returns a JSON response with:
     *   { "url": "https://...", "expires_at": "2026-..." }
     */
    public function signedUrl(SubmissionFile $file): JsonResponse
    {
        $this->authorizeFileAccess($file);

        $disk = $file->storage_disk ?? config('filesystems.default');

        // Local disk: generate a signed route URL via Laravel's route signing
        if ($disk === 'local') {
            $url = route('files.download', ['file' => $file->id]);
            return response()->json([
                'url'        => $url,
                'expires_at' => null,
                'disk'       => 'local',
                'note'       => 'Local file — redirect to download route.',
            ]);
        }

        // Generate a temporary presigned URL if the disk driver supports it (e.g., S3/R2)
        $expiresAt = now()->addMinutes(15);
        $diskInstance = Storage::disk($disk);

        try {
            // Check if method exists on the adapter to avoid "Undefined method" errors
            if (method_exists($diskInstance, 'temporaryUrl')) {
                $url = $diskInstance->temporaryUrl(
                    $file->file_path,
                    $expiresAt,
                );
            } else {
                // Fallback for disks that don't support signed URLs (like 'local')
                $url = route('files.download', ['file' => $file->id]);
                return response()->json([
                    'url'        => $url,
                    'expires_at' => null,
                    'disk'       => $disk,
                    'note'       => 'Disk does not support temporary URLs — using internal download route.',
                ]);
            }
        } catch (\Throwable $e) {
            // Catching Throwable to handle both Error and Exception
            return response()->json([
                'error'   => 'Could not generate signed URL.',
                'message' => $e->getMessage(),
                'disk'    => $disk,
            ], 500);
        }

        return response()->json([
            'url'        => $url,
            'expires_at' => $expiresAt->toIso8601String(),
            'disk'       => $disk,
        ]);
    }

    /**
     * Upload a file directly to the configured storage disk (R2 by default).
     *
     * POST /api/storage/upload
     *
     * Request body (multipart/form-data):
     *   - file           : required|file|mimes:pdf,doc,docx|max:10240
     *   - submission_id  : required|integer (must belong to authenticated student)
     *   - file_type      : required|in:proposal,final_document,presentation,revision
     *
     * Returns: JSON with the created SubmissionFile record.
     */
    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file'          => 'required|file|mimes:pdf,doc,docx|max:10240',
            'submission_id' => 'required|integer|exists:thesis_submissions,id',
            'file_type'     => 'required|in:proposal,final_document,presentation,revision',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $submission = ThesisSubmission::findOrFail($validated['submission_id']);

        // Only the owning student (or admin/kaprodi) may upload
        $isOwner   = (int) $submission->student_id === (int) $user->id;
        $isElevated = $user->hasAnyRole(['admin', 'kaprodi']);

        if (!$isOwner && !$isElevated) {
            return response()->json(['error' => 'Forbidden. You do not own this submission.'], 403);
        }

        $uploadedFile = $request->file('file');
        $disk         = config('filesystems.default', 'local');
        $path         = $uploadedFile->store('submissions/' . $submission->id, $disk);

        $record = $submission->files()->create([
            'file_name'    => $uploadedFile->getClientOriginalName(),
            'file_path'    => $path,
            'file_type'    => $validated['file_type'],
            'file_size'    => $uploadedFile->getSize(),
            'mime_type'    => $uploadedFile->getMimeType(),
            'uploaded_by'  => $user->id,
            'storage_disk' => $disk,
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy($user)
            ->log("Uploaded file via API to disk [{$disk}]: {$record->file_name}");

        return response()->json([
            'message' => 'File uploaded successfully.',
            'file'    => [
                'id'           => $record->id,
                'name'         => $record->file_name,
                'type'         => $record->file_type,
                'size_bytes'   => $record->file_size,
                'size_human'   => $record->getFormattedFileSize(),
                'mime_type'    => $record->mime_type,
                'storage_disk' => $record->storage_disk,
                'uploaded_at'  => $record->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Delete a file from storage and the database.
     * Admin only — guarded in route definition via 'role:admin' middleware.
     *
     * DELETE /api/storage/{file}
     */
    public function destroy(SubmissionFile $file): JsonResponse
    {
        $disk = $file->storage_disk ?? config('filesystems.default');

        if (Storage::disk($disk)->exists($file->file_path)) {
            Storage::disk($disk)->delete($file->file_path);
        }

        $fileName = $file->file_name;
        $file->delete();

        activity()->log("Admin deleted file via API: {$fileName} from disk [{$disk}]");

        return response()->json([
            'message' => "File '{$fileName}' deleted successfully.",
        ]);
    }

    /**
     * Migrate a file from one storage disk to another (e.g., local → r2).
     * Admin only.
     *
     * PATCH /api/storage/{file}/migrate-disk
     *
     * Request body (JSON):
     *   { "target_disk": "r2" }
     */
    public function migrateDisk(Request $request, SubmissionFile $file): JsonResponse
    {
        $validated = $request->validate([
            'target_disk' => 'required|string|in:local,r2,s3',
        ]);

        $sourceDisk = $file->storage_disk ?? config('filesystems.default');
        $targetDisk = $validated['target_disk'];

        if ($sourceDisk === $targetDisk) {
            return response()->json(['message' => "File is already on disk [{$targetDisk}]."], 200);
        }

        if (!Storage::disk($sourceDisk)->exists($file->file_path)) {
            return response()->json(['error' => "File not found on source disk [{$sourceDisk}]."], 404);
        }

        // Stream copy from source to target
        $stream = Storage::disk($sourceDisk)->readStream($file->file_path);
        Storage::disk($targetDisk)->writeStream($file->file_path, $stream);

        // Delete from source
        Storage::disk($sourceDisk)->delete($file->file_path);

        // Update DB record
        $file->update(['storage_disk' => $targetDisk]);

        activity()->log(
            "Migrated file [{$file->file_name}] from disk [{$sourceDisk}] to [{$targetDisk}]"
        );

        return response()->json([
            'message'     => "File migrated from [{$sourceDisk}] to [{$targetDisk}] successfully.",
            'storage_disk' => $targetDisk,
        ]);
    }

    /**
     * Shared access check: students can only access their own submission's files.
     * Dosen / Kaprodi / Admin have full access.
     */
    protected function authorizeFileAccess(SubmissionFile $file): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['dosen', 'admin', 'kaprodi', 'koordinator'])) {
            return;
        }

        if ($user->hasRole('mahasiswa')) {
            $submission = $file->thesisSubmission;
            if ((int) $submission->student_id !== (int) $user->id) {
                abort(403, 'You do not have access to this file.');
            }
        }
    }
}
