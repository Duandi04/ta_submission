<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ThesisSubmission;
use App\Services\Examiner\AssessmentService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function __construct(
        protected AssessmentService $assessmentService
    ) {
    }

    public function index()
    {
        $assessments = $this->assessmentService->getExaminerAssessments();

        return view('examiner.assessments.index', compact('assessments'));
    }

    public function create(Request $request)
    {
        $submission = ThesisSubmission::findOrFail($request->submission_id);

        $existing = $this->assessmentService->findExistingAssessment($submission->id);

        if ($existing) {
            return redirect()
                ->route('examiner.assessments.edit', $existing)
                ->with('info', 'Anda sudah memiliki penilaian untuk pengajuan ini.');
        }

        $criteria = $this->assessmentService->getCriteria();

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

        $existing = $this->assessmentService->findExistingAssessment($validated['thesis_submission_id']);

        if ($existing) {
            return back()->withErrors(['error' => 'Anda sudah membuat penilaian untuk pengajuan ini.']);
        }

        $assessment = $this->assessmentService->create($validated, $validated['scores']);

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
        abort_if(!$this->assessmentService->canEdit($assessment), 403, 'Penilaian yang sudah disubmit tidak dapat diedit.');

        $assessment->load(['scores']);
        $criteria = $this->assessmentService->getCriteria();
        $submission = $assessment->thesisSubmission;

        return view('examiner.assessments.edit', compact('assessment', 'submission', 'criteria'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        abort_if(!$this->assessmentService->canEdit($assessment), 403);

        $validated = $request->validate([
            'comments' => 'nullable',
            'strengths' => 'nullable',
            'weaknesses' => 'nullable',
            'recommendations' => 'nullable',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $this->assessmentService->update($assessment, $validated, $validated['scores']);

        return redirect()
            ->route('examiner.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function destroy(Assessment $assessment)
    {
        abort_if(!$this->assessmentService->canEdit($assessment), 403, 'Penilaian yang sudah disubmit tidak dapat dihapus.');

        $this->assessmentService->delete($assessment);

        return redirect()
            ->route('examiner.assessments.index')
            ->with('success', 'Penilaian berhasil dihapus!');
    }
}
