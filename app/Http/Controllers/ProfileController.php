<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil berdasarkan role user yang login.
     */
    public function show()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);
            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);
            if (view()->exists('profile.show_mahasiswa')) {
                return view('profile.show_mahasiswa', compact('user', 'mahasiswa', 'role'));
            }
            $dosen = $this->adaptMahasiswaToDosen($mahasiswa);
            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        return view('profile.show', compact('user', 'role'))->with('dosen', null);
    }

    /**
     * Form edit profil berdasarkan role.
     */
    public function edit()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);
            return view('profile.edit', compact('user', 'dosen', 'role'));
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);
            if (view()->exists('profile.edit_mahasiswa')) {
                return view('profile.edit_mahasiswa', compact('user', 'mahasiswa', 'role'));
            }

            return view('profile.edit', [
                'user' => $user,
                'dosen' => $this->adaptMahasiswaToDosen($mahasiswa),
                'role' => $role
            ]);
        }

        return view('profile.edit', ['user' => $user, 'dosen' => null, 'role' => $role]);
    }

    /**
     * Update profil berdasarkan role.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        // 🔹 Validasi umum untuk semua role
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // update data user (tabel users)
        $user->update(['name' => $request->input('name')]);

        /**
         * ====================== UNTUK DOSEN ======================
         */
        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email, true);

            // 🔹 Pastikan semua kolom yang sesuai dengan tabel dosens saja
            $this->safeFill($dosen, [
                'nama' => $request->name,
                'nidn' => $request->nidn,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'status_ikatan_kerja' => $request->status_ikatan_kerja,
                'status_aktivitas' => $request->status_aktivitas, // ✅ enum: Aktif / Tidak Aktif / Cuti
                'nomor_hp' => $request->nomor_hp ?? $request->no_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
            ]);

            if (Schema::hasColumn($dosen->getTable(), 'email')) {
                $dosen->email = $user->email;
            }
            if (Schema::hasColumn($dosen->getTable(), 'user_id')) {
                $dosen->user_id = $user->id;
            }

            $dosen->save();
        }

        /**
         * ====================== UNTUK MAHASISWA ======================
         */
        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email, true);
            $this->safeFill($mahasiswa, [
                'nama' => $request->name,
                'nim' => $request->nim,
                'jenjang_pendidikan' => $request->jenjang_pendidikan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'semester' => $request->semester,
                'status_aktivitas' => $request->status_aktivitas, // ✅ enum sama
                'nomor_hp' => $request->nomor_hp ?? $request->no_hp,
            ]);

            if (Schema::hasColumn($mahasiswa->getTable(), 'user_id')) {
                $mahasiswa->user_id = $user->id;
            }
            if (Schema::hasColumn($mahasiswa->getTable(), 'email')) {
                $mahasiswa->email = $user->email;
            }

            $mahasiswa->save();
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Hapus akun user & relasi.
     */
    public function destroy()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);
            if ($dosen) $dosen->delete();
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);
            if ($mahasiswa) $mahasiswa->delete();
        }

        Auth::logout();
        $user->delete();

        return redirect('/login')->with('success', 'Akun berhasil dihapus.');
    }

    /* ===================== Helpers ===================== */

    private function findDosenFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $table = (new Dosen)->getTable();
        $q = Dosen::query();

        if (Schema::hasColumn($table, 'user_id')) $q->where('user_id', $userId);
        if (Schema::hasColumn($table, 'email')) $q->orWhere('email', $email);

        $row = $q->first();
        if (!$row && $createIfMissing) {
            $row = new Dosen();
            if (Schema::hasColumn($table, 'user_id')) $row->user_id = $userId;
            if (Schema::hasColumn($table, 'email')) $row->email = $email;
        }

        return $row;
    }

    private function findMahasiswaFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $table = (new Mahasiswa)->getTable();
        $q = Mahasiswa::query();

        if (Schema::hasColumn($table, 'user_id')) $q->where('user_id', $userId);
        if (Schema::hasColumn($table, 'email')) $q->orWhere('email', $email);

        $row = $q->first();
        if (!$row && $createIfMissing) {
            $row = new Mahasiswa();
            if (Schema::hasColumn($table, 'user_id')) $row->user_id = $userId;
            if (Schema::hasColumn($table, 'email')) $row->email = $email;
        }

        return $row;
    }

    private function adaptMahasiswaToDosen(?Mahasiswa $m)
    {
        if (!$m) return null;
        return (object) [
            'nama' => $m->nama,
            'email' => $m->email,
            'nomor_hp' => $m->nomor_hp ?? $m->no_hp,
            'nidn' => $m->nim,
            'jenis_kelamin' => $m->jenis_kelamin,
            'pendidikan_terakhir' => $m->jenjang_pendidikan,
            'status_ikatan_kerja' => null,
            'status_aktivitas' => $m->status_aktivitas,
            'foto' => $m->foto ?? $m->photo,
        ];
    }

    private function safeFill($model, array $data)
    {
        $columns = Schema::getColumnListing($model->getTable());
        foreach ($data as $key => $value) {
            if ($value === null) continue;
            if (in_array($key, $columns)) {
                $model->{$key} = $value;
            }
        }
    }
}
