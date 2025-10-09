<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ResearchProject;
use App\Models\ProjectImage;

class ResearchProjectController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $rows = ResearchProject::query()
            ->when($q, fn($x)=>$x->where('judul','like',"%$q%"))
            ->latest()->paginate(15);
        return view('projects.index', compact('rows','q'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis'=>'required|in:penelitian,pengabdian',
            'judul'=>'required|string|max:255',
            'kategori_kegiatan'=>'nullable|string|max:255',
            'bidang_ilmu'=>'nullable|string|max:255',
            'skema'=>'nullable|string|max:255',
            'abstrak'=>'nullable|string',
            'mulai'=>'nullable|date',
            'selesai'=>'nullable|date|after_or_equal:mulai',
            'sumber_dana'=>'nullable|string|max:255',
            'biaya'=>'nullable|numeric',
            'is_public'=>'nullable|boolean',
            'images'=>'nullable|array|max:5',
            'images.*'=>'image|max:2048'
        ]);

        $data['ketua_id'] = auth()->id();
        $project = ResearchProject::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('projects','public');
                $project->images()->create(['path'=>$path]);
            }
        }

        return redirect()->route('projects.show',$project)->with('ok','Kegiatan disimpan.');
    }

    public function show(ResearchProject $project)
    {
        return view('projects.show', compact('project'));
    }
}
