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

        // Semua mahasiswa dapat melihat seluruh data mahasiswa lain
        $mahasiswas = Mahasiswa::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('program_studi', 'like', "%{$search}%")
                      ->orWhere('perguruan_tinggi', 'like', "%{$search}%");
            })
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('mahasiswa.index', compact('mahasiswas', 'search'));
    }

    // ========================================================
    // 📘 SHOW — detail profil mahasiswa
    // ========================================================
    public function show($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = Auth::user();

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

        $user = User::create([
            'name'     => $validated['nama'],
            'username' => strtolower(str_replace(' ', '', $validated['nama'])),
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nama'             => $validated['nama'],
            'nim'              => $validated['nim'],
            'email'            => $validated['email'],
            'jenis_kelamin'    => $request->jenis_kelamin,
            'program_studi'    => $request->program_studi,
            'perguruan_tinggi' => $request->perguruan_tinggi,
            'status_terakhir'  => $request->status_aktivitas,
            'semester'         => $request->semester,
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

        $validatedUser = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        $validatedMahasiswa = $request->validate([
            'nim'              => 'required|string|max:50|unique:mahasiswas,nim,' . ($mahasiswa?->id ?? 'NULL'),
            'jenis_kelamin'    => 'nullable|string|max:20',
            'program_studi'    => 'nullable|string|max:100',
            'perguruan_tinggi' => 'nullable|string|max:150',
            'semester'         => 'nullable|string|max:10',
            'status_terakhir'  => 'nullable|string|max:100',
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
    // 📘 EDIT & UPDATE MAHASISWA (untuk admin/pemilik akun)
    // ========================================================
    public function edit($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        // Hanya pemilik akun atau admin yang boleh edit
        if (auth()->user()->id !== $mahasiswa->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }

        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'email'             => 'required|email',
            'nim'               => 'required|string|max:20',
            'program_studi'     => 'required|string|max:100',
            'perguruan_tinggi'  => 'required|string|max:255',
            'jenis_kelamin'     => 'required|string',
            'semester'          => 'required|string|max:10',
            'status_aktivitas'  => 'required|string|max:100',
        ]);

        $mahasiswa->update([
            'nim'              => $validated['nim'],
            'nama'             => $validated['nama'],
            'perguruan_tinggi' => $validated['perguruan_tinggi'],
            'program_studi'    => $validated['program_studi'],
            'jenis_kelamin'    => $validated['jenis_kelamin'],
            'semester'         => $validated['semester'],
            'status_aktivitas' => $validated['status_aktivitas'],
        ]);

        if ($mahasiswa->user) {
            $mahasiswa->user->update([
                'name'  => $validated['nama'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('mahasiswa.show', $mahasiswa->id)
                         ->with('success', 'Profil berhasil diperbarui!');
    }
}
