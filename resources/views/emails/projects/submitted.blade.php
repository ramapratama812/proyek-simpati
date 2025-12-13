<p>Yth. Admin SIMPATI,</p>

<p>Telah diajukan kegiatan baru:</p>

<ul>
    <li>Judul: {{ $project->judul }}</li>
    <li>Ketua: {{ optional($project->ketua)->name }}</li>
    <li>Jenis: {{ $project->jenis ?? '-' }}</li>
    <li>Tahun: {{ $project->tahun ?? '-' }}</li>
</ul>

<p>Silakan lakukan validasi pada menu Kelola Kegiatan di dasbor admin.</p>
