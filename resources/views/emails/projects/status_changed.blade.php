<p>Halo {{ optional($project->ketua)->name }},</p>

<p>
    Status kegiatan <strong>{{ $project->judul }}</strong> telah diperbarui menjadi:
    <strong>{{ strtoupper($project->validation_status) }}</strong>.
</p>

@if(!empty($project->validation_note))
<p>Catatan dari Admin:</p>
<p>{{ $project->validation_note }}</p>
@endif

<p>Silakan masuk ke SIMPATI untuk melihat detail lengkap kegiatan.</p>
<p>Salam,<br>
Tim SIMPATI</p>
