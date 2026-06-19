<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FurnitureSetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'full_description',
        'start_price',
        'image_path',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'start_price' => 'decimal:2',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }
}
