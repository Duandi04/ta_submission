<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NavigationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['roles', 'programStudi'])
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest();
            })
            ->paginate(15);
        $roles = Role::all();
        $programStudis = ProgramStudi::all();

        return view('admin.users.index', compact('users', 'roles', 'programStudis'));
    }

    public function create()
    {
        $roles = Role::all();
        $programStudis = \App\Models\ProgramStudi::all();
        return view('admin.users.create', compact('roles', 'programStudis'));
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

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(Request $request, User $user)
    {
        $user->load(['roles', 'programStudi.faculty', 'thesisSubmissions', 'supervisedTheses', 'assessments.thesisSubmission.student']);

        $activities = \Spatie\Activitylog\Models\Activity::where(function ($q) use ($user) {
            $q->where('causer_id', $user->id)
                ->where('causer_type', User::class);
        })
            ->orWhere(function ($q) use ($user) {
                $q->where('subject_id', $user->id)
                    ->where('subject_type', User::class);
            })
            ->latest()
            ->take(20)
            ->get();

        // Contextual navigation
        $query = User::filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($user, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.users.show', compact('user', 'activities', 'navigation'));
    }

    public function edit(Request $request, User $user)
    {
        $roles = Role::all();
        $programStudis = ProgramStudi::all();

        $query = User::filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($user, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('admin.users.edit', compact('user', 'roles', 'programStudis', 'navigation'));
    }

    public function update(UserRequest $request, User $user)
    {
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
            if ($user->profile_photo) {
                Storage::disk('local')->delete($user->profile_photo);
            }
            $userData['profile_photo'] = null;
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('local')->delete($user->profile_photo);
            }
            $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === Auth::id(), 403, 'Tidak dapat menghapus akun sendiri.');

        activity()
            ->performedOn($user)
            ->log('Deleted user');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
