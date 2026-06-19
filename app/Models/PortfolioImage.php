<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'bedroom' => 'ห้องนอน',
        'living_room' => 'ห้องนั่งเล่น',
        'dining_room' => 'ห้องอาหาร',
        'working_room' => 'ห้องทำงาน',
    ];

    protected $fillable = [
        'title',
        'category',
        'image_path',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
