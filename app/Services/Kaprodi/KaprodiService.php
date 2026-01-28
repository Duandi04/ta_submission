<?php

namespace App\Services\Kaprodi;

use App\Models\User;
use App\Models\Setting;
use App\Models\Rubric;
use App\Models\ThesisSubmission;
use Illuminate\Support\Facades\Auth;

class KaprodiService
{
    /**
     * Get all students with their submission counts.
     */
    public function getAllStudents(int $perPage = 10)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        return User::role('mahasiswa')
            ->when($prodiId, function ($query) use ($prodiId) {
                return $query->where('program_studi_id', $prodiId);
            })
            ->withCount('thesisSubmissions')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get settings for Kaprodi.
     */
    public function getSettings()
    {
        return Setting::all()->pluck('value', 'key');
    }

    /**
     * Update settings.
     */
    public function updateSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Get submissions for a specific student.
     */
    public function getStudentSubmissions(int $studentId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        return ThesisSubmission::where('student_id', $studentId)
            ->when($prodiId, function ($query) use ($prodiId) {
                return $query->whereHas('student', function ($q) use ($prodiId) {
                    $q->where('program_studi_id', $prodiId);
                });
            })
            ->with(['supervisor', 'files', 'assessments'])
            ->latest()
            ->get();
    }

    /**
     * Get all lecturers in the same program studi.
     */
    public function getLecturers()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        return User::role('dosen')
            ->when($prodiId, function ($query) use ($prodiId) {
                return $query->where('program_studi_id', $prodiId);
            })
            ->get();
    }

    /**
     * Assign lecturers to a submission.
     */
    public function assignLecturers(int $submissionId, array $data)
    {
        $submission = ThesisSubmission::findOrFail($submissionId);
        $assessorIds = $data['assessor_ids'] ?? [];

        // Check if submission is in 'submitted' status only.
        // If it is 'under_review', it means lecturers are already assigned and we should not allow changes (per user request: "udah gak bisa hapus penilainya, ataupun ubah")
        if ($submission->status === 'under_review') {
             throw new \Exception('Dosen penilai sudah ditetapkan dan tidak dapat diubah lagi.');
        }

        if ($submission->status !== 'submitted') {
             throw new \Exception('Mahasiswa belum melakukan submit pengajuan (final) atau status tidak valid untuk penetapan dosen.');
        }

        // Get the active rubric to lock it for this assignment
        $activeRubric = Rubric::where('is_active', true)->first();
        
        if (!$activeRubric) {
             throw new \Exception('Belum ada rubrik penilaian yang aktif. Harap aktifkan salah satu rubrik terlebih dahulu.');
        }

        $rubricId = $activeRubric->id;
        $rubricSnapshot = $activeRubric->criteria;

        // Delete old assessments that are not in the new list
        \App\Models\Assessment::where('thesis_submission_id', $submission->id)
            ->whereNotIn('evaluator_id', $assessorIds)
            ->delete();

        // Add or keep existing assessors with the locked rubric
        foreach ($assessorIds as $assessorId) {
            \App\Models\Assessment::withTrashed()->updateOrCreate(
                [
                    'thesis_submission_id' => $submission->id,
                    'evaluator_id' => $assessorId,
                ],
                [
                    'evaluator_type' => 'assessor',
                    'rubric_id' => $rubricId,
                    'rubric_snapshot' => $rubricSnapshot,
                    'deleted_at' => null, // Restore if it was soft deleted
                ]
            );
        }

        // Always update status to under_review when dosen is assigned
        $previousStatus = $submission->status;
        if ($previousStatus !== 'under_review' && count($assessorIds) > 0) {
            $submission->update(['status' => 'under_review']);
            
            // Log status change
            \App\Models\ThesisStatus::create([
                'thesis_submission_id' => $submission->id,
                'old_status' => $previousStatus,
                'new_status' => 'under_review',
                'changed_by' => Auth::id(),
                'comment' => 'Dosen penilai telah ditetapkan oleh Kaprodi. Pengajuan dalam penilaian.',
            ]);

            activity()
                ->performedOn($submission)
                ->causedBy(Auth::user())
                ->log('Dosen penilai ditetapkan, status berubah ke Dalam Penilaian');
        }
    }

    /**
     * Get all rubrics.
     */
    public function getRubrics()
    {
        return Rubric::latest()->get();
    }

    /**
     * Get rubric by ID.
     */
    public function getRubricById(int $id)
    {
        return Rubric::findOrFail($id);
    }

    /**
     * Create a new rubric.
     */
    public function createRubric(array $data)
    {
        return Rubric::create($data);
    }

    /**
     * Update an existing rubric.
     */
    public function updateRubric(int $id, array $data)
    {
        $rubric = Rubric::findOrFail($id);
        $rubric->update($data);
        return $rubric;
    }

    /**
     * Delete a rubric.
     */
    public function deleteRubric(int $id)
    {
        $rubric = Rubric::findOrFail($id);
        return $rubric->delete();
    }
}

