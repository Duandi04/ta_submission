<?php

namespace App\Http\Controllers;

use App\Helpers\SimilarityHelper;
use Illuminate\Http\Request;

class SimilarityController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:5',
            'exclude_id' => 'nullable|integer'
        ]);

        $similarSubmissions = SimilarityHelper::findSimilarSubmissions(
            $request->title,
            50, // threshold
            $request->exclude_id
        );

        return response()->json([
            'count' => $similarSubmissions->count(),
            'data' => $similarSubmissions->take(10)->values()
        ]);
    }
}
