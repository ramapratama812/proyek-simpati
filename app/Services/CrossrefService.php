<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CrossrefService
{
    public function byDoi(string $doi): array
    {
        $res = Http::acceptJson()->get("https://api.crossref.org/works/".urlencode($doi));
        if(!$res->ok()) throw new \Exception('DOI tidak ditemukan');
        $w = $res->json('message');
        return [
            'judul' => $w['title'][0] ?? '',
            'jurnal'=> $w['container-title'][0] ?? null,
            'tahun' => $w['issued']['date-parts'][0][0] ?? null,
            'doi'   => $w['DOI'] ?? null,
            'penulis'=> collect($w['author'] ?? [])->map(fn($a)=>trim(($a['given']??'').' '.($a['family']??'')))->values()->all(),
            'sumber'=> ['provider'=>'crossref','raw'=>$w]
        ];
    }
}
