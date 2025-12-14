<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil berdasarkan role user yang login.
     */
    public function show()
    {
        $user = Auth::user()->fresh();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);

            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }

            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);

            if (view()->exists('profile.show_mahasiswa')) {
                return view('profile.show_mahasiswa', compact('user', 'mahasiswa', 'role'));
            }

            $dosen = $this->adaptMahasiswaToDosen($mahasiswa);
            if (!$dosen) {
                $dosen = (object) $this->getDefaultDosenProperties();
            }

            return view('profile.show', compact('user', 'dosen', 'role'));
        }

        $dosen = (object) $this->getDefaultDosenProperties();
        return view('profile.show', compact('user', 'role'))->with('dosen', $dosen);
    }

    /**
     * Form edit profil.
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

            return view('profile.edit', compact('user', 'dosen', 'role'));
        }

        $dosen = (object) $this->getDefaultDosenProperties();
        return view('profile.edit', compact('user', 'dosen', 'role'));
    }

    /**
     * Update profil.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        $rules = [
            'name' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',
            // ❗ PERUBAHAN DI SINI: Batasan ukuran dinaikkan ke 5MB (5120 KB) ❗
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', 
        ];

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

        DB::beginTransaction();
        try {

            if ($role === 'dosen') {
                $dosen = $this->findDosenFor($user->id, $user->email, true);

                $this->safeFill($dosen, [
                    'nama' => $validated['name'],
                    'nidn' => $request->input('nidn'),
                    'pendidikan_terakhir' => $request->input('pendidikan_terakhir'),
                    'status_ikatan_kerja' => $request->input('status_ikatan_kerja'),
                    'status_aktivitas' => $request->input('status_aktivitas'),
                    'nomor_hp' => $request->input('nomor_hp'),
                    'jenis_kelamin' => $request->input('jenis_kelamin'),
                    'sinta_id' => $request->input('sinta_id'),
                    'link_pddikti' => $request->input('link_pddikti'),
                ]);

                // ===== FOTO DOSEN LOGIC =====
                if ($request->hasFile('foto')) {

                    if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                        Storage::disk('public')->delete($dosen->foto);
                    }

                    $dosen->foto = $request->file('foto')
                        ->store('foto-dosen', 'public');
                }

                if (Schema::hasColumn($dosen->getTable(), 'user_id')) {
                    $dosen->user_id = $user->id;
                }
                if (Schema::hasColumn($dosen->getTable(), 'email')) {
                    $dosen->email = $user->email;
                }

                $dosen->save();
            }

            $user->update([
                'name' => $validated['name'],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui profil: ' . $e->getMessage()]);
        }

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui');
    }

    public function destroy()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? '');

        if ($role === 'dosen') {
            $dosen = $this->findDosenFor($user->id, $user->email);
            if ($dosen) {
                if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                    Storage::disk('public')->delete($dosen->foto);
                }
                $dosen->delete();
            }
        }

        if ($role === 'mahasiswa') {
            $mahasiswa = $this->findMahasiswaFor($user->id, $user->email);
            if ($mahasiswa) {
                 if (Schema::hasColumn($mahasiswa->getTable(), 'foto') && $mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                    Storage::disk('public')->delete($mahasiswa->foto);
                 }
                $mahasiswa->delete();
            }
        }

        Auth::logout();
        $user->delete();

        return redirect('/login')->with('success', 'Akun berhasil dihapus.');
    }

    /* ===================== HELPERS (MODIFIKASI KECIL) ===================== */

    private function findDosenFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $table = (new Dosen)->getTable();
        $q = Dosen::query();

        $q->where(function ($query) use ($table, $userId, $email) {
            if (Schema::hasColumn($table, 'user_id')) {
                $query->where('user_id', $userId);
                if (Schema::hasColumn($table, 'email')) {
                    $query->orWhere('email', $email);
                }
            } elseif (Schema::hasColumn($table, 'email')) {
                $query->where('email', $email);
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

    private function findMahasiswaFor(int $userId, string $email, bool $createIfMissing = false)
    {
        $q = Mahasiswa::query();
        $q->where(function ($query) use ($userId, $email) {
            $query->where('user_id', $userId)->orWhere('email', $email);
        });
        
        return $q->first();
    }

    private function adaptMahasiswaToDosen(?Mahasiswa $m)
    {
        if (!$m) return null;

        return (object) [
            'nama' => $m->nama,
            'email' => $m->email,
            'nomor_hp' => $m->nomor_hp,
            'nidn' => $m->nim, 
            'jenis_kelamin' => $m->jenis_kelamin,
            'pendidikan_terakhir' => null,
            'status_ikatan_kerja' => null,
            'status_aktivitas' => null,
            'foto' => $m->foto ?? null,
            'link_pddikti' => null,
            'sinta_id' => null,
        ];
    }

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

    private function safeFill($model, array $data)
    {
        if (!$model) return;

        $columns = Schema::getColumnListing($model->getTable());

        foreach ($data as $key => $value) {
            $value = ($value === '') ? null : $value;
            if (in_array($key, $columns)) {
                $model->{$key} = $value;
            }
        }
    }
}
