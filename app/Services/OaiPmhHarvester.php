<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OaiPmhHarvester
{
    public function listRecords(string $baseUrl, ?string $set = null, string $prefix='oai_dc'): array
    {
        $params = ['verb'=>'ListRecords','metadataPrefix'=>$prefix];
        if ($set) $params['set']=$set;
        $res = Http::get($baseUrl, $params);
        if(!$res->ok()) throw new \Exception('OAI error');
        $xml = simplexml_load_string($res->body());
        $xml->registerXPathNamespace('oai','http://www.openarchives.org/OAI/2.0/');
        $xml->registerXPathNamespace('dc','http://purl.org/dc/elements/1.1/');
        $out=[];
        foreach ($xml->xpath('//oai:record') as $rec){
            $dc = $rec->metadata->children('http://purl.org/dc/elements/1.1/');
            $title = (string)($dc->title[0] ?? '');
            $date  = (string)($dc->date[0] ?? '');
            $creators = [];
            foreach ($dc->creator as $c){ $creators[]=(string)$c; }
            $out[] = ['judul'=>$title,'penulis'=>$creators,'tahun'=>substr($date,0,4),'sumber'=>['provider'=>'oai','raw'=>json_decode(json_encode($rec),true)]];
        }
        return $out;
    }
}
