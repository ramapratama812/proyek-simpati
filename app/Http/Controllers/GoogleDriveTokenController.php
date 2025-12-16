<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveTokenService;
use Illuminate\Http\Request;

class GoogleDriveTokenController extends Controller
{
    public function show(Request $request, GoogleDriveTokenService $svc)
    {
        try {
            $token = $svc->getAccessToken($request->user());

            $apiKey = config('google-drive.picker.api_key');
            \Log::info('GDRIVE_TOKEN_DEBUG: API Key prefix: ' . substr($apiKey, 0, 5) . '...');

            return response()->json([
                'access_token' => $token,
                'api_key' => $apiKey,
                'app_id'  => config('google-drive.picker.app_id'),
            ], 200);

        } catch (\RuntimeException $e) {
            return response()->json([
                'code' => 'GDRIVE_NOT_READY',
                'message' => $e->getMessage(),
            ], 409);

        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'code' => 'GDRIVE_TOKEN_ERROR',
                'message' => 'Token endpoint error (cek storage/logs/laravel.log).',
            ], 500);
        }
    }
}
