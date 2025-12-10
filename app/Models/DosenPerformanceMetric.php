<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dosen;
use App\Models\User;

class DosenPerformanceMetric extends Model
{
    use HasFactory;

    protected $table = 'dosen_performance_metrics';

    protected $fillable = [
        'user_id', // NOTE: ini sebenarnya menunjuk ke dosens.id
        'tahun',
        'sinta_score',
        'sinta_score_3yr',
        'jumlah_hibah',
        'publikasi_scholar_1th',
        'jumlah_penelitian',
        'jumlah_p3m',
        'jumlah_publikasi',
        'skor_akhir',
        'peringkat',
    ];

    protected $casts = [
        'tahun'          => 'integer',
        'sinta_score'    => 'float',
        'sinta_score_3yr'=> 'float',
        'skor_akhir'     => 'float',
    ];

    // FK user_id -> dosens.id
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'user_id');
    }

    // relasi ke users melalui tabel dosens
    public function user()
    {
        return $this->hasOneThrough(
            User::class,  // model akhir
            Dosen::class, // model perantara
            'id',         // kunci di tabel dosens yang dirujuk oleh metrics.user_id
            'id',         // kunci di tabel users yang dirujuk oleh dosens.user_id
            'user_id',    // kolom di metrics yang mengarah ke dosens.id
            'user_id'     // kolom di dosens yang mengarah ke users.id
        );
    }
}
