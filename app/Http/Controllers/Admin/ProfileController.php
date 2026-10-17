<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman pengaturan profil & akun user yang sedang login.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Perbarui data profil (Nama, Email, No. Telp, Foto Profil & Tanda Tangan Digital).
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Max 2MB
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature_data' => ['nullable', 'string'], // Data Base64 dari canvas TTD manual
            'remove_avatar' => ['nullable', 'boolean'],
            'remove_signature' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini telah digunakan oleh akun lain.',
            'avatar.image' => 'Foto profil harus berupa file gambar.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
            'signature_file.image' => 'File tanda tangan harus berupa file gambar.',
            'signature_file.max' => 'Ukuran tanda tangan maksimal 2 MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        // 1. Kelola Foto Profil (Avatar)
        if (!empty($validated['remove_avatar']) && $user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // 2. Kelola Tanda Tangan Digital
        if (!empty($validated['remove_signature']) && $user->signature) {
            if (!str_starts_with($user->signature, 'data:image') && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }
            $user->signature = null;
        } elseif ($request->hasFile('signature_file')) {
            // Unggah via file
            if ($user->signature && !str_starts_with($user->signature, 'data:image') && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }
            $user->signature = $request->file('signature_file')->store('signatures', 'public');
        } elseif (!empty($validated['signature_data']) && str_starts_with($validated['signature_data'], 'data:image')) {
            // TTD Manual dari Canvas (Simpan sebagai file PNG transparan)
            if ($user->signature && !str_starts_with($user->signature, 'data:image') && Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }

            $imageParts = explode(';base64,', $validated['signature_data']);
            if (count($imageParts) === 2) {
                $imageDecoded = base64_decode($imageParts[1]);
                $fileName = 'signatures/signature_' . $user->id . '_' . time() . '.png';
                Storage::disk('public')->put($fileName, $imageDecoded);
                $user->signature = $fileName;
            }
        }

        $user->save();

        return back()->with('success', 'Profil dan informasi akun berhasil diperbarui!');
    }

    /**
     * Ubah Kata Sandi Akun.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak cocok dengan data kami.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui dengan aman!');
    }
}
