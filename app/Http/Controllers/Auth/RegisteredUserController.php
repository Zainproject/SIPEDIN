<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'role' => 'nullable|string',

            // avatar
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // pejabat
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:50',
            'pangkat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'masa_bakti' => 'nullable|string|max:255',

            // bendahara
            'bendahara_nama' => 'required|string|max:255',
            'bendahara_nip' => 'required|string|max:50',
            'bendahara_pangkat' => 'nullable|string|max:255',
            'bendahara_jabatan' => 'nullable|string|max:255',
            'bendahara_masa_bakti' => 'nullable|string|max:255',

            // user auth
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = \App\Models\User::create([
            'role' => $request->role ?? 'pejabat',
            'avatar' => $avatarPath,

            // pejabat
            'name' => $request->name,
            'nip' => $request->nip,
            'pangkat' => $request->pangkat,
            'jabatan' => $request->jabatan,
            'masa_bakti' => $request->masa_bakti,

            // bendahara
            'bendahara_nama' => $request->bendahara_nama,
            'bendahara_nip' => $request->bendahara_nip,
            'bendahara_pangkat' => $request->bendahara_pangkat,
            'bendahara_jabatan' => $request->bendahara_jabatan,
            'bendahara_masa_bakti' => $request->bendahara_masa_bakti,

            // auth
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('home')->with('login_success', true);
    }
}
