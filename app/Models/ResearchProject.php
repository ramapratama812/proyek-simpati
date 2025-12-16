<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ResearchProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis',
        'judul',
        'kategori_kegiatan',
        'bidang_ilmu',
        'skema',
        'abstrak',
        'surat_proposal',
        'gdrive_proposal_id',      // BARU
        'gdrive_proposal_name',    // BARU
        'gdrive_proposal_mime',    // BARU
        'gdrive_proposal_size',    // BARU
        'gdrive_proposal_view_link', // BARU
        'surat_persetujuan',       // BARU

        'mulai',
        'selesai',
        'sumber_dana',
        'biaya',
        'ketua_id',
        'is_public',
        'external_refs',

        'created_by',
        'tahun_usulan',
        'tahun_pelaksanaan',
        'status',

        'validation_status',       // BARU
        'validation_note',         // BARU
        'validated_by',            // BARU
        'validated_at',            // BARU

        'tkt',
        'mitra_nama',
        'lokasi',
        'nomor_kontrak',
        'tanggal_kontrak',
        'target_luaran',
        'keywords',
        'tautan',
    ];

    protected $casts = [
        'external_refs'   => 'array',
        'is_public'       => 'boolean',
        'mulai'           => 'date',
        'selesai'         => 'date',
        'tanggal_kontrak' => 'date',
        'target_luaran'   => 'array',
        'validated_at'    => 'datetime', // BARU
    ];

    public function ketua()
    {
        return $this->belongsTo(User::class,'ketua_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class,'project_id');
    }

    public function media()
    {
        return $this->hasMany(ResearchProjectMedia::class, 'research_project_id');
    }

    // public function publications(){ return $this->hasMany(Publication::class,'project_id'); }
    public function publications()
    {
        return $this->belongsToMany(\App\Models\Publication::class, 'project_publications', 'project_id', 'publication_id')
                    ->withTimestamps();
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withPivot('peran')
                    ->withTimestamps();
    }

    // method untuk relasi admin yang memvalidasi
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isApproved(): bool
    {
        return $this->validation_status === 'approved';
    }

    public function isLockedForLecturer(): bool
    {
        // aturan: setelah disetujui admin, ketua/pembuat tidak boleh edit/hapus atau tambah publikasi
        return $this->isApproved();
    }

}
