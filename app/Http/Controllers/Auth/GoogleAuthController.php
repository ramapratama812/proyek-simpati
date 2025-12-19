<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

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
        } catch (InvalidStateException $e) {
            Log::warning('Google OAuth state mismatch (session hilang)', [
                'host' => $request->getHost(),
                'full_url' => $request->fullUrl(),
                'app_url' => config('app.url'),
            ]);

            return redirect()->route('login')->with(
                'error',
                'Sesi login Google hilang.'
            );
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        $email = strtolower($googleUser->getEmail() ?? '');
        $name  = $googleUser->getName() ?? 'Pengguna';
        $googleId = $googleUser->getId();

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user) {
            Auth::login($user, true);
            $request->session()->regenerate();

            return $this->redirectByRole($user);
        }

        // 🔹 Cek apakah ada permohonan pendaftaran yang pending/approved (belum jadi user tapi sudah request)
        //    Jika ada dan status != rejected, tolak login/register ulang.
        $existingReq = \App\Models\RegistrationRequest::where('email', $email)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingReq) {
            return redirect()->route('login')->with('error', 'Permohonan pendaftaran akun Anda sedang diproses (atau sudah disetujui). Silakan tunggu konfirmasi admin.');
        }

        $domain = substr(strrchr($email, "@"), 1);

        if ($domain === 'politala.ac.id') {
            $role = 'dosen';
        } elseif ($domain === 'mhs.politala.ac.id') {
            $role = 'mahasiswa';
        } else {
            return redirect()->route('login')->with('error', 'Gunakan email Politala untuk login.');
        }

        session([
            'google_user' => [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'google_id' => $googleId,
            ]
        ]);

        return redirect()->route('register.google.complete');
    }

    protected function redirectByRole(User $user)
    {
        $role = strtolower($user->role ?? '');

        if (in_array($role, ['admin','dosen','mahasiswa'])) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->with('error', 'Role akun tidak dikenali.');
    }
}
