<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'waiting' => 'รอผลิต',
        'cutting' => 'กำลังตัดไม้',
        'assembling' => 'กำลังประกอบ',
        'sanding' => 'กำลังขัด',
        'painting' => 'กำลังพ่นสี',
        'ready_delivery' => 'พร้อมส่งมอบ',
        'delivered' => 'ส่งมอบแล้ว',
    ];

    protected $fillable = [
        'lead_id',
        'quotation_id',
        'production_order_number',
        'status',
        'delivery_date',
        'installation_date',
        'installation_status',
        'delivery_address',
        'material_cost',
        'labor_cost',
        'delivery_cost',
        'total_cost',
        'gross_margin',
        'materials_reserved_at',
        'materials_consumed_at',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'installation_date' => 'date',
            'material_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'delivery_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'gross_margin' => 'decimal:2',
            'materials_reserved_at' => 'datetime',
            'materials_consumed_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function craftsmen(): BelongsToMany
    {
        return $this->belongsToMany(Craftsman::class)->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}
