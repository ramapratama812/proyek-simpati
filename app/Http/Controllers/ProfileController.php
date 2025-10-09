<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // ✅ Tampilkan profil + form edit
    public function show()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa; // relasi dari model User

        return view('profile.show', compact('user', 'mahasiswa'));
    }

    // ✅ Update data profil & mahasiswa
    public function update(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        // ✅ Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nim' => 'nullable|string|max:50',
            'program_studi' => 'nullable|string|max:255',
            'perguruan_tinggi' => 'nullable|string|max:255',
            'jenjang_pendidikan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'nullable|string|max:20',
            'semester' => 'nullable|integer|min:1',
            'status_aktivitas' => 'nullable|string|max:50',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // ✅ Update data user
        $user->name = $validated['nama'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // ✅ Pastikan relasi mahasiswa ada (jika belum, buat baru)
        if (!$mahasiswa) {
            $mahasiswa = $user->mahasiswa()->create([]);
        }

        // ✅ Update data mahasiswa
        $mahasiswa->update([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'] ?? $mahasiswa->nim,
            'program_studi' => $validated['program_studi'] ?? $mahasiswa->program_studi,
            'perguruan_tinggi' => $validated['perguruan_tinggi'] ?? $mahasiswa->perguruan_tinggi,
            'jenjang_pendidikan' => $validated['jenjang_pendidikan'] ?? $mahasiswa->jenjang_pendidikan,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? $mahasiswa->jenis_kelamin,
            'semester' => $validated['semester'] ?? $mahasiswa->semester,
            'status_aktivitas' => $validated['status_aktivitas'] ?? $mahasiswa->status_aktivitas,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    // ✅ Hapus akun dan data mahasiswa
    public function destroy()
    {
        $user = Auth::user();

        if ($user->mahasiswa) {
            $user->mahasiswa->delete();
        }

        $user->delete();
        Auth::logout();

        return redirect('/login')->with('success', 'Akun Anda telah dihapus.');
    }
}
