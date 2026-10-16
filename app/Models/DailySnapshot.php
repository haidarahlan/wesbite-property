<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'total_leads',
        'new_leads',
        'surveys_scheduled',
        'surveys_completed',
        'units_available',
        'units_booked',
        'units_sold',
        'total_portfolio_value',
        'metrics_breakdown',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'total_portfolio_value' => 'decimal:2',
        'metrics_breakdown' => 'array',
    ];
}
