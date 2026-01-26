<?php

namespace App\Services\Student;

use App\Models\ThesisSubmission;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class SubmissionService
{
    /**
     * Get paginated submissions for the current student.
     */
    public function getStudentSubmissions(int $perPage = 10)
    {
        return Auth::user()->thesisSubmissions()
            ->with(['supervisor', 'assessments'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all available supervisors.
     */
    public function getSupervisors()
    {
        return User::role('dosen_pembimbing')->get();
    }

    /**
     * Create a new thesis submission.
     */
    public function create(array $data, ?UploadedFile $file = null): ThesisSubmission
    {
        $maxDrafts = (int) Setting::getValue('max_thesis_drafts', 3);
        $currentCount = Auth::user()->thesisSubmissions()->count();

        if ($currentCount >= $maxDrafts) {
            throw new \Exception("Anda telah mencapai batas maksimal pengunggahan draft ({$maxDrafts} draft). Tengah hubungi Kaprodi jika ada kendala.");
        }

        $submission = Auth::user()->thesisSubmissions()->create([
            ...$data,
            'status' => 'draft',
        ]);

        if ($file) {
            $this->uploadFile($submission, $file, 'proposal');
        }

        activity()
            ->performedOn($submission)
            ->log('Created thesis submission');

        return $submission;
    }

    /**
     * Update an existing thesis submission.
     */
    public function update(ThesisSubmission $submission, array $data, ?UploadedFile $file = null): ThesisSubmission
    {
        $submission->update($data);

        if ($file) {
            $this->uploadFile($submission, $file, 'proposal');
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
     * Upload a file for the submission.
     */
    protected function uploadFile(ThesisSubmission $submission, UploadedFile $file, string $type): void
    {
        $path = $file->store('submissions/' . $submission->id, 'public');

        $submission->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $type,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => Auth::id(),
        ]);
    }

    /**
     * Check if submission can be edited by the student.
     */
    public function canEdit(ThesisSubmission $submission): bool
    {
        return $submission->student_id === Auth::id() && $submission->canBeEditedByStudent();
    }

    /**
     * Check if submission can be deleted by the student.
     */
    public function canDelete(ThesisSubmission $submission): bool
    {
        return $submission->student_id === Auth::id() && $submission->status === 'draft';
    }
}
