<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'template_message',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
