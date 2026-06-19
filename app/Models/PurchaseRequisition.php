<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'ฉบับร่าง',
        'waiting_approval' => 'รออนุมัติ',
        'approved' => 'อนุมัติแล้ว',
        'rejected' => 'ไม่อนุมัติ',
        'converted_to_po' => 'แปลงเป็นใบสั่งซื้อแล้ว',
    ];

    protected $fillable = [
        'pr_number',
        'request_date',
        'requested_by',
        'reason',
        'status',
        'production_order_id',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
