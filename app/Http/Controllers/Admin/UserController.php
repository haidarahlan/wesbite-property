<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (Hanya Superadmin).
     */
    public function index(Request $request)
    {
        // Pastikan hanya superadmin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang memiliki hak akses manajemen user.');
        }

        $query = User::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Reset password user oleh Superadmin.
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berhak mereset password.');
        }

        $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', "Password untuk akun {$user->name} ({$user->email}) berhasil direset!");
    }

    /**
     * Ubah status aktif/nonaktif user oleh Superadmin.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berhak mengubah status akun.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}
