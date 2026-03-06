<?php

namespace App\Services\Student;

use App\Models\ThesisSubmission;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SubmissionService
{
    /**
     * Get paginated submissions for the current student.
     */
    public function getStudentSubmissions(int $perPage = 10)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->thesisSubmissions()
            ->with(['supervisor', 'assessments'])
            ->latest()
            ->paginate($perPage);
    }


    /**
     * Store a revision file.
     */
    public function storeRevision(ThesisSubmission $submission, ?UploadedFile $file = null, ?string $googleFileId = null, ?string $googleAccessToken = null): void
    {
        if ($file) {
            $this->uploadFile($submission, $file, 'revision');
        } elseif ($googleFileId && $googleAccessToken) {
            $this->storeFromDrive($submission, $googleFileId, $googleAccessToken, 'revision');
        }

        activity()
            ->performedOn($submission)
            ->log('Uploaded revision file');
    }

    /**
     * Create a new thesis submission.
     */
    public function create(array $data, ?UploadedFile $file = null, ?string $googleFileId = null, ?string $googleAccessToken = null): ThesisSubmission
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $currentCount = $user->thesisSubmissions()->count();

        $maxDrafts = (int) Setting::getValue('max_thesis_drafts', 3);
        if ($currentCount >= $maxDrafts) {
            throw ValidationException::withMessages([
                'limit' => "Anda telah mencapai batas maksimal pengunggahan draft ({$maxDrafts} draft). Silakan hubungi Kaprodi jika ada kendala."
            ]);
        }

        $submission = $user->thesisSubmissions()->create([
            ...collect($data)->except(['google_file_id', 'google_access_token'])->toArray(),
            'status' => 'draft',
        ]);

        if ($file) {
            $this->uploadFile($submission, $file, 'proposal');
        } elseif ($googleFileId && $googleAccessToken) {
            $this->storeFromDrive($submission, $googleFileId, $googleAccessToken, 'proposal');
        }

        activity()
            ->performedOn($submission)
            ->log('Created thesis submission');

        return $submission;
    }

    /**
     * Update an existing thesis submission.
     */
    public function update(ThesisSubmission $submission, array $data, ?UploadedFile $file = null, ?string $googleFileId = null, ?string $googleAccessToken = null): ThesisSubmission
    {
        $submission->update(collect($data)->except(['google_file_id', 'google_access_token', 'revision_file'])->toArray());

        if ($file) {
            $this->uploadFile($submission, $file, 'proposal');
        } elseif ($googleFileId && $googleAccessToken) {
            $this->storeFromDrive($submission, $googleFileId, $googleAccessToken, 'proposal');
        }

        activity()
            ->performedOn($submission)
            ->log('Updated thesis submission');

        return $submission;
    }

    /**
     * Delete a thesis submission.
     */
    public function delete(ThesisSubmission $submission): void
    {
        activity()
            ->performedOn($submission)
            ->log('Deleted thesis submission');

        $submission->delete();
    }

    /**
     * Upload a file for the submission from local or configured disk.
     */
    protected function uploadFile(ThesisSubmission $submission, UploadedFile $file, string $type): void
    {
        $disk = config('filesystems.default', 'local');
        $path = $file->store('submissions/' . $submission->id, $disk);

        $submission->files()->create([
            'file_name'    => $file->getClientOriginalName(),
            'file_path'    => $path,
            'file_type'    => $type,
            'file_size'    => $file->getSize(),
            'mime_type'    => $file->getMimeType(),
            'uploaded_by'  => Auth::id(),
            'storage_disk' => $disk,
        ]);
    }

    /**
     * Store a file from Google Drive.
     */
    public function storeFromDrive(ThesisSubmission $submission, string $fileId, string $accessToken, string $type): void
    {
        // Fetch file metadata from Google Drive API
        $metadataResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?fields=name,size,mimeType");

        if ($metadataResponse->failed()) {
            throw new \Exception('Failed to fetch file metadata from Google Drive.');
        }

        $metadata = $metadataResponse->json();
        $fileName = $metadata['name'];
        $fileSize = $metadata['size'] ?? 0;
        $mimeType = $metadata['mimeType'];

        // Download file content
        $downloadResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

        if ($downloadResponse->failed()) {
            throw new \Exception('Failed to download file from Google Drive.');
        }

        $content = $downloadResponse->body();
        $disk    = config('filesystems.default', 'local');
        $path    = 'submissions/' . $submission->id . '/' . \Illuminate\Support\Str::random(40);

        // Add extension if missing in path but present in filename
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($extension) {
            $path .= '.' . $extension;
        }

        \Illuminate\Support\Facades\Storage::disk($disk)->put($path, $content);

        $submission->files()->create([
            'file_name'    => $fileName,
            'file_path'    => $path,
            'file_type'    => $type,
            'file_size'    => $fileSize,
            'mime_type'    => $mimeType,
            'uploaded_by'  => Auth::id(),
            'storage_disk' => $disk,
        ]);
    }

    /**
     * Check if submission can be edited by the student.
     */
    public function canEdit(ThesisSubmission $submission): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $submission->student_id === $user->id && $submission->canBeEditedByStudent();
    }

    /**
     * Check if submission can be deleted by the student.
     */
    public function canDelete(ThesisSubmission $submission): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $submission->student_id === $user->id && $submission->status === 'draft';
    }
}
