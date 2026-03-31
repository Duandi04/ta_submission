<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Helpers\NavigationHelper;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $students = User::role('mahasiswa')
            ->where('program_studi_id', $user->program_studi_id)
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc')->orderBy('users.id', $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest('users.id');
            })
            ->withCount('thesisSubmissions')
            ->with(['thesisSubmissions' => function ($query) {
                $query->where('status', 'approved')->with('supervisor');
            }])
            ->paginate(15);

        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();

        return view('kaprodi.students.manage.index', compact('students', 'programStudis'));
    }

    public function create()
    {
        $user = Auth::user();
        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();
        return view('kaprodi.students.manage.create', compact('programStudis'));
    }

    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nim_nip' => $validated['nim_nip'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'program_studi_id' => $validated['program_studi_id'],
            'angkatan' => $validated['angkatan'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'can_exceed_submission_limit' => $request->boolean('can_exceed_submission_limit'),
        ];

        if ($request->hasFile('profile_photo')) {
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $user = User::create($userData);
        $user->assignRole('mahasiswa');

        return redirect()->route('kaprodi.students.manage.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Request $request, User $manage)
    {
        $student = $manage;
        $user = Auth::user();
        if (!$student->hasRole('mahasiswa') || $student->program_studi_id !== $user->program_studi_id)
            abort(404);

        $query = User::role('mahasiswa')->where('program_studi_id', $user->program_studi_id)->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($student, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('kaprodi.students.manage.show', compact('student', 'navigation'));
    }

    public function edit(Request $request, User $manage)
    {
        $student = $manage;
        $user = Auth::user();
        if (!$student->hasRole('mahasiswa') || $student->program_studi_id !== $user->program_studi_id)
            abort(404);
        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();

        $query = User::role('mahasiswa')->where('program_studi_id', $user->program_studi_id)->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($student, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('kaprodi.students.manage.edit', compact('student', 'programStudis', 'navigation'));
    }

    public function update(UserRequest $request, User $manage)
    {
        $student = $manage;
        $userAuth = Auth::user();
        if (!$student->hasRole('mahasiswa') || $student->program_studi_id !== $userAuth->program_studi_id)
            abort(404);
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'program_studi_id' => $validated['program_studi_id'],
            'angkatan' => $validated['angkatan'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'can_exceed_submission_limit' => $request->boolean('can_exceed_submission_limit'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle profile photo
        if ($request->boolean('remove_photo')) {
            if ($student->profile_photo) {
                Storage::disk('local')->delete($student->profile_photo);
            }
            $userData['profile_photo'] = null;
        } elseif ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('local')->delete($student->profile_photo);
            }
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $student->update($userData);

        return redirect()->route('kaprodi.students.manage.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(User $manage)
    {
        $student = $manage;
        $user = Auth::user();
        if (!$student->hasRole('mahasiswa') || $student->program_studi_id !== $user->program_studi_id)
            abort(404);
        $student->delete();
        return redirect()->route('kaprodi.students.manage.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
