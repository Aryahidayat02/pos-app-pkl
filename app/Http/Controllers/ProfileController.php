<?php

namespace App\Http\Controllers;

use App\Support\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request, ImageService $imageService)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldPhoto = $user->photo;
        if ($request->hasFile('photo')) {
            $validated['photo'] = $imageService->storeCover($request->file('photo'), 'profiles');
        }

        $user->update($validated);

        if ($request->hasFile('photo') && $oldPhoto && ! filter_var($oldPhoto, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        Auth::user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
