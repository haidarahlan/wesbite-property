<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'unit_id',
        'pic_user_id',
        'survey_date',
        'status',
        'notes',
        'result',
        'reminder_sent_at',
    ];

    protected $casts = [
        'survey_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }
}
