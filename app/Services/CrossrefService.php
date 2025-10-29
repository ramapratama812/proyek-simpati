<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CrossrefService
{
    public function byDoi(string $doi): array
    {
        try {
            $res = Http::acceptJson()->get("https://api.crossref.org/works/".urlencode($doi));
            if (!$res->ok()) {
                throw new \Exception('DOI tidak ditemukan');
            }
        } catch (\Exception $e) {
            throw new \Exception('Terjadi kesalahan saat menghubungi Crossref: ' . $e->getMessage());
        }
        $w = $res->json('message');
        return [
            'judul' => $w['title'][0] ?? '',
            'jurnal'=> $w['container-title'][0] ?? null,
            'tahun' => $w['issued']['date-parts'][0][0] ?? null,
            'doi'   => $w['DOI'] ?? null,
            'volume' => $w['volume'] ?? null,
            'nomor' => $w['issue'] ?? null,
            'abstrak' => $w['abstract'] ?? null,
            'jumlah_halaman' => $this->calculatePageCount($w['page'] ?? null),
            'tautan' => $w['URL'] ?? null,
            'penulis'=> collect($w['author'] ?? [])->map(fn($a)=>trim(($a['given']??'').' '.($a['family']??'')))->values()->all(),
            'sumber'=> ['provider'=>'crossref','raw'=>$w]
        ];
    }

    // Method buat ngitung halaman
    private function calculatePageCount(?string $page): ?int
    {
        if (!$page) {
            return null;
        }

        // Handle formats like "123-145" or "123"
        if (preg_match('/(\d+)-(\d+)/', $page, $matches)) {
            $start = (int) $matches[1];
            $end = (int) $matches[2];
            return $end - $start + 1;
        } elseif (preg_match('/\d+/', $page, $matches)) {
            return 1; // Single page
        }

        return null;
    }
}
