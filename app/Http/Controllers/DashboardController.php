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

    $owned = ResearchProject::with('ketua')->latest('id');
    if (!$isAdmin) {
        if (Schema::hasColumn('research_projects','created_by')) {
            $owned->where('created_by', $u->id);
        } else {
            $owned->where('ketua_id', $u->id);
        }
    }
    $projects = $owned->take(5)->get();

    $memberProjects = ResearchProject::with('ketua')
        ->whereHas('members', function($q) use ($u) { $q->where('users.id',$u->id); })
        ->latest('id')->take(5)->get();

    $pubs = Publication::query()->latest('id');
    if (!$isAdmin && Schema::hasColumn('publications','owner_id')) {
        $pubs->where('owner_id', $u->id);
    }
    $pubs = $pubs->take(5)->get();

    $projectCount = $isAdmin
        ? ResearchProject::count()
        : (Schema::hasColumn('research_projects','created_by')
            ? ResearchProject::where('created_by',$u->id)->count()
            : ResearchProject::where('ketua_id',$u->id)->count());

    $publicationCount = $isAdmin
        ? Publication::count()
        : (Schema::hasColumn('publications','owner_id')
            ? Publication::where('owner_id',$u->id)->count()
            : Publication::count());

    $notifications = UserNotification::where('user_id',$u->id)->where('is_shown',false)->orderBy('created_at')->get();
    if ($notifications->count()) {
        UserNotification::whereIn('id',$notifications->pluck('id'))->update(['is_shown'=>true]);
    }

    return view('dashboard.index', compact('projects','memberProjects','pubs','projectCount','publicationCount','isAdmin','notifications'));
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
