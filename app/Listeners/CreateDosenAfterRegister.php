<?php

namespace App\Listeners;

use App\Models\Dosen;
use Illuminate\Auth\Events\Registered;

class CreateDosenAfterRegister
{
    /**
     * Handle event saat user register.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        // Cegah duplikasi dosen
        if (Dosen::where('email', $user->email)->exists()) {
            return;
        }

        // Buat entri dosen baru otomatis
        Dosen::create([
            'nama' => $user->name ?? 'Nama Belum Diisi',
            'email' => $user->email,
            'status_aktivitas' => 'Aktif',
        ]);
    }
}
