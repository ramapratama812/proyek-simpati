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
        $role = strtolower($user->role ?? '');

        // Fokus: tampilan dosen
        $totalKegiatan = ResearchProject::where(function ($q) use ($user) {
                $q->where('ketua_id', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('members', function ($qq) use ($user) {
                      $qq->where('users.id', $user->id);
                  });
            })->count();

        $totalPublikasi = Publication::where('owner_id', $user->id)->count();

        $pendingValidation = ResearchProject::where('ketua_id', $user->id)
            ->where('validation_status', 'pending')
            ->count();

        $needRevision = ResearchProject::where('ketua_id', $user->id)
            ->where('validation_status', 'revision_requested')
            ->count();

        $activityByYear = ResearchProject::selectRaw('COALESCE(tahun_pelaksanaan, tahun_usulan) as tahun, COUNT(*) as total')
            ->where(function ($q) use ($user) {
                $q->where('ketua_id', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('members', function ($qq) use ($user) {
                      $qq->where('users.id', $user->id);
                  });
            })
            ->whereNotNull('tahun_usulan')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $publicationByYear = Publication::selectRaw('tahun, COUNT(*) as total')
            ->where('owner_id', $user->id)
            ->whereNotNull('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $kegiatanSayaKetua = ResearchProject::where('ketua_id', $user->id)
            ->latest()->take(5)->get();

        $kegiatanSebagaiAnggota = ResearchProject::whereHas('members', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->latest()->take(5)->get();

        $publikasiSaya = Publication::where('owner_id', $user->id)
            ->latest()->take(5)->get();

        $notifications = UserNotification::where('user_id', $user->id)
            ->latest()->take(10)->get();

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
            'notifications'
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
