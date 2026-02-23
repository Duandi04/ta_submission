<?php

namespace App\Helpers;

use App\Models\ThesisSubmission;

class SimilarityHelper
{
    /**
     * Find submissions with similar titles.
     *
     * @param string $title
     * @param int $threshold Minimum percentage of similarity (0-100)
     * @param int|null $excludeId ID to exclude from search
     * @return \Illuminate\Support\Collection
     */
    public static function findSimilarSubmissions(string $title, int $threshold = 50, ?int $excludeId = null)
    {
        $allSubmissions = ThesisSubmission::where('status', '!=', 'draft')
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->get();

        $similar = collect();

        foreach ($allSubmissions as $submission) {
            similar_text(strtolower($title), strtolower($submission->title), $percent);

            if ($percent >= $threshold) {
                $similar->push([
                    'id' => $submission->id,
                    'title' => $submission->title,
                    'student' => $submission->student->name ?? 'Unknown',
                    'similarity' => round($percent, 2),
                    'status' => $submission->status
                ]);
            }
        }

        return $similar->sortByDesc('similarity');
    }
}
