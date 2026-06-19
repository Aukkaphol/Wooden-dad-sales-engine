<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'province',
        'rating',
        'review_text',
        'image_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'rating' => 'integer',
    ];
}
