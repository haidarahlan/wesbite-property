<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySection extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'content',
        'image_path',
        'extra_data',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];
}
