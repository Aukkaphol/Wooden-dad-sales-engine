<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'ฉบับร่าง',
        'sent' => 'ส่งให้ลูกค้าแล้ว',
        'approved' => 'อนุมัติแล้ว',
        'rejected' => 'ไม่อนุมัติ',
        'expired' => 'หมดอายุ',
    ];

    protected $fillable = [
        'lead_id',
        'quotation_no',
        'quotation_number',
        'customer_name',
        'phone',
        'province',
        'project_name',
        'status',
        'subtotal',
        'discount',
        'shipping_cost',
        'deposit_amount',
        'grand_total',
        'valid_until',
        'remark',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'valid_until' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function productionOrder(): HasOne
    {
        return $this->hasOne(ProductionOrder::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getDisplayNumberAttribute(): string
    {
        return $this->quotation_no ?: $this->quotation_number;
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->grand_total - (float) $this->deposit_amount);
    }
}
