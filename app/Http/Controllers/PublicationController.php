<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\Schema;


class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim($request->get('q',''));
        $year = $request->get('year');
        $sort = $request->get('sort','latest');

        $pubs = Publication::query();

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
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        $years = Publication::selectRaw("DISTINCT $yearExpr AS y")
            ->whereRaw("$yearExpr IS NOT NULL")
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
            'judul'      => 'required|string|max:255',
            'jenis'      => 'nullable|string|max:100',
            'jurnal'     => 'nullable|string|max:255',
            'tahun'      => 'nullable|integer',
            'doi'        => 'nullable|string|max:255',
            'project_id' => 'nullable|integer|exists:research_projects,id',
        ]);

        // set pemilik
        if (Schema::hasColumn('publications', 'owner_id')) {
            $validated['owner_id'] = auth()->id();
        }

        // jika publikasi dikirim dari halaman publikasi-kegiatan, pastikan yang login = ketua/creator
        $pid = $validated['project_id'] ?? null;
        if ($pid) {
            $project = ResearchProject::findOrFail($pid);
            $this->authorizeAttachToProject($project);   // <-- method baru di bawah
        }

        $pub = Publication::create($validated);

        if ($pid) {
            // attach aman (id valid + user berhak)
            $pub->projects()->syncWithoutDetaching([$pid]);
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
        return view('publications.show', ['pub' => $publication]);
    }

    public function edit(Publication $publication)
    {
        $this->authorizeManage($publication);
        return view('publications.edit', compact('publication'));
    }

    public function update(Request $request, Publication $publication)
    {
        $this->authorizeManage($publication);

        $validated = $request->validate([
            'judul'  => 'required|string|max:255',
            'jenis'  => 'nullable|string|max:100',
            'jurnal' => 'nullable|string|max:255',
            'tahun'  => 'nullable|integer',
            'doi'    => 'nullable|string|max:255',
        ]);

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

    // Backupan metode diatas
    // public function show(Publication $publication)
    // {
    //     // View saat ini memakai variabel $pub
    //     return view('publications.show', ['pub' => $publication]);
    // }

    // /** Form edit. */
    // public function edit(Publication $publication)
    // {
    //     $this->authorizeManage($publication);
    //     return view('publications.edit', compact('publication'));
    // }

    // /** Update publikasi. */
    // public function update(Request $request, Publication $publication)
    // {
    //     $this->authorizeManage($publication);

    //     $validated = $request->validate([
    //         'judul'  => 'required|string|max:255',
    //         'jenis'  => 'nullable|string|max:100',
    //         'jurnal' => 'nullable|string|max:255',
    //         'tahun'  => 'nullable|integer',
    //         'doi'    => 'nullable|string|max:255',
    //     ]);

    //     $publication->update($validated);

    //     return redirect()
    //         ->route('publications.show', $publication)
    //         ->with('ok', 'Publikasi berhasil diperbarui.');
    // }

    // /** Hapus publikasi. */
    // public function destroy(Publication $publication)
    // {
    //     $this->authorizeManage($publication);

    //     // Putuskan relasi ke project (kalau ada)
    //     if (method_exists($publication, 'projects')) {
    //         $publication->projects()->detach();
    //     }

    //     $publication->delete();

    //     return redirect()->to(url('/publications'))
    //         ->with('ok', 'Publikasi telah dihapus.');
    // }

}
