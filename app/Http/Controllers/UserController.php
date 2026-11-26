<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request): View|JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->ajax()) {
            return DataTables::eloquent($users)
                ->addIndexColumn()
                ->addColumn('status_display', function (User $user): string {
                    return view('users.datatables.status', ['user' => $user])->render();
                })
                ->addColumn('actions', function (User $user): string {
                    return view('users.datatables.actions', ['user' => $user])->render();
                })
                ->rawColumns(['status_display', 'actions'])
                ->toJson();
        }

        return view('users.index');
    }

    public function create()
    {
        return view('users.form', ['user' => new User]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'role' => 'required|in:admin,kasir',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|in:admin,kasir',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
