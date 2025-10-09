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

    // public function index(ResearchProject $project)
    // {
    //     // Otorisasi: hanya ketua/creator atau admin yang bisa mengelola publikasi
    //     $this->authorizeAttachToProject($project);

    //     $project->load('publications');

    //     $u = auth()->user();
    //     $isAdmin = strtolower($u->role ?? '') === 'admin';

    //     // kandidat publikasi untuk ditautkan (milik user; admin melihat semua)
    //     $existing = Publication::query()
    //         ->when(!$isAdmin && Schema::hasColumn('publications','owner_id'),
    //             fn($q) => $q->where('owner_id', $u->id))
    //         ->orderByDesc('id')
    //         ->get();

    //     $already   = $project->publications->pluck('id')->all();
    //     $candidates = $existing->whereNotIn('id', $already);

    //     return view('projects.publications', compact('project','candidates') + [
    //         'publications' => $project->publications,
    //         'isAdmin'      => $isAdmin,
    //     ]);
    // }

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

    // public function attach(Request $request, ResearchProject $project)
    // {
    //     $this->authorizeManage($project);
    //     $this->authorizeAttachToProject($project);

    //     // Pastikan tabel pivot ada agar tidak 500
    //     if (!Schema::hasTable('project_publications')) {
    //         return back()->with('err', 'Tabel "project_publications" belum ada. Jalankan: php artisan migrate');
    //     }

    //     $data = $request->validate([
    //         'publication_id' => ['required','integer','exists:publications,id'],
    //     ]);

    //     // Hanya pemilik publikasi yang boleh menautkan (kecuali admin)
    //     $u = auth()->user();
    //     $isAdmin = strtolower($u->role ?? '') === 'admin';
    //     if (!$isAdmin && Schema::hasColumn('publications','owner_id')) {
    //         $owned = Publication::where('id', $data['publication_id'])
    //                             ->where('owner_id', $u->id)
    //                             ->exists();
    //         abort_unless($owned, 403);
    //     }

    //     // Cegah duplikasi secara manual (selain unique index di DB)
    //     $already = $project->publications()
    //                     ->where('publications.id', $data['publication_id'])
    //                     ->exists();

    //     if (!$already) {
    //         // pakai attach yang simpel dan stabil
    //         $project->publications()->attach($data['publication_id']);
    //     }

    //     return back()->with('ok', $already
    //         ? 'Publikasi ini sudah dikaitkan sebelumnya.'
    //         : 'Publikasi berhasil dikaitkan dengan kegiatan.');
    // }

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

    public function detach(ResearchProject $project, Publication $publication)
    {
        abort_unless($this->canManage($project), 403);

        $project->publications()->detach($publication->id);

        return back()->with('ok', 'Publikasi berhasil dilepas.');
    }

    // Cek apakah user boleh mengelola (ketua/creator atau admin)
    protected function canManage(ResearchProject $project): bool
    {
        $u = auth()->user();
        $isAdmin  = strtolower($u->role ?? '') === 'admin';
        $isKetua  = (int) $project->ketua_id === (int) ($u->id ?? 0);
        $isMaker  = (int) ($project->created_by ?? 0) === (int) ($u->id ?? 0);

        return $isAdmin || $isKetua || $isMaker;
    }
}
