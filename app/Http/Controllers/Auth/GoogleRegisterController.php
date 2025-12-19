<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewRegistrationRequestMail;
use App\Mail\RegistrationReceivedMail;
use App\Models\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class GoogleRegisterController extends Controller
{
    public function showCompleteForm(Request $request)
    {
        $googleData = session('google_user');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sesi login Google tidak ditemukan.');
        }

        return view('auth.google-complete', ['google' => $googleData]);
    }

    public function storeComplete(Request $request)
    {
        $googleData = session('google_user');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sesi login Google tidak ditemukan.');
        }

        // role sudah ditentukan dari proses Google sebelumnya
        $role = $googleData['role'] ?? null;

        $validated = $request->validate([
            'username' => [
                'required', 'string', 'max:50',
                'unique:users,username',
                'unique:registration_requests,username',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // tetap satu field "identity", isinya NIM atau NIDN/NIP tergantung role
            'identity' => [
                'required', 'string', 'max:50',
                'unique:registration_requests,identity',
            ],

            // SINTA ID opsional (relevan kalau role = dosen)
            'sinta_id' => ['nullable', 'string', 'max:50'],
        ]);

        $sintaId = ($role === 'dosen') ? ($validated['sinta_id'] ?? null) : null;

        // 🔹 Cek duplikasi permohonan (status != rejected)
        $existingRequest = RegistrationRequest::where(function ($q) use ($googleData, $validated) {
            $q->where('email', $googleData['email'])
              ->orWhere('identity', $validated['identity']);
        })
        ->where('status', '!=', 'rejected')
        ->first();

        if ($existingRequest) {
            return redirect()->back()
                ->withErrors(['identity' => 'Permohonan dengan Email atau Identitas ini sudah ada dan belum ditolak.'])
                ->withInput();
        }

        $req = RegistrationRequest::create([
            'name'      => $googleData['name'],
            'email'     => $googleData['email'],
            'role'      => $role,
            'identity'  => $validated['identity'],     // NIM / NIDN/NIP
            'username'  => $validated['username'],
            'password'  => Hash::make($validated['password']),
            'status'    => 'pending',
            'google_id' => $googleData['google_id'] ?? null,
            'sinta_id'  => $sintaId,                    // <—— disimpan juga di sini
        ]);

        // Kirim email ke admin
        $admins = User::where('role', 'admin')->pluck('email')->all();
        foreach ($admins as $email) {
            Mail::to($email)->send(new NewRegistrationRequestMail($req));
        }

        // Kirim email ke user
        Mail::to($req->email)->send(new RegistrationReceivedMail($req));

        session()->forget('google_user');

        return redirect()->route('login')->with('status', 'Permohonan pendaftaran akun telah dikirim ke admin.');
    }
}
