<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcquisitionTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_name',
        'start_date',
        'end_date',
        'target_units',
        'target_value',
        'target_region',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_units' => 'integer',
        'target_value' => 'decimal:2',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(AcquisitionCandidate::class, 'target_id');
    }

    public function approvedCandidates(): HasMany
    {
        return $this->candidates()->where('status', 'Disetujui');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Hitung persentase capaian unit yang disetujui terhadap target.
     */
    public function getUnitsProgressPercentageAttribute(): float
    {
        if ($this->target_units <= 0) return 0;
        $approvedCount = $this->approvedCandidates()->count();
        return min(100, round(($approvedCount / $this->target_units) * 100, 1));
    }

    /**
     * Hitung total nilai estimasi properti yang disetujui.
     */
    public function getAcquiredValueAttribute(): float
    {
        return (float) $this->approvedCandidates()->sum('estimated_price');
    }

    /**
     * Hitung persentase nilai akuisisi terhadap target nilai.
     */
    public function getValueProgressPercentageAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        return min(100, round(($this->acquired_value / $this->target_value) * 100, 1));
    }
}
