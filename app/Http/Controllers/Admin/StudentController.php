<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Helpers\NavigationHelper;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::role('mahasiswa')
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate(15);

        $programStudis = ProgramStudi::all();

        return view('admin.students.index', compact('students', 'programStudis'));
    }

    public function create()
    {
        $programStudis = ProgramStudi::all();
        return view('admin.students.create', compact('programStudis'));
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
        ];

        if ($request->hasFile('profile_photo')) {
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $user = User::create($userData);
        $user->assignRole('mahasiswa');

        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Request $request, User $student)
    {
        if (!$student->hasRole('mahasiswa'))
            abort(404);

        $query = User::role('mahasiswa')->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($student, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.students.show', compact('student', 'navigation'));
    }

    public function edit(Request $request, User $student)
    {
        if (!$student->hasRole('mahasiswa'))
            abort(404);
        $programStudis = ProgramStudi::all();

        $query = User::role('mahasiswa')->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($student, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.students.edit', compact('student', 'programStudis', 'navigation'));
    }

    public function update(UserRequest $request, User $student)
    {
        if (!$student->hasRole('mahasiswa'))
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

        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(User $student)
    {
        if (!$student->hasRole('mahasiswa'))
            abort(404);
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
