<p>Yth. Admin SIMPATI,</p>

<p>Telah masuk permohonan pendaftaran akun SIMPATI dengan data berikut:</p>

<ul>
    <li>Nama: {{ $requestData->name }}</li>
    <li>Email: {{ $requestData->email }}</li>
    <li>Role: {{ ucfirst($requestData->role) }}</li>
    <li>Diajukan pada: {{ $requestData->created_at->format('d-m-Y H:i') }}</li>
</ul>

<p>Silakan masuk ke dasbor admin untuk memvalidasi permohonan ini.</p>
