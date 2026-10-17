<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'slug',
        'developer_id',
        'province',
        'city',
        'district',
        'address',
        'latitude',
        'longitude',
        'type',
        'price',
        'land_area',
        'building_area',
        'bedrooms',
        'bathrooms',
        'certificate',
        'electricity',
        'specifications',
        'description',
        'status',
        'is_published',
        'valid_until',
        'external_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'land_area' => 'integer',
        'building_area' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_published' => 'boolean',
        'valid_until' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {
            if (empty($unit->slug)) {
                $unit->slug = Str::slug($unit->title) . '-' . strtolower($unit->code);
            }
        });
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(UnitPhoto::class)->orderBy('sort_order', 'asc');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(UnitPhoto::class)->where('is_primary', true);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(UnitStatusHistory::class)->latest();
    }

    public function availabilityRequests(): HasMany
    {
        return $this->hasMany(AvailabilityRequest::class)->latest();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $primary = $this->photos()->where('is_primary', true)->first();
        if ($primary) {
            return $primary->url;
        }

        $first = $this->photos()->first();
        if ($first) {
            return $first->url;
        }

        return 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
    }
}
