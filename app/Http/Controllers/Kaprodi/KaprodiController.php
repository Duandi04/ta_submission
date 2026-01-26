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
        return view('kaprodi.students.show', compact('student', 'submissions'));
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
}
