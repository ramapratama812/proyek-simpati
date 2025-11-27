<p>Halo {{ $requestData->name }},</p>

<p>Terima kasih telah mendaftar, permohonan pendaftaran akun SIMPATI kamu sudah kami terima.</p>

<p>
    Status saat ini: <strong>{{ strtoupper($requestData->status) }}</strong> (menunggu
    konfirmasi dari Admin).
</p>

<p>
    Kamu akan menerima email kembali ketika Admin telah menyetujui permohonan ini.
</p>

<p>Salam,<br>
Tim SIMPATI</p>
