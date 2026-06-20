<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'type',
        'reference_type',
        'reference_id',
        'qty',
        'before_stock',
        'after_stock',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
            'before_stock' => 'decimal:3',
            'after_stock' => 'decimal:3',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
