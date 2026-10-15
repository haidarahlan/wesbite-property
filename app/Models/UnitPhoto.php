<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'photo_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->photo_path, 'http')) {
            return $this->photo_path;
        }

        return asset('storage/' . $this->photo_path);
    }
}
