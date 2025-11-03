<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ProjectValidation untuk tracking validasi proyek
 * File: app/Models/ProjectValidation.php
 */
class ProjectValidation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_validations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'validated_by',
        'status',
        'notes',
        'approval_letter',
        'validated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the validation.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    /**
     * Get the admin who validated.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Scope untuk mendapatkan validasi approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope untuk mendapatkan validasi rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope untuk mendapatkan validasi yang memerlukan revisi
     */
    public function scopeRevision($query)
    {
        return $query->where('status', 'revision');
    }

    /**
     * Check if validation is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if validation is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if validation needs revision
     */
    public function needsRevision(): bool
    {
        return $this->status === 'revision';
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        $statusMap = [
            'pending' => 'Menunggu',
            'revision' => 'Perlu Revisi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classMap = [
            'pending' => 'bg-warning',
            'revision' => 'bg-info',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
        ];

        return $classMap[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get parsed notes if JSON
     */
    public function getParsedNotesAttribute()
    {
        // Try to decode JSON notes (for revision points)
        $decoded = json_decode($this->notes, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $this->notes;
    }
}
