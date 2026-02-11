<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            // Data Pejabat
            'name'       => ['required', 'string', 'max:150'],
            'nip'        => ['required', 'string', 'max:50'],
            'pangkat'    => ['nullable', 'string', 'max:80'],
            'masa_bakti' => ['nullable', 'string', 'max:80'],
            'jabatan'    => ['nullable', 'string', 'max:120'],

            // Data Bendahara
            'bendahara_nama'       => ['required', 'string', 'max:150'],
            'bendahara_nip'        => ['required', 'string', 'max:50'],
            'bendahara_pangkat'    => ['nullable', 'string', 'max:80'],
            'bendahara_masa_bakti' => ['nullable', 'string', 'max:80'],
            'bendahara_jabatan'    => ['nullable', 'string', 'max:120'],

            // Data Login
            'email'  => ['required', 'email', 'max:120', 'unique:users,email,' . $user->id],

            // Avatar
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Kata sandi berhasil diperbarui.');
    }
}
