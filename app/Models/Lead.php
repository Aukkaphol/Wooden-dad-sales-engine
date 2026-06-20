<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    public const PIPELINE_STATUSES = [
        'new_lead' => 'ลีดใหม่',
        'contacted' => 'ติดต่อแล้ว',
        'site_survey' => 'นัดวัดพื้นที่',
        'designing' => 'กำลังออกแบบ',
        'quotation_sent' => 'ส่งใบเสนอราคาแล้ว',
        'negotiation' => 'เจรจาต่อรอง',
        'won' => 'ปิดการขายสำเร็จ',
        'lost' => 'ปิดการขายไม่สำเร็จ',
    ];

    public const SOURCE_LABELS = [
        'website' => 'Website',
        'facebook' => 'Facebook',
        'facebook_lead_ads' => 'Facebook',
        'facebook_messenger' => 'Facebook',
        'line' => 'LINE OA',
        'line_oa' => 'LINE OA',
        'manual' => 'Manual',
    ];

    public const QUOTATION_STATUSES = [
        'not_started' => 'ยังไม่เริ่ม',
        'drafting' => 'กำลังจัดทำ',
        'sent' => 'ส่งให้ลูกค้าแล้ว',
        'approved' => 'ลูกค้าอนุมัติแล้ว',
        'rejected' => 'ไม่อนุมัติ',
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'source',
        'source_platform',
        'source_channel',
        'province',
        'budget',
        'room_type',
        'room_size',
        'room_width',
        'room_length',
        'message',
        'room_image',
        'status',
        'lead_status',
        'quotation_status',
        'follow_up_date',
        'admin_notes',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'campaign_name',
        'ad_name',
        'referrer_url',
        'external_lead_id',
        'channel_payload',
        'raw_payload_json',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'channel_payload' => 'array',
            'raw_payload_json' => 'array',
        ];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->latest();
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class)->latest();
    }

    public function getLeadStatusLabelAttribute(): string
    {
        $status = $this->status ?: $this->lead_status;

        return self::PIPELINE_STATUSES[$status] ?? ucfirst((string) $status);
    }

    public function getSourceLabelAttribute(): string
    {
        $source = $this->source_platform ?: $this->source ?: 'website';

        return self::SOURCE_LABELS[$source] ?? ucfirst((string) $source);
    }

    public function getQuotationStatusLabelAttribute(): string
    {
        return self::QUOTATION_STATUSES[$this->quotation_status] ?? ucfirst((string) $this->quotation_status);
    }
}
