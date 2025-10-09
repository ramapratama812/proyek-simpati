<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
        ]);

        // 🔹 Simpan user baru ke tabel users
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // 🔹 Jika role-nya dosen, buat juga data dasar di tabel dosens
        if ($user->role === 'dosen') {
            Dosen::create([
                'nama' => $user->name,
                'email' => $user->email,
                'nidn' => null,
                'nip' => null,
                'perguruan_tinggi' => null,
                'status_ikatan_kerja' => null,
                'jenis_kelamin' => null,
                'program_studi' => null,
                'pendidikan_terakhir' => null,
                'status_aktivitas' => null,
                'foto' => null,
            ]);
        }

        // 🔹 Login otomatis setelah daftar
        Auth::login($user);

        // 🔹 Arahkan berdasarkan peran
        if ($user->role === 'dosen') {
            return redirect()->route('dosen.profile.show') // ubah ke route profil dosen kamu
                ->with('success', 'Pendaftaran berhasil! Silakan lengkapi profil Anda.');
        }

        if ($user->role === 'mahasiswa') {
            return redirect()->route('mahasiswa.index')
                ->with('success', 'Pendaftaran berhasil! Selamat datang di SIMPATI.');
        }

        // Default redirect
        return redirect()->route('dashboard');
    }
}
