<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'product_image',
        'image',
        'name',
        'description',
        'category',
        'unit',
        'selling_price',
        'cost_price',
        'material_cost',
        'labor_cost',
        'hardware_cost',
        'finishing_cost',
        'other_cost',
        'total_cost',
        'profit_amount',
        'profit_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'material_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'hardware_cost' => 'decimal:2',
            'finishing_cost' => 'decimal:2',
            'other_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'profit_amount' => 'decimal:2',
            'profit_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(BomItem::class);
    }
}
