<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'nomor_hp',
        'nidn',
        'sinta_id', // nambahin ini buat kolom sinta_id, buat nyimpen SINTA ID dosen
        'nip', // tambahkan ini jika memang ada kolom NIP di tabel
        'status_ikatan_kerja',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'status_aktivitas', // enum: Aktif / Tidak Aktif / Cuti
        'foto',
    ];

    /**
     * Relasi ke tabel users (jika ingin dihubungkan)
     * Asumsi: user.email = dosen.email
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

    public function performanceMetrics()
    {
        return $this->hasMany(DosenPerformanceMetric::class);
    }
}
public function kegiatanDiketuai(): HasMany
    {
      
        return $this->hasMany(ResearchProject::class, 'ketua_id', 'user_id'); 
    }

    public function anggotaProyek(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'user_id', 'user_id');
    }
 public function publikasi()
{
    return $this->hasMany(Publication::class, 'owner_id', 'user_id');
}

