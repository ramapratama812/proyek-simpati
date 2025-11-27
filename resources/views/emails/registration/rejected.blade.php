@component('mail::message')
# Permohonan Akun SIMPATI Ditolak

Halo {{ $request->name }},

Terima kasih atas permohonan pendaftaran akun SIMPATI yang telah kamu ajukan.

Mohon maaf, permohonan akun kamu **belum dapat disetujui** saat ini.

@isset($request->note)
**Catatan dari admin:**
> {{ $request->note }}
@endisset

Jika kamu merasa ini adalah kesalahan atau ingin mengajukan kembali, silakan hubungi admin SIMPATI atau ajukan permohonan baru sesuai ketentuan yang berlaku.

Salam,
Tim SIMPATI
@endcomponent
