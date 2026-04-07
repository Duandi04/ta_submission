<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ThesisSubmission;
use App\Services\Examiner\AssessmentService;
use App\Helpers\SimilarityHelper;
use Illuminate\Http\Request;
use App\Models\Rubric; // Added Rubric model
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function __construct(
        protected AssessmentService $assessmentService
    ) {
    }

    public function index(Request $request)
    {
        $evaluatorId = Auth::id();
        $query = Assessment::where('evaluator_id', $evaluatorId)
            ->with(['thesisSubmission.student', 'thesisSubmission']);

        // Search by student name or NIM
        if ($search = $request->query('search')) {
            $query->whereHas('thesisSubmission.student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        // Filter by status (draft / submitted)
        if ($status = $request->query('status')) {
            if ($status === 'submitted') {
                $query->where('is_submitted', true);
            } elseif ($status === 'draft') {
                $query->where('is_submitted', false);
            }
        }

        $assessments = $query->latest()->paginate(10)->withQueryString();

        return view('dosen.assessments.index', compact('assessments'));
    }

    public function create(Request $request)
    {
        $submissionId = $request->query('submission_id');
        $submission = ThesisSubmission::with('student', 'files')->findOrFail($submissionId);

        $existing = $this->assessmentService->findExistingAssessment($submission->id);

        if ($existing) {
            return redirect()
                ->route('dosen.assessments.edit', $existing) // Changed route to dosen
                ->with('info', 'Anda sudah memiliki penilaian untuk pengajuan ini.');
        }

        // Get rubric assigned to submission
        $rubric = $submission->rubric ?? Rubric::where('is_active', true)->firstOrFail();

        // Get similar submissions
        $similarSubmissions = SimilarityHelper::findSimilarSubmissions($submission->title, 50, $submission->id)->take(5);

        return view('dosen.assessments.create', compact('submission', 'rubric', 'similarSubmissions')); // Changed view to dosen and compact rubric
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'thesis_submission_id' => 'required|exists:thesis_submissions,id',
            'rubric_id' => 'required|exists:rubrics,id',
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

        $submission = ThesisSubmission::findOrFail($validated['thesis_submission_id']);
        $assessment = $this->assessmentService->create($submission, $validated, $validated['scores']);

        return redirect()
            ->route('dosen.assessments.show', $assessment) // Changed route to dosen
            ->with('success', 'Penilaian berhasil disimpan sebagai draft!');
    }

    public function show(Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== Auth::id(), 403);

        $assessment->load(['thesisSubmission.student', 'thesisSubmission.files', 'scores.criterion']); // Added thesisSubmission.files
        return view('dosen.assessments.show', compact('assessment')); // Changed view to dosen
    }

    public function edit(Assessment $assessment)
    {
        // Add authorization check
        // if (!$assessment->canBeEditedBy(auth()->user())) abort(403);
        abort_if(!$this->assessmentService->canEdit($assessment), 403, 'Penilaian yang sudah disubmit tidak dapat diedit.');

        $assessment->load(['thesisSubmission.student', 'scores']); // Added thesisSubmission.student
        // If rubric format might verify, we could load it, but usually we use snapshot if existing?
        // But for editing we might default to snapshot.
        $criteria = $this->assessmentService->getCriteriaForAssessment($assessment); // Kept original logic for criteria
        $submission = $assessment->thesisSubmission; // Kept original logic for submission

        // Get similar submissions
        $similarSubmissions = SimilarityHelper::findSimilarSubmissions($submission->title, 50, $submission->id)->take(5);

        return view('dosen.assessments.edit', compact('assessment', 'submission', 'criteria', 'similarSubmissions')); // Changed view to dosen, kept submission and criteria
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
            ->route('dosen.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil diperbarui!');
    }

    public function destroy(Assessment $assessment)
    {
        abort_if(!$this->assessmentService->canEdit($assessment), 403, 'Penilaian yang sudah disubmit tidak dapat dihapus.');

        $this->assessmentService->delete($assessment);

        return redirect()
            ->route('dosen.assessments.index')
            ->with('success', 'Penilaian berhasil dihapus!');
    }

    public function submit(Assessment $assessment)
    {
        abort_if($assessment->evaluator_id !== Auth::id(), 403);
        abort_if($assessment->is_submitted, 403, 'Penilaian sudah disubmit.');

        if ($assessment->scores()->count() === 0) {
            return back()->with('error', 'Tidak dapat melakukan submit. Anda belum mengisi draft penilaian.');
        }

        $assessment->update([
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('dosen.assessments.show', $assessment)
            ->with('success', 'Penilaian berhasil disubmit!');
    }
}
