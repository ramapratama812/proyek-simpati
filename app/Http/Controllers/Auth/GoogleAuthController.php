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
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        $email = $googleUser->getEmail();
        $name  = $googleUser->getName();
        $googleId = $googleUser->getId();

        $user = User::where('email', $email)->first();

        // Jika user sudah ada, langsung login
        if ($user) {
            Auth::login($user);

            return $this->redirectByRole($user);
        }

        // Jika belum ada, tentukan role otomatis berdasarkan domain
        $domain = substr(strrchr($email, "@"), 1);
        $role = null;

        if ($domain === 'politala.ac.id') {
            $role = 'dosen';
        } elseif ($domain === 'mhs.politala.ac.id') {
            $role = 'mahasiswa';
        }

        // Jika domain tidak dikenali, kembalikan ke login
        if (!$role) {
            return redirect()->route('login')->with('error', 'Gunakan email Politala untuk login.');
        }

        // Simpan data sementara di session untuk melengkapi registrasi
        session([
            'google_user' => [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'google_id' => $googleId,
            ]
        ]);

        // Arahkan ke form pendaftaran tambahan
        return redirect()->route('register.google.complete');
    }

    protected function redirectByRole(User $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard');
            case 'dosen':
                return redirect()->route('dashboard');
            case 'mahasiswa':
                return redirect()->route('dashboard');
            default:
                return redirect()->route('login');
        }
    }
}
