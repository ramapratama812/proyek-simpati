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
use App\Services\GoogleDriveFileService;
use App\Services\GoogleDriveTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    public function store(Request $request, GoogleDriveFileService $gdriveFiles)
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
            'surat_proposal' => 'nullable|file|mimes:pdf|max:10240',
            'gdrive_pdf_proposal_json' => 'nullable|string',
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
            'images.*' => 'image|max:10248',
            'gdrive_images_json' => 'nullable|string',
            'gdrive_image_json' => 'nullable|string', // Add this new field
        ]);

        // Pre-process Google Drive Images to validate them before transaction
        $gdriveData = [];
        $imagesJson = $request->input('gdrive_images_json');
        if ($imagesJson) {
            $docs = json_decode($imagesJson, true) ?: [];
            $allowed = ['image/jpeg','image/png','image/webp'];

            foreach ($docs as $doc) {
                $fileId = $doc['id'] ?? null;
                if (!$fileId) continue;

                $meta = $gdriveFiles->getMeta($request->user(), $fileId);

                if (!in_array($meta['mime'], $allowed, true)) {
                    return back()->with('err', 'Dokumentasi harus gambar (jpg/png/webp).')->withInput();
                }

                $gdriveData[] = $meta;
            }
        }

        return DB::transaction(function () use ($request, $data, $gdriveData) {
            $project = new ResearchProject();
            // Handle file upload for surat_proposal
            $suratProposalPath = null;
            if ($request->hasFile('surat_proposal')) {
                $suratProposalPath = $request->file('surat_proposal')->store('proposals', 'public');
            }

            // Handle Google Drive Proposal
            $gdriveProposalData = [];
            if ($request->filled('gdrive_pdf_proposal_json')) {
                $picked = json_decode($request->input('gdrive_pdf_proposal_json'), true);
                if (is_array($picked) && !empty($picked['id'])) {
                    try {
                        $dl = $this->downloadPdfFromDrive($picked['id']);
                        // If local file was uploaded, overwrite it (Drive takes priority if both present, though UI should prevent this)
                        if ($suratProposalPath) {
                            Storage::disk('public')->delete($suratProposalPath);
                        }
                        $suratProposalPath = $dl['path'];
                        $gdriveProposalData = [
                            'gdrive_proposal_id'        => $dl['meta']['id'],
                            'gdrive_proposal_name'      => $dl['meta']['name'],
                            'gdrive_proposal_mime'      => $dl['meta']['mimeType'],
                            'gdrive_proposal_size'      => null, // Size not always available from simple meta fetch unless requested
                            'gdrive_proposal_view_link' => $dl['meta']['url'],
                        ];
                    } catch (\Throwable $e) {
                         // Log error or handle gracefully? For now, maybe just ignore or let it fail?
                         // Ideally we should probably throw to rollback transaction, but let's just log
                         \Log::error("Failed to download proposal from Drive: " . $e->getMessage());
                         throw $e; // Re-throw to trigger rollback
                    }
                }
            }

            if (!$suratProposalPath) {
                throw new \Illuminate\Validation\ValidationException(\Illuminate\Validation\Validator::make([], []), [
                    'surat_proposal' => ['Dokumen proposal wajib diunggah (lokal atau Google Drive).']
                ]);
            }

            // Handle Google Drive Proposal
            $gdriveProposalData = [];
            if ($request->filled('gdrive_pdf_proposal_json')) {
                $picked = json_decode($request->input('gdrive_pdf_proposal_json'), true);
                if (is_array($picked) && !empty($picked['id'])) {
                    try {
                        $dl = $this->downloadPdfFromDrive($picked['id']);
                        // If local file was uploaded, overwrite it (Drive takes priority if both present, though UI should prevent this)
                        if ($suratProposalPath) {
                            Storage::disk('public')->delete($suratProposalPath);
                        }
                        $suratProposalPath = $dl['path'];
                        $gdriveProposalData = [
                            'gdrive_proposal_id'        => $dl['meta']['id'],
                            'gdrive_proposal_name'      => $dl['meta']['name'],
                            'gdrive_proposal_mime'      => $dl['meta']['mimeType'],
                            'gdrive_proposal_size'      => null, // Size not always available from simple meta fetch unless requested
                            'gdrive_proposal_view_link' => $dl['meta']['url'],
                        ];
                    } catch (\Throwable $e) {
                         // Log error or handle gracefully? For now, maybe just ignore or let it fail?
                         // Ideally we should probably throw to rollback transaction, but let's just log
                         \Log::error("Failed to download proposal from Drive: " . $e->getMessage());
                         throw $e; // Re-throw to trigger rollback
                    }
                }
            }

            if (!$suratProposalPath) {
                throw new \Illuminate\Validation\ValidationException(\Illuminate\Validation\Validator::make([], []), [
                    'surat_proposal' => ['Dokumen proposal wajib diunggah (lokal atau Google Drive).']
                ]);
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
                'gdrive_proposal_id'        => $gdriveProposalData['gdrive_proposal_id'] ?? null,
                'gdrive_proposal_name'      => $gdriveProposalData['gdrive_proposal_name'] ?? null,
                'gdrive_proposal_mime'      => $gdriveProposalData['gdrive_proposal_mime'] ?? null,
                'gdrive_proposal_size'      => $gdriveProposalData['gdrive_proposal_size'] ?? null,
                'gdrive_proposal_view_link' => $gdriveProposalData['gdrive_proposal_view_link'] ?? null,
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

            // Insert Google Drive Images (Old Logic - if any)
            foreach ($gdriveData as $meta) {
                \DB::table('research_project_media')->insert([
                    'research_project_id' => $project->id,
                    'gdrive_file_id' => $meta['id'],
                    'name' => $meta['name'],
                    'mime_type' => $meta['mime'],
                    'size' => $meta['size'],
                    'web_view_link' => $meta['webViewLink'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert Google Drive Images (New Logic from gdrive_image_json)
            if ($request->filled('gdrive_image_json')) {
                $gdriveImages = json_decode($request->input('gdrive_image_json'), true);
                if (is_array($gdriveImages)) {
                    foreach ($gdriveImages as $img) {
                        if (empty($img['id'])) continue;

                        // Validasi mime type sederhana (opsional, karena di frontend sudah filter)
                        // Kalau mau ketat, bisa fetch metadata lagi via service, tapi boros API call.
                        // Kita percaya data dari picker (frontend) dulu, atau minimal cek mimeType string.
                        if (isset($img['mimeType']) && !str_starts_with($img['mimeType'], 'image/')) {
                            continue;
                        }

                        \App\Models\ResearchProjectMedia::create([
                            'research_project_id' => $project->id,
                            'gdrive_file_id' => $img['id'],
                            'name' => $img['name'] ?? 'Untitled',
                            'mime_type' => $img['mimeType'] ?? null,
                            'size' => null, // Size gak selalu ada di picker response standard kecuali diminta fields
                            'web_view_link' => $img['url'] ?? null,
                        ]);
                    }
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
            'images' => 'nullable|array',
            'images.*' => 'image|max:10248', // max 10MB per image
            'gdrive_image_json' => 'nullable|string',
        ]);

        if (!$request->hasFile('images') && !$request->filled('gdrive_image_json')) {
            return back()->with('err', 'Pilih minimal satu gambar (lokal atau Google Drive).');
        }

        // Handle Local Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('projects','public');
                $project->images()->create(['path'=>$path]);
            }
        }

        // Handle Google Drive Images
        if ($request->filled('gdrive_image_json')) {
            $gdriveImages = json_decode($request->input('gdrive_image_json'), true);
            if (is_array($gdriveImages)) {
                foreach ($gdriveImages as $img) {
                    if (empty($img['id'])) continue;

                    if (isset($img['mimeType']) && !str_starts_with($img['mimeType'], 'image/')) {
                        continue;
                    }

                    \App\Models\ResearchProjectMedia::create([
                        'research_project_id' => $project->id,
                        'gdrive_file_id' => $img['id'],
                        'name' => $img['name'] ?? 'Untitled',
                        'mime_type' => $img['mimeType'] ?? null,
                        'size' => null,
                        'web_view_link' => $img['url'] ?? null,
                    ]);
                }
            }
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

    public function destroyMedia(ResearchProject $project, $mediaId)
    {
        if (!$this->isParticipant($project)) {
            abort(403);
        }

        $media = \App\Models\ResearchProjectMedia::where('research_project_id', $project->id)
                    ->where('id', $mediaId)
                    ->firstOrFail();

        $media->delete();

        return redirect()->route('projects.show',$project)->with('ok','Dokumentasi Google Drive berhasil dihapus.');
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
            'surat_proposal' => 'nullable|file|mimes:pdf|max:10240',
            'gdrive_pdf_proposal_json' => 'nullable|string',
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
            'gdrive_pdf_proposal_json' => 'nullable|string',
            'gdrive_image_json' => 'nullable|string',
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

        // Handle Google Drive Proposal
        $gdriveProposalData = [];
        if ($request->filled('gdrive_pdf_proposal_json')) {
            $picked = json_decode($request->input('gdrive_pdf_proposal_json'), true);
            if (is_array($picked) && !empty($picked['id'])) {
                try {
                    $dl = $this->downloadPdfFromDrive($picked['id']);
                    // If local file was uploaded or existing file exists, overwrite/delete it
                    if ($suratProposalPath) {
                        \Storage::disk('public')->delete($suratProposalPath);
                    }
                    $suratProposalPath = $dl['path'];
                    $gdriveProposalData = [
                        'gdrive_proposal_id'        => $dl['meta']['id'],
                        'gdrive_proposal_name'      => $dl['meta']['name'],
                        'gdrive_proposal_mime'      => $dl['meta']['mimeType'],
                        'gdrive_proposal_size'      => null,
                        'gdrive_proposal_view_link' => $dl['meta']['url'],
                    ];
                } catch (\Throwable $e) {
                     \Log::error("Failed to download proposal from Drive: " . $e->getMessage());
                     return back()->with('err', 'Gagal mengunduh proposal dari Google Drive: ' . $e->getMessage())->withInput();
                }
            }
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
            'gdrive_proposal_id'        => $gdriveProposalData['gdrive_proposal_id'] ?? $project->gdrive_proposal_id,
            'gdrive_proposal_name'      => $gdriveProposalData['gdrive_proposal_name'] ?? $project->gdrive_proposal_name,
            'gdrive_proposal_mime'      => $gdriveProposalData['gdrive_proposal_mime'] ?? $project->gdrive_proposal_mime,
            'gdrive_proposal_size'      => $gdriveProposalData['gdrive_proposal_size'] ?? $project->gdrive_proposal_size,
            'gdrive_proposal_view_link' => $gdriveProposalData['gdrive_proposal_view_link'] ?? $project->gdrive_proposal_view_link,
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

        $request->validate([
            'note'              => 'nullable|string',
            'surat_persetujuan' => 'nullable|file|mimes:pdf|max:10240',
            'gdrive_pdf_persetujuan_json' => 'nullable|string',
        ]);

        if (!$request->hasFile('surat_persetujuan') && !$request->filled('gdrive_pdf_persetujuan_json')) {
            return back()->withErrors(['surat_persetujuan' => 'Surat Persetujuan wajib diunggah (Lokal atau Google Drive).']);
        }

        // Handle Google Drive File
        if ($request->filled('gdrive_pdf_persetujuan_json')) {
            $picked = json_decode($request->input('gdrive_pdf_persetujuan_json'), true);
            if (is_array($picked) && !empty($picked['id'])) {
                try {
                    /** @var \App\Services\GoogleDriveTokenService $tokenService */
                    $tokenService = app(GoogleDriveTokenService::class);
                    $accessToken = $tokenService->getAccessToken(auth()->user());

                    if (!$accessToken) {
                        throw new \RuntimeException('Akun Google Drive belum terhubung.');
                    }

                    // Download content
                    $fileRes = Http::withToken($accessToken)
                        ->get("https://www.googleapis.com/drive/v3/files/{$picked['id']}", [
                            'alt' => 'media'
                        ]);

                    if (!$fileRes->successful()) {
                        throw new \RuntimeException('Gagal mengunduh file dari Google Drive.');
                    }

                    $filename = 'approval_' . time() . '_' . Str::slug($picked['name']);
                    if (!str_ends_with($filename, '.pdf')) $filename .= '.pdf';
                    
                    $path = 'surat_persetujuan/' . $filename;
                    Storage::disk('public')->put($path, $fileRes->body());

                    // Delete old file
                    if ($project->surat_persetujuan) {
                        Storage::disk('public')->delete($project->surat_persetujuan);
                    }
                    $project->surat_persetujuan = $path;

                } catch (\Exception $e) {
                    return back()->withErrors(['gdrive_pdf_persetujuan_json' => 'Gagal download dari GDrive: ' . $e->getMessage()]);
                }
            }
        }

        // Handle Local File (Priority over GDrive if both present)
        if ($request->hasFile('surat_persetujuan')) {
            // hapus file lama jika ada
            if ($project->surat_persetujuan) {
                Storage::disk('public')->delete($project->surat_persetujuan);
            }
            $path = $request->file('surat_persetujuan')->store('surat_persetujuan', 'public');
            $project->surat_persetujuan = $path;
        }

        $project->validation_status = 'approved';
        $project->validation_note   = $request->input('note');
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

    private function downloadPdfFromDrive(string $fileId): array
    {
        /** @var \App\Services\GoogleDriveTokenService $tokenService */
        $tokenService = app(GoogleDriveTokenService::class);

        $accessToken = $tokenService->getAccessToken(auth()->user());

        if (!$accessToken) {
            throw new \RuntimeException('Akun Google Drive belum terhubung atau token tidak tersedia.');
        }

        // Ambil metadata dulu (validasi mime)
        $metaRes = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'fields' => 'id,name,mimeType,webViewLink'
            ]);

        if (!$metaRes->successful()) {
            throw new \RuntimeException('Gagal mengambil metadata file dari Google Drive.');
        }

        $meta = $metaRes->json();
        if (($meta['mimeType'] ?? '') !== 'application/pdf') {
            throw new \RuntimeException('File yang dipilih harus PDF.');
        }

        // Download konten file
        $fileRes = Http::withToken($accessToken)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'alt' => 'media'
            ]);

        if (!$fileRes->successful()) {
            throw new \RuntimeException('Gagal mengunduh file PDF dari Google Drive.');
        }

        $filename = Str::uuid()->toString() . '.pdf';
        $path = 'proposals/' . $filename;

        Storage::disk('public')->put($path, $fileRes->body());

        return [
            'path' => $path,
            'meta' => [
                'id' => $meta['id'] ?? $fileId,
                'name' => $meta['name'] ?? null,
                'mimeType' => $meta['mimeType'] ?? 'application/pdf',
                'url' => $meta['webViewLink'] ?? null,
            ],
        ];
    }
}
