<?php

namespace App\Http\Controllers;

use App\Http\Requests\DosenPrestasiRequest;
use App\Models\DosenPrestasi;
use App\Models\Dosen;
use Illuminate\Support\Facades\Storage;

class DosenPrestasiController extends Controller
{
    public function index()
    {
        $prestasis = DosenPrestasi::with('dosen')->paginate(10);
        return view('dosen_prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        $dosens = Dosen::all();
        return view('dosen_prestasi.create', compact('dosens'));
    }

    public function store(DosenPrestasiRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file_bukti')) {
            $data['file_bukti'] = $request->file('file_bukti')->store('prestasi_files', 'public');
        }

        DosenPrestasi::create($data);

        return redirect()->route('dosen-prestasi.index')->with('success', 'Prestasi berhasil ditambahkan');
    }

    public function show(DosenPrestasi $dosenPrestasi)
    {
        return view('dosen_prestasi.show', compact('dosenPrestasi'));
    }

    public function edit(DosenPrestasi $dosenPrestasi)
    {
        $dosens = Dosen::all();
        return view('dosen_prestasi.edit', compact('dosenPrestasi', 'dosens'));
    }

    public function update(DosenPrestasiRequest $request, DosenPrestasi $dosenPrestasi)
    {
        $data = $request->validated();

        if ($request->hasFile('file_bukti')) {
            Storage::disk('public')->delete($dosenPrestasi->file_bukti);
            $data['file_bukti'] = $request->file('file_bukti')->store('prestasi_files', 'public');
        }

        $dosenPrestasi->update($data);

        return redirect()->route('dosen-prestasi.index')->with('success', 'Prestasi berhasil diupdate');
    }

    public function destroy(DosenPrestasi $dosenPrestasi)
    {
        Storage::disk('public')->delete($dosenPrestasi->file_bukti);
        $dosenPrestasi->delete();

        return redirect()->route('dosen-prestasi.index')->with('success', 'Prestasi berhasil dihapus');
    }
}
