<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcquisitionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'submitted_by',
        'title',
        'property_type',
        'estimated_price',
        'city',
        'district',
        'address',
        'owner_name',
        'owner_phone',
        'description',
        'status',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
        'converted_unit_id',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function target(): BelongsTo
    {
        return $this->belongsTo(AcquisitionTarget::class, 'target_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function convertedUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'converted_unit_id');
    }
}
