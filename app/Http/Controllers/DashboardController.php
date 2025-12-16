<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\Publication;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        /** @var \App\Models\User $user */
        $role = strtolower($user->role ?? '');

        // Inisialisasi default supaya aman dipakai di view
        $totalKegiatan          = 0;
        $totalPublikasi         = 0;
        $pendingValidation      = 0;
        $needRevision           = 0;
        $pubPending             = 0;
        $pubNeedRevision        = 0;
        $activityByYear         = collect();
        $publicationByYear      = collect();
        $kegiatanSayaKetua      = collect();
        $kegiatanSebagaiAnggota = collect();
        $publikasiSaya          = collect();

        // Flag peringatan profil belum lengkap (Dosen/Mahasiswa)
        $needsProfile = false;

        // =========================
        // ROLE MAHASISWA
        // =========================
        if ($role === 'mahasiswa') {

            // Cek apakah sudah punya record di tabel mahasiswa
            if (Schema::hasTable((new \App\Models\Mahasiswa)->getTable())) {
                $needsProfile = ! \App\Models\Mahasiswa::where('user_id', $user->id)->exists();
            }

            // Mahasiswa hanya melihat kegiatan yang diikuti sebagai anggota
            $kegiatanSebagaiAnggota = \App\Models\ResearchProject::whereHas('members', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->latest()
                ->take(50)
                ->get();

            // Statistik lain & validasi publikasi dibiarkan default (0),
            // karena memang tidak ditampilkan/dianggap relevan untuk mahasiswa.
        }

        // =========================
        // ROLE DOSEN / ADMIN / LAINNYA
        // =========================
        else {

            // Kalau role dosen → cek profil dosen
            if ($role === 'dosen' && Schema::hasTable((new \App\Models\Dosen)->getTable())) {
                $needsProfile = ! \App\Models\Dosen::where('user_id', $user->id)->exists();
            }

            // Fokus: tampilan dosen/admin
            $totalKegiatan = \App\Models\ResearchProject::where('ketua_id', $user->id)
                ->where('validation_status', 'approved')
                ->count();

            $totalPublikasi = \App\Models\Publication::where('owner_id', $user->id)
                ->where('validation_status', 'approved')
                ->count();

            // Validasi kegiatan
            $pendingValidation = \App\Models\ResearchProject::where('ketua_id', $user->id)
                ->where('validation_status', 'pending')
                ->count();

            $needRevision = \App\Models\ResearchProject::where('ketua_id', $user->id)
                ->where('validation_status', 'revision_requested')
                ->count();

            // Validasi publikasi
            $pubPending = \App\Models\Publication::where('owner_id', $user->id)
                ->where('validation_status', 'pending')
                ->count();

            $pubNeedRevision = \App\Models\Publication::where('owner_id', $user->id)
                ->where('validation_status', 'revision_requested')
                ->count();

            // Grafik kegiatan per tahun (Approved only)
            $activityByYear = \App\Models\ResearchProject::selectRaw('COALESCE(tahun_pelaksanaan, tahun_usulan) as tahun, jenis, COUNT(*) as total')
                ->where(function ($q) use ($user) {
                    $q->where('ketua_id', $user->id)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('members', function ($qq) use ($user) {
                        $qq->where('users.id', $user->id);
                    });
                })
                ->where('validation_status', 'approved')
                ->whereNotNull('tahun_usulan')
                ->groupBy('tahun', 'jenis')
                ->orderBy('tahun')
                ->get();

            // Grafik publikasi per tahun (Approved only)
            $publicationByYear = \App\Models\Publication::selectRaw('tahun, COUNT(*) as total')
                ->where('owner_id', $user->id)
                ->where('validation_status', 'approved')
                ->whereNotNull('tahun')
                ->groupBy('tahun')
                ->orderBy('tahun')
                ->get();

            // List kegiatan & publikasi terbaru
            $kegiatanSayaKetua = \App\Models\ResearchProject::where('ketua_id', $user->id)
                ->latest()->take(50)->get();

            $kegiatanSebagaiAnggota = \App\Models\ResearchProject::whereHas('members', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->latest()->take(50)->get();

            $publikasiSaya = \App\Models\Publication::where('owner_id', $user->id)
                ->latest()->take(50)->get();
        }

        // NOTIFIKASI → tetap dipakai semua role
        $notifications = UserNotification::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $unreadNotificationCount = UserNotification::where('user_id', $user->id)
            ->unread()
            ->count();

        return view('dashboard.index', compact(
            'totalKegiatan',
            'totalPublikasi',
            'pendingValidation',
            'needRevision',
            'activityByYear',
            'publicationByYear',
            'kegiatanSayaKetua',
            'kegiatanSebagaiAnggota',
            'publikasiSaya',
            'notifications',
            'unreadNotificationCount',
            'pubPending',
            'pubNeedRevision',
            'role',
            'needsProfile'
        ));
    }

    public function lecturers()
    {
        $users = User::where('role','dosen')->orderBy('name')->paginate(20);
        return view('search.results', ['title'=>'Dosen','dosen'=>$users, 'mhs'=>collect(), 'proj'=>collect(), 'pubs'=>collect(), 'q'=>null]);
    }

    public function students()
    {
        $users = User::where('role','mahasiswa')->orderBy('name')->paginate(20);
        return view('search.results', ['title'=>'Mahasiswa','dosen'=>collect(), 'mhs'=>$users, 'proj'=>collect(), 'pubs'=>collect(), 'q'=>null]);
    }
}
