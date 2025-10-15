<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    /**
     * 🔹 Menampilkan daftar dosen (dengan fitur pencarian)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $dosens = Dosen::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                      ->orWhere('nidn', 'like', "%{$search}%")
                      ->orWhere('program_studi', 'like', "%{$search}%");
            })
            ->orderBy('nama', 'asc')
            ->get();

        return view('dosen.index', compact('dosens', 'search'));
    }

    /**
     * 🔹 Menampilkan detail profil dosen tertentu
     */
    public function show($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('dosen.show', compact('dosen'));
    }

    /**
     * 🔹 Menampilkan form edit profil dosen
     */
    public function edit($id)
    {
        $dosen = Dosen::findOrFail($id);
        return view('dosen.edit', compact('dosen'));
    }

    /**
     * 🔹 Memperbarui data profil dosen
     */
    public function update(Request $request, $id)
    {
        $dosen = Dosen::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nomor_hp' => 'nullable|string|max:20',
            'nidn' => 'nullable|string|max:20',
            'perguruan_tinggi' => 'nullable|string|max:100',
            'program_studi' => 'nullable|string|max:100',
            'status_ikatan_kerja' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|string|max:20',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'status_aktivitas' => 'nullable|string|max:50',
        ]);

        $dosen->update($validated);

        return redirect()
            ->route('dosen.show', $dosen->id)
            ->with('success', 'Profil dosen berhasil diperbarui!');
    }

    /**
     * 🔹 Menghapus data dosen
     */
    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $dosen->delete();

        return redirect()
            ->route('dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
