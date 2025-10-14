<?php

// 🔹🔹🔹 Backup CRUD Profil Dosen + User 🔹🔹🔹

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;

class ProfileController extends Controller
{
    /**
     * 🔹 Menampilkan profil pengguna (dosen yang login)
     */
    public function show()
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();

        return view('profile.show', compact('user', 'dosen'));
    }

    /**
     * 🔹 Menampilkan halaman edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();

        return view('profile.edit', compact('user', 'dosen'));
    }

    /**
     * 🔹 Memperbarui profil (User + Dosen)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nidn' => 'nullable|string|max:20',
            'perguruan_tinggi' => 'nullable|string|max:100',
            'status_ikatan_kerja' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|string|max:20',
            'program_studi' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_aktivitas' => 'nullable|string|max:50',
            'nomor_hp' => 'nullable|string|max:20',
        ]);

        // ✅ Update tabel users (nama saja)
        $user->update([
            'name' => $validated['name']
        ]);

        // ✅ Update / buat data di tabel dosen
        $dosen = Dosen::firstOrNew(['email' => $user->email]);

        // Isi semua field dengan nilai validasi (jangan overwrite yang kosong)
        $dosen->nama = $validated['name'];
        $dosen->nidn = $validated['nidn'] ?? $dosen->nidn;
        $dosen->perguruan_tinggi = $validated['perguruan_tinggi'] ?? $dosen->perguruan_tinggi;
        $dosen->status_ikatan_kerja = $validated['status_ikatan_kerja'] ?? $dosen->status_ikatan_kerja;
        $dosen->jenis_kelamin = $validated['jenis_kelamin'] ?? $dosen->jenis_kelamin;
        $dosen->program_studi = $validated['program_studi'] ?? $dosen->program_studi;
        $dosen->pendidikan_terakhir = $validated['pendidikan_terakhir'] ?? $dosen->pendidikan_terakhir;
        $dosen->status_aktivitas = $validated['status_aktivitas'] ?? $dosen->status_aktivitas;
        $dosen->nomor_hp = $validated['nomor_hp'] ?? $dosen->nomor_hp; // 🔥 ini penting

        // Email wajib ikut disimpan (buat relasi)
        $dosen->email = $user->email;
        $dosen->save();


        // Edit Mahasiswa
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

    /**
     * 🔹 Menghapus akun pengguna
     */
    public function destroy()
    {
        $user = Auth::user();
        $dosen = Dosen::where('email', $user->email)->first();

        if ($dosen) {
            $dosen->delete();
        }

        $user->delete();
        Auth::logout();

        return redirect('/login')->with('success', 'Akun berhasil dihapus.');
    }
}



// 🔹🔹🔹 Backup CRUD Profil Mahasiswa + User 🔹🔹🔹

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;

// class ProfileController extends Controller
// {
//     // ✅ Tampilkan profil + form edit
//     public function show()
//     {
//         $user = Auth::user();
//         $mahasiswa = $user->mahasiswa; // relasi dari model User

//         return view('profile.show', compact('user', 'mahasiswa'));
//     }

//     // ✅ Update data profil & mahasiswa
//     public function update(Request $request)
//     {
//         $user = Auth::user();
//         $mahasiswa = $user->mahasiswa;

//         // ✅ Validasi input
//         $validated = $request->validate([
//             'nama' => 'required|string|max:255',
//             'email' => 'required|email|max:255|unique:users,email,' . $user->id,
//             'nim' => 'nullable|string|max:50',
//             'program_studi' => 'nullable|string|max:255',
//             'perguruan_tinggi' => 'nullable|string|max:255',
//             'jenjang_pendidikan' => 'nullable|string|max:50',
//             'jenis_kelamin' => 'nullable|string|max:20',
//             'semester' => 'nullable|integer|min:1',
//             'status_aktivitas' => 'nullable|string|max:50',
//             'password' => 'nullable|min:8|confirmed',
//         ]);

//         // ✅ Update data user
//         $user->name = $validated['nama'];
//         $user->email = $validated['email'];

//         if (!empty($validated['password'])) {
//             $user->password = Hash::make($validated['password']);
//         }

//         $user->save();

//         // ✅ Pastikan relasi mahasiswa ada (jika belum, buat baru)
//         if (!$mahasiswa) {
//             $mahasiswa = $user->mahasiswa()->create([]);
//         }

//         // ✅ Update data mahasiswa
//         $mahasiswa->update([
//             'nama' => $validated['nama'],
//             'nim' => $validated['nim'] ?? $mahasiswa->nim,
//             'program_studi' => $validated['program_studi'] ?? $mahasiswa->program_studi,
//             'perguruan_tinggi' => $validated['perguruan_tinggi'] ?? $mahasiswa->perguruan_tinggi,
//             'jenjang_pendidikan' => $validated['jenjang_pendidikan'] ?? $mahasiswa->jenjang_pendidikan,
//             'jenis_kelamin' => $validated['jenis_kelamin'] ?? $mahasiswa->jenis_kelamin,
//             'semester' => $validated['semester'] ?? $mahasiswa->semester,
//             'status_aktivitas' => $validated['status_aktivitas'] ?? $mahasiswa->status_aktivitas,
//         ]);

//         return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
//     }

//     // ✅ Hapus akun dan data mahasiswa
//     public function destroy()
//     {
//         $user = Auth::user();

//         if ($user->mahasiswa) {
//             $user->mahasiswa->delete();
//         }

//         $user->delete();
//         Auth::logout();

//         return redirect('/login')->with('success', 'Akun Anda telah dihapus.');
//     }
// }
