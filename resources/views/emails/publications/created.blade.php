<p>Yth. Admin SIMPATI,</p>

<p>Telah ditambahkan publikasi baru oleh {{ optional($publication->owner)->name }}.</p>

<ul>
    <li>Judul: {{ $publication->judul }}</li>
    <li>Jenis: {{ $publication->jenis ?? '-' }}</li>
    <li>Tahun: {{ $publication->tahun ?? '-' }}</li>
    <li>DOI: {{ $publication->doi ?? '-' }}</li>
</ul>

<p>Silakan cek dan validasi pada menu Kelola Publikasi.</p>
<p>Salam,<br>
Tim SIMPATI</p>
