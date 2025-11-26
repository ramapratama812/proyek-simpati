<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke halaman login Google.
     * Query string ?role=dosen atau ?role=mahasiswa
     */
    public function redirect(Request $request)
    {
        $role = strtolower($request->query('role', 'mahasiswa'));

        if (! in_array($role, ['dosen', 'mahasiswa'])) {
            abort(400, 'Role tidak valid');
        }

        // Simpan role di session supaya bisa dipakai di callback
        session(['google_register_role' => $role]);

        // Optional: kirim parameter hd ke Google sebagai "hint" domain
        // (Tetap WAJIB cek lagi di server, ini hanya filter tampilan.)
        // Dosen: politala.ac.id
        // Mahasiswa: mhs.politala.ac.id
        $hd = $role === 'dosen' ? 'politala.ac.id' : 'mhs.politala.ac.id';

        $driver = Socialite::driver('google');

        // Untuk production, boleh pakai hd.
        // Untuk local testing dengan email pribadi, kita matikan hd via env.
        if (! config('app.debug')) {
            $driver = $driver->with(['hd' => $hd]);
        }

        return $driver->redirect();
    }

    /**
     * Callback sesudah user login di Google.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            // Kalau gagal, balik ke halaman login dengan pesan error
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }

        $email = strtolower($googleUser->getEmail());
        $name  = $googleUser->getName() ?: $googleUser->getNickname();

        // Ambil role yang sebelumnya tersimpan di session
        $role = session('google_register_role', 'mahasiswa');

        // Tentukan domain yang seharusnya
        $expectedDomain = $role === 'dosen'
            ? 'politala.ac.id'
            : 'mhs.politala.ac.id';

        $domain = Str::after($email, '@');

        $allowTest = filter_var(env('GOOGLE_ALLOW_TEST', false), FILTER_VALIDATE_BOOL);
        $testEmail = strtolower(env('GOOGLE_TEST_EMAIL', ''));

        $isTestEmail = $allowTest && $email === $testEmail;

        // Validasi domain: harus sesuai, kecuali email test
        if ($domain !== $expectedDomain && ! $isTestEmail) {
            return redirect()->route('login')
                ->with('error', 'Alamat email tidak sesuai domain untuk role yang dipilih.');
        }

        // Cek apakah user sudah pernah ada (berdasarkan google_id atau email)
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // Update google_id kalau belum diisi
            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
            }

            // Optional: sync role kalau kosong
            if (! $user->role) {
                $user->role = $role;
            }

            // Kalau kolom username kosong, isi otomatis dari email
            if (empty($user->username)) {
                $user->username = Str::before($email, '@');
            }

            $user->save();
        } else {
            // Ambil username dari bagian sebelum '@' di email,
            // misalnya: 240130155.muhammad.ramadhani1@mhs.politala.ac.id
            // => username = '240130155.muhammad.ramadhani1'
            $username = \Illuminate\Support\Str::before($email, '@');

            // Buat user baru
            $user = User::create([
                'name'      => $name,
                'username'  => $username, // <-- WAJIB diisi supaya DB gak error
                'email'     => $email,
                'password'  => bcrypt(Str::random(32)), // password random, user login pakai Google
                'role'      => $role,
                'google_id' => $googleUser->getId(),
                'status'    => 'active', // atau 'pending' kalau mau disetujui admin dulu
            ]);
        }

        // Kalau pakai status approval di user, kamu bisa tambahkan pengecekan:
        if ($user->status !== 'active') {
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum aktif. Silakan menunggu persetujuan admin.');
        }

        Auth::login($user, true);

        return redirect()->route('dashboard'); // sesuaikan nama route dashboard kamu
    }
}
