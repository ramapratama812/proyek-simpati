<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Publication;
use Illuminate\Support\Facades\Schema;
use App\Mail\ResearchProjectSubmittedMail;
use App\Mail\ResearchProjectStatusChangedMail;
use Illuminate\Support\Facades\Mail;

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
        $q           = trim($request->get('q', ''));
        $year        = $request->get('year');
        $type        = $request->get('type');
        $sort        = $request->get('sort', 'latest');
        $chartFilter = $request->get('chart_filter', 'all'); // all, penelitian, pengabdian

        $projects = ResearchProject::query();

        $user = auth()->user();
        $isAdmin = strtolower($user->role ?? '') === 'admin';

        // Jika kolom validation_status sudah ada dan user bukan admin,
        // hanya tampilkan:
        // - kegiatan yang sudah disetujui, atau
        // - kegiatan yang melibatkan user (ketua/creator/anggota)
        if (Schema::hasColumn('research_projects','validation_status') && !$isAdmin) {
            $projects->where(function ($q2) use ($user) {
                $q2->where('validation_status', 'approved')
                   ->orWhere('ketua_id', $user->id)
                   ->orWhere('created_by', $user->id)
                   ->orWhereHas('members', function ($qq) use ($user) {
                        $qq->where('users.id', $user->id);
                   });
            });
        }


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

        if ($type) {
            $projects->where('jenis', $type);
        }

        switch ($sort) {
            case 'year_desc': $projects->orderByRaw("$yearExpr DESC"); break;
            case 'year_asc' : $projects->orderByRaw("$yearExpr ASC");  break;
            case 'name'     : $projects->orderBy('judul');          break;
            case 'latest'   :
            default         : $projects->latest('created_at');      break;
        }

        $projects = $projects->paginate(12)->withQueryString();

        // --- Data chart ---
        // Hanya hitung yang sudah APPROVED
        $chartQuery = DB::table('research_projects')
            ->selectRaw("$yearExpr AS y, COUNT(*) AS c")
            ->whereRaw("$yearExpr IS NOT NULL");

        if (Schema::hasColumn('research_projects', 'validation_status')) {
            $chartQuery->where('validation_status', 'approved');
        }

        if ($chartFilter === 'penelitian') {
            $chartQuery->where('jenis', 'penelitian');
        } elseif ($chartFilter === 'pengabdian') {
            $chartQuery->where('jenis', 'pengabdian');
        }

        $chartRows = $chartQuery
            ->groupBy('y')
            ->orderBy('y')
            ->get();

        // --- Daftar tahun untuk <select> ---
        $years = DB::table('research_projects')
            ->selectRaw("DISTINCT $yearExpr AS y")
            ->whereRaw("$yearExpr IS NOT NULL")
            ->orderBy('y', 'desc')
            ->pluck('y');

        return view('projects.index', [
            'projects'    => $projects,
            'chart'       => $chartRows,
            'years'       => $years,
            'q'           => $q,
            'year'        => $year,
            'type'        => $type,
            'sort'        => $sort,
            'chartFilter' => $chartFilter,
        ]);
    }

    public function create()
    {
        if (strtolower(auth()->user()->role ?? '') === 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

        $lecturers = User::where('role','dosen')->orderBy('name')->get(['id','name']);
        $students  = User::where('role','mahasiswa')->orderBy('name')->get(['id','name']);
        return view('projects.create', compact('lecturers','students'));
    }

    public function store(Request $request)
    {
        if (strtolower(auth()->user()->role ?? '') === 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

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
            'surat_proposal' => 'required|file|mimes:pdf|max:10240',
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
            'target_luaran' => 'nullable|array',
            'target_luaran.*' => 'string',
            'keywords' => 'nullable|string|max:255',
            'tautan' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'image|max:10248',
        ]);

        return DB::transaction(function () use ($request, $data) {
            $project = new ResearchProject();
            // Handle file upload for surat_proposal
            $suratProposalPath = null;
            if ($request->hasFile('surat_proposal')) {
                $suratProposalPath = $request->file('surat_proposal')->store('proposals', 'public');
            }

            $project->fill([
                'jenis'             => $data['jenis'],
                'judul'             => $data['judul'],
                'kategori_kegiatan' => $data['kategori_kegiatan'] ?? null,
                'bidang_ilmu'       => $data['bidang_ilmu'] ?? null,
                'skema'             => $data['skema'] ?? null,
                'abstrak'           => $data['abstrak'] ?? null,
                'surat_proposal'    => $suratProposalPath,
                'mulai'             => $data['mulai'] ?? null,
                'selesai'           => $data['selesai'] ?? null,
                'sumber_dana'       => $data['sumber_dana'] ?? null,
                'biaya'             => $data['biaya'] ?? null,
                'ketua_id'          => $data['ketua_user_id'] ?? null,
                'tahun_usulan'      => $data['tahun_usulan'] ?? null,
                'tahun_pelaksanaan' => $data['tahun_pelaksanaan'] ?? null,
                'status'            => $data['status'] ?? 'usulan',
                'tkt'               => $data['tkt'] ?? null,
                'mitra_nama'        => $data['mitra_nama'] ?? null,
                'lokasi'            => $data['lokasi'] ?? null,
                'nomor_kontrak'     => $data['nomor_kontrak'] ?? null,
                'tanggal_kontrak'   => $data['tanggal_kontrak'] ?? null,
                'target_luaran'     => $data['target_luaran'] ?? null,
                'keywords'          => $data['keywords'] ?? null,
                'tautan'            => $data['tautan'] ?? null,
                'created_by'        => auth()->id(),
                'validation_status' => 'draft',   // BARU
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

            // kirim email notifikasi ke admin
            $adminEmails = User::where('role', 'admin')->pluck('email')->all();

            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new ResearchProjectSubmittedMail($project));
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

    public function destroyImage(ResearchProject $project, $imageId)
    {
        if (!$this->isParticipant($project)) {
            abort(403);
        }

        $image = $project->images()->where('id', $imageId)->firstOrFail();
        \Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->route('projects.show',$project)->with('ok','Gambar berhasil dihapus.');
    }

    public function show(ResearchProject $project)
    {
        $project->load(['members','ketua','images']);
        return view('projects.show', compact('project'));
    }

    public function edit(ResearchProject $project)
    {
        if (strtolower(auth()->user()->role ?? '') === 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

        $this->authorizeProject($project);
        $lecturers = User::where('role','dosen')->orderBy('name')->get(['id','name']);
        $students  = User::where('role','mahasiswa')->orderBy('name')->get(['id','name']);
        $selectedAnggota = $project->members()->wherePivot('peran','anggota')->pluck('users.id')->toArray();
        return view('projects.edit', compact('project','lecturers','students','selectedAnggota'));
    }

    public function update(Request $request, ResearchProject $project)
    {
        if (strtolower(auth()->user()->role ?? '') === 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

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
            'surat_proposal' => 'required|file|mimes:pdf|max:10240',
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
            'target_luaran' => 'nullable|array',
            'target_luaran.*' => 'string',
            'keywords' => 'nullable|string|max:255',
            'tautan' => 'nullable|url',
        ]);

        // Handle file upload for surat_proposal if provided
        if ($request->hasFile('surat_proposal')) {
            // Delete old file if exists
            if ($project->surat_proposal) {
                \Storage::disk('public')->delete($project->surat_proposal);
            }
            $suratProposalPath = $request->file('surat_proposal')->store('proposals', 'public');
        } else {
            $suratProposalPath = $project->surat_proposal;
        }

        $project->update([
            'jenis' => $data['jenis'],
            'judul' => $data['judul'],
            'kategori_kegiatan' => $data['kategori_kegiatan'] ?? null,
            'bidang_ilmu' => $data['bidang_ilmu'] ?? null,
            'skema' => $data['skema'] ?? null,
            'abstrak' => $data['abstrak'] ?? null,
            'surat_proposal' => $suratProposalPath,
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

    // method untuk mengajukan validasi ke admin (untuk dosen)
    public function submitValidation(ResearchProject $project)
    {
        // pastikan hanya ketua/pembuat yang boleh ajukan
        $uid = auth()->id();
        if (!$uid || ($project->ketua_id != $uid && $project->created_by != $uid)) {
            abort(403);
        }

        if ($project->validation_status === 'approved') {
            return back()->with('ok', 'Kegiatan sudah divalidasi, tidak perlu diajukan lagi.');
        }

        $project->validation_status = 'pending';
        $project->validation_note   = null;
        $project->validated_by      = null;
        $project->validated_at      = null;
        $project->save();

        // === Notifikasi internal ke admin (sudah ada) ===
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            UserNotification::create([
                'user_id'    => $admin->id,
                'project_id' => $project->id,
                'type'       => 'validation_submitted',
                'message'    => 'Usulan kegiatan baru menunggu validasi: ' . $project->judul,
            ]);
        }

        // === Tambahan: kirim EMAIL ke semua admin ===
        $adminEmails = $admins->pluck('email')->filter()->unique()->all();

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new ResearchProjectSubmittedMail($project));
        }

        return back()->with('ok', 'Kegiatan berhasil diajukan untuk validasi.');
    }

    public function destroy(ResearchProject $project)
    {
        if (strtolower(auth()->user()->role ?? '') === 'mahasiswa') {
            abort(403, 'Akses ditolak.');
        }

        $this->authorizeProject($project);
        $project->delete();
        return redirect()->route('projects.index')->with('ok','Kegiatan berhasil dihapus.');
    }

    protected function authorizeProject(ResearchProject $project): void
    {
        $uid  = auth()->id();
        $role = strtolower(auth()->user()->role ?? '');
        $isAdmin = $role === 'admin';

        // admin selalu boleh
        if ($isAdmin) {
            return;
        }

        // jika sudah disetujui admin, dosen tidak boleh ubah/hapus
        if ($project->validation_status === 'approved') {
            abort(403, 'Kegiatan sudah divalidasi dan tidak dapat diubah. Hubungi admin jika perlu revisi.');
        }

        if ($project->created_by && $project->created_by === $uid) return;
        if ($project->ketua_id && $project->ketua_id === $uid) return;

        abort(403);
    }

    // method untuk memastikan user adalah admin
    protected function ensureAdmin(): void
    {
        $role = strtolower(auth()->user()->role ?? '');
        abort_unless($role === 'admin', 403);
    }

    // method untuk mengirim notifikasi ke dosen terkait validasi
    protected function notifyLecturersForValidation(ResearchProject $project, string $type, string $message): void
    {
        $userIds = [];

        if ($project->created_by) {
            $userIds[] = (int) $project->created_by;
        }
        if ($project->ketua_id) {
            $userIds[] = (int) $project->ketua_id;
        }

        $userIds = array_unique(array_filter($userIds));

        // Kumpulkan email dosen yang akan dikirimi email
        $emails = [];

        foreach ($userIds as $uid) {
            // notifikasi internal (sudah ada)
            UserNotification::create([
                'user_id'    => $uid,
                'project_id' => $project->id,
                'type'       => $type,
                'message'    => $message,
            ]);

            // ambil email user untuk email notifikasi
            $user = User::find($uid);
            if ($user && $user->email) {
                $emails[] = $user->email;
            }
        }

        $emails = array_unique($emails);

        // Kirim email status kegiatan kalau ada email yang valid
        if (!empty($emails)) {
            Mail::to($emails)->send(new ResearchProjectStatusChangedMail($project));
        }
    }

    public function publications()
    {
        return $this->belongsToMany(Publication::class, 'project_publications', 'project_id', 'publication_id')
                    ->withTimestamps();
    }

    // selanjutnya: field method untuk validasi oleh admin
    public function validationIndex(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->get('status', 'pending');

        $query = ResearchProject::with('ketua')
            ->orderByDesc('created_at');

        if (in_array($status, ['draft','pending','approved','revision_requested','rejected'], true)) {
            $query->where('validation_status', $status);
        }

        $projects = $query->paginate(20)->withQueryString();

        return view('projects.validation_admin_index', [
            'projects' => $projects,
            'status'   => $status,
        ]);
    }

    public function validationShow(ResearchProject $project)
    {
        $this->ensureAdmin();
        $project->load([
            'ketua',
            'members',
            'images',
            'publications',
        ]);

        return view('projects.validation_admin_show', compact('project'));
    }

    public function approveValidation(Request $request, ResearchProject $project)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'note'              => 'nullable|string',
            'surat_persetujuan' => 'required|file|mimes:pdf|max:10240',
        ]);

        // upload surat persetujuan
        if ($request->hasFile('surat_persetujuan')) {
            // hapus file lama jika ada
            if ($project->surat_persetujuan) {
                \Storage::disk('public')->delete($project->surat_persetujuan);
            }
            $path = $request->file('surat_persetujuan')->store('surat_persetujuan', 'public');
            $project->surat_persetujuan = $path;
        }

        $project->validation_status = 'approved';
        $project->validation_note   = $data['note'] ?? null;
        $project->validated_by      = auth()->id();
        $project->validated_at      = now();
        $project->save();

        $this->notifyLecturersForValidation(
            $project,
            'validation_approved',
            'Usulan kegiatan Anda telah disetujui: ' . $project->judul
        );

        return redirect()->route('projects.validation.index')
            ->with('ok', 'Kegiatan berhasil disetujui.');
    }

    public function requestRevision(Request $request, ResearchProject $project)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'note' => 'required|string',
        ]);

        $project->validation_status = 'revision_requested';
        $project->validation_note   = $data['note'];
        $project->validated_by      = auth()->id();
        $project->validated_at      = now();
        $project->save();

        $this->notifyLecturersForValidation(
            $project,
            'validation_revision',
            'Usulan kegiatan Anda memerlukan revisi: ' . $project->judul
        );

        return back()->with('ok', 'Permintaan revisi telah dikirim ke dosen.');
    }

    public function rejectValidation(Request $request, ResearchProject $project)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'note' => 'required|string',
        ]);

        $project->validation_status = 'rejected';
        $project->validation_note   = $data['note'];
        $project->validated_by      = auth()->id();
        $project->validated_at      = now();
        $project->save();

        $this->notifyLecturersForValidation(
            $project,
            'validation_rejected',
            'Usulan kegiatan Anda ditolak: ' . $project->judul
        );

        return redirect()->route('projects.validation.index')
            ->with('ok', 'Usulan kegiatan ditolak.');
    }

    // method buat kelola kegiatan saya (untuk dosen)
    public function myProjects(Request $request)
    {
        $user = auth()->user();

        $q      = trim($request->get('q', ''));
        $jenis  = $request->get('jenis');   // penelitian / pengabdian
        $status = $request->get('status');  // validation_status
        $tahun  = $request->get('tahun');   // tahun usulan/pelaksanaan

        $projects = ResearchProject::with(['ketua','members'])
            ->where(function ($query) use ($user) {
                $query->where('ketua_id', $user->id)
                      ->orWhere('created_by', $user->id)
                      ->orWhereHas('members', function ($q2) use ($user) {
                          $q2->where('users.id', $user->id);
                      });
            });

        if ($q !== '') {
            $projects->where(function ($qq) use ($q) {
                $qq->where('judul', 'like', "%{$q}%")
                   ->orWhere('skema', 'like', "%{$q}%")
                   ->orWhere('bidang_ilmu', 'like', "%{$q}%");
            });
        }

        if ($jenis) {
            $projects->where('jenis', $jenis);
        }

        if ($status) {
            $projects->where('validation_status', $status);
        }

        if ($tahun) {
            $projects->where(function ($qq) use ($tahun) {
                $qq->where('tahun_usulan', $tahun)
                   ->orWhere('tahun_pelaksanaan', $tahun);
            });
        }

        $projects = $projects
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $tahunOptions = ResearchProject::select('tahun_usulan')
            ->whereNotNull('tahun_usulan')
            ->distinct()
            ->orderBy('tahun_usulan', 'desc')
            ->pluck('tahun_usulan');

        return view('projects.my_index', [
            'projects'      => $projects,
            'tahunOptions'  => $tahunOptions,
            'filterQ'       => $q,
            'filterJenis'   => $jenis,
            'filterStatus'  => $status,
            'filterTahun'   => $tahun,
        ]);
    }

}
