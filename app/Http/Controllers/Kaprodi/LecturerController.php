<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

use App\Helpers\NavigationHelper;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class LecturerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $lecturers = User::role(['dosen', 'kaprodi'])
            ->where('program_studi_id', $user->program_studi_id)
            ->filterByRequest($request)
            ->when($request->sort_by, function ($q) use ($request) {
                $q->orderBy($request->sort_by, $request->sort_order ?: 'asc')->orderBy('users.id', $request->sort_order ?: 'asc');
            }, function ($q) {
                $q->latest('users.id');
            })
            ->paginate(15);

        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();

        return view('kaprodi.lecturers.manage.index', compact('lecturers', 'programStudis'));
    }

    public function create()
    {
        $user = Auth::user();
        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();
        $roles = Role::whereIn('name', ['dosen'])->get();
        return view('kaprodi.lecturers.manage.create', compact('programStudis', 'roles'));
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
        
        $roles = [$validated['role']];
        if ($validated['role'] === 'kaprodi') {
            $roles[] = 'dosen';
        }
        $user->assignRole($roles);

        return redirect()->route('kaprodi.lecturers.manage.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function show(Request $request, User $manage)
    {
        $lecturer = $manage;
        $user = Auth::user();
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']) || $lecturer->program_studi_id !== $user->program_studi_id)
            abort(404);

        $query = User::role(['dosen', 'kaprodi'])->where('program_studi_id', $user->program_studi_id)->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($lecturer, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('kaprodi.lecturers.manage.show', compact('lecturer', 'navigation'));
    }

    public function edit(Request $request, User $manage)
    {
        $lecturer = $manage;
        $user = Auth::user();
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']) || $lecturer->program_studi_id !== $user->program_studi_id)
            abort(404);
        
        $programStudis = ProgramStudi::where('id', $user->program_studi_id)->get();
        // Kaprodi can only change roles to dosen, or keep them as kaprodi if they already are
        $roles = Role::whereIn('name', ['dosen', 'kaprodi'])->get();

        $query = User::role(['dosen', 'kaprodi'])->where('program_studi_id', $user->program_studi_id)->filterByRequest($request);
        $navigation = NavigationHelper::getNavigation($lecturer, $query, $request->sort_by ?: 'created_at', $request->sort_order ?: 'desc');

        return view('kaprodi.lecturers.manage.edit', compact('lecturer', 'programStudis', 'roles', 'navigation'));
    }

    public function update(UserRequest $request, User $manage)
    {
        $lecturer = $manage;
        $userAuth = Auth::user();
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']) || $lecturer->program_studi_id !== $userAuth->program_studi_id)
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

        $roles = [$validated['role']];
        if ($validated['role'] === 'kaprodi') {
            $roles[] = 'dosen';
        }
        $lecturer->syncRoles($roles);

        return redirect()->route('kaprodi.lecturers.manage.index')->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(User $manage)
    {
        $lecturer = $manage;
        $user = Auth::user();
        // Prevent deleting oneself
        if (!$lecturer->hasAnyRole(['dosen', 'kaprodi']) || $lecturer->program_studi_id !== $user->program_studi_id || $lecturer->id === $user->id)
            abort(404);
            
        $lecturer->delete();
        return redirect()->route('kaprodi.lecturers.manage.index')->with('success', 'Dosen berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $fileName = 'dosen_' . $user->programStudi->name . '_' . now()->format('Y-m-d') . '.xlsx';
        
        // Export only 'dosen' and 'kaprodi' roles for this prodi
        return Excel::download(new UserExport('dosen', $user->program_studi_id, $request->search), $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new UserImport('dosen'), $request->file('file'));
            return redirect()->back()->with('success', 'Data dosen berhasil diimpor.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . " (" . $failure->attribute() . "): " . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Gagal impor! Periksa data Anda:')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
