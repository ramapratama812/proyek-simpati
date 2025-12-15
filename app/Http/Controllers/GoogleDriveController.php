<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class GoogleDriveController extends Controller
{
    public function redirect(Request $request)
    {
        return Socialite::driver('google')
        ->redirectUrl(config('google-drive.redirect_uri'))
        ->scopes([
            'openid','email','profile',
            config('google-drive.scope'), // drive.file :contentReference[oaicite:5]{index=5}
        ])
        ->with([
            'access_type' => 'offline', // refresh token :contentReference[oaicite:6]{index=6}
            'prompt' => 'consent',      // paksa consent agar refresh token keluar lagi kalau pernah authorize :contentReference[oaicite:7]{index=7}
            'include_granted_scopes' => 'true',
        ])
        ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            // Cek apakah user membatalkan (biasanya ada parameter error)
            if ($request->has('error')) {
                return redirect()->route('profile.show')
                    ->with('error', 'Akses Google Drive dibatalkan: ' . $request->get('error'));
            }

            $g = Socialite::driver('google')
                ->redirectUrl(config('google-drive.redirect_uri'))
                ->user();

            $user = $request->user();
            $user->google_id = $g->getId();
            $user->google_access_token = $g->token;

            // refreshToken kadang null → jangan overwrite yang lama dengan null
            if (!empty($g->refreshToken)) {
                $user->google_refresh_token = $g->refreshToken;
            }

            $user->google_token_expires_at = now()->addSeconds($g->expiresIn ?? 3600);
            $user->save();

            return redirect()->route('profile.show')
                ->with('ok', 'Google Drive berhasil terhubung.');

        } catch (\Exception $e) {
            \Log::error('Google Drive Callback Error: ' . $e->getMessage());
            return redirect()->route('profile.show')
                ->with('error', 'Gagal menghubungkan Google Drive. Silakan coba lagi.');
        }
    }
}
