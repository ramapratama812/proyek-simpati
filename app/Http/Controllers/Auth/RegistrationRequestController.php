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
     * Daftar permohonan (halaman Admin).
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        // ambil nilai filter dari query string
        $status = $request->get('status', 'all');   // all | pending | approved | rejected
        $sort   = $request->get('sort', 'latest');  // latest | oldest | name_asc | name_desc

        $query = RegistrationRequest::query();

        // filter status (kecuali "all")
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        // urutkan
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('admin.registration_requests', [
            'requests' => $requests,
            'status'   => $status,
            'sort'     => $sort,
        ]);
    }

    /**
     * Tampilkan detail permohonan.
     */
    public function show(RegistrationRequest $registrationRequest)
    {
        $this->ensureAdmin();
        return view('admin.registration_requests_show', [
            'req' => $registrationRequest
        ]);
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

        $role     = $registrationRequest->role;
        $identity = $registrationRequest->identity;

        // 1) Buat / ambil user berdasarkan email
        $user = User::firstOrCreate(
            ['email' => strtolower($registrationRequest->email)],
            [
                'name'     => $registrationRequest->name,
                'username' => $registrationRequest->username ?? \Illuminate\Support\Str::before($registrationRequest->email, '@'),
                'password' => $registrationRequest->password, // sudah di-hash saat register
                'role'     => $registrationRequest->role,
                'status'   => 'active',
                'nim'      => null,
                'nidn'     => null,
                'sinta_id' => null,
            ]
        );

        // 2) Update kolom identitas sesuai role
        $updateData = [];

        if ($role === 'mahasiswa') {
            // identity = NIM
            $updateData['nim'] = $identity;
        } elseif ($role === 'dosen') {
            // identity = NIDN/NIP
            $updateData['nidn']     = $identity;
            $updateData['sinta_id'] = $registrationRequest->sinta_id;
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        // 3) Tandai request sebagai approved + simpan note
        $registrationRequest->status = 'approved';

        $validatedData = request()->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $registrationRequest->note = $validatedData['note'] ?? null;
        $registrationRequest->save();

        // 4) Email ke user bahwa akun sudah aktif
        Mail::to($user->email)->send(new AccountApprovedMail($user));

        return back()->with('ok', 'Permohonan disetujui dan akun pengguna telah dibuat/diaktifkan.');
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

        // Tambahin note opsional dari admin
        $validatedData = request()->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $registrationRequest->note = $validatedData['note'] ?? null;

        // Simpan perubahan
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
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $role = strtolower($user->role ?? '');
        abort_unless($role === 'admin', 403);
    }
}
