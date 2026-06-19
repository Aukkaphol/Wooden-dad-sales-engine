<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'ฉบับร่าง',
        'sent' => 'ส่งให้ผู้จำหน่ายแล้ว',
        'partial_received' => 'รับเข้าบางส่วน',
        'completed' => 'รับครบแล้ว',
        'cancelled' => 'ยกเลิก',
    ];

    protected $fillable = [
        'po_number',
        'supplier_id',
        'purchase_requisition_id',
        'order_date',
        'expected_delivery_date',
        'total_cost',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery_date' => 'date',
            'total_cost' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
