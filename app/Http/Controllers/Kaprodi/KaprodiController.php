<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Services\Kaprodi\KaprodiService;
use Illuminate\Http\Request;

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

    public function studentDetails(int $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);
        $submissions = $this->kaprodiService->getStudentSubmissions($studentId);
        $lecturers = $this->kaprodiService->getLecturers();
        return view('kaprodi.students.show', compact('student', 'submissions', 'lecturers'));
    }

    public function assignLecturers(Request $request, int $submissionId)
    {
        $data = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
            'examiner_1_id' => 'required|exists:users,id',
            'examiner_2_id' => 'required|exists:users,id',
        ]);

        $this->kaprodiService->assignLecturers($submissionId, $data);

        return back()->with('success', 'Dosen pembimbing dan penguji berhasil ditetapkan.');
    }

    public function settings()
    {
        $settings = $this->kaprodiService->getSettings();
        return view('kaprodi.settings.index', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'max_thesis_drafts' => 'required|integer|min:1',
        ]);

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

    public function storeRubric(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0|max:100',
            'criteria.*.description' => 'nullable|string',
        ]);

        $totalWeight = array_sum(array_column($request->criteria, 'weight'));
        if ($totalWeight != 100) {
            return back()->withErrors(['criteria' => 'Total bobot kriteria harus tepat 100% (saat ini ' . $totalWeight . '%).'])->withInput();
        }

        $this->kaprodiService->createRubric($data);

        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil ditambahkan.');
    }

    public function editRubric(int $id)
    {
        $rubric = $this->kaprodiService->getRubricById($id);
        return view('kaprodi.rubrics.edit', compact('rubric'));
    }

    public function updateRubric(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0|max:100',
            'criteria.*.description' => 'nullable|string',
        ]);

        $totalWeight = array_sum(array_column($request->criteria, 'weight'));
        if ($totalWeight != 100) {
            return back()->withErrors(['criteria' => 'Total bobot kriteria harus tepat 100% (saat ini ' . $totalWeight . '%).'])->withInput();
        }

        $this->kaprodiService->updateRubric($id, $data);

        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil diperbarui.');
    }

    public function destroyRubric(int $id)
    {
        $this->kaprodiService->deleteRubric($id);
        return redirect()->route('kaprodi.rubrics.index')->with('success', 'Rubrik berhasil dihapus.');
    }
}
