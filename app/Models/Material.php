<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'unit',
        'current_stock',
        'minimum_stock',
        'reserved_stock',
        'low_stock_level',
        'cost_price',
        'unit_cost',
        'supplier_name',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:3',
            'minimum_stock' => 'decimal:3',
            'reserved_stock' => 'decimal:3',
            'low_stock_level' => 'decimal:3',
            'cost_price' => 'decimal:2',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getMinimumStockValueAttribute(): float
    {
        return (float) ($this->minimum_stock ?: $this->low_stock_level);
    }

    public function getCostPriceValueAttribute(): float
    {
        return (float) ($this->cost_price ?: $this->unit_cost);
    }

    public function getStockStatusAttribute(): string
    {
        if ((float) $this->current_stock <= 0) {
            return 'หมด';
        }

        if ((float) $this->current_stock <= $this->minimum_stock_value) {
            return 'ใกล้หมด';
        }

        return 'ปกติ';
    }
}
