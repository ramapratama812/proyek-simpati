<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\AccountApprovedMail;
use App\Models\RegistrationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationReceivedMail;
use App\Mail\RegistrationRejectedMail;
use App\Mail\NewRegistrationRequestMail;

class RegistrationRequestController extends Controller
{
    /**
     * Form permohonan pendaftaran.
     */
    public function create()
    {
        return view('auth.request-register');
    }

    /**
     * Simpan permohonan dan kirim email ke Admin + User.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role'  => ['required', 'in:dosen,mahasiswa'],
        ]);

        $req = RegistrationRequest::create($data);

        // Email ke admin
        $adminEmails = $this->getAdminEmails();

        if (!empty($adminEmails)) {
            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->send(new NewRegistrationRequestMail($req));
            }
        }


        // Email ke user (konfirmasi permohonan diterima)
        Mail::to($req->email)->send(new RegistrationReceivedMail($req));

        return redirect()->route('login')
            ->with('status', 'Permohonan pendaftaran berhasil dikirim. Silakan cek email Anda.');
    }

    /**
     * Daftar permohonan (halaman Admin).
     */
    public function index()
    {
        $this->ensureAdmin();

        $requests = RegistrationRequest::orderByDesc('created_at')->paginate(20);

        return view('admin.registration_requests', compact('requests'));
    }

    /**
     * Admin menyetujui permohonan → buat akun User + kirim email.
     */
    public function approve(RegistrationRequest $registrationRequest)
    {
        $this->ensureAdmin();

        if ($registrationRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        // Buat / update user
        $user = User::firstOrCreate(
            ['email' => strtolower($registrationRequest->email)],
            [
                'name'      => $registrationRequest->name,
                'username'  => Str::before($registrationRequest->email, '@'),
                'password'  => bcrypt(Str::random(12)), // bisa diganti strategi lain
                'role'      => $registrationRequest->role,
                'status'    => 'active',
            ]
        );

        // Tandai request sebagai approved
        $registrationRequest->status = 'approved';
        $registrationRequest->note   = 'Disetujui oleh ' . auth()->user()->name;
        $registrationRequest->save();

        // Email ke user bahwa akun sudah aktif
        Mail::to($user->email)->send(new AccountApprovedMail($user));

        return back()->with('ok', 'Permohonan disetujui dan akun pengguna telah dibuat / diaktifkan.');
    }

    /**
     * Admin menolak permohonan.
     */
    public function reject(RegistrationRequest $registrationRequest, Request $request)
    {
        $this->ensureAdmin();

        if ($registrationRequest->status !== 'pending') {
            return back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'note' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ]);

        $registrationRequest->status = 'rejected';
        $registrationRequest->note   = $data['note'] ?? 'Permohonan ditolak.';
        $registrationRequest->save();

        // Kirim email ke pemohon bahwa permohonan ditolak
        if ($registrationRequest->email) {
            Mail::to($registrationRequest->email)
                ->send(new RegistrationRejectedMail($registrationRequest));
        }

        return back()->with('ok', 'Permohonan ditolak dan email pemberitahuan telah dikirim.');
    }

    /**
     * Ambil semua email admin.
     * Sesuaikan dengan struktur role di SIMPATI (Spatie / kolom role biasa).
     */
    protected function getAdminEmails(): array
    {
        // Jika di tabel users ada kolom 'role'
        return User::where('role', 'admin')
        ->pluck('email')
        ->filter()   // buang yang null/kosong
        ->unique()
        ->values()
        ->all();

        // Jika pakai Spatie Permission:
        // return User::role('admin')->pluck('email')->all();
    }

    protected function ensureAdmin(): void
    {
        $role = strtolower(auth()->user()->role ?? '');
        abort_unless($role === 'admin', 403);
    }
}
