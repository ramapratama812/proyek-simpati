<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OaiPmhHarvester
{
    public function listRecords(string $baseUrl, ?string $set = null, string $prefix='oai_dc'): array
    {
        $out = [];
        $params = ['verb' => 'ListRecords', 'metadataPrefix' => $prefix];
        if ($set) $params['set'] = $set;

        do {
            $res = Http::get($baseUrl, $params);
            if (!$res->ok()) {
                throw new \Exception('OAI request failed with status ' . $res->status() . ': ' . $res->body());
            }

            // Check if response is XML
            $contentType = $res->header('Content-Type');
            if (!str_contains($contentType, 'xml') && !str_contains($contentType, 'application/octet-stream')) {
                throw new \Exception('OAI response is not XML. Content-Type: ' . $contentType . '. Body: ' . substr($res->body(), 0, 500));
            }

            try {
                $xml = simplexml_load_string($res->body());
                if ($xml === false) {
                    throw new \Exception('Failed to parse XML response');
                }
            } catch (\Exception $e) {
                throw new \Exception('XML parsing error: ' . $e->getMessage() . '. Response body: ' . substr($res->body(), 0, 500));
            }

            $xml->registerXPathNamespace('oai', 'http://www.openarchives.org/OAI/2.0/');
            $xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');

            foreach ($xml->xpath('//oai:record') as $rec) {
                if (!$rec->metadata) continue;
                $dc = $rec->metadata->children('dc', true); // Use namespace prefix
                if (!$dc) continue; // Skip if no DC elements
                $title = (string)($dc->title[0] ?? '');
                $date = (string)($dc->date[0] ?? '');
                $creators = [];
                if ($dc->creator) {
                    foreach ($dc->creator as $c) {
                        $creators[] = (string)$c;
                    }
                }
                $out[] = [
                    'judul' => $title,
                    'penulis' => $creators,
                    'tahun' => substr($date, 0, 4),
                    'sumber' => ['provider' => 'oai', 'raw' => json_decode(json_encode($rec), true)]
                ];
            }

            // Check for resumptionToken
            $resumptionToken = $xml->xpath('//oai:resumptionToken');
            if (!empty($resumptionToken)) {
                $params = ['verb' => 'ListRecords', 'resumptionToken' => (string)$resumptionToken[0]];
            } else {
                $params = null; // No more pages
            }
        } while ($params);

        return $out;
    }
}
