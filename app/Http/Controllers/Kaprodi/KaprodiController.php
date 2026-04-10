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
            $request,
            10,
            $request->query('sort_by', 'name'),
            $request->query('sort_order', 'asc')
        );
        return view('kaprodi.students.index', compact('students'));
    }

    public function reportIndex()
    {
        $students = \App\Models\User::role('mahasiswa')
            ->where('program_studi_id', \Illuminate\Support\Facades\Auth::user()->program_studi_id)
            ->whereHas('thesisSubmissions', function ($query) {
                $query->where('status', 'approved');
            })
            ->with(['thesisSubmissions' => function ($query) {
                $query->where('status', 'approved')->with('supervisor');
            }])
            ->orderBy('nim_nip', 'asc')
            ->get();

        return view('kaprodi.reports.index', compact('students'));
    }

    public function reportPrint()
    {
        $students = \App\Models\User::role('mahasiswa')
            ->where('program_studi_id', \Illuminate\Support\Facades\Auth::user()->program_studi_id)
            ->whereHas('thesisSubmissions', function ($query) {
                $query->where('status', 'approved');
            })
            ->with(['thesisSubmissions' => function ($query) {
                $query->where('status', 'approved')->with('supervisor');
            }])
            ->orderBy('nim_nip', 'asc')
            ->get();

        return view('kaprodi.reports.print', compact('students'));
    }

    public function submissions(\Illuminate\Http\Request $request)
    {
        $submissions = $this->kaprodiService->getAllSubmissions(
            $request,
            15,
            $request->query('sort_by', 'created_at'),
            $request->query('sort_order', 'desc')
        );
        $lecturers = $this->kaprodiService->getLecturers();
        $rubrics = $this->kaprodiService->getRubrics();
        return view('kaprodi.submissions.index', compact('submissions', 'lecturers', 'rubrics'));
    }

    public function studentDetails(\Illuminate\Http\Request $request, int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = $this->kaprodiService->getStudentSubmissions($studentId);
        $lecturers = $this->kaprodiService->getLecturers();

        // Contextual navigation
        $query = $this->kaprodiService->getStudentsQuery($request);
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

    public function batchAssignLecturers(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:thesis_submissions,id',
            'batch_assessor_ids' => 'required|array',
            'batch_assessor_ids.*' => 'exists:users,id',
            'batch_rubric_id' => 'required|exists:rubrics,id',
        ]);

        try {
            $this->kaprodiService->batchAssignLecturers($validated);
            return back()->with('success', count($validated['submission_ids']) . ' pengajuan berhasil diproses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function acceptSubmission(\Illuminate\Http\Request $request, int $submissionId)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_id',
        ]);

        try {
            $this->kaprodiService->acceptSubmission($submissionId, $validated);
            return back()->with('success', 'Pengajuan berhasil diterima dan dosen pembimbing telah ditetapkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectSubmission(\Illuminate\Http\Request $request, int $submissionId)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            $this->kaprodiService->rejectSubmission($submissionId, $validated);
            return back()->with('success', 'Pengajuan berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submissionShow(\Illuminate\Http\Request $request, int $submissionId)
    {
        $submission = \App\Models\ThesisSubmission::with(['student', 'files', 'assessments.evaluator', 'assessments.scores'])
            ->findOrFail($submissionId);

        // Contextual navigation
        $query = $this->kaprodiService->getSubmissionsQuery($request);
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
    public function create()
    {
        $students = \App\Models\User::role('mahasiswa')
            ->where('program_studi_id', \Illuminate\Support\Facades\Auth::user()->program_studi_id)
            ->orderBy('name')
            ->get();
        $lecturers = $this->kaprodiService->getLecturers();
        return view('kaprodi.submissions.create', compact('students', 'lecturers'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'research_field' => 'nullable|string|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_id',
            'submission_date' => 'nullable|date',
        ]);

        try {
            $this->kaprodiService->createHistoricalSubmission($validated);
            return redirect()->route('kaprodi.submissions.index')->with('success', 'Data history pengajuan berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function editHistorical(int $id)
    {
        $submission = \App\Models\ThesisSubmission::where('is_historical', true)->findOrFail($id);
        $students = \App\Models\User::role('mahasiswa')
            ->where('program_studi_id', \Illuminate\Support\Facades\Auth::user()->program_studi_id)
            ->orderBy('name')
            ->get();
        $lecturers = $this->kaprodiService->getLecturers();
        return view('kaprodi.submissions.edit_historical', compact('submission', 'students', 'lecturers'));
    }

    public function updateHistorical(\Illuminate\Http\Request $request, int $id)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'research_field' => 'nullable|string|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'supervisor_2_id' => 'nullable|exists:users,id|different:supervisor_id',
            'submission_date' => 'nullable|date',
        ]);

        try {
            $this->kaprodiService->updateHistoricalSubmission($id, $validated);
            return redirect()->route('kaprodi.submissions.show', $id)->with('success', 'Data history pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
