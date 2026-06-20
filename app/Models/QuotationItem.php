<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_name',
        'product_name',
        'description',
        'qty',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->item_name ?: $this->product_name;
    }

    public function getDisplayQuantityAttribute(): float
    {
        return (float) ($this->qty ?: $this->quantity);
    }

    public function getDisplayTotalAttribute(): float
    {
        return (float) ($this->total_price ?: $this->subtotal);
    }
}
