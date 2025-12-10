<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 🔹 Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['dosen', 'mahasiswa'])],

            // ❌ NIDN dihapus total
            // 'nidn' => 'nullable|string|max:20',

            // hanya mahasiswa yg butuh NIM
            'nim' => 'nullable|string|max:20',
        ]);

        // 🔹 Simpan user baru ke tabel users
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // 🔹 Jika role dosen — simpan data ke tabel dosens
        if ($user->role === 'dosen') {
            Dosen::create([
                'nama' => $user->name,
                'email' => $user->email,

                // ❌ tidak ada nidn lagi
                'nidn' => null,

                'nip' => null,
                'perguruan_tinggi' => null,
                'status_ikatan_kerja' => null,
                'jenis_kelamin' => null,
                'program_studi' => null,
                'pendidikan_terakhir' => null,
                'foto' => null,
            ]);
        }

        // 🔹 Jika role mahasiswa — simpan data mahasiswa
        if ($user->role === 'mahasiswa') {
            Mahasiswa::create([
                'nama'  => $validated['name'],
                'nim'   => $validated['nim'] ?? null,
                'email' => $validated['email'],
            ]);
        }

        // 🔹 Login otomatis
        Auth::login($user);

        // 🔹 Redirect
        return redirect()->route('dashboard')
            ->with('success', 'Pendaftaran berhasil! Selamat datang di SIMPATI.');
    }
}
