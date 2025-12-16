<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Mail\PublicationCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Mail\PublicationStatusChangedMail;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim($request->get('q',''));
        $year = $request->get('year');
        $sort = $request->get('sort','latest');

        $pubs = Publication::query();
        $pubs->where('validation_status', 'approved');

        if ($q !== '') {
            $pubs->where(function($w) use ($q){
                $w->where('judul','like',"%{$q}%")
                  ->orWhere('jurnal','like',"%{$q}%");
            });
        }

        $yearExpr = "COALESCE(tahun, YEAR(created_at))";

        if ($year) {
            $pubs->whereRaw("$yearExpr = ?", [$year]);
        }

        switch ($sort) {
            case 'year_desc': $pubs->orderByRaw("$yearExpr DESC"); break;
            case 'year_asc' : $pubs->orderByRaw("$yearExpr ASC");  break;
            case 'name'     : $pubs->orderBy('judul');             break;
            case 'latest'   :
            default         : $pubs->latest('created_at');         break;
        }

        $pubs = $pubs->paginate(15)->withQueryString();

        $chartRows = Publication::selectRaw("$yearExpr AS y, COUNT(*) AS c")
            ->whereRaw("$yearExpr IS NOT NULL")
            ->where('validation_status', 'approved')
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        $years = Publication::selectRaw("DISTINCT $yearExpr AS y")
            ->whereRaw("$yearExpr IS NOT NULL")
            ->where('validation_status', 'approved')
            ->orderBy('y','desc')
            ->pluck('y');

        return view('publications.index', [
            'pubs'  => $pubs,
            'chart' => $chartRows,
            'years' => $years,
        ]);
    }

    public function create(Request $request)
    {
        $projectId = $request->query('project_id');

        if ($projectId && !\App\Models\ResearchProject::whereKey($projectId)->exists()) {
            $projectId = null;
        }


        return view('publications.create', compact('projectId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'jenis'           => 'nullable|string|max:100',
            'jurnal'          => 'nullable|string|max:255',
            'tahun'           => 'nullable|integer',
            'volume'          => 'nullable|string|max:100',
            'nomor'           => 'nullable|string|max:100',
            'abstrak'         => 'nullable|string',
            'jumlah_halaman'  => 'nullable|integer',
            'penulis'         => 'nullable|string',
            'doi'             => 'nullable|string|max:255',
            'project_id'      => 'nullable|integer|exists:research_projects,id',
            'file'            => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Process penulis into array
        if (isset($validated['penulis']) && $validated['penulis']) {
            $validated['penulis'] = array_map('trim', preg_split('/[,;\n]/', $validated['penulis']));
            $validated['penulis'] = array_filter($validated['penulis'], fn($p) => !empty($p));
        } else {
            $validated['penulis'] = [];
        }

        // set pemilik
        if (Schema::hasColumn('publications', 'owner_id')) {
            $validated['owner_id'] = auth()->id();
        }

        // jika publikasi dikirim dari halaman publikasi-kegiatan, pastikan yang login = ketua/creator
        $pid = $validated['project_id'] ?? null;
        if ($pid) {
            $project = ResearchProject::findOrFail($pid);
            $this->authorizeAttachToProject($project);   // <-- method kamu sendiri
        }

        // === simpan publikasi ===
        $validated['validation_status'] = 'draft';

        $pub = Publication::create($validated);

        // attach ke project jika ada
        if ($pid) {
            $pub->projects()->syncWithoutDetaching([$pid]);
        }

        // kirim email notifikasi ke admin
        $adminEmails = User::where('role', 'admin')
            ->pluck('email')
            ->filter()
            ->unique()
            ->all();

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new PublicationCreatedMail($pub));
        }

        return redirect()
            ->route('publications.show', $pub)
            ->with('ok', 'Publikasi berhasil dibuat!');
    }

    /** Cek otorisasi: hanya owner atau admin yang boleh edit/hapus. */
    protected function authorizeManage(Publication $publication): void
    {
        $user = auth()->user();
        $isAdmin = strtolower($user->role ?? '') === 'admin';

        $owns = Schema::hasColumn('publications', 'owner_id')
            ? ($publication->owner_id === ($user->id ?? null))
            : false;

        abort_unless($owns || $isAdmin, 403);
    }

    // Otorisasi untuk dosen ketua/creator atau admin yang bisa upload publikasi
    protected function authorizeAttachToProject(ResearchProject $project): void
    {
        $uid = auth()->id();
        $isAdmin = strtolower(auth()->user()->role ?? '') === 'admin';

        $isKetua   = (int)$project->ketua_id === (int)$uid;
        $isCreator = (int)($project->created_by ?? 0) === (int)$uid;

        abort_unless($isAdmin || $isKetua || $isCreator, 403);
    }

    /** CRUD Publikasi. */
    public function show(Publication $publication)
    {
        $user = auth()->user();
        $isOwner = $publication->owner_id === ($user->id ?? null);
        $isAdmin = strtolower($user->role ?? '') === 'admin';

        if (!$isOwner && !$isAdmin && $publication->validation_status !== 'approved') {
            return redirect()
                ->route('publications.index')
                ->with('popup_error', 'Publikasi ini belum tersedia untuk umum.');
        }

        return view('publications.show', ['pub' => $publication]);
    }

    public function edit(Publication $publication)
    {
        $this->authorizeManage($publication);

        // membatalkan edit jika status publikasi sudah approved
        if($publication->validation_status === 'approved') {
            return redirect()
            ->back()
            ->with('status', 'Publikasi yang sudah disetujui tidak boleh diedit.');
        }

        return view('publications.edit', compact('publication'));
    }

    public function update(Request $request, Publication $publication)
    {
        $this->authorizeManage($publication);

        abort_if($publication->validation_status === 'approved', 403);

        $validated = $request->validate([
            'judul'          => 'required|string|max:255',
            'jenis'          => 'nullable|string|max:100',
            'jurnal'         => 'nullable|string|max:255',
            'tahun'          => 'nullable|integer',
            'volume'         => 'nullable|string|max:100',
            'nomor'          => 'nullable|string|max:100',
            'abstrak'        => 'nullable|string',
            'jumlah_halaman' => 'nullable|integer',
            'penulis'        => 'nullable|string',
            'doi'            => 'nullable|string|max:255',
            'file'           => 'nullable|file|mimes:pdf|max:2048',
            'remove_file'    => 'nullable|boolean', // untuk menghapus file
        ]);

        // Process penulis into array
        if (isset($validated['penulis']) && $validated['penulis']) {
            $validated['penulis'] = array_map('trim', preg_split('/[,;\n]/', $validated['penulis']));
            $validated['penulis'] = array_filter($validated['penulis'], fn($p) => !empty($p));
        } else {
            $validated['penulis'] = [];
        }

        // kalau user centang hapus file
        if ($request->boolean('remove_file')) {
            if (!empty($publication->file)) {
                Storage::disk('public')->delete($publication->file);
            }
            $publication->file = null;
        }

        // kalau ada file baru, hapus lama lalu simpan baru
        if ($request->hasFile('file')) {
            if (!empty($publication->file)) {
                Storage::disk('public')->delete($publication->file);
            }
            $validated['file'] = $request->file('file')->store('publications', 'public');
        } else {
            unset($validated['file']); // jangan ngereset file jadi null tanpa sengaja
        }

        $publication->update($validated);

        return redirect()
            ->route('publications.show', $publication)
            ->with('ok', 'Publikasi berhasil diperbarui.');
    }

    public function destroy(Publication $publication)
    {
        $this->authorizeManage($publication);

        if (method_exists($publication, 'projects')) {
            $publication->projects()->detach();
        }
        $publication->delete();

        return redirect()->to(url('/publications'))
            ->with('ok', 'Publikasi telah dihapus.');
    }

    // method buat kelola publikasi saya (untuk dosen)
    public function myPublications(Request $request)
    {
        $user = auth()->user();

        $q      = trim($request->get('q', ''));
        $jenis  = $request->get('jenis');
        $tahun  = $request->get('tahun');
        $status = $request->get('status');  // validation_status

        $pubs = Publication::where('owner_id', $user->id);

        if ($q !== '') {
            $pubs->where(function ($qq) use ($q) {
                $qq->where('judul', 'like', "%{$q}%")
                   ->orWhere('jurnal', 'like', "%{$q}%")
                   ->orWhere('penerbit', 'like', "%{$q}%");
            });
        }

        if ($jenis) {
            $pubs->where('jenis', $jenis);
        }

        if ($tahun) {
            $pubs->where('tahun', $tahun);
        }

        if ($status) {
            $pubs->where('validation_status', $status);
        }

        $pubs = $pubs
            ->orderByDesc('tahun')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $tahunOptions = Publication::select('tahun')
            ->whereNotNull('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('publications.my_index', [
            'publications'  => $pubs,
            'filterQ'       => $q,
            'filterJenis'   => $jenis,
            'filterTahun'   => $tahun,
            'tahunOptions'  => $tahunOptions,
            'filterStatus'  => $status,
        ]);
    }

    // method cadangan untuk update status publikasi (belum dipakai)
    public function updateStatus(Request $request, Publication $publication)
    {
        // batasi ke admin saja
        $user = auth()->user();
        abort_unless(strtolower($user->role ?? '') === 'admin', 403);

        $data = $request->validate([
            'status'          => 'required|string|max:100',
            'validation_note' => 'nullable|string',
        ]);

        if (Schema::hasColumn('publications', 'status')) {
            $publication->status = $data['status'];
        }

        if (Schema::hasColumn('publications', 'validation_note')) {
            $publication->validation_note = $data['validation_note'] ?? null;
        }

        $publication->save();

        // kirim email ke pemilik publikasi
        if (method_exists($publication, 'owner') && $publication->owner && $publication->owner->email) {
            Mail::to($publication->owner->email)
                ->send(new PublicationStatusChangedMail($publication));
        }

        return back()->with('ok', 'Status publikasi berhasil diperbarui.');
    }

    public function submitValidation(Publication $publication)
    {
        // hanya pemilik publikasi yang boleh ajukan
        abort_unless(auth()->id() === $publication->owner_id, 403);

        // jika sudah approved, tidak bisa diajukan ulang
        abort_if($publication->validation_status === 'approved', 403);

        // ubah status -> pending
        $publication->update([
            'validation_status' => 'pending',
        ]);

        // tambahkan notifikasi untuk admin
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            \App\Models\UserNotification::create([
                'user_id' => $admin->id,
                'type'    => 'publication_validation_request',
                'message' => "Publikasi \"{$publication->judul}\" diajukan untuk validasi.",
            ]);
        }

        return back()->with('ok', 'Publikasi berhasil diajukan untuk validasi admin.');
    }
}
