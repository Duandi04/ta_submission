<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|max:20',
            'address' => 'nullable',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ];

        // Only allow name change if NOT a student or lecturer (e.g., Admin)
        if (!$user->hasRole('mahasiswa') && !$user->hasRole('dosen')) {
            $rules['name'] = 'required|max:255';
        }

        $validated = $request->validate($rules);

        // Handle profile photo
        if ($request->boolean('remove_photo')) {
            if ($user->profile_photo) {
                Storage::disk('local')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = null;
            unset($validated['remove_photo']);
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('local')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'local');
        } else {
            unset($validated['profile_photo']);
            unset($validated['remove_photo']);
        }

        $user->update($validated);

        activity()
            ->performedOn($user)
            ->log('Updated profile');

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        activity()
            ->performedOn($user)
            ->log('Updated password');

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Password berhasil diubah!');
    }

    public function removePhoto()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Foto profil berhasil dihapus!');
    }
}
