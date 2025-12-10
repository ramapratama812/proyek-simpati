<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            // Jika belum ada usernya, buat baru
            if (!$user) {
                $user = User::create([
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'username' => explode('@', $googleUser->getEmail())[0], // contoh: nada@gmail.com → nada
                    'password' => Hash::make('google_login_default'), // password dummy
                    'role'     => 'mahasiswa', // ubah sesuai kebutuhan
                ]);
            } else {
                // update nama jika berubah di Google
                $user->update([
                    'name' => $googleUser->getName(),
                ]);
            }

            // login ke sistem
            Auth::login($user);

            return redirect('/dashboard')->with('success', 'Login berhasil!');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }
}
