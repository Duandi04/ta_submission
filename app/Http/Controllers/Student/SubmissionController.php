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
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $prodi = $user->programStudi;
        if ($prodi && ($prodi->submission_start || $prodi->submission_end)) {
            $now = now();
            if ($prodi->submission_start && $now->lt($prodi->submission_start)) {
                return redirect()->route('student.submissions.index')
                    ->with('error', "Masa pengajuan belum dimulai. Mulai pada: " . $prodi->submission_start->format('d/m/Y H:i'));
            }
            if ($prodi->submission_end && $now->gt($prodi->submission_end)) {
                return redirect()->route('student.submissions.index')
                    ->with('error', "Masa pengajuan telah berakhir pada: " . $prodi->submission_end->format('d/m/Y H:i'));
            }
        }

        return view('student.submissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'proposal_file' => 'required|file|mimes:pdf|max:10240',
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
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if($submission->student_id !== $user->id, 403);

        $submission->load(['supervisor', 'files', 'assessments.evaluator', 'statuses.changer']);

        $navigation = \App\Helpers\NavigationHelper::getNavigation(
            $submission,
            ThesisSubmission::where('student_id', $user->id),
            'created_at',
            'desc'
        );

        return view('student.submissions.show', compact('submission', 'navigation'));
    }

    public function edit(ThesisSubmission $submission)
    {
        abort_if(!$this->submissionService->canEdit($submission), 403, 'Pengajuan ini tidak dapat diedit.');

        $navigation = \App\Helpers\NavigationHelper::getNavigation(
            $submission,
            ThesisSubmission::where('student_id', \Illuminate\Support\Facades\Auth::id()),
            'created_at',
            'desc'
        );

        return view('student.submissions.edit', compact('submission', 'navigation'));
    }

    public function update(Request $request, ThesisSubmission $submission)
    {
        abort_if(!$this->submissionService->canEdit($submission), 403);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'abstract' => 'required',
            'research_field' => 'nullable|max:100',
            'proposal_file' => 'nullable|file|mimes:pdf|max:10240',
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

    public function cancel(ThesisSubmission $submission)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if($submission->student_id !== $user->id, 403);
        abort_if(!in_array($submission->status, ['draft', 'submitted']), 403, 'Pengajuan tidak dapat dibatalkan.');

        $submission->update(['status' => 'cancelled']);

        // Log status change
        $submission->statuses()->create([
            'previous_status' => $submission->getOriginal('status'),
            'new_status' => 'cancelled',
            'changed_by' => $user->id,
            'comment' => 'Dibatalkan oleh mahasiswa.',
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy($user)
            ->log('Membatalkan pengajuan');

        return redirect()
            ->route('student.submissions.index')
            ->with('success', 'Pengajuan berhasil dibatalkan!');
    }

    public function submit(ThesisSubmission $submission)
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        abort_if($submission->student_id !== $user->id, 403);
        abort_if($submission->status !== 'draft', 403, 'Hanya pengajuan draft yang dapat disubmit.');

        // Check Deadline
        $prodi = $user->programStudi;
        if ($prodi && ($prodi->submission_start || $prodi->submission_end)) {
            $now = now();
            if ($prodi->submission_start && $now->lt($prodi->submission_start)) {
                return redirect()->route('student.submissions.show', $submission)
                    ->with('error', "Masa pengajuan belum dimulai. Mulai pada: " . $prodi->submission_start->format('d/m/Y H:i'));
            }
            if ($prodi->submission_end && $now->gt($prodi->submission_end)) {
                return redirect()->route('student.submissions.show', $submission)
                    ->with('error', "Masa pengajuan telah berakhir pada: " . $prodi->submission_end->format('d/m/Y H:i'));
            }
        }

        $submission->update(['status' => 'submitted', 'submission_date' => now()]);

        // Log status change
        $submission->statuses()->create([
            'previous_status' => 'draft',
            'new_status' => 'submitted',
            'changed_by' => $user->id,
            'comment' => 'Pengajuan disubmit oleh mahasiswa.',
        ]);

        activity()
            ->performedOn($submission)
            ->causedBy($user)
            ->log('Mengajukan submission');

        return redirect()
            ->route('student.submissions.show', $submission)
            ->with('success', 'Pengajuan berhasil disubmit! Menunggu review Kaprodi.');
    }
}
