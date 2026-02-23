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

    public function index(\Illuminate\Http\Request $request)
    {
        $students = $this->kaprodiService->getAllStudents(
            10,
            $request->query('search'),
            $request->query('sort_by', 'name'),
            $request->query('sort_order', 'asc')
        );
        return view('kaprodi.students.index', compact('students'));
    }

    public function submissions(\Illuminate\Http\Request $request)
    {
        $submissions = $this->kaprodiService->getAllSubmissions(
            15,
            $request->query('search'),
            $request->query('status'),
            $request->query('sort_by', 'created_at'),
            $request->query('sort_order', 'desc')
        );
        return view('kaprodi.submissions.index', compact('submissions'));
    }

    public function studentDetails(\Illuminate\Http\Request $request, int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = $this->kaprodiService->getStudentSubmissions($studentId);
        $lecturers = $this->kaprodiService->getLecturers();

        // Contextual navigation
        $query = $this->kaprodiService->getStudentsQuery($request->search);
        $navigation = \App\Helpers\NavigationHelper::getNavigation(
            $student,
            $query,
            $request->sort_by ?: 'name',
            $request->sort_order ?: 'asc'
        );

        return view('kaprodi.students.show', compact('student', 'submissions', 'lecturers', 'navigation'));
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

    public function submissionShow(\Illuminate\Http\Request $request, int $submissionId)
    {
        $submission = \App\Models\ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($submissionId);

        // Contextual navigation
        $query = $this->kaprodiService->getSubmissionsQuery($request->search, $request->status);
        $navigation = \App\Helpers\NavigationHelper::getNavigation(
            $submission,
            $query,
            $request->sort_by ?: 'created_at',
            $request->sort_order ?: 'desc'
        );

        $lecturers = $this->kaprodiService->getLecturers();
        $rubrics = $this->kaprodiService->getRubrics();
        return view('kaprodi.submissions.show', compact('submission', 'lecturers', 'navigation', 'rubrics'));
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
