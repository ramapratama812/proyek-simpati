<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    // ========================================================
    // 📘 INDEX — daftar semua mahasiswa
    // ========================================================
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Mahasiswa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->orderBy('nama')->paginate(10);
        return view('mahasiswa.index', compact('mahasiswas'));
    }

    // ========================================================
    // 📘 SHOW — detail profil mahasiswa
    // ========================================================
    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = Auth::user(); // user yang sedang login

        return view('mahasiswa.show', compact('mahasiswa', 'user'));
    }

    // ========================================================
    // 📘 CREATE & STORE — tambah mahasiswa (khusus admin)
    // ========================================================
    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nim'      => 'required|string|unique:mahasiswas,nim',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat akun user
        $user = User::create([
            'name'     => $validated['nama'],
            'username' => strtolower(str_replace(' ', '', $validated['nama'])),
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'mahasiswa',
        ]);

        // Buat data mahasiswa
        Mahasiswa::create([
            'nama'             => $validated['nama'],
            'nim'              => $validated['nim'],
            'email'            => $validated['email'],
            'jenis_kelamin'    => $request->jenis_kelamin,
            'program_studi'    => $request->program_studi,
            'perguruan_tinggi' => $request->perguruan_tinggi,
            'status_terakhir'  => $request->status_terakhir,
            'user_id'          => $user->id,
        ]);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    // ========================================================
    // 📘 PROFIL MAHASISWA LOGIN
    // ========================================================
    public function profile()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        return view('mahasiswa.show', compact('user', 'mahasiswa'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        return view('mahasiswa.edit', compact('user', 'mahasiswa'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        // ✅ Validasi tabel users
        $validatedUser = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        // ✅ Validasi tabel mahasiswas
        $validatedMahasiswa = $request->validate([
            'nim'              => 'required|string|max:50|unique:mahasiswas,nim,' . ($mahasiswa?->id ?? 'NULL'),
            'jenis_kelamin'    => 'nullable|string|max:20',
            'program_studi'    => 'nullable|string|max:100',
            'perguruan_tinggi' => 'nullable|string|max:150',
            'semester'         => 'nullable|string|max:10',
            'status_terakhir'  => 'nullable|string|max:100',
        ]);

        // ✅ Upload foto jika ada
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists(str_replace('storage/', '', $user->foto))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->foto));
            }

            $path = $request->file('foto')->store('fotos', 'public');
            $validatedUser['foto'] = 'storage/' . $path;
        }

        // ✅ Update password hanya jika diisi
        if ($request->filled('password')) {
            $validatedUser['password'] = bcrypt($request->password);
        } else {
            unset($validatedUser['password']);
        }

        // ✅ Update data user
        $user->update($validatedUser);

        // ✅ Update atau buat data mahasiswa
        if ($mahasiswa) {
            $mahasiswa->update($validatedMahasiswa + [
                'nama'  => $validatedUser['name'],
                'email' => $validatedUser['email'],
            ]);
        } else {
            $user->mahasiswa()->create(
                $validatedMahasiswa + [
                    'nama'  => $validatedUser['name'],
                    'email' => $validatedUser['email'],
                ]
            );
        }

        // ✅ Refresh agar data terbaru langsung muncul
        auth()->user()->refresh();

        return redirect()->route('profile.show')->with('success', 'Data berhasil diperbarui!');
    }

    // ========================================================
    // 📘 HAPUS AKUN MAHASISWA LOGIN
    // ========================================================
    public function destroyProfile()
    {
        $user = Auth::user();

        if ($user->foto && Storage::disk('public')->exists(str_replace('storage/', '', $user->foto))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $user->foto));
        }

        if ($user->mahasiswa) {
            $user->mahasiswa->delete();
        }

        $user->delete();
        Auth::logout();

        return redirect('/login')->with('success', 'Akun Anda telah dihapus.');
    }

    // ========================================================
    // 🚫 NONAKTIFKAN CRUD DEFAULT
    // ========================================================
    public function edit($id)  { abort(403, 'Akses ditolak.'); }
    public function update(Request $r, $id) { abort(403, 'Akses ditolak.'); }
    public function destroy($id) { abort(403, 'Akses ditolak.'); }
}
