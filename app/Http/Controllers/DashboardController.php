<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\Publication;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $u = auth()->user();
        $projects = ResearchProject::query()
            ->when($u->role !== 'admin', fn($q)=>$q->where('ketua_id', $u->id))
            ->get();

        $pubs = Publication::query()
            ->when($u->role !== 'admin', fn($q)=>$q->where('owner_id', $u->id))
            ->get();

        return view('dashboard.index', [
            'projectCount' => $projects->count(),
            'publicationCount' => $pubs->count(),
            'projects' => $projects->take(5),
            'pubs' => $pubs->take(5),
        ]);
    }

    public function lecturers()
    {
        $users = User::where('role','dosen')->orderBy('name')->paginate(20);
        return view('search.results', ['title'=>'Dosen','dosen'=>$users,'mhs'=>collect(), 'proj'=>collect(), 'pubs'=>collect(), 'q'=>null]);
    }

    public function students()
    {
        $users = User::where('role','mahasiswa')->orderBy('name')->paginate(20);
        return view('search.results', ['title'=>'Mahasiswa','dosen'=>collect(), 'mhs'=>$users, 'proj'=>collect(), 'pubs'=>collect(), 'q'=>null]);
    }
}
