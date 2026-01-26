<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use App\Services\Student\SubmissionService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService
    ) {
    }

    public function index()
    {
        $submissions = $this->submissionService->getStudentSubmissions();

        return view('student.submissions.index', compact('submissions'));
    }

    public function create()
    {
        $supervisors = $this->submissionService->getSupervisors();

        return view('student.submissions.create', compact('supervisors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $submission = $this->submissionService->create(
            $validated,
            $request->file('proposal_file')
        );

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil dibuat!');
    }

    public function show(ThesisSubmission $submission)
    {
        abort_if($submission->student_id !== auth()->id(), 403);

        $submission->load(['supervisor', 'files', 'assessments.evaluator', 'comments.user', 'statuses.changer']);

        return view('student.submissions.show', compact('submission'));
    }

    public function edit(ThesisSubmission $submission)
    {
        abort_if(!$this->submissionService->canEdit($submission), 403, 'Pengajuan ini tidak dapat diedit.');

        $supervisors = $this->submissionService->getSupervisors();

        return view('student.submissions.edit', compact('submission', 'supervisors'));
    }

    public function update(Request $request, ThesisSubmission $submission)
    {
        abort_if(!$this->submissionService->canEdit($submission), 403);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'proposal_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $this->submissionService->update(
            $submission,
            $validated,
            $request->file('proposal_file')
        );

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }

    public function destroy(ThesisSubmission $submission)
    {
        abort_if(!$this->submissionService->canDelete($submission), 403, 'Hanya pengajuan draft yang dapat dihapus.');

        $this->submissionService->delete($submission);

        return redirect()
            ->route('student.submissions.index')
            ->with('success', 'Pengajuan berhasil dihapus!');
    }
}
