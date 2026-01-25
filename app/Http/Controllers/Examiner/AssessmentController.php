<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentCriterion;
use App\Models\ThesisSubmission;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = auth()->user()->assessments()
            ->with(['thesisSubmission.student', 'thesisSubmission'])
            ->latest()
            ->paginate(10);

        return view('examiner.assessments.index', compact('assessments'));
    }

    public function create(Request $request)
    {
        $submission = ThesisSubmission::findOrFail($request->submission_id);

        // Check if examiner already has assessment for this submission
        $existing = Assessment::where('thesis_submission_id', $submission->id)
            ->where('evaluator_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()
                ->route('examiner.assessments.edit', $existing)
                ->with('info', 'Anda sudah memiliki penilaian untuk pengajuan ini.');
        }

        $criteria = AssessmentCriterion::active()->ordered()->get();
        return view('examiner.assessments.create', compact('submission', 'criteria'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'thesis_submission_id' => 'required|exists:thesis_submissions,id',
            'evaluator_type' => 'required|in:supervisor,examiner_1,examiner_2',
            'comments' => 'nullable',
            'strengths' => 'nullable',
            'weaknesses' => 'nullable',
            'recommendations' => 'nullable',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        // Check for duplicate
        $existing = Assessment::where('thesis_submission_id', $validated['thesis_submission_id'])
            ->where('evaluator_id', auth()->id())
            ->first();

        if ($existing) {
            return back()->withErrors(['error' => 'Anda sudah membuat penilaian untuk pengajuan ini.']);
        }

        $assessment = Assessment::create([
            'thesis_submission_id' => $validated['thesis_submission_id'],
            'evaluator_id' => auth()->id(),
            'evaluator_type' => $validated['evaluator_type'],
            'comments' => $validated['comments'],
            'strengths' => $validated['strengths'],
            'weaknesses' => $validated['weaknesses'],
            'recommendations' => $validated['recommendations'],
            'is_submitted' => false,
        ]);

        // Save scores
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($validated['scores'] as $criterionId => $score) {
            $criterion = AssessmentCriterion::find($criterionId);

            $assessment->scores()->create([
                'criterion_id' => $criterionId,
                'score' => $score,
            ]);

            $totalScore += ($score * $criterion->weight_percentage / 100);
            $totalWeight += $criterion->weight_percentage;
        }

        // Calculate weighted average
        $finalScore = $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0;
        $assessment->update(['total_score' => round($finalScore, 2)]);

        activity()
            ->performedOn($assessment)
            ->log('Created assessment');

        return redirect()
            ->route('examiner.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil disimpan sebagai draft!');
    }

    public function show(Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== auth()->id(), 403);

        $assessment->load(['thesisSubmission.student', 'scores.criterion']);
        return view('examiner.assessments.show', compact('assessment'));
    }

    public function edit(Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== auth()->id(), 403);
        abort_if($assessment->is_submitted, 403, 'Penilaian yang sudah disubmit tidak dapat diedit.');

        $assessment->load(['scores']);
        $criteria = AssessmentCriterion::active()->ordered()->get();
        $submission = $assessment->thesisSubmission;

        return view('examiner.assessments.edit', compact('assessment', 'submission', 'criteria'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== auth()->id(), 403);
        abort_if($assessment->is_submitted, 403);

        $validated = $request->validate([
            'comments' => 'nullable',
            'strengths' => 'nullable',
            'weaknesses' => 'nullable',
            'recommendations' => 'nullable',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $assessment->update([
            'comments' => $validated['comments'],
            'strengths' => $validated['strengths'],
            'weaknesses' => $validated['weaknesses'],
            'recommendations' => $validated['recommendations'],
        ]);

        // Update scores
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($validated['scores'] as $criterionId => $score) {
            $criterion = AssessmentCriterion::find($criterionId);

            $assessment->scores()->updateOrCreate(
                ['criterion_id' => $criterionId],
                ['score' => $score]
            );

            $totalScore += ($score * $criterion->weight_percentage / 100);
            $totalWeight += $criterion->weight_percentage;
        }

        $finalScore = $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0;
        $assessment->update(['total_score' => round($finalScore, 2)]);

        activity()
            ->performedOn($assessment)
            ->log('Updated assessment');

        return redirect()
            ->route('examiner.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function destroy(Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== auth()->id(), 403);
        abort_if($assessment->is_submitted, 403, 'Penilaian yang sudah disubmit tidak dapat dihapus.');

        activity()
            ->performedOn($assessment)
            ->log('Deleted assessment');

        $assessment->delete();

        return redirect()
            ->route('examiner.assessments.index')
            ->with('success', 'Penilaian berhasil dihapus!');
    }
}
