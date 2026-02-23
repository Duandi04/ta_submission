<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

use App\Helpers\NavigationHelper;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        $lecturers = User::role(['dosen', 'kaprodi'])
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate(15);

        $programStudis = ProgramStudi::all();

        return view('admin.lecturers.index', compact('lecturers', 'programStudis'));
    }

    public function create()
    {
        $programStudis = ProgramStudi::all();
        $roles = Role::whereIn('name', ['dosen', 'kaprodi'])->get();
        return view('admin.lecturers.create', compact('programStudis', 'roles'));
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
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('profile_photo')) {
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $user = User::create($userData);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.lecturers.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function show(Request $request, User $lecturer)
    {
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']))
            abort(404);

        $query = User::role(['dosen', 'kaprodi'])->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($lecturer, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.lecturers.show', compact('lecturer', 'navigation'));
    }

    public function edit(Request $request, User $lecturer)
    {
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']))
            abort(404);
        $programStudis = ProgramStudi::all();
        $roles = Role::whereIn('name', ['dosen', 'kaprodi'])->get();

        $query = User::role(['dosen', 'kaprodi'])->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($lecturer, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.lecturers.edit', compact('lecturer', 'programStudis', 'roles', 'navigation'));
    }

    public function update(UserRequest $request, User $lecturer)
    {
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']))
            abort(404);
        $validated = $request->validated();

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'program_studi_id' => $validated['program_studi_id'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle profile photo
        if ($request->boolean('remove_photo')) {
            if ($lecturer->profile_photo) {
                Storage::disk('local')->delete($lecturer->profile_photo);
            }
            $userData['profile_photo'] = null;
        } elseif ($request->hasFile('profile_photo')) {
            if ($lecturer->profile_photo) {
                Storage::disk('local')->delete($lecturer->profile_photo);
            }
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $lecturer->update($userData);
        $lecturer->syncRoles([$validated['role']]);

        return redirect()->route('admin.lecturers.index')->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(User $lecturer)
    {
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']))
            abort(404);
        $lecturer->delete();
        return redirect()->route('admin.lecturers.index')->with('success', 'Dosen berhasil dihapus.');
    }
}
