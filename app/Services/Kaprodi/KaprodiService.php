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
            ->withCount('thesisSubmissions')
            ->with(['thesisSubmissions' => function ($query) {
                $query->where('status', 'approved')->with('supervisor');
            }]);
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
            ->when($request->tahun_pengajuan ?? null, function ($query, $tahun) {
                return $query->whereYear('thesis_submissions.created_at', $tahun);
            });
    }

    /**
     * Get settings for Kaprodi.
     */
    public function getSettings()
    {
        $prodi = Auth::user()->programStudi;
        if (!$prodi) {
            return collect();
        }
        return collect([
            'max_batches' => $prodi->max_batches ?? 2,
            'attempts_per_batch' => $prodi->attempts_per_batch ?? 3,
            'submission_start' => $prodi->submission_start,
            'submission_end' => $prodi->submission_end,
        ]);
    }

    /**
     * Update settings.
     */
    public function updateSettings(array $settings)
    {
        $prodi = Auth::user()->programStudi;
        if ($prodi) {
            $prodi->update([
                'max_batches' => $settings['max_batches'],
                'attempts_per_batch' => $settings['attempts_per_batch'],
                'submission_start' => $settings['submission_start'] ?? null,
                'submission_end' => $settings['submission_end'] ?? null,
            ]);
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

    public function acceptSubmission(int $submissionId, array $data)
    {
        $submission = ThesisSubmission::with('assessments')->findOrFail($submissionId);

        if ($submission->status !== 'under_review') {
            throw new \Exception('Pengajuan tidak dalam status Penilaian.');
        }

        $existingApproved = ThesisSubmission::where('student_id', $submission->student_id)
            ->where('status', 'approved')
            ->exists();
        if ($existingApproved) {
            throw new \Exception('Mahasiswa ini sudah memiliki proposal yang diterima. Satu mahasiswa hanya boleh memiliki satu proposal yang diterima.');
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
            'status' => 'approved',
        ]);

        // Sync supervisors to ALL other submissions for this same student (consistency)
        ThesisSubmission::where('student_id', $submission->student_id)
            ->where('id', '!=', $submission->id)
            ->update([
                'supervisor_id' => $data['supervisor_id'],
                'supervisor_2_id' => $data['supervisor_2_id'] ?? null,
            ]);

        \App\Models\ThesisStatus::create([
            'thesis_submission_id' => $submission->id,
            'old_status' => 'under_review',
            'new_status' => 'approved',
            'changed_by' => Auth::id(),
            'comment' => 'Pengajuan telah diterima oleh Kaprodi dan dosen pembimbing telah ditetapkan.',
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy(Auth::user())
            ->log('Pengajuan diterima, pembimbing ditetapkan, status berubah ke Diterima');

        // Automatically reject other submissions from this student that are not approved.
        $otherSubmissions = ThesisSubmission::where('student_id', $submission->student_id)
            ->where('id', '!=', $submission->id)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->get();

        foreach ($otherSubmissions as $otherSubmission) {
            $oldStatus = $otherSubmission->status;
            $otherSubmission->update([
                'status' => 'rejected',
            ]);

            \App\Models\ThesisStatus::create([
                'thesis_submission_id' => $otherSubmission->id,
                'old_status' => $oldStatus,
                'new_status' => 'rejected',
                'changed_by' => Auth::id(),
                'comment' => 'Ditolak otomatis. Judul yang diterima: ' . $submission->title,
            ]);

            activity()
                ->performedOn($otherSubmission)
                ->causedBy(Auth::user())
                ->log('Pengajuan ditolak otomatis karena pengajuan lain diterima');
        }
    }

    /**
     * Reject submission.
     */
    public function rejectSubmission(int $submissionId, array $data)
    {
        $submission = ThesisSubmission::findOrFail($submissionId);

        if ($submission->status === 'approved') {
            throw new \Exception('Pengajuan yang sudah diterima tidak dapat ditolak.');
        }

        $oldStatus = $submission->status;
        
        $submission->update([
            'status' => 'rejected',
        ]);

        \App\Models\ThesisStatus::create([
            'thesis_submission_id' => $submission->id,
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'changed_by' => Auth::id(),
            'comment' => 'Pengajuan ditolak oleh Kaprodi. Alasan: ' . $data['rejection_reason'],
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy(Auth::user())
            ->log('Pengajuan ditolak, status berubah ke Ditolak');
    }

    /**
     * Get all rubrics.
     */
    public function getRubrics()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Rubric::where('program_studi_id', $user->program_studi_id)
            ->orWhereNull('program_studi_id')
            ->latest()
            ->get();
    }

    /**
     * Get rubric by ID.
     */
    public function getRubricById(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Rubric::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('program_studi_id', $user->program_studi_id)
                    ->orWhereNull('program_studi_id');
            })->firstOrFail();
    }

    /**
     * Create a new rubric.
     */
    public function createRubric(array $data)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data['program_studi_id'] = $user->program_studi_id;
        return Rubric::create($data);
    }

    /**
     * Update an existing rubric.
     */
    public function updateRubric(int $id, array $data)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $rubric = Rubric::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('program_studi_id', $user->program_studi_id)
                    ->orWhereNull('program_studi_id');
            })
            ->firstOrFail();
            
        $rubric->update($data);
        return $rubric;
    }

    /**
     * Delete a rubric.
     */
    public function deleteRubric(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $rubric = Rubric::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('program_studi_id', $user->program_studi_id)
                    ->orWhereNull('program_studi_id');
            })
            ->firstOrFail();
            
        return $rubric->delete();
    }
    /**
     * Create a historical submission by Kaprodi.
     */
    public function createHistoricalSubmission(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $submission = ThesisSubmission::create([
                'student_id' => $data['student_id'],
                'supervisor_id' => $data['supervisor_id'],
                'supervisor_2_id' => $data['supervisor_2_id'] ?? null,
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'research_field' => $data['research_field'],
                'status' => 'approved',
                'is_historical' => true,
                'submission_date' => $data['submission_date'] ?? now(),
            ]);

            \App\Models\ThesisStatus::create([
                'thesis_submission_id' => $submission->id,
                'old_status' => null,
                'new_status' => 'draft',
                'changed_by' => Auth::id(),
                'comment' => 'Dibuat oleh Kaprodi (Data History)',
            ]);

            \App\Models\ThesisStatus::create([
                'thesis_submission_id' => $submission->id,
                'old_status' => 'draft',
                'new_status' => 'approved',
                'changed_by' => Auth::id(),
                'comment' => 'Disetujui otomatis oleh Kaprodi (Data History)',
            ]);

            activity()
                ->performedOn($submission)
                ->causedBy(Auth::user())
                ->log('Pengajuan data history dibuat dan disetujui oleh Kaprodi');

            return $submission;
        });
    }

    /**
     * Update a historical submission.
     */
    public function updateHistoricalSubmission(int $id, array $data)
    {
        $submission = ThesisSubmission::where('is_historical', true)->findOrFail($id);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($submission, $data) {
            $submission->update([
                'student_id' => $data['student_id'],
                'supervisor_id' => $data['supervisor_id'],
                'supervisor_2_id' => $data['supervisor_2_id'] ?? null,
                'title' => $data['title'],
                'abstract' => $data['abstract'],
                'research_field' => $data['research_field'],
                'submission_date' => $data['submission_date'] ?? $submission->submission_date,
            ]);

            activity()
                ->performedOn($submission)
                ->causedBy(Auth::user())
                ->log('Data history pengajuan diperbarui oleh Kaprodi');

            return $submission;
        });
    }
}

