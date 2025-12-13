<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;
    
    // ... (Fillable lainnya) ...

    // =======================================================
    //              RELASI KEGIATAN & PUBLIKASI (KOREKSI TERAKHIR)
    // =======================================================
    
    // 1. Kegiatan yang Diketuai (ResearchProject)
    public function kegiatanDiketuai(): HasMany
    {
        // KOREKSI ERROR 1: Menggunakan 'ketua_id' karena ResearchProject.php menggunakan 'ketua_id'
        // Local Key: 'user_id' (ID User Dosen)
        return $this->hasMany(ResearchProject::class, 'ketua_id', 'user_id'); 
    }

    // 2. Kegiatan yang Diikuti (Melalui ProjectMember)
    public function anggotaProyek(): HasMany
    {
        // KOREKSI: ProjectMember adalah tabel pivot yang menyimpan user_id
        return $this->hasMany(ProjectMember::class, 'user_id', 'user_id');
    }
    
    // 3. Publikasi
 public function publikasi()
{
    return $this->hasMany(Publication::class, 'owner_id', 'user_id');
}


    
    // ... (Relasi lainnya)
}
