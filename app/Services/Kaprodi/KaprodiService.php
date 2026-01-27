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

        // Update supervisor
        if (isset($data['supervisor_id'])) {
            $submission->update(['supervisor_id' => $data['supervisor_id']]);
            
            // Sync supervisor assessment
            \App\Models\Assessment::updateOrCreate(
                [
                    'thesis_submission_id' => $submission->id,
                    'evaluator_type' => 'supervisor',
                ],
                ['evaluator_id' => $data['supervisor_id']]
            );
        }

        // Assign/Update Examiners
        if (isset($data['examiner_1_id'])) {
            \App\Models\Assessment::updateOrCreate(
                [
                    'thesis_submission_id' => $submission->id,
                    'evaluator_type' => 'examiner_1',
                ],
                ['evaluator_id' => $data['examiner_1_id']]
            );
        }

        if (isset($data['examiner_2_id'])) {
            \App\Models\Assessment::updateOrCreate(
                [
                    'thesis_submission_id' => $submission->id,
                    'evaluator_type' => 'examiner_2',
                ],
                ['evaluator_id' => $data['examiner_2_id']]
            );
        }

        // Update status if it was just submitted
        if ($submission->status === 'submitted') {
            $submission->update(['status' => 'under_review']);
            
            // Log status change
            \App\Models\ThesisStatus::create([
                'thesis_submission_id' => $submission->id,
                'old_status' => 'submitted',
                'new_status' => 'under_review',
                'changed_by' => Auth::id(),
                'comment' => 'Dosen pembimbing dan penguji telah ditetapkan oleh Kaprodi.',
            ]);
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

