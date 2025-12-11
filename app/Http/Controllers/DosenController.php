<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DosenController extends Controller
{
    /**
     * 🔹 Menampilkan daftar dosen (dengan pencarian, filter, dan urutan)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sort = strtolower($request->input('sort', 'asc')); // default asc

        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';
        }

        $query = Dosen::query();

        // 🔍 Pencarian nama / NIDN / NIP / email
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

        // 🔹 Filter berdasarkan status aktivitas
        if ($status && $status !== 'Semua Status' && Schema::hasColumn('dosens', 'status_aktivitas')) {
            $query->where('status_aktivitas', $status);
        }

        // 🔹 Urutkan berdasarkan nama
        $query->orderBy('nama', $sort);

        // 🔹 Ambil hasil
        $dosens = $query->paginate(10);

        return view('dosen.index', compact('dosens', 'search', 'status', 'sort'));
    }

    /**
     * 🔹 Menampilkan detail dosen
     */
    public function show($id)
    {
        // Eager load relasi yang diperlukan
        $dosen = Dosen::with([
            'kegiatanDiketuai',
            'anggotaProyek.project',
            'publikasi'
        ])->findOrFail($id);

        return view('dosen.show', compact('dosen'));
    }

    /**
     * 🔹 Form tambah dosen
     */
    public function create()
    {
        return view('dosen.create');
    }

    /**
     * 🔹 Simpan data dosen baru
     */
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
        ]);

        // 🔸 Pastikan kolom status_aktivitas tidak null
        if (empty($validated['status_aktivitas'])) {
            $validated['status_aktivitas'] = 'Aktif';
        }

        // 🔸 Filter hanya kolom yang ada di tabel
        $columns = Schema::getColumnListing('dosens');
        $data = array_filter($validated, fn($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);

        // 🔹 Simpan ke database
        Dosen::create($data);

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    /**
     * 🔹 Form edit dosen
     */
    public function edit($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('dosen.edit', compact('dosen'));
    }

    /**
     * 🔹 Update data dosen
     */
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
            'sinta_id' => 'nullable|string|max:50',
        ]);

        // Default kalau tidak diisi
        if (empty($validated['status_aktivitas'])) {
            $validated['status_aktivitas'] = $dosen->status_aktivitas ?? 'Aktif';
        }

        $columns = Schema::getColumnListing('dosens');
        $data = array_filter($validated, fn($key) => in_array($key, $columns), ARRAY_FILTER_USE_KEY);

        $dosen->update($data);

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * 🔹 Hapus dosen
     */
    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus.');
    }
}
