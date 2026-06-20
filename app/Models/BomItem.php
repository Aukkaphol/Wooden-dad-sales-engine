<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'material_id',
        'qty_required',
        'quantity',
        'waste_percent',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'qty_required' => 'decimal:3',
            'waste_percent' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function getRequiredQuantityAttribute(): float
    {
        return (float) ($this->qty_required ?: $this->quantity);
    }
}
