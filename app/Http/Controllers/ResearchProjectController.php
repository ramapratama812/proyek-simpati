<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Publication;

class ResearchProjectController extends Controller
{
    protected function canManage(\App\Models\ResearchProject $project): bool
    {
        $uid = auth()->id();
        return $uid && ($project->created_by == $uid || $project->ketua_id == $uid);
    }
    protected function isParticipant(\App\Models\ResearchProject $project): bool
    {
        $uid = auth()->id();
        if (!$uid) return false;
        if ($project->ketua_id == $uid) return true;
        return $project->members()->where('users.id',$uid)->exists();
    }

    public function index(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $year  = $request->get('year');
        $type  = $request->get('type');
        $sort  = $request->get('sort', 'latest');

        $projects = ResearchProject::query();

        if ($q !== '') {
            $projects->where(function ($w) use ($q) {
                $w->where('judul', 'like', "%{$q}%")
                  ->orWhere('skema', 'like', "%{$q}%")
                  ->orWhere('kategori_kegiatan', 'like', "%{$q}%");
            });
        }

        // Ekspresi tahun tunggal untuk dipakai berulang
        $yearExpr = "COALESCE(YEAR(mulai), YEAR(selesai), tahun_pelaksanaan, YEAR(created_at))";

        if ($year) {
            $projects->whereRaw("$yearExpr = ?", [$year]);
        }

        switch ($sort) {
            case 'year_desc': $projects->orderByRaw("$yearExpr DESC"); break;
            case 'year_asc' : $projects->orderByRaw("$yearExpr ASC");  break;
            case 'name'     : $projects->orderBy('judul');             break;
            case 'latest'   :
            default         : $projects->latest('created_at');         break;
        }

        $projects = $projects->paginate(12)->withQueryString();

        // --- Data chart ---
        $chartRows = DB::table('research_projects')
            ->selectRaw("$yearExpr AS y, COUNT(*) AS c")
            ->whereRaw("$yearExpr IS NOT NULL")               // ✅ pakai ekspresi, bukan alias
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        // --- Daftar tahun untuk <select> ---
        $years = DB::table('research_projects')
            ->selectRaw("DISTINCT $yearExpr AS y")
            ->whereRaw("$yearExpr IS NOT NULL")               // ✅ hindari WHERE y IS NOT NULL
            ->orderBy('y', 'desc')
            ->pluck('y');

        return view('projects.index', [
            'projects' => $projects,
            'chart'    => $chartRows,
            'years'    => $years,
            'q'        => $q,
            'year'     => $year,
            'type'     => $type,
            'sort'     => $sort,
        ]);
    }

    public function create()
    {
        $lecturers = User::where('role','dosen')->orderBy('name')->get(['id','name']);
        $students  = User::where('role','mahasiswa')->orderBy('name')->get(['id','name']);
        return view('projects.create', compact('lecturers','students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis' => 'required|in:penelitian,pengabdian',
            'judul' => 'required|string|max:255',
            'kategori_kegiatan' => 'nullable|string|max:255',
            'bidang_ilmu' => 'nullable|string|max:255',
            'skema' => 'nullable|string|max:255',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'sumber_dana' => 'nullable|string|max:255',
            'biaya' => 'nullable|numeric|min:0',
            'abstrak' => 'nullable|string',
            'ketua_user_id' => 'nullable|exists:users,id',
            'anggota_user_ids' => 'nullable|array',
            'anggota_user_ids.*' => 'exists:users,id',
            'tahun_usulan' => 'nullable|digits:4',
            'tahun_pelaksanaan' => 'nullable|digits:4',
            'status' => 'nullable|in:usulan,didanai,berjalan,selesai',
            'tkt' => 'nullable|integer|min:1|max:9',
            'mitra_nama' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'nomor_kontrak' => 'nullable|string|max:255',
            'tanggal_kontrak' => 'nullable|date',
            'lama_kegiatan_bulan' => 'nullable|integer|min:1|max:60',
            'target_luaran' => 'nullable|array',
            'target_luaran.*' => 'string',
            'keywords' => 'nullable|string|max:255',
            'tautan' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10248',
        ]);

        return DB::transaction(function () use ($request, $data) {
            $project = new ResearchProject();
            $project->fill([
                'jenis' => $data['jenis'],
                'judul' => $data['judul'],
                'kategori_kegiatan' => $data['kategori_kegiatan'] ?? null,
                'bidang_ilmu' => $data['bidang_ilmu'] ?? null,
                'skema' => $data['skema'] ?? null,
                'abstrak' => $data['abstrak'] ?? null,
                'mulai' => $data['mulai'] ?? null,
                'selesai' => $data['selesai'] ?? null,
                'sumber_dana' => $data['sumber_dana'] ?? null,
                'biaya' => $data['biaya'] ?? null,
                'ketua_id' => $data['ketua_user_id'] ?? null,
                'tahun_usulan' => $data['tahun_usulan'] ?? null,
                'tahun_pelaksanaan' => $data['tahun_pelaksanaan'] ?? null,
                'status' => $data['status'] ?? 'usulan',
                'tkt' => $data['tkt'] ?? null,
                'mitra_nama' => $data['mitra_nama'] ?? null,
                'lokasi' => $data['lokasi'] ?? null,
                'nomor_kontrak' => $data['nomor_kontrak'] ?? null,
                'tanggal_kontrak' => $data['tanggal_kontrak'] ?? null,
                'lama_kegiatan_bulan' => $data['lama_kegiatan_bulan'] ?? null,
                'target_luaran' => $data['target_luaran'] ?? null,
                'keywords' => $data['keywords'] ?? null,
                'tautan' => $data['tautan'] ?? null,
                'created_by' => auth()->id(),
            ]);
            $project->save();

            $members = $data['anggota_user_ids'] ?? [];
            if (!empty($data['ketua_user_id'])) {
                $project->members()->syncWithoutDetaching([$data['ketua_user_id'] => ['peran'=>'ketua']]);
            }
            if (!empty($members)) {
                $attach = [];
                foreach ($members as $uid) { $attach[$uid] = ['peran'=>'anggota']; }
                $project->members()->syncWithoutDetaching($attach);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('projects','public');
                    $project->images()->create(['path'=>$path]);
                }
            }

            return redirect()->route('projects.show',$project)->with('ok','Kegiatan berhasil disimpan.');
        });
    }

    public function storeImages(Request $request, ResearchProject $project)
    {
        if (!$this->isParticipant($project)) {
            abort(403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:10248', // max 10MB per image
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('projects','public');
            $project->images()->create(['path'=>$path]);
        }

        return redirect()->route('projects.show',$project)->with('ok','Gambar berhasil diunggah.');
    }

    public function show(ResearchProject $project)
    {
        $project->load(['members','ketua']);
        return view('projects.show', compact('project'));
    }

    public function edit(ResearchProject $project)
    {
        $this->authorizeProject($project);
        $lecturers = User::where('role','dosen')->orderBy('name')->get(['id','name']);
        $students  = User::where('role','mahasiswa')->orderBy('name')->get(['id','name']);
        $selectedAnggota = $project->members()->wherePivot('peran','anggota')->pluck('users.id')->toArray();
        return view('projects.edit', compact('project','lecturers','students','selectedAnggota'));
    }

    public function update(Request $request, ResearchProject $project)
    {
        $this->authorizeProject($project);

        $data = $request->validate([
            'jenis' => 'required|in:penelitian,pengabdian',
            'judul' => 'required|string|max:255',
            'kategori_kegiatan' => 'nullable|string|max:255',
            'bidang_ilmu' => 'nullable|string|max:255',
            'skema' => 'nullable|string|max:255',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'sumber_dana' => 'nullable|string|max:255',
            'biaya' => 'nullable|numeric|min:0',
            'abstrak' => 'nullable|string',
            'ketua_user_id' => 'nullable|exists:users,id',
            'anggota_user_ids' => 'nullable|array',
            'anggota_user_ids.*' => 'exists:users,id',
            'tahun_usulan' => 'nullable|digits:4',
            'tahun_pelaksanaan' => 'nullable|digits:4',
            'status' => 'nullable|in:usulan,didanai,berjalan,selesai',
            'tkt' => 'nullable|integer|min:1|max:9',
            'mitra_nama' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'nomor_kontrak' => 'nullable|string|max:255',
            'tanggal_kontrak' => 'nullable|date',
            'lama_kegiatan_bulan' => 'nullable|integer|min:1|max:60',
            'target_luaran' => 'nullable|array',
            'target_luaran.*' => 'string',
            'keywords' => 'nullable|string|max:255',
            'tautan' => 'nullable|url',
        ]);

        $project->update([
            'jenis' => $data['jenis'],
            'judul' => $data['judul'],
            'kategori_kegiatan' => $data['kategori_kegiatan'] ?? null,
            'bidang_ilmu' => $data['bidang_ilmu'] ?? null,
            'skema' => $data['skema'] ?? null,
            'abstrak' => $data['abstrak'] ?? null,
            'mulai' => $data['mulai'] ?? null,
            'selesai' => $data['selesai'] ?? null,
            'sumber_dana' => $data['sumber_dana'] ?? null,
            'biaya' => $data['biaya'] ?? null,
            'ketua_id' => $data['ketua_user_id'] ?? null,
            'tahun_usulan' => $data['tahun_usulan'] ?? null,
            'tahun_pelaksanaan' => $data['tahun_pelaksanaan'] ?? null,
            'status' => $data['status'] ?? 'usulan',
            'tkt' => $data['tkt'] ?? null,
            'mitra_nama' => $data['mitra_nama'] ?? null,
            'lokasi' => $data['lokasi'] ?? null,
            'nomor_kontrak' => $data['nomor_kontrak'] ?? null,
            'tanggal_kontrak' => $data['tanggal_kontrak'] ?? null,
            'lama_kegiatan_bulan' => $data['lama_kegiatan_bulan'] ?? null,
            'target_luaran' => $data['target_luaran'] ?? null,
            'keywords' => $data['keywords'] ?? null,
            'tautan' => $data['tautan'] ?? null,
        ]);

        // sync full members set: ketua + anggota
        $sync = [];
        if (!empty($data['ketua_user_id'])) {
            $sync[$data['ketua_user_id']] = ['peran'=>'ketua'];
        }
        foreach (($data['anggota_user_ids'] ?? []) as $uid) {
            $sync[$uid] = ['peran'=>'anggota'];
        }
        // cek anggota lama vs baru untuk kirim notifikasi hanya kepada yang baru ditambahkan
        $old = $project->members()->pluck('users.id')->toArray();
        $project->members()->sync($sync);
        $new = array_keys($sync);
        $added = array_diff($new, $old);
        foreach ($added as $uid) {
            if ($uid == auth()->id()) continue;
            UserNotification::create([
                'user_id' => $uid,
                'project_id' => $project->id,
                'type' => 'joined',
                'message' => 'Anda telah diikutsertakan dalam kegiatan: ' . $project->judul,
            ]);
        }

        return redirect()->route('projects.show',$project)->with('ok','Kegiatan berhasil diperbarui.');
    }

    public function destroy(ResearchProject $project)
    {
        $this->authorizeProject($project);
        $project->delete();
        return redirect()->route('projects.index')->with('ok','Kegiatan berhasil dihapus.');
    }

    protected function authorizeProject(ResearchProject $project): void
    {
        $uid = auth()->id();
        if ($project->created_by && $project->created_by === $uid) return;
        if ($project->ketua_id && $project->ketua_id === $uid) return;
        abort(403);
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'project_publications', 'project_id', 'publication_id')
                    ->withTimestamps();
    }

}
