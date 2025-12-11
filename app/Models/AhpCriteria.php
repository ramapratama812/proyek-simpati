<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhpCriteria extends Model
{
    protected $table = 'ahp_criteria';

    protected $fillable = [
        'kode',
        'nama',
        'bobot',
        'is_benefit',
    ];

    public function rowComparisons()
    {
        return $this->hasMany(AhpCriteriaComparison::class, 'row_criteria_id');
    }

    public function colComparisons()
    {
        return $this->hasMany(AhpCriteriaComparison::class, 'col_criteria_id');
    }
}
