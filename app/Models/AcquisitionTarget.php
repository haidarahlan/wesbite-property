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
     * Ambil unit yang berhasil terjual (closing) dalam rentang periode target.
     * Mengambil unit berstatus 'Terjual' ATAU unit dari leads yang berhasil 'Closing' dalam rentang target.
     */
    public function soldUnits()
    {
        $start = $this->start_date ? $this->start_date->copy()->startOfDay() : null;
        $end = $this->end_date ? $this->end_date->copy()->endOfDay() : null;

        // Ambil ID unit dari leads yang closing pada periode target
        $closingUnitIdsFromLeads = Lead::where('status', 'Closing')
            ->whereNotNull('unit_id')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->where(function ($sub) use ($start, $end) {
                    $sub->whereBetween('updated_at', [$start, $end])
                        ->orWhereBetween('created_at', [$start, $end]);
                });
            })
            ->pluck('unit_id')
            ->toArray();

        return Unit::where(function ($q) use ($start, $end, $closingUnitIdsFromLeads) {
            $q->where('status', 'Terjual')
              ->orWhereIn('id', $closingUnitIdsFromLeads);
        });
    }

    /**
     * Hitung total unit yang terjual / closing dalam periode ini.
     */
    public function getSoldUnitsCountAttribute(): int
    {
        return $this->soldUnits()->count();
    }

    /**
     * Hitung total nilai transaksi closing dalam periode ini.
     */
    public function getSoldUnitsValueAttribute(): float
    {
        return (float) $this->soldUnits()->sum('price');
    }

    /**
     * Hitung persentase capaian unit terjual terhadap target.
     */
    public function getUnitsProgressPercentageAttribute(): float
    {
        if ($this->target_units <= 0) return 0;
        return min(100, round(($this->sold_units_count / $this->target_units) * 100, 1));
    }

    /**
     * Hitung persentase nilai closing terhadap target nilai.
     */
    public function getValueProgressPercentageAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        return min(100, round(($this->sold_units_value / $this->target_value) * 100, 1));
    }
}
