<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

// Mail notifikasi yang sudah kita pakai di flow permohonan
use App\Mail\NewRegistrationRequestMail;
use App\Mail\RegistrationReceivedMail;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 🔹 Validasi input form register lama
        $validated = $request->validate([
            'name'                  => [
                'required', 'string', 'max:255'
            ],
            'username'              => [
                'required', 'string', 'max:255',
                'unique:users,username',
                'unique:registration_requests,username',
            ],
            'email'                 => [
                'required', 'email', 'max:255',
                'unique:users,email',
                'unique:registration_requests,email',
            ],
            'password'              => [
                'required', 'string', 'min:8', 'confirmed'
            ],
            'role'                  => [
                'required', Rule::in(['dosen', 'mahasiswa'])
            ],

            // NIM / NIDN-NIP, wajib tergantung role
            'nidn'                  => [
                'nullable', 'string', 'max:20', 'required_if:role,dosen'
            ],
            'nim'                   => [
                'nullable', 'string', 'max:20', 'required_if:role,mahasiswa'
            ],
            ], [
                'nidn.required_if'      => 'NIDN/NIP wajib diisi untuk dosen.',
                'nim.required_if'       => 'NIM wajib diisi untuk mahasiswa.',
            ]);

        // 🔹 Satukan NIM / NIDN ke satu field "identity"
        $identity = $validated['role'] === 'mahasiswa'
            ? $validated['nim']
            : $validated['nidn'];

        // 🔹 Pastikan identitas belum pernah dipakai di permohonan lain
        if (RegistrationRequest::where('identity', $identity)->exists()) {
            $field = $validated['role'] === 'mahasiswa' ? 'nim' : 'nidn';

            return back()
                ->withErrors([
                    $field => 'NIM atau NIDN/NIP ini sudah digunakan.',
                ])
                ->withInput();
        }

        // 🔹 Simpan sebagai permohonan pendaftaran (BUKAN langsung buat user)
        $req = RegistrationRequest::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'username'  => $validated['username'],
            'identity'  => $identity,                          // NIM atau NIDN/NIP
            'role'      => $validated['role'],
            'password'  => Hash::make($validated['password']), // simpan sudah di-hash
            'status'    => 'pending',
            'note'      => null,
            'google_id' => null,
        ]);

        // 🔹 Kirim email ke semua admin (notifikasi permohonan baru)
        $adminEmails = User::where('role', 'admin')
            ->pluck('email')
            ->filter()
            ->unique()
            ->all();

        foreach ($adminEmails as $adminEmail) {
            Mail::to($adminEmail)->send(new NewRegistrationRequestMail($req));
        }

        // 🔹 Kirim email ke pemohon ("permohonan kamu sudah diterima")
        Mail::to($req->email)->send(new RegistrationReceivedMail($req));

        // 🔹 Jangan auto-login; arahkan balik ke halaman login
        return redirect()
            ->route('login')
            ->with('status', 'Permohonan pendaftaran akun kamu sudah dikirim dan menunggu persetujuan admin.');
    }
}
