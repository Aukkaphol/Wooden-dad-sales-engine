<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_lead_id',
        'page_id',
        'form_id',
        'form_name',
        'full_name',
        'phone',
        'email',
        'province',
        'budget',
        'room_type',
        'room_size',
        'raw_payload',
        'crm_lead_id',
        'status',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function crmLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'crm_lead_id');
    }
}
