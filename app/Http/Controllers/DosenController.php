<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    /**
     * 🔹 Menampilkan daftar dosen
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sort   = strtolower($request->input('sort', 'asc'));

        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';
        }

        $query = Dosen::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%");

                if (Schema::hasColumn('dosens', 'nip')) {
                    $q->orWhere('nip', 'like', "%{$search}%");
                }

                if (Schema::hasColumn('dosens', 'email')) {
                    $q->orWhere('email', 'like', "%{$search}%");
                }
            });
        }

        if ($status && $status !== 'Semua Status' && Schema::hasColumn('dosens', 'status_aktivitas')) {
            $query->where('status_aktivitas', $status);
        }

        $query->orderBy('nama', $sort);

        if (Schema::hasColumn('dosens', 'status_aktivitas')) {
            $totalDosenAktif = Dosen::where('status_aktivitas', 'Aktif')->count();
            $totalDosenCuti  = Dosen::where('status_aktivitas', 'Cuti')->count();
        } else {
            $totalDosenAktif = 0;
            $totalDosenCuti  = 0;
        }

        $dosens = $query->paginate(10);

        return view('dosen.index', compact(
            'dosens',
            'search',
            'status',
            'sort',
            'totalDosenAktif',
            'totalDosenCuti'
        ));
    }

    /**
     * 🔹 DETAIL DOSEN
     */
    public function show($id)
    {
        // Tetap menggunakan Eager Loading yang sudah ada
        $dosen = Dosen::with([
            'kegiatanDiketuai' => function ($q) {
                $q->where('validation_status', 'approved');
            },
            'anggotaProyek' => function ($q) {
                $q->whereHas('project', function ($p) {
                    $p->where('validation_status', 'approved');
                });
            },
            'anggotaProyek.project' => function ($q) {
                $q->where('validation_status', 'approved');
            },
            'publikasi'
        ])->findOrFail($id);

        return view('dosen.show', compact('dosen'));
    }

    public function create()
    {
        return view('dosen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:dosens,email',
            'nidn' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:20',
            'status_ikatan_kerja' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_aktivitas' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (empty($validated['status_aktivitas'])) {
            $validated['status_aktivitas'] = 'Aktif';
        }

        // ===== TAMBAHAN FOTO =====
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')
                ->store('foto-dosen', 'public');
        }

        $columns = Schema::getColumnListing('dosens');
        $data = array_filter($validated, fn ($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);

        Dosen::create($data);

        return redirect()->route('dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('dosen.edit', compact('dosen'));
    }

    public function update(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:dosens,email,' . $id,
            'nidn' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:20',
            'status_ikatan_kerja' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_aktivitas' => 'nullable|string|max:20',
            'link_pddikti' => 'nullable|url|max:255',
            // Validasi input form: sinta_id
            'sinta_id' => 'nullable|string|max:50', 
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (empty($validated['status_aktivitas'])) {
            $validated['status_aktivitas'] = $dosen->status_aktivitas ?? 'Aktif';
        }
        
        // --- START PERBAIKAN PENTING UNTUK ID SINTA ---
        
        // Asumsi Kuat: Nama kolom di database Anda adalah 'id_sinta',
        // karena ini sesuai dengan pemanggilan yang Anda lakukan di view ($dosen->id_sinta).
        // Kita petakan input 'sinta_id' ke kolom 'id_sinta'.
        
        if (isset($validated['sinta_id']) && Schema::hasColumn('dosens', 'id_sinta')) {
            // Pemetaan jika kolom di DB adalah 'id_sinta'
            $validated['id_sinta'] = $validated['sinta_id'];
        }
        
        // Jika kolom di DB BUKAN 'id_sinta' tapi 'sinta_id', maka tidak perlu pemetaan,
        // karena $validated sudah memiliki 'sinta_id'. Kita hanya perlu memastikan 
        // variabel 'sinta_id' dihapus agar tidak bentrok jika database menggunakan 'id_sinta'.
        
        // Pastikan 'sinta_id' dari form dihapus, kecuali jika memang nama kolomnya 'sinta_id'.
        // Kita akan biarkan filter kolom di bawah yang mengurus, tetapi
        // demi keamanan, kita hanya unset jika 'id_sinta' berhasil dipetakan.
        if (isset($validated['id_sinta'])) {
            unset($validated['sinta_id']);
        }

        // --- END PERBAIKAN ---

        $columns = Schema::getColumnListing('dosens');
        // Filter array validated agar hanya menyisakan key yang ada di tabel dosens (termasuk 'id_sinta' atau 'sinta_id')
        $data = array_filter($validated, fn ($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);

        // ===== TAMBAHAN FOTO =====
        if ($request->hasFile('foto')) {

            if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                Storage::disk('public')->delete($dosen->foto);
            }

            $data['foto'] = $request->file('foto')
                ->store('foto-dosen', 'public');
        }

        $dosen->update($data);

        return redirect()->route('dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);

        if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
            Storage::disk('public')->delete($dosen->foto);
        }

        $dosen->delete();

        return redirect()->route('dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
