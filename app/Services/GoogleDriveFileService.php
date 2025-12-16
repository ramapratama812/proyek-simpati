<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Drive;

class GoogleDriveFileService
{
    private function drive(User $user): Drive
    {
        if (!$user->google_refresh_token) {
            throw new \RuntimeException('Belum connect Google Drive.');
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setScopes([config('google-drive.scope')]);

        $token = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
        if (empty($token['access_token'])) throw new \RuntimeException('Token gagal.');

        $client->setAccessToken($token['access_token']);
        return new Drive($client);
    }

    public function getMeta(User $user, string $fileId): array
    {
        $drive = $this->drive($user);

        $f = $drive->files->get($fileId, [
            'fields' => 'id,name,mimeType,size,webViewLink',
        ]);

        return [
            'id' => $f->id,
            'name' => $f->name,
            'mime' => $f->mimeType,
            'size' => (int) ($f->size ?? 0),
            'webViewLink' => $f->webViewLink ?? null,
        ];
    }
}
