<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;

class GoogleDriveController extends Controller
{
    public function driveFiles()
    {
        // Inisialisasi Google Client
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json')); // file kredensial JSON dari Google Cloud
        $client->addScope(Drive::DRIVE_READONLY);
        $client->setAccessType('offline');

        // Pastikan token akses disimpan
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        } else {
            return "Belum ada token Google Drive. Silakan lakukan autentikasi dulu.";
        }

        // Panggil API Drive
        $service = new Drive($client);
        $files = $service->files->listFiles([
            'pageSize' => 10,
            'fields' => 'files(id, name)',
        ]);

        // Tampilkan hasil
        return view('drive.files', ['files' => $files->getFiles()]);
    }
}
