<?php

namespace App\Http\Controllers;

use App\Models\ResearchProject;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProjectPublicationController extends Controller
{
    protected function authorizeAttachToProject(ResearchProject $project): void
    {
        $uid = auth()->id();
        $isAdmin = strtolower(auth()->user()->role ?? '') === 'admin';
        $isKetua   = (int)$project->ketua_id === (int)$uid;
        $isCreator = (int)($project->created_by ?? 0) === (int)$uid;
        abort_unless($isAdmin || $isKetua || $isCreator, 403);
    }

    public function index(ResearchProject $project)
    {
        // JANGAN abort/authorize di sini; semua user boleh melihat daftar
        $canManage = $this->canManage($project);

        $related = $project->publications()
            ->with('owner')     // opsional, untuk menampilkan pemilik
            ->latest('id')
            ->get();

        // daftar publikasi milik user sendiri (hanya jika boleh kelola)
        $myPubs = collect();
        if ($canManage) {
            $myPubs = Publication::where('owner_id', auth()->id())
                ->orderByDesc('id')
                ->get(['id','judul']);
        }

        return view('projects.publications', compact('project','related','myPubs','canManage'));
    }

    public function attach(Request $request, ResearchProject $project)
    {
        abort_unless($this->canManage($project), 403);

        $data = $request->validate([
            'publication_id' => 'required|integer|exists:publications,id',
        ]);

        // pastikan publikasi itu milik user (owner)
        $pub = Publication::whereKey($data['publication_id'])
            ->where('owner_id', auth()->id())
            ->firstOrFail();

        $project->publications()->syncWithoutDetaching([$pub->id]);

        return back()->with('ok', 'Publikasi berhasil dikaitkan.');
    }

    protected function authorizeManage(ResearchProject $project): void
    {
        $uid = auth()->id();
        abort_unless(
            $uid && ($uid == $project->created_by || $uid == $project->ketua_id || (auth()->user()->role ?? null) === 'admin'),
            403
        );
    }

    public function destroy(ResearchProject $project, Publication $publication)
    {
        if (!$this->canDetach($project)) {
            return back()->with('err',
                'Publikasi hanya bisa dilepas oleh ketua sebelum kegiatan diajukan untuk validasi.');
        }

        $project->publications()->detach($publication->id);

        return back()->with('ok', 'Publikasi berhasil dilepas dari kegiatan.');
    }

    // Cek apakah user boleh mengelola (ketua/creator atau admin)
    protected function canManage(ResearchProject $project): bool
    {
        $u = auth()->user();
        $isAdmin  = strtolower($u->role ?? '') === 'admin';
        $isKetua  = (int) $project->ketua_id === (int) ($u->id ?? 0);
        $isMaker  = (int) ($project->created_by ?? 0) === (int) ($u->id ?? 0);

        // setelah divalidasi, hanya admin yang boleh ubah publikasi
        if ($project->validation_status === 'approved' && !$isAdmin) {
            return false;
        }

        return $isAdmin || $isKetua || $isMaker;
    }

        /**
     * Hanya admin + ketua/pembuat yang boleh melepas publikasi,
     * dan itu pun hanya jika kegiatan belum diajukan untuk validasi.
     */
    protected function canDetach(ResearchProject $project): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $role     = strtolower($user->role ?? '');
        $isAdmin  = $role === 'admin';
        $isKetua  = (int) $project->ketua_id === (int) $user->id;
        $isMaker  = (int) ($project->created_by ?? 0) === (int) $user->id;

        // Admin selalu boleh
        if ($isAdmin) {
            return true;
        }

        // Kalau bukan ketua / pembuat, nggak boleh
        if (!($isKetua || $isMaker)) {
            return false;
        }

        // Ketua/pembuat hanya boleh melepas publikasi jika
        // kegiatan BELUM diajukan untuk validasi admin
        // (bukan 'pending' dan bukan 'approved')
        return in_array($project->validation_status, [
            null,
            'draft',
            'revision_requested',
            'rejected',
        ], true);
    }

}
