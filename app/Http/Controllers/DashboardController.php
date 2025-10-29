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
        $u = auth()->user();
        $isAdmin = strtolower($u->role ?? '') === 'admin';

        // Always filter for logged-in user
        $owned = ResearchProject::with('ketua')->latest('id');
        if (Schema::hasColumn('research_projects','created_by')) {
            $owned->where('created_by', $u->id);
        } else {
            $owned->where('ketua_id', $u->id);
        }
        $projects = $owned->take(5)->get();

        $memberProjects = ResearchProject::with('ketua')
            ->whereHas('members', function($q) use ($u) { $q->where('users.id',$u->id); })
            ->latest('id')->take(5)->get();

        $pubs = Publication::query()->latest('id');
        if (Schema::hasColumn('publications','owner_id')) {
            $pubs->where('owner_id', $u->id);
        }
        $pubs = $pubs->take(5)->get();

        $projectCount = Schema::hasColumn('research_projects','created_by')
            ? ResearchProject::where('created_by',$u->id)->count()
            : ResearchProject::where('ketua_id',$u->id)->count();

        $publicationCount = Schema::hasColumn('publications','owner_id')
            ? Publication::where('owner_id',$u->id)->count()
            : Publication::count(); // Fallback, but should have owner_id

        // Data for charts: count per year
        $projectCountsByYear = ResearchProject::selectRaw('YEAR(mulai) as year, COUNT(*) as count')
            ->where(function($q) use ($u) {
                if (Schema::hasColumn('research_projects','created_by')) {
                    $q->where('created_by', $u->id);
                } else {
                    $q->where('ketua_id', $u->id);
                }
            })
            ->whereNotNull('mulai')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('count', 'year');

        $publicationCountsByYear = Publication::selectRaw('tahun as year, COUNT(*) as count')
            ->where('owner_id', $u->id)
            ->whereNotNull('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get()
            ->pluck('count', 'year');

        $notifications = UserNotification::where('user_id',$u->id)->where('is_shown',false)->orderBy('created_at')->get();
        if ($notifications->count()) {
            UserNotification::whereIn('id',$notifications->pluck('id'))->update(['is_shown'=>true]);
        }

        return view('dashboard.index', compact('projects','memberProjects','pubs','projectCount','publicationCount','isAdmin','notifications','projectCountsByYear','publicationCountsByYear'));
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
