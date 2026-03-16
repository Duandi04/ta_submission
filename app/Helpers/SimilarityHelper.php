<?php

namespace App\Helpers;

use App\Models\ThesisSubmission;

class SimilarityHelper
{
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
                $obj = new \stdClass();
                $obj->id = $submission->id;
                $obj->title = $submission->title;
                $obj->student = (object)['name' => $submission->student->name ?? 'Unknown'];
                $obj->similarity_percentage = round($percent, 2);
                $obj->status = $submission->status;
                $obj->created_at = $submission->created_at;
                
                $similar->push($obj);
            }
        }

        return $similar->sortByDesc('similarity_percentage');
    }
}
