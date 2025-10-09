<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PddiktiFeederClient
{
    protected string $base;
    protected ?string $token = null;

    public function __construct()
    {
        $this->base = config('services.pddikti.base', env('PDDIKTI_BASE', 'http://localhost:3003'));
    }

    public function auth(): void
    {
        $res = Http::post($this->base.'/GetToken', [
            'username'=>config('services.pddikti.username', env('PDDIKTI_USER')),
            'password'=>config('services.pddikti.password', env('PDDIKTI_PASS')),
        ]);

        $this->token = data_get($res->json(), 'data.token');
        if (!$this->token) {
            throw new \Exception('Token Feeder gagal dibuat');
        }
    }

    protected function call(string $method, array $payload=[])
    {
        $payload = array_merge(['act'=>$method,'token'=>$this->token], $payload);
        return Http::asForm()->post($this->base.'/ws/live2.php', $payload)->json();
    }

    public function listDosen(int $limit=100, int $offset=0){ return $this->call('GetListDosen', compact('limit','offset')); }
    public function listMahasiswa(int $limit=100, int $offset=0){ return $this->call('GetStatusMahasiswa', compact('limit','offset')); }
}
