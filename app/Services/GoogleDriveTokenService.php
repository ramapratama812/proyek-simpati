<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Http\Request;

class GoogleDriveTokenService
{
    public function getAccessToken(User $user): string
    {
        if (empty($user->google_refresh_token)) {
            throw new \RuntimeException('Belum connect Google Drive (refresh token kosong).');
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $scopes = explode(',', config('google-drive.scope'));
        if (!in_array('https://www.googleapis.com/auth/drive.file', $scopes)) {
            $scopes[] = 'https://www.googleapis.com/auth/drive.file';
        }
        $client->setScopes($scopes);

        $token = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

        // kalau gagal, Google akan balikin ['error'=>..., 'error_description'=>...]
        if (!empty($token['error'])) {
            $desc = $token['error_description'] ?? $token['error'];
            throw new \RuntimeException("Google token refresh gagal: {$desc}");
        }

        if (empty($token['access_token'])) {
            throw new \RuntimeException('Google token refresh gagal: access_token kosong.');
        }

        $user->google_access_token = $token['access_token'];
        $user->google_token_expires_at = now()->addSeconds($token['expires_in'] ?? 3600);
        $user->save();

        return $token['access_token'];
    }
}
