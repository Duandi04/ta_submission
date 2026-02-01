<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kaprodi\KaprodiAssignLecturersRequest;
use App\Http\Requests\Kaprodi\KaprodiRubricRequest;
use App\Http\Requests\Kaprodi\KaprodiSettingsRequest;
use App\Services\Kaprodi\KaprodiService;

class KaprodiController extends Controller
{
    public function __construct(
        protected KaprodiService $kaprodiService
    ) {
    }

    public function index()
    {
        $students = $this->kaprodiService->getAllStudents();
        return view('kaprodi.students.index', compact('students'));
    }

    public function submissions()
    {
        $submissions = $this->kaprodiService->getAllSubmissions();
        return view('kaprodi.submissions.index', compact('submissions'));
    }

    public function studentDetails(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = $this->kaprodiService->getStudentSubmissions($studentId);
        $lecturers = $this->kaprodiService->getLecturers();
        return view('kaprodi.students.show', compact('student', 'submissions', 'lecturers'));
    }

    public function assignLecturers(KaprodiAssignLecturersRequest $request, int $submissionId)
    {
        $data = $request->validated();

        try {
            $this->kaprodiService->assignLecturers($submissionId, $data);
            return back()->with('success', 'Dosen penilai berhasil ditetapkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submissionShow(int $submissionId)
    {
        $submission = \App\Models\ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($submissionId);

        // Navigation logic
        $prev = \App\Models\ThesisSubmission::where('id', '<', $submissionId)->orderBy('id', 'desc')->first();
        $next = \App\Models\ThesisSubmission::where('id', '>', $submissionId)->orderBy('id', 'asc')->first();
        $count = \App\Models\ThesisSubmission::count();
        $position = \App\Models\ThesisSubmission::where('id', '<=', $submissionId)->count();

        $navigation = [
            'prev' => $prev?->id,
            'next' => $next?->id,
            'current' => $position,
            'total' => $count,
            'query' => []
        ];

        $lecturers = $this->kaprodiService->getLecturers();
        return view('kaprodi.submissions.show', compact('submission', 'lecturers', 'navigation'));
    }

    public function assessmentShow(\App\Models\Assessment $assessment)
    {
        $assessment->load(['evaluator', 'scores', 'thesisSubmission.student', 'thesisSubmission.files']);
        return view('kaprodi.assessments.show', compact('assessment'));
    }

    public function settings()
    {
        $settings = $this->kaprodiService->getSettings();
        return view('kaprodi.settings.index', compact('settings'));
    }

    public function updateSettings(KaprodiSettingsRequest $request)
    {
        $data = $request->validated();

        $this->kaprodiService->updateSettings($data);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function rubrics()
    {
        $rubrics = $this->kaprodiService->getRubrics();
        return view('kaprodi.rubrics.index', compact('rubrics'));
    }

    public function createRubric()
    {
        return view('kaprodi.rubrics.create');
    }

    public function storeRubric(KaprodiRubricRequest $request)
    {
        $data = $request->validated();

        $this->kaprodiService->createRubric($data);

        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil ditambahkan.');
    }

    public function editRubric(int $id)
    {
        $rubric = $this->kaprodiService->getRubricById($id);
        return view('kaprodi.rubrics.edit', compact('rubric'));
    }

    public function updateRubric(KaprodiRubricRequest $request, int $id)
    {
        $data = $request->validated();

        $this->kaprodiService->updateRubric($id, $data);

        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil diperbarui.');
    }

    public function destroyRubric(int $id)
    {
        $this->kaprodiService->deleteRubric($id);
        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil dihapus.');
    }
}
