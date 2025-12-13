<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil berdasarkan role user yang login.
     */
    public function show()
    {
        // Pastikan mengambil data user terbaru
        $user = Auth::user()->fresh();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);

            // Jika tidak ditemukan, berikan object default agar view aman
            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }

            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);

            // Jika ada view khusus mahasiswa gunakan itu
            if (view()->exists('profile.show_mahasiswa')) {
                return view('profile.show_mahasiswa', compact('user', 'mahasiswa', 'role'));
            }

            // Jika tidak, adaptasi mahasiswa agar view profil dosen tetap bisa dipakai
            $dosen = $this->adaptMahasiswaToDosen($mahasiswa);
            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }

            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        // Fallback: tampilkan view dengan dosen default agar tidak error
        $dosen = (object) $this->getDefaultDosenProperties();
        return view('profile.show', compact('user', 'role'))->with('dosen', $dosen);
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
            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }
            return view('profile.edit', compact('user', 'dosen', 'role'));
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);
            if (view()->exists('profile.edit_mahasiswa')) {
                return view('profile.edit_mahasiswa', compact('user', 'mahasiswa', 'role'));
            }

            $dosen = $this->adaptMahasiswaToDosen($mahasiswa);
            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }

            return view('profile.edit', [
                'user' => $user,
                'dosen' => $dosen,
                'role' => $role
            ]);
        }

        $dosen = (object) $this->getDefaultDosenProperties();
        return view('profile.edit', ['user' => $user, 'dosen' => $dosen, 'role' => $role]);
    }

    /**
     * Update profil berdasarkan role.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        // Validasi umum
        $rules = [
            'name' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',
        ];

        // Validasi khusus dosen
        if ($role === 'dosen') {
            $rules['nidn'] = 'nullable|string|max:50';
            $rules['pendidikan_terakhir'] = ['nullable', Rule::in(['S1', 'S2', 'S3'])];
            $rules['status_ikatan_kerja'] = ['nullable', Rule::in(['Dosen Tetap', 'Dosen Tidak Tetap'])];
            $rules['status_aktivitas'] = ['nullable', Rule::in(['Aktif', 'Tidak Aktif', 'Cuti'])];
            $rules['jenis_kelamin'] = ['nullable', Rule::in(['Laki-laki', 'Perempuan'])];
            $rules['sinta_id'] = 'nullable|string|max:50';
            $rules['link_pddikti'] = 'nullable|url|max:255';
        }

        $validated = $request->validate($rules);

        // Persiapkan data untuk update users table
        $userData = [
            'name' => $validated['name'],
        ];

        // Gunakan transaction agar update konsisten
        DB::beginTransaction();
        try {
            // ====================== UNTUK DOSEN ======================
            if ($role === 'dosen') {
                // Temukan atau buat instance (tapi belum disimpan jika baru)
                $dosen = $this->findDosenFor($user->id, $user->email, true);

                // Isi field yang ada di DB secara aman
                $this->safeFill($dosen, [
                    'nama'                  => $validated['name'],
                    'nidn'                  => $request->input('nidn'),
                    'pendidikan_terakhir'   => $request->input('pendidikan_terakhir'),
                    'status_ikatan_kerja'   => $request->input('status_ikatan_kerja'),
                    'status_aktivitas'      => $request->input('status_aktivitas'),
                    'nomor_hp'              => $request->input('nomor_hp') ?? $request->input('no_hp'),
                    'jenis_kelamin'         => $request->input('jenis_kelamin'),
                    'sinta_id'              => $request->input('sinta_id'),
                    'link_pddikti'          => $request->input('link_pddikti'),
                ]);

                // Pastikan kolom email dan user_id kalau ada di tabel
                if (Schema::hasColumn($dosen->getTable(), 'email')) {
                    $dosen->email = $user->email;
                }
                if (Schema::hasColumn($dosen->getTable(), 'user_id')) {
                    $dosen->user_id = $user->id;
                }

                // Simpan (insert/update)
                $dosen->save();

                // Jika tabel users memiliki kolom sinta_id, update sinkronnya
                if (Schema::hasColumn($user->getTable(), 'sinta_id')) {
                    $userData['sinta_id'] = $request->input('sinta_id');
                }
            }

            // ====================== UNTUK MAHASISWA ======================
            if ($role === 'mahasiswa') {
                $mahasiswa = $this->findMahasiswaFor($user->id, $user->email, true);

                $this->safeFill($mahasiswa, [
                    'nama'              => $validated['name'],
                    'nim'               => $request->input('nim'),
                    'jenis_kelamin'     => $request->input('jenis_kelamin'),
                    'semester'          => $request->input('semester'),
                    'status_aktivitas'  => $request->input('status_aktivitas'),
                    'nomor_hp'          => $request->input('nomor_hp') ?? $request->input('no_hp'),
                ]);

                if (Schema::hasColumn($mahasiswa->getTable(), 'user_id')) {
                    $mahasiswa->user_id = $user->id;
                }
                if (Schema::hasColumn($mahasiswa->getTable(), 'email')) {
                    $mahasiswa->email = $user->email;
                }

                $mahasiswa->save();
            }

            // ====================== UPDATE USERS ======================
            // Update user hanya sekali
            $user->update($userData);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // Jika perlu debugging sementara, bisa log error:
            // \Log::error('Profile update failed: '.$e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal memperbarui profil.']);
        }

        // Redirect ke show (metode show akan mengambil fresh user & dosen)
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

    /**
     * Temukan Dosen secara robust:
     * - Jika ada kolom user_id, cari berdasarkan user_id terlebih dahulu
     * - Jika tidak ditemukan, cari berdasarkan email
     * - Saat $createIfMissing = true, buat instance baru (belum disimpan) dan isi email/user_id jika kolom tersedia
     */
    private function findDosenFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $table = (new Dosen)->getTable();
        $q = Dosen::query();

        // Gunakan grouping agar orWhere tidak memecah kondisi lain di query global
        $q->where(function ($query) use ($table, $userId, $email) {
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $userId);
                // Jika juga ada kolom email, kita coba fallback ke email (untuk kompatibilitas)
                if (Schema::hasColumn($table, 'email')) {
                    $query->orWhere('email', $email);
                }
            } else {
                // Jika tidak ada kolom user_id, pakai email saja (jika tersedia)
                if (Schema::hasColumn($table, 'email')) {
                    $query->where('email', $email);
                } else {
                    // Jika tabel dosen tidak memiliki kolom user_id maupun email, kembalikan query kosong
                    $query->whereRaw('1 = 0');
                }
            }
        });

        $row = $q->first();

        if (!$row && $createIfMissing) {
            $row = new Dosen();
            if (Schema::hasColumn($table, 'user_id')) $row->user_id = $userId;
            if (Schema::hasColumn($table, 'email')) $row->email = $email;
        }

        return $row;
    }

    /**
     * Temukan Mahasiswa dengan logika serupa findDosenFor
     */
    private function findMahasiswaFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $table = (new Mahasiswa)->getTable();
        $q = Mahasiswa::query();

        $q->where(function ($query) use ($table, $userId, $email) {
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $userId);
                if (Schema::hasColumn($table, 'email')) {
                    $query->orWhere('email', $email);
                }
            } else {
                if (Schema::hasColumn($table, 'email')) {
                    $query->where('email', $email);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        });

        $row = $q->first();

        if (!$row && $createIfMissing) {
            $row = new Mahasiswa();
            if (Schema::hasColumn($table, 'user_id')) $row->user_id = $userId;
            if (Schema::hasColumn($table, 'email')) $row->email = $email;
        }

        return $row;
    }

    /**
     * Adaptasi object Mahasiswa menjadi struktur yang mirip Dosen (untuk view reuse)
     */
    private function adaptMahasiswaToDosen(?Mahasiswa $m)
    {
        if (!$m) return null;
        return (object) [
            'nama' => $m->nama,
            'email' => $m->email,
            'nomor_hp' => $m->nomor_hp ?? $m->no_hp,
            'nidn' => $m->nim,
            'jenis_kelamin' => $m->jenis_kelamin,
            'pendidikan_terakhir' => $m->jenjang_pendidikan ?? null,
            'status_ikatan_kerja' => null,
            'status_aktivitas' => $m->status_aktivitas ?? null,
            'foto' => $m->foto ?? $m->photo ?? null,
            // pastikan property ada agar view aman
            'link_pddikti' => null,
            'sinta_id' => null,
        ];
    }

    /**
     * Default properties agar view tidak error ketika data tidak ada
     */
    private function getDefaultDosenProperties()
    {
        return [
            'nama' => null,
            'email' => null,
            'nomor_hp' => null,
            'nidn' => null,
            'jenis_kelamin' => null,
            'pendidikan_terakhir' => null,
            'status_ikatan_kerja' => null,
            'status_aktivitas' => null,
            'foto' => null,
            'link_pddikti' => null,
            'sinta_id' => null,
        ];
    }

    /**
     * Safe fill: hanya set attribute jika kolom ada di DB.
     * Mengonversi string kosong ('') menjadi null sehingga kolom dapat dikosongkan oleh user.
     */
    private function safeFill($model, array $data)
    {
        if (!$model) return;

        $columns = Schema::getColumnListing($model->getTable());

        foreach ($data as $key => $value) {
            // normalisasi: ubah empty string jadi null
            $value = ($value === '') ? null : $value;

            if (in_array($key, $columns)) {
                $model->{$key} = $value;
            }
        }
    }
}
