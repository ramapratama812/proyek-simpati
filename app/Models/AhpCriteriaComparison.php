<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpCriteriaComparison extends Model
{
    use HasFactory;

    protected $table = 'ahp_criteria_comparisons';

    protected $fillable = [
        'row_criteria_id',
        'col_criteria_id',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public function rowCriteria()
    {
        return $this->belongsTo(AhpCriteria::class, 'row_criteria_id');
    }

    public function colCriteria()
    {
        return $this->belongsTo(AhpCriteria::class, 'col_criteria_id');
    }
}
