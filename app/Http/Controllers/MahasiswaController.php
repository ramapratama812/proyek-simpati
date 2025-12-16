<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MahasiswaController extends Controller
{
    // ========================================================
    // 📘 INDEX — Daftar Mahasiswa + Statistik Dashboard
    // ========================================================
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Query Data with Search
        $query = Mahasiswa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $mahasiswas = $query->orderBy('nama', 'asc')->paginate(10);

        // 2. Statistics Data (For Blue Header)
        $totalMahasiswa = Mahasiswa::count();
        $mahasiswaAktif = Mahasiswa::where('status_aktivitas', 'Aktif')->count();
        $mahasiswaCuti 	= Mahasiswa::where('status_aktivitas', 'Cuti')->count();

        return view('mahasiswa.index', compact(
            'mahasiswas',
            'totalMahasiswa',
            'mahasiswaAktif',
            'mahasiswaCuti'
        ));
    }

    // ========================================================
    // 📘 SHOW — Biodata Detail (Mahasiswa Lain)
    // ========================================================
    public function show($id)
    {
        // [FIX] Eager load 'projectMembers' dari relasi User untuk menampilkan riwayat kegiatan (mengatasi error RelationNotFoundException)
        $mahasiswa = Mahasiswa::with(['user.projectMembers.project'])->findOrFail($id);
        $user = $mahasiswa->user;

        // PDDikti URL
        $pddiktiUrl = "https://pddikti.kemdiktisaintek.go.id/search/mahasiswa/" . $mahasiswa->nim;

        // ❗ PENTING: Set ke false, agar tombol Edit/Hapus disembunyikan
        $isProfileView = false;

        return view('mahasiswa.show', compact('mahasiswa', 'user', 'pddiktiUrl', 'isProfileView'));
    }

    // ========================================================
    // 📘 CREATE — Add Form (Admin)
    // ========================================================
    public function create()
    {
        // Pastikan hanya admin yang bisa mengakses
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        return view('mahasiswa.create');
    }

    // ========================================================
    // 📘 STORE — Save New Data (Admin)
    // ========================================================
    public function store(Request $request)
    {
        // 1. Validate Complete Input
        $validated = $request->validate([
            'nama' 	 	 	 	 	 => 'required|string|max:255',
            'email' 	 	 	 	 => 'required|email|unique:users,email',
            'nim' 	 	 	 	 	 => 'required|string|unique:mahasiswa,nim',
            'password' 	 	 => 'required|string|min:6|confirmed',
            // Additional Data
            'jenis_kelamin' 	 => 'nullable|string|in:Laki-laki,Perempuan',
            'program_studi' 	 => 'nullable|string|max:100',
            'perguruan_tinggi' => 'nullable|string|max:150',
            'semester' 	 	 => 'nullable|integer',
            'status_aktivitas' => 'nullable|string',
        ]);

        // 2. Save Data with Transaction (User + Mahasiswa)
        DB::transaction(function () use ($validated) {

            // Create Login User
            $user = User::create([
                'name' 	 	 => $validated['nama'],
                'username' => $this->generateUniqueUsername($validated['nama']),
                'email' 	 => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' 	 	 => 'mahasiswa',
                'status' 	 => 'active',
            ]);

            // Create Mahasiswa Data
            Mahasiswa::create([
                'user_id' 	 	 	 	 => $user->id,
                'nama' 	 	 	 	 	 => $validated['nama'],
                'nim' 	 	 	 	 	 => $validated['nim'],
                'email' 	 	 	 	 => $validated['email'],
                'jenis_kelamin' 	 => $validated['jenis_kelamin'] ?? null,
                'program_studi' 	 => $validated['program_studi'] ?? null,
                'perguruan_tinggi' => $validated['perguruan_tinggi'] ?? null,
                'semester' 	 	 => $validated['semester'] ?? null,
                'status_aktivitas' => $validated['status_aktivitas'] ?? 'Aktif',
            ]);
        });

        return redirect()
            ->route('mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    // ========================================================
    // 📘 EDIT — Edit Form (Admin/Owner)
    // ========================================================
    public function edit($id)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);
        $user = Auth::user();

        // Check Access Rights
        if ($user->role !== 'admin' && $mahasiswa->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak berhak mengedit data ini.');
        }

        return view('mahasiswa.edit', compact('mahasiswa', 'user'));
    }

    // ========================================================
    // 📘 UPDATE — Save Changes (Admin)
    // ========================================================
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = $mahasiswa->user;

        // Check Access Rights
        if (Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        return $this->handleUpdate($request, $user, $mahasiswa, 'mahasiswa.show');
    }

    // ========================================================
    // 📘 PROFILE ROUTES (Untuk User yang Sedang Login)
    // ========================================================
    public function profile()
    {
        $user = Auth::user();
        // [FIX] Eager load 'projectMembers' dari relasi User untuk menampilkan riwayat kegiatan
        $mahasiswa = Mahasiswa::with(['user.projectMembers.project'])->where('user_id', $user->id)->first();

        // Jika belum ada data mahasiswa, inisialisasi objek kosong
        if (!$mahasiswa) {
             $mahasiswa = new Mahasiswa();
        }

        // PDDikti URL
        $pddiktiUrl = $mahasiswa->nim ? "https://pddikti.kemdiktisaintek.go.id/search/mahasiswa/" . $mahasiswa->nim : '#';

        // ❗ PENTING: Set ke true, agar tombol Edit/Hapus ditampilkan di view
        $isProfileView = true;

        return view('mahasiswa.show', compact('user', 'mahasiswa', 'pddiktiUrl', 'isProfileView'));
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

        // Jika mahasiswa belum ada, buat instance baru tapi jangan simpan dulu
        if (!$mahasiswa) {
            $mahasiswa = new Mahasiswa();
            $mahasiswa->user_id = $user->id;
            // Kita perlu simpan dulu agar punya ID untuk validasi unique, atau handle validasi khusus
            // Untuk simplifikasi, kita anggap create baru di handleUpdate
        }

        return $this->handleUpdate($request, $user, $mahasiswa, 'profile.show');
    }

    // ========================================================
    // 🛠️ HELPER: Centralized Update Logic (User + Mahasiswa)
    // ========================================================
    private function handleUpdate(Request $request, User $user, Mahasiswa $mahasiswa, $redirectRoute)
    {
        // 1. Validate User (Account)
        $validatedUser = $request->validate([
            'nama' 	 	 => 'required|string|max:255',
            'email' 	 => 'required|email|unique:users,email,' . $user->id,
            'foto' 	 	 => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        // 2. Validate Mahasiswa (Academic Data)
        // Handle unique validation for new record vs existing record
        $mahasiswaId = $mahasiswa->exists ? $mahasiswa->id : 'NULL';

        $validatedMahasiswa = $request->validate([
            'nim' 	 	 	 	 	 => 'required|string|max:50|unique:mahasiswa,nim,' . $mahasiswaId,
            'jenis_kelamin' 	 => 'nullable|string|in:Laki-laki,Perempuan',
            'program_studi' 	 => 'nullable|string|max:100',
            'perguruan_tinggi' => 'nullable|string|max:150',
            'semester' 	 	 => 'nullable|integer',
            'status_aktivitas' => 'nullable|string',
        ]);

        // 3. Process Photo Upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                $oldPath = $user->foto;
                // Check if path contains 'storage/' prefix and remove it for checking existence
                $relativePath = str_replace('storage/', '', $oldPath);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
            // Simpan foto baru
            $path = $request->file('foto')->store('fotos', 'public');
            $validatedUser['foto'] = 'storage/' . $path; // Ensure 'storage/' prefix is added
        } else if ($request->exists('delete_foto') && $request->input('delete_foto') == 1) {
            // Handle hapus foto (opsional)
            if ($user->foto) {
                $oldPath = $user->foto;
                $relativePath = str_replace('storage/', '', $oldPath);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
                $validatedUser['foto'] = null;
            }
        } else {
             // Pastikan field foto tidak dimasukkan ke update jika tidak ada perubahan
             unset($validatedUser['foto']);
        }


        // 4. Process Password
        if ($request->filled('password')) {
            $validatedUser['password'] = Hash::make($request->password);
        } else {
            unset($validatedUser['password']);
        }

        // 5. Mapping form name -> user database name
        $validatedUser['name'] = $validatedUser['nama'];
        unset($validatedUser['nama']);


        // 6. Save to Database
        DB::transaction(function () use ($user, $mahasiswa, $validatedUser, $validatedMahasiswa) {
            // Update User
            $user->update($validatedUser);

            // Update or Create Mahasiswa
            if ($mahasiswa->exists) {
                $mahasiswa->update($validatedMahasiswa + [
                    'nama' 	=> $user->name,
                    'email' => $user->email,
                ]);
            } else {
                // Create new Mahasiswa record linked to user
                $user->mahasiswa()->create($validatedMahasiswa + [
                    'nama' 	=> $user->name,
                    'email' => $user->email,
                ]);
                // Refresh mahasiswa instance to get the new ID if needed (though we redirect anyway)
            }
        });

        // 7. Redirect to the correct show page
        if ($redirectRoute === 'mahasiswa.show') {
            // If we just created the mahasiswa, we need to fetch it again or use the relation
            $targetId = $mahasiswa->exists ? $mahasiswa->id : $user->mahasiswa->id;
            return redirect()->route($redirectRoute, $targetId)->with('success', 'Data profil berhasil diperbarui!');
        } else {
             return redirect()->route($redirectRoute)->with('success', 'Data profil berhasil diperbarui!');
        }
    }

    // ========================================================
    // 🛠️ HELPER: Generate Unique Username
    // ========================================================
    private function generateUniqueUsername($name)
    {
        $baseUsername = strtolower(str_replace(' ', '', $name));
        $username = $baseUsername;
        $count = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $count;
            $count++;
        }
        return $username;
    }


    // ========================================================
    // ❌ DESTROY — Delete Data (Admin)
    // ========================================================
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh menghapus.');
        }

        $mahasiswa = Mahasiswa::findOrFail($id);
        $user = $mahasiswa->user;

        DB::transaction(function () use ($user, $mahasiswa) {
            if ($user) {
                // Delete Photo
                if ($user->foto) {
                    $photoPath = str_replace('storage/', '', $user->foto);
                    if (Storage::disk('public')->exists($photoPath)) {
                        Storage::disk('public')->delete($photoPath);
                    }
                }
                $user->delete(); // Cascade delete will remove mahasiswa data
            } else {
                $mahasiswa->delete(); // If user is null (orphaned data)
            }
        });

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    // ========================================================
    // ❌ DESTROY PROFILE — Delete Own Account
    // ========================================================
    public function destroyProfile()
    {
        $user = Auth::user();

        DB::transaction(function () use ($user) {
            // Delete Photo
            if ($user->foto) {
                $photoPath = str_replace('storage/', '', $user->foto);
                if (Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
            }
            if ($user->mahasiswa) {
                $user->mahasiswa->delete();
            }
            $user->delete();
        });

        Auth::logout();
        return redirect('/login')->with('success', 'Akun Anda telah dihapus.');
    }
}
