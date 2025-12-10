<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class SintaCrawler
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sinta.base_url'), '/');
        $this->timeout = (int) config('services.sinta.timeout', 10);
    }

    protected function get(string $path): string
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $response = Http::withHeaders([
                'User-Agent' => 'SIMPATI-AHP-Crawler/1.0',
            ])
            ->timeout($this->timeout)
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("Request ke SINTA gagal ({$response->status()})");
        }

        return $response->body();
    }

    /**
     * Ambil SINTA Score Overall & 3Yr dari halaman profil author.
     */
    public function fetchScores(string $sintaId): array
    {
        $html = $this->get("authors/profile/{$sintaId}");

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        $overall = $this->extractNumberBeforeLabel($text, 'SINTA Score Overall');
        $score3yr = $this->extractNumberBeforeLabel($text, 'SINTA Score 3Yr');

        return [
            'sinta_score_overall' => $overall,
            'sinta_score_3yr'     => $score3yr,
        ];
    }

    /**
     * JUMLAH HIBAH:
     * jumlah Value (V3 Overall Sinta) untuk tiga baris:
     *  - JUMLAH PENELITIAN HIBAH LUAR NEGERI
     *  - JUMLAH PENELITIAN HIBAH EKSTERNAL
     *  - JUMLAH PENELITIAN INTERNAL INSTITUSI
     *
     * di halaman ?view=matrics (Score in Research).
     */
    public function countHibahFromMatricsOverall(string $sintaId): int
    {
        $html = $this->get("authors/profile/{$sintaId}/?view=matrics");

        $crawler = new Crawler($html);
        $totalHibah = 0;

        $crawler->filter('tr')->each(function (Crawler $tr) use (&$totalHibah) {
            $rowText = strtoupper(trim($tr->text()));

            if ($rowText === '' || ! str_contains($rowText, 'HIBAH')) {
                return;
            }

            // fokus hanya ke baris yang memang baris hibah penelitian
            if (
                ! str_contains($rowText, 'HIBAH LUAR NEGERI') &&
                ! str_contains($rowText, 'HIBAH EKSTERNAL') &&
                ! str_contains($rowText, 'INTERNAL INSTITUSI')
            ) {
                return;
            }

            // pecah jadi token, ambil angka numerik ke-2 (weight = pertama, value = kedua)
            $tokens       = preg_split('/\s+/', $rowText);
            $numericCount = 0;

            foreach ($tokens as $token) {
                if (! $this->looksLikeNumber($token)) {
                    continue;
                }

                $numericCount++;

                if ($numericCount === 2) {
                    $value      = (int) round($this->parseNumber($token));
                    $totalHibah += $value;
                    break;
                }
            }
        });

        return $totalHibah;
    }

    /**
     * Hitung jumlah publikasi Google Scholar per tahun
     * dari view=googlescholar (biarkan dulu seperti sebelumnya).
     */
    public function countGoogleScholarPublicationsByYear(string $sintaId, int $tahun): int
    {
        $html = $this->get("authors/profile/{$sintaId}/?view=googlescholar");

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        // Pola: "2023 7 cited", dll.
        $pattern = '/\b' . preg_quote((string) $tahun, '/') . '\b\s+\d+\s+cited/';

        if (!preg_match_all($pattern, $text, $matches)) {
            return 0;
        }

        return count($matches[0]);
    }

    /**
     * Helper: ambil angka tepat sebelum label tertentu,
     * misal "436 SINTA Score Overall".
     */
    protected function extractNumberBeforeLabel(string $text, string $label): float
    {
        $pattern = '/([\d.,]+)\s*' . preg_quote($label, '/') . '/';

        if (!preg_match($pattern, $text, $matches)) {
            return 0.0;
        }

        return $this->parseNumber($matches[1]);
    }

    /**
     * Cek apakah token tampak seperti angka (boleh ada koma/titik).
     */
    protected function looksLikeNumber(string $token): bool
    {
        $token = trim($token);
        return (bool) preg_match('/^-?\d+([.,]\d+)?$/', $token);
    }

    /**
     * Konversi string angka versi lokal ke float.
     * - buang bagian dalam tanda kurung: "209 (104)" -> "209"
     * - hilangkan pemisah ribuan '.', ganti ',' jadi '.' untuk desimal
     */

    protected function parseNumber(string $token): float
    {
        $token = trim($token);
        // buang teks dalam kurung, contoh "209 (104)" -> "209"
        $token = preg_replace('/\(.*$/', '', $token);

        $token = str_replace('.', '', $token);   // hapus thousand sep.
        $token = str_replace(',', '.', $token);  // koma -> titik

        if ($token === '' || $token === '-') {
            return 0.0;
        }

        return (float) $token;
    }
}
