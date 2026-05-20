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


    public function storeRevision(ThesisSubmission $submission, ?UploadedFile $file = null): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // No longer limiting file revisions per submission
        if ($file) {
            $this->uploadFile($submission, $file, 'revision');
        }

        activity()
            ->performedOn($submission)
            ->log('Uploaded revision file');
    }

    public function create(array $data, ?UploadedFile $file = null): ThesisSubmission
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // 1. Check Deadline
        $prodi = $user->programStudi;
        if ($prodi && ($prodi->submission_start || $prodi->submission_end)) {
            $now = now();
            if ($prodi->submission_start && $now->lt($prodi->submission_start)) {
                throw ValidationException::withMessages([
                    'deadline' => "Masa pengajuan belum dimulai. Mulai pada: " . $prodi->submission_start->format('d/m/Y H:i')
                ]);
            }
            if ($prodi->submission_end && $now->gt($prodi->submission_end)) {
                throw ValidationException::withMessages([
                    'deadline' => "Masa pengajuan telah berakhir pada: " . $prodi->submission_end->format('d/m/Y H:i')
                ]);
            }
        }

        // 2. Check Submission Limit (Strict Batch Logic)
        $prodi = $user->programStudi;
        $attemptsPerBatch = $prodi ? (int) ($prodi->attempts_per_batch ?? 3) : (int) Setting::getValue('attempts_per_batch', 3);
        $maxBatches = $prodi ? (int) ($prodi->max_batches ?? 2) : (int) Setting::getValue('max_batches', 2);
        $maxTotal = $attemptsPerBatch * $maxBatches;

        $allSubmissions = $user->thesisSubmissions()->orderBy('id', 'asc')->get();
        $totalCount = $allSubmissions->count();

        if (!$user->can_exceed_submission_limit) {
            // Check if already has approved submission
            if ($allSubmissions->where('status', 'approved')->count() > 0) {
                throw ValidationException::withMessages([
                    'limit' => "Anda sudah memiliki pengajuan yang disetujui. Tidak diperbolehkan membuat pengajuan baru."
                ]);
            }

            // Check global total limit
            if ($totalCount >= $maxTotal) {
                throw ValidationException::withMessages([
                    'limit' => "Anda telah mencapai batas maksimal total pengajuan ({$maxTotal} kali)."
                ]);
            }

            // Check if current batch is "full" and needs all rejected before refill
            if ($totalCount > 0 && $totalCount % $attemptsPerBatch === 0) {
                $lastBatch = $allSubmissions->take(-$attemptsPerBatch);
                $allFinished = $lastBatch->every(fn($s) => in_array($s->status, ['rejected', 'cancelled']));

                if (!$allFinished) {
                    throw ValidationException::withMessages([
                        'limit' => "Batch pengajuan Anda saat ini ({$attemptsPerBatch} judul) belum selesai diproses. Anda hanya dapat memulai batch baru jika semua pengajuan di batch sebelumnya telah ditolak."
                    ]);
                }
            }
        }

        $submission = $user->thesisSubmissions()->create([
            ...collect($data)->toArray(),
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
        $submission->update(collect($data)->except(['revision_file'])->toArray());

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
