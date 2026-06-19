<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LineNotifyService
{
    public function sendNewLead(Lead $lead): void
    {
        $token = config('services.line.channel_access_token');

        if (! $token) {
            Log::warning('LINE notification skipped: LINE_CHANNEL_ACCESS_TOKEN is missing.', [
                'lead_id' => $lead->id,
            ]);

            return;
        }

        $message = $this->newLeadMessage($lead);
        $recipient = config('services.line.group_id') ?: config('services.line.user_id');

        try {
            if ($recipient) {
                $this->pushMessage($token, $recipient, $message);

                return;
            }

            Log::warning('LINE notification recipient missing; using broadcast message.', [
                'lead_id' => $lead->id,
            ]);

            $this->broadcastMessage($token, $message);
        } catch (Throwable $exception) {
            Log::warning('LINE notification failed.', [
                'lead_id' => $lead->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function pushMessage(string $token, string $recipient, string $message): void
    {
        Http::withToken($token)
            ->acceptJson()
            ->post('https://api.line.me/v2/bot/message/push', [
                'to' => $recipient,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $message,
                    ],
                ],
            ])
            ->throw();
    }

    private function broadcastMessage(string $token, string $message): void
    {
        Http::withToken($token)
            ->acceptJson()
            ->post('https://api.line.me/v2/bot/message/broadcast', [
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $message,
                    ],
                ],
            ])
            ->throw();
    }

    private function newLeadMessage(Lead $lead): string
    {
        $detailUrl = rtrim(config('services.line.admin_base_url', 'http://127.0.0.1:8000'), '/')
            ."/admin/leads/{$lead->id}";

        return "🔥 Lead ใหม่ Wooden Dad Design\n\n"
            ."ชื่อ: {$lead->name}\n"
            ."เบอร์: {$lead->phone}\n"
            ."จังหวัด: {$lead->province}\n"
            ."งบประมาณ: {$lead->budget}\n"
            ."ขนาดห้อง: {$lead->room_width} x {$lead->room_length} ม.\n"
            ."ข้อความ: ".($lead->message ?: '-')."\n\n"
            ."ดูรายละเอียด:\n"
            .$detailUrl;
    }
}
