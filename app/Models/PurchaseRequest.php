<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequest extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'ฉบับร่าง',
        'pending' => 'รออนุมัติ',
        'approved' => 'อนุมัติแล้ว',
        'ordered' => 'สั่งซื้อแล้ว',
        'received' => 'รับเข้าแล้ว',
        'cancelled' => 'ยกเลิก',
    ];

    protected $fillable = [
        'pr_no',
        'material_id',
        'production_order_id',
        'requested_qty',
        'reason',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'requested_qty' => 'decimal:3',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
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
