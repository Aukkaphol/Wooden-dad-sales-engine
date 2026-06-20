<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LineNotificationLog;
use App\Models\LineSetting;
use App\Models\ProductionOrder;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LineNotificationService
{
    public function notifyNewLead(Lead $lead): void
    {
        $this->sendToAdmin('lead_created', "🪵 ลูกค้าใหม่จากเว็บไซต์\n\n"
            ."ชื่อ: {$lead->name}\n"
            ."เบอร์: {$lead->phone}\n"
            ."จังหวัด: {$lead->province}\n"
            ."งบประมาณ: {$lead->budget}\n\n"
            ."เปิดดู Lead: ".$this->routeUrl('admin.leads.show', $lead), $lead);
    }

    public function notifyFacebookLead(Lead $lead, ?string $email = null): void
    {
        $this->sendToAdmin('facebook_lead_created', "📘 Lead ใหม่จาก Facebook\n\n"
            ."ชื่อ: {$lead->name}\n"
            ."เบอร์: ".($lead->phone ?: '-')."\n"
            ."อีเมล: ".($email ?: $lead->email ?: '-')."\n"
            ."จังหวัด: ".($lead->province ?: '-')."\n"
            ."งบประมาณ: ".($lead->budget ?: '-')."\n\n"
            ."เปิดดู Lead:\n".$this->routeUrl('admin.leads.show', $lead), $lead);
    }

    public function notifyQuotationCreated(Quotation $quotation): void
    {
        $quotation->loadMissing('lead');

        $this->sendToAdmin('quotation_created', "🧾 สร้างใบเสนอราคาใหม่\n\n"
            ."เลขที่: {$quotation->display_number}\n"
            ."ลูกค้า: {$quotation->lead->name}\n"
            ."ยอดรวม: ฿".number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2)."\n"
            ."สถานะ: {$quotation->status_label}\n\n"
            ."เปิดดูใบเสนอราคา:\n".$this->routeUrl('admin.quotations.show', $quotation), $quotation);
    }

    public function notifyQuotationApproved(Quotation $quotation): void
    {
        $quotation->loadMissing(['lead', 'productionOrder']);
        $productionOrder = $quotation->productionOrder;

        $this->sendToAdmin('quotation_approved', "📋 ลูกค้าอนุมัติใบเสนอราคา\n\n"
            ."เลขที่:\n{$quotation->display_number}\n\n"
            ."ลูกค้า:\n{$quotation->lead->name}\n\n"
            ."ยอดรวม:\n฿".number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2)."\n\n"
            ."ระบบสร้างใบสั่งผลิตแล้ว\n"
            .($productionOrder?->production_order_number ?? '-')."\n\n"
            ."เปิดดูงานผลิต:\n".($productionOrder ? $this->routeUrl('admin.production.show', $productionOrder) : $this->routeUrl('admin.quotations.show', $quotation)), $quotation);
    }

    public function notifyProductionStarted(ProductionOrder $productionOrder): void
    {
        $productionOrder->loadMissing('lead');

        $this->sendToProduction('production_started', "🏭 เริ่มงานผลิตแล้ว\n\n"
            ."เลข PO: {$productionOrder->production_order_number}\n"
            ."ลูกค้า: {$productionOrder->lead->name}\n"
            ."สถานะ: {$productionOrder->status_label}\n"
            ."เปิดดูงาน: ".$this->routeUrl('admin.production.show', $productionOrder), $productionOrder);
    }

    public function notifyProductionReady(ProductionOrder $productionOrder): void
    {
        $productionOrder->loadMissing('lead');

        $this->sendToProduction('production_ready', "🚚 งานผลิตพร้อมส่งแล้ว\n\n"
            ."เลข PO: {$productionOrder->production_order_number}\n"
            ."ลูกค้า: {$productionOrder->lead->name}\n"
            ."เบอร์: {$productionOrder->lead->phone}\n"
            ."วันที่นัดส่ง: ".($productionOrder->delivery_date?->format('d/m/Y') ?? '-')."\n\n"
            ."เปิดดูงาน:\n".$this->routeUrl('admin.production.show', $productionOrder), $productionOrder);
    }

    public function notifyMaterialShortage(ProductionOrder $productionOrder, string $materialName, float $shortageQty, string $unit, string $prNo): void
    {
        $this->sendToProduction('material_shortage', "⚠️ วัสดุไม่เพียงพอ\n\n"
            ."เลข PO:\n{$productionOrder->production_order_number}\n\n"
            ."วัสดุ:\n{$materialName}\n\n"
            ."ขาดจำนวน:\n".number_format($shortageQty, 3)." {$unit}\n\n"
            ."ระบบสร้าง PR แล้ว:\n{$prNo}", $productionOrder);
    }

    public function notifyDeliveryScheduled(ProductionOrder $productionOrder): void
    {
        $productionOrder->loadMissing(['lead', 'craftsmen']);
        $team = $productionOrder->craftsmen->pluck('name')->implode(', ') ?: '-';
        $date = $productionOrder->installation_date ?? $productionOrder->delivery_date;

        $this->sendToDelivery('installation_scheduled', "📅 นัดส่ง/ติดตั้งงานแล้ว\n\n"
            ."ลูกค้า: {$productionOrder->lead->name}\n"
            ."วันที่: ".($date?->format('d/m/Y') ?? '-')."\n"
            ."ทีมติดตั้ง: {$team}\n\n"
            ."เปิดดูตารางนัด:\n".$this->routeUrl('admin.installation.index'), $productionOrder);
    }

    public function notifyInstallationCompleted(ProductionOrder $productionOrder): void
    {
        $productionOrder->loadMissing(['lead', 'craftsmen']);
        $team = $productionOrder->craftsmen->pluck('name')->implode(', ') ?: '-';

        $this->sendToDelivery('installation_completed', "🎉 ติดตั้งงานเสร็จเรียบร้อยแล้ว\n\n"
            ."เลข PO: {$productionOrder->production_order_number}\n"
            ."ลูกค้า: {$productionOrder->lead->name}\n"
            ."ทีมติดตั้ง: {$team}\n\n"
            ."เปิดดูงาน:\n".$this->routeUrl('admin.production.show', $productionOrder), $productionOrder);
    }

    private function sendToAdmin(string $event, string $message, ?Model $notifiable = null): void
    {
        $this->send($event, 'admin', $message, company()->line_staff_recipient ?: LineSetting::current()->admin_recipient_id, $notifiable);
    }

    private function sendToProduction(string $event, string $message, ?Model $notifiable = null): void
    {
        $setting = LineSetting::current();
        $this->send($event, 'production', $message, $setting->production_group_id ?: company()->line_staff_recipient ?: $setting->admin_recipient_id, $notifiable);
    }

    private function sendToDelivery(string $event, string $message, ?Model $notifiable = null): void
    {
        $setting = LineSetting::current();
        $this->send($event, 'delivery', $message, $setting->delivery_group_id ?: company()->line_staff_recipient ?: $setting->admin_recipient_id, $notifiable);
    }

    public function sendTestStaffNotification(): void
    {
        $company = company();

        $this->sendToAdmin('test_staff_notification', "✅ ทดสอบ LINE OA สำเร็จ\n\n"
            ."ระบบ: {$company->display_name} ERP\n"
            ."APP_URL: ".config('app.url')."\n"
            ."เวลา: ".now()->format('d/m/Y H:i'));
    }

    private function send(string $event, string $channel, string $message, ?string $recipient, ?Model $notifiable): void
    {
        $setting = LineSetting::current();
        $company = company();
        $accessToken = $setting->channel_access_token ?: $company->line_channel_access_token;
        $baseLog = [
            'event' => $event,
            'channel' => $channel,
            'recipient_id' => $recipient,
            'notifiable_type' => $notifiable ? $notifiable::class : null,
            'notifiable_id' => $notifiable?->getKey(),
            'message' => $message,
        ];

        if (! $setting->notifications_enabled) {
            $this->recordLog($baseLog + ['status' => 'skipped', 'error_message' => 'ปิดการแจ้งเตือน LINE OA']);
            Log::info('LINE notification skipped: notifications disabled.', ['event' => $event, 'channel' => $channel]);

            return;
        }

        if (! $accessToken) {
            $this->recordLog($baseLog + ['status' => 'skipped', 'error_message' => 'ไม่ได้ตั้งค่า Channel Access Token']);
            Log::warning('LINE notification skipped: channel access token is missing.', ['event' => $event, 'channel' => $channel]);

            return;
        }

        if (! $recipient) {
            $this->recordLog($baseLog + ['status' => 'skipped', 'error_message' => 'ไม่ได้ตั้งค่าผู้รับข้อความ']);
            Log::warning('LINE notification skipped: recipient is missing.', ['event' => $event, 'channel' => $channel]);

            return;
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $recipient,
                    'messages' => [
                        ['type' => 'text', 'text' => $message],
                    ],
                ]);

            if ($response->successful()) {
                $this->recordLog($baseLog + ['status' => 'sent', 'response_status' => $response->status()]);

                return;
            }

            $this->recordLog($baseLog + [
                'status' => 'failed',
                'response_status' => $response->status(),
                'error_message' => $response->body(),
            ]);
            Log::warning('LINE notification failed.', ['event' => $event, 'channel' => $channel, 'status' => $response->status()]);
        } catch (Throwable $exception) {
            $this->recordLog($baseLog + ['status' => 'failed', 'error_message' => $exception->getMessage()]);
            Log::warning('LINE notification failed.', [
                'event' => $event,
                'channel' => $channel,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function recordLog(array $payload): void
    {
        try {
            LineNotificationLog::create($payload);
        } catch (Throwable $exception) {
            Log::warning('Unable to record LINE notification log.', ['error' => $exception->getMessage()]);
        }
    }

    private function routeUrl(string $name, mixed $parameters = []): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $path = route($name, $parameters, false);

        return rtrim($baseUrl, '/').$path;
    }
}
