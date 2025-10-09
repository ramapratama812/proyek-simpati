<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\CrossrefService;
use App\Services\BibtexImporter;
use App\Services\OaiPmhHarvester;
use App\Models\Publication;

class ImportController extends Controller
{
    public function crossrefByDoi(Request $r, CrossrefService $svc)
    {
        $data = $r->validate(['doi'=>'required|string']);
        $meta = $svc->byDoi($data['doi']);

        $pub = Publication::create([
            'owner_id'=>auth()->id(),
            'judul'=>$meta['judul'],
            'jurnal'=>$meta['jurnal'] ?? null,
            'tahun'=>$meta['tahun'] ?? null,
            'doi'=>$meta['doi'] ?? null,
            'penulis'=>$meta['penulis'] ?? [],
            'sumber'=>$meta['sumber'] ?? [],
        ]);

        return back()->with('ok',"Publikasi dibuat dari DOI {$pub->doi}");
    }

    public function bibtexUpload(Request $r, BibtexImporter $imp)
    {
        $r->validate(['file'=>'required|file|mimes:bib,txt']);
        $entries = $imp->parse(file_get_contents($r->file('file')->getRealPath()));

        foreach ($entries as $e) {
            Publication::create([
                'owner_id'=>auth()->id(),
                'judul'=>$e['judul'],
                'jurnal'=>$e['jurnal'] ?? null,
                'tahun'=>$e['tahun'] ?? null,
                'doi'=>$e['doi'] ?? null,
                'penulis'=>$e['penulis'] ?? [],
                'sumber'=>$e['sumber'] ?? [],
            ]);
        }
        return back()->with('ok', count($entries).' publikasi diimpor dari BibTeX.');
    }

    public function oaiHarvest(Request $r, OaiPmhHarvester $harv)
    {
        $data = $r->validate([
            'base_url'=>'required|url',
            'set'=>'nullable|string',
            'author_like'=>'nullable|string'
        ]);
        $rows = $harv->listRecords($data['base_url'], $data['set'] ?? null);
        $rows = collect($rows)->filter(function($x) use ($data){
            return empty($data['author_like']) || collect($x['penulis'])->contains(function($n) use ($data){
                return \Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($n), \Illuminate\Support\Str::lower($data['author_like']));
            });
        });

        foreach ($rows as $e) {
            Publication::firstOrCreate(
                ['owner_id'=>auth()->id(), 'judul'=>$e['judul'], 'tahun'=>$e['tahun']],
                ['penulis'=>$e['penulis'], 'sumber'=>$e['sumber']]
            );
        }

        return back()->with('ok', $rows->count().' publikasi dipanen via OAI-PMH.');
    }
}
