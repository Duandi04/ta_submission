<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ThesisSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = auth()->user()->thesisSubmissions()
            ->with(['supervisor', 'assessments'])
            ->latest()
            ->paginate(10);

        return view('student.submissions.index', compact('submissions'));
    }

    public function create()
    {
        $supervisors = \App\Models\User::role('dosen_pembimbing')->get();
        return view('student.submissions.create', compact('supervisors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        $submission = auth()->user()->thesisSubmissions()->create([
            ...$validated,
            'status' => 'draft',
        ]);

        // Handle file upload
        if ($request->hasFile('proposal_file')) {
            $file = $request->file('proposal_file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            $submission->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'proposal',
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        activity()
            ->performedOn($submission)
            ->log('Created thesis submission');

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
        abort_if($submission->student_id !== auth()->id(), 403);
        abort_if(!$submission->canBeEditedByStudent(), 403, 'Pengajuan ini tidak dapat diedit.');

        $supervisors = \App\Models\User::role('dosen_pembimbing')->get();
        return view('student.submissions.edit', compact('submission', 'supervisors'));
    }

    public function update(Request $request, ThesisSubmission $submission)
    {
        abort_if($submission->student_id !== auth()->id(), 403);
        abort_if(!$submission->canBeEditedByStudent(), 403);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'supervisor_id' => 'required|exists:users,id',
            'proposal_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $submission->update($validated);

        // Handle file upload
        if ($request->hasFile('proposal_file')) {
            $file = $request->file('proposal_file');
            $path = $file->store('submissions/' . $submission->id, 'public');

            $submission->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => 'proposal',
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        activity()
            ->performedOn($submission)
            ->log('Updated thesis submission');

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }

    public function destroy(ThesisSubmission $submission)
    {
        abort_if($submission->student_id !== auth()->id(), 403);
        abort_if($submission->status !== 'draft', 403, 'Hanya pengajuan draft yang dapat dihapus.');

        activity()
            ->performedOn($submission)
            ->log('Deleted thesis submission');

        $submission->delete();

        return redirect()
            ->route('student.submissions.index')
            ->with('success', 'Pengajuan berhasil dihapus!');
    }
}
