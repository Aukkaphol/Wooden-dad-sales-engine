<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'quotation_item_id',
        'item_name',
        'description',
        'qty',
        'unit',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function quotationItem(): BelongsTo
    {
        return $this->belongsTo(QuotationItem::class);
    }
}
