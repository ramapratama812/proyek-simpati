<p>Halo {{ optional($publication->owner)->name }},</p>

@php
    $status = $publication->validation_status ?? $publication->status ?? '-';
@endphp

<p>
    Status publikasi <strong>{{ $publication->judul }}</strong> telah diperbarui menjadi:
    <strong>{{ strtoupper($status) }}</strong>.
</p>

@if(!empty($publication->validation_note))
    <p>Catatan dari Admin:</p>
    <p>{{ $publication->validation_note }}</p>
@endif

<p>Silakan masuk ke SIMPATI untuk melihat detail publikasi.</p>
<p>Salam,<br>
Tim SIMPATI</p>
