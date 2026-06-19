<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    public const PIPELINE_STATUSES = [
        'new' => 'ลีดใหม่',
        'contacted' => 'ติดต่อแล้ว',
        'designing' => 'กำลังออกแบบ',
        'quoted' => 'ส่งใบเสนอราคาแล้ว',
        'deposit_paid' => 'รับมัดจำแล้ว',
        'production' => 'เข้าสู่การผลิต',
        'installation' => 'รอติดตั้ง',
        'completed' => 'ปิดงานแล้ว',
        'lost' => 'ยกเลิก/ไม่สำเร็จ',
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
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
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
        return self::PIPELINE_STATUSES[$this->lead_status] ?? ucfirst((string) $this->lead_status);
    }

    public function getQuotationStatusLabelAttribute(): string
    {
        return self::QUOTATION_STATUSES[$this->quotation_status] ?? ucfirst((string) $this->quotation_status);
    }
}
