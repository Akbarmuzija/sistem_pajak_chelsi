<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'nip'      => 'nullable|string|max:30|unique:users,nip',
            'email'    => 'required|email|unique:users,email',
            'jabatan'  => 'nullable|string|max:100',
            'role'     => 'required|in:staff,pimpinan',
            'password' => 'required|min:8|confirmed',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'nip'     => 'nullable|string|max:30|unique:users,nip,' . $user->id,
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'jabatan' => 'nullable|string|max:100',
            'role'    => 'required|in:staff,pimpinan',
        ]);
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }
        $user->update($validated);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function profile()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'nip'     => 'nullable|string|max:30|unique:users,nip,' . $user->id,
            'jabatan' => 'nullable|string|max:100',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) Storage::disk('public')->delete($user->foto);
            $validated['foto'] = $request->file('foto')->store('foto-profil', 'public');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
