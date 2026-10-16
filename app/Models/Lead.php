<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_code',
        'name',
        'phone',
        'unit_id',
        'source',
        'status',
        'assigned_to',
        'notes',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class)->latest();
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class)->latest('survey_date');
    }
}
