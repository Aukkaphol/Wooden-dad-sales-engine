<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'reserved_stock',
        'low_stock_level',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:3',
            'reserved_stock' => 'decimal:3',
            'low_stock_level' => 'decimal:3',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }
}
