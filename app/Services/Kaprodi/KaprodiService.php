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
    public function getAllStudents(\Illuminate\Http\Request $request, int $perPage = 10, ?string $sortBy = 'name', ?string $sortOrder = 'asc')
    {
        return $this->getStudentsQuery($request)
            ->when($sortBy, function ($query) use ($sortBy, $sortOrder) {
                return $query->orderBy($sortBy, $sortOrder ?: 'asc')->orderBy('users.id', $sortOrder ?: 'asc');
            }, function ($query) {
                return $query->latest('users.id');
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get the base query for all students (scoped to prodi).
     */
    public function getStudentsQuery(\Illuminate\Http\Request $request)
    {
        $search = $request->query('search');
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        return User::role('mahasiswa')
            ->when($prodiId, function ($query) use ($prodiId) {
                return $query->where('program_studi_id', $prodiId);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nim_nip', 'like', "%{$search}%");
                });
            })
            ->withCount('thesisSubmissions');
    }

    /**
     * Get all submissions (scoped to prodi).
     */
    public function getAllSubmissions(\Illuminate\Http\Request $request, int $perPage = 15, ?string $sortBy = 'created_at', ?string $sortOrder = 'desc')
    {
        return $this->getSubmissionsQuery($request)
            ->when($sortBy, function ($query) use ($sortBy, $sortOrder) {
                return $query->orderBy($sortBy, $sortOrder ?: 'asc')->orderBy('thesis_submissions.id', $sortOrder ?: 'desc');
            }, function ($query) {
                return $query->latest('thesis_submissions.id');
            })
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get the base query for all submissions (scoped to prodi).
     */
    public function getSubmissionsQuery(\Illuminate\Http\Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prodiId = $user->program_studi_id;

        return ThesisSubmission::with(['student', 'student.programStudi', 'files'])
            ->when($prodiId, function ($query) use ($prodiId) {
                return $query->whereHas('student', function ($q) use ($prodiId) {
                    $q->where('program_studi_id', $prodiId);
                });
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%")
                                ->orWhere('nim_nip', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($request->angkatan ?? null, function ($query, $angkatan) {
                return $query->whereHas('student', function ($q) use ($angkatan) {
                    $q->where('angkatan', $angkatan);
                });
            });
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
        $rubricId = $data['rubric_id'] ?? null;

        // Check if submission is in 'submitted' status only.
        if ($submission->status === 'under_review') {
            throw new \Exception('Dosen penilai sudah ditetapkan dan tidak dapat diubah lagi.');
        }

        if ($submission->status !== 'submitted') {
            throw new \Exception('Mahasiswa belum melakukan submit pengajuan (final) atau status tidak valid untuk penetapan dosen.');
        }

        $submission->update([
            'rubric_id' => $rubricId,
        ]);

        // Create or update rubric snapshot for this submission
        $rubricTemplate = Rubric::findOrFail($rubricId);
        $assessmentRubric = \App\Models\AssessmentRubric::updateOrCreate(
            ['thesis_submission_id' => $submission->id],
            [
                'name' => $rubricTemplate->name,
                'description' => $rubricTemplate->description,
                'criteria' => $rubricTemplate->criteria,
            ]
        );

        // Delete old assessments that are not in the new list
        \App\Models\Assessment::where('thesis_submission_id', $submission->id)
            ->whereNotIn('evaluator_id', $assessorIds)
            ->delete();

        // Add or keep existing assessors with the locked rubric snapshot
        foreach ($assessorIds as $assessorId) {
            \App\Models\Assessment::withTrashed()->updateOrCreate(
                [
                    'thesis_submission_id' => $submission->id,
                    'evaluator_id' => $assessorId,
                ],
                [
                    'evaluator_type' => 'assessor',
                    'rubric_id' => $rubricId, // Keep for reference to template
                    'assessment_rubric_id' => $assessmentRubric->id, // LINK TO SNAPSHOT
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
     * Batch assign lecturers to multiple submissions.
     */
    public function batchAssignLecturers(array $data)
    {
        $submissionIds = $data['submission_ids'];
        $assessorIds = $data['batch_assessor_ids'];
        $rubricId = $data['batch_rubric_id'];

        foreach ($submissionIds as $submissionId) {
            $this->assignLecturers($submissionId, [
                'assessor_ids' => $assessorIds,
                'rubric_id' => $rubricId,
            ]);
        }
    }

    /**
     * Accept submission, assign advisors and calculate final score.
     */
    public function acceptSubmission(int $submissionId, array $data)
    {
        $submission = ThesisSubmission::with('assessments')->findOrFail($submissionId);

        if ($submission->status !== 'under_review') {
            throw new \Exception('Pengajuan tidak dalam status Penilaian.');
        }

        if ($submission->assessments->count() === 0) {
            throw new \Exception('Dosen penilai belum ditetapkan.');
        }

        if ($submission->assessments->where('is_submitted', false)->count() > 0) {
            throw new \Exception('Semua dosen penilai harus mensubmit nilai terlebih dahulu.');
        }

        $finalScore = $submission->assessments->avg('total_score');

        $submission->update([
            'supervisor_id' => $data['supervisor_id'],
            'supervisor_2_id' => $data['supervisor_2_id'] ?? null,
            'final_score' => $finalScore,
            'status' => 'completed',
        ]);

        \App\Models\ThesisStatus::create([
            'thesis_submission_id' => $submission->id,
            'old_status' => 'under_review',
            'new_status' => 'completed',
            'changed_by' => Auth::id(),
            'comment' => 'Pengajuan telah diterima oleh Kaprodi dan dosen pembimbing telah ditetapkan.',
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy(Auth::user())
            ->log('Pengajuan diterima, pembimbing ditetapkan, status berubah ke Selesai');
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

