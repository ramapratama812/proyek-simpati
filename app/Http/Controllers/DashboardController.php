<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $u = auth()->user();

        return view('dashboard.index', [
            'projectCount' => 0,
            'publicationCount' => 0,
            'projects' => collect(),
            'pubs' => collect(),
        ]);
    }

    public function lecturers()
    {
        $users = User::where('role','dosen')->orderBy('name')->paginate(20);
        return view('search.results', [
            'title'=>'Dosen',
            'dosen'=>$users,
            'mhs'=>collect(), 
            'proj'=>collect(), 
            'pubs'=>collect(), 
            'q'=>null
        ]);
    }

    public function students()
    {
        $users = User::where('role','mahasiswa')->orderBy('name')->paginate(20);
        return view('search.results', [
            'title'=>'Mahasiswa',
            'dosen'=>collect(), 
            'mhs'=>$users, 
            'proj'=>collect(), 
            'pubs'=>collect(), 
            'q'=>null
        ]);
    }
}
