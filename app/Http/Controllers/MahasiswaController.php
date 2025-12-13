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
    // 📘 SHOW — Biodata Mahasiswa + Kegiatan (project_members)
    // ========================================================
    public function show($id)
    {
        $mahasiswa = Mahasiswa::with([
            'user.projectMembers.project'
        ])->findOrFail($id);

        // user pemilik mahasiswa (BUKAN Auth::user())
        $user = $mahasiswa->user;

        $pddiktiUrl = "https://pddikti.kemdikbud.go.id/data_mahasiswa/" . $mahasiswa->nim;

        return view('mahasiswa.show', compact('mahasiswa', 'user', 'pddiktiUrl'));
    }

    // ========================================================
    // 📘 CREATE — khusus admin
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
            'nim'      => 'required|string|unique:mahasiswa,nim',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['nama'],
            'username' => strtolower(str_replace(' ', '', $validated['nama'])),
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nama'    => $validated['nama'],
            'nim'     => $validated['nim'],
            'email'   => $validated['email'],
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    // ========================================================
    // 📘 PROFIL MAHASISWA LOGIN
    // ========================================================
    public function profile()
    {
        $user = Auth::user();

        $mahasiswa = Mahasiswa::with([
            'user.projectMembers.project'
        ])->where('user_id', $user->id)
          ->firstOrFail();

        $pddiktiUrl = "https://pddikti.kemdikbud.go.id/data_mahasiswa/" . $mahasiswa->nim;

        return view('mahasiswa.show', compact('user', 'mahasiswa', 'pddiktiUrl'));
    }

    // ========================================================
    // 📘 EDIT PROFILE
    // ========================================================
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

        $validatedUser = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        $validatedMahasiswa = $request->validate([
            'nim'              => 'required|string|max:50|unique:mahasiswa,nim,' . $mahasiswa->id,
            'jenis_kelamin'    => 'nullable|string|max:20',
            'program_studi'    => 'nullable|string|max:100',
            'perguruan_tinggi' => 'nullable|string|max:150',
            'semester'         => 'nullable|string|max:10',
            'status_aktivitas' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists(str_replace('storage/', '', $user->foto))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $user->foto));
            }

            $path = $request->file('foto')->store('fotos', 'public');
            $validatedUser['foto'] = 'storage/' . $path;
        }

        if ($request->filled('password')) {
            $validatedUser['password'] = bcrypt($request->password);
        } else {
            unset($validatedUser['password']);
        }

        $user->update($validatedUser);

        $mahasiswa->update($validatedMahasiswa + [
            'nama'  => $validatedUser['name'],
            'email' => $validatedUser['email'],
        ]);

        $user->refresh();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Data berhasil diperbarui!');
    }

    // ========================================================
    // ❌ DELETE PROFILE
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
    // 🚫 DISABLE CRUD DEFAULT
    // ========================================================
    public function edit($id) { abort(403, 'Akses ditolak.'); }
    public function update(Request $r, $id) { abort(403, 'Akses ditolak.'); }
    public function destroy($id) { abort(403, 'Akses ditolak.'); }
}
