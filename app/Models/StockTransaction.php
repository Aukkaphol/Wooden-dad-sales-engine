<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    public const TYPES = [
        'receive' => 'รับเข้าคลัง',
        'adjust' => 'ปรับยอดสต็อก',
        'reserve' => 'จองใช้ผลิต',
        'consume' => 'ตัดใช้ผลิต',
    ];

    protected $fillable = [
        'material_id',
        'production_order_id',
        'type',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_cost' => 'decimal:2',
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
}
