<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $rows = Publication::query()
            ->when($q, fn($x)=>$x->where('judul','like',"%$q%"))
            ->latest()->paginate(15);
        return view('publications.index', compact('rows','q'));
    }

    public function create()
    {
        return view('publications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'=>'required|string|max:255',
            'jurnal'=>'nullable|string|max:255',
            'jenis'=>'nullable|string|max:100',
            'tahun'=>'nullable|integer',
            'doi'=>'nullable|string|max:255',
        ]);

        $data['owner_id'] = auth()->id();
        $pub = Publication::create($data);

        return redirect()->route('publications.show',$pub)->with('ok','Publikasi dibuat.');
    }

    public function show(Publication $publication)
    {
        return view('publications.show', ['pub'=>$publication]);
    }
}
