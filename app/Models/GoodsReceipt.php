<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'purchase_order_item_id',
        'material_id',
        'receive_date',
        'ordered_quantity',
        'received_quantity',
        'remaining_quantity',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'receive_date' => 'date',
            'ordered_quantity' => 'decimal:3',
            'received_quantity' => 'decimal:3',
            'remaining_quantity' => 'decimal:3',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
