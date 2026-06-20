<?php

namespace App\Services;

use App\Models\FacebookLead;
use App\Models\FacebookSetting;
use App\Models\FacebookWebhookLog;
use App\Models\Lead;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookLeadService
{
    public function __construct(
        private readonly FacebookService $facebookService,
        private readonly LineNotificationService $lineNotificationService,
    ) {}

    public function handleLeadgen(array $event, FacebookWebhookLog $log, FacebookSetting $setting): void
    {
        if (! $setting->lead_ads_enabled) {
            $log->update(['status' => 'skipped', 'error_message' => 'ยังไม่ได้เปิดใช้งาน Facebook Lead Ads']);
            return;
        }

        $leadgenId = data_get($event, 'leadgen_id');

        if (! $leadgenId) {
            $log->update(['status' => 'failed', 'error_message' => 'ไม่พบ leadgen_id']);
            return;
        }

        $leadData = $this->facebookService->getLeadData((string) $leadgenId, $setting);

        if (! $leadData) {
            $log->update(['status' => 'failed', 'error_message' => 'ดึงข้อมูล Lead จาก Facebook Graph API ไม่สำเร็จ']);
            return;
        }

        try {
            $mapped = $this->mapLeadFields($leadData);
            $crmLead = $this->createCrmLead($mapped);

            FacebookLead::updateOrCreate(
                ['facebook_lead_id' => (string) $leadgenId],
                [
                    'page_id' => data_get($event, 'page_id'),
                    'form_id' => data_get($event, 'form_id') ?: data_get($leadData, 'form_id'),
                    'form_name' => data_get($leadData, 'form_name'),
                    'full_name' => $mapped['full_name'],
                    'phone' => $mapped['phone'],
                    'email' => $mapped['email'],
                    'province' => $mapped['province'],
                    'budget' => $mapped['budget'],
                    'room_type' => $mapped['room_type'],
                    'room_size' => $mapped['room_size'],
                    'raw_payload' => $leadData,
                    'crm_lead_id' => $crmLead->id,
                    'status' => 'converted',
                ]
            );

            $this->lineNotificationService->notifyFacebookLead($crmLead, $mapped['email']);
            $log->update(['status' => 'processed']);
        } catch (Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            Log::warning('Facebook leadgen processing failed.', ['error' => $exception->getMessage(), 'event' => $event]);
        }
    }

    public function handleMessenger(array $event, FacebookWebhookLog $log, FacebookSetting $setting): void
    {
        if (! $setting->messenger_enabled) {
            $log->update(['status' => 'logged', 'error_message' => 'ยังไม่ได้เปิดใช้งาน Messenger']);
            return;
        }

        try {
            $senderId = data_get($event, 'sender.id');
            $text = data_get($event, 'message.text') ?: data_get($event, 'postback.payload') ?: 'Messenger event';

            if (! $senderId) {
                $log->update(['status' => 'logged', 'error_message' => 'ไม่พบ sender id']);
                return;
            }

            $lead = Lead::query()
                ->where('source', 'facebook_messenger')
                ->where('admin_notes', 'like', '%Sender ID: '.$senderId.'%')
                ->first();

            if (! $lead) {
                Lead::create([
                    'name' => 'Facebook User',
                    'phone' => null,
                    'email' => null,
                    'source' => 'facebook_messenger',
                    'source_platform' => 'facebook',
                    'source_channel' => 'facebook_messenger',
                    'province' => null,
                    'budget' => null,
                    'room_type' => null,
                    'room_size' => null,
                    'room_width' => null,
                    'room_length' => null,
                    'message' => $text,
                    'status' => 'new_lead',
                    'lead_status' => 'new_lead',
                    'quotation_status' => 'not_started',
                    'admin_notes' => "Facebook Messenger\nSender ID: {$senderId}\nข้อความล่าสุด: {$text}",
                ]);
            } else {
                $lead->update([
                    'message' => $text,
                    'admin_notes' => trim((string) $lead->admin_notes)."\nข้อความล่าสุด: {$text}",
                ]);
            }

            $log->update(['status' => 'processed']);
        } catch (Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            Log::warning('Facebook messenger processing failed.', ['error' => $exception->getMessage(), 'event' => $event]);
        }
    }

    private function createCrmLead(array $mapped): Lead
    {
        $message = collect([
            'ที่มา: Facebook Lead Ads',
            'อีเมล: '.($mapped['email'] ?: '-'),
            'ประเภทห้อง: '.($mapped['room_type'] ?: '-'),
            'ขนาดห้อง: '.($mapped['room_size'] ?: '-'),
        ])->implode("\n");

        return Lead::create([
            'name' => $mapped['full_name'] ?: 'Facebook Lead',
            'phone' => $mapped['phone'],
            'email' => $mapped['email'],
            'source' => 'facebook_lead_ads',
            'source_platform' => 'facebook',
            'source_channel' => 'facebook_lead_ads',
            'province' => $mapped['province'],
            'budget' => $mapped['budget'],
            'room_type' => $mapped['room_type'],
            'room_size' => $mapped['room_size'],
            'room_width' => null,
            'room_length' => null,
            'message' => $message,
            'status' => 'new_lead',
            'lead_status' => 'new_lead',
            'quotation_status' => 'not_started',
            'admin_notes' => $message,
        ]);
    }

    private function mapLeadFields(array $leadData): array
    {
        $fields = collect($leadData['field_data'] ?? [])
            ->mapWithKeys(function (array $field): array {
                $name = (string) ($field['name'] ?? '');
                $value = Arr::first($field['values'] ?? []) ?? null;

                return [$name => $value];
            });

        return [
            'full_name' => $fields->get('full_name') ?: $fields->get('name') ?: $fields->get('ชื่อ') ?: null,
            'phone' => $fields->get('phone_number') ?: $fields->get('phone') ?: $fields->get('เบอร์โทร') ?: null,
            'email' => $fields->get('email') ?: null,
            'province' => $fields->get('province') ?: $fields->get('จังหวัด') ?: null,
            'budget' => $fields->get('budget') ?: $fields->get('งบประมาณ') ?: null,
            'room_type' => $fields->get('room_type') ?: $fields->get('ประเภทห้อง') ?: null,
            'room_size' => $fields->get('room_size') ?: $fields->get('ขนาดห้อง') ?: null,
        ];
    }
}
