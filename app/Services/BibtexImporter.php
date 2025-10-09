<?php

namespace App\Services;

use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;

class BibtexImporter
{
    public function parse(string $bibtex): array
    {
        $listener = new Listener();
        (new Parser())->addListener($listener)->parseString($bibtex);
        $entries = $listener->export();
        return collect($entries)->map(function($e){
            return [
                'judul'=>$e['title']??'',
                'jurnal'=>$e['journal']??($e['booktitle']??null),
                'tahun'=>isset($e['year']) ? (int)$e['year'] : null,
                'doi'=>$e['doi']??null,
                'penulis'=>isset($e['author']) ? array_map('trim', explode(' and ',$e['author'])) : [],
                'sumber'=>['provider'=>'bibtex','raw'=>$e],
            ];
        })->all();
    }
}
