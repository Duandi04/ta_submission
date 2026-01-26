<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->role_group === 'lecturer') {
            $query->role(['dosen_pembimbing', 'dosen_penguji', 'kaprodi']);
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $programStudis = \App\Models\ProgramStudi::all();
        return view('admin.users.create', compact('roles', 'programStudis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nim_nip' => 'nullable|string|max:50|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'program_studi_id' => 'required_if:role,mahasiswa,dosen_pembimbing,dosen_penguji,kaprodi|nullable|exists:program_studis,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nim_nip' => $validated['nim_nip'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'program_studi_id' => $validated['program_studi_id'],
            'is_active' => $request->has('is_active'),
        ]);

        $user->assignRole($validated['role']);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('User created: ' . $user->name);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'programStudi.faculty', 'thesisSubmissions', 'supervisedTheses', 'assessments.thesisSubmission.student']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $programStudis = \App\Models\ProgramStudi::all();
        return view('admin.users.edit', compact('user', 'roles', 'programStudis'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'nim_nip' => 'nullable|string|max:50|unique:users,nim_nip,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|string|exists:roles,name',
            'program_studi_id' => 'required_if:role,mahasiswa,dosen_pembimbing,dosen_penguji,kaprodi|nullable|exists:program_studis,id',
            'is_active' => 'boolean',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'program_studi_id' => $validated['program_studi_id'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('User updated: ' . $user->name);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak dapat menghapus akun sendiri.');

        activity()
            ->performedOn($user)
            ->log('Deleted user');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
