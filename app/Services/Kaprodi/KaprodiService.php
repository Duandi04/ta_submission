<?php

namespace App\Services\Kaprodi;

use App\Models\User;
use App\Models\Setting;
use App\Models\Rubric;
use App\Models\ThesisSubmission;

class KaprodiService
{
    /**
     * Get all students with their submission counts.
     */
    public function getAllStudents(int $perPage = 10)
    {
        $prodiId = auth()->user()->program_studi_id;

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
     * Get all rubrics.
     */
    public function getRubrics()
    {
        return Rubric::all();
    }

    /**
     * Get submissions for a specific student.
     */
    public function getStudentSubmissions(int $studentId)
    {
        $prodiId = auth()->user()->program_studi_id;

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
}
