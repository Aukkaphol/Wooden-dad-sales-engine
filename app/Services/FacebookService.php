<?php

namespace App\Services;

use App\Models\FacebookSetting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookService
{
    public function testConnection(?FacebookSetting $setting = null): array
    {
        $setting ??= FacebookSetting::current();

        if (! $setting->page_access_token) {
            return ['ok' => false, 'message' => 'ยังไม่ได้ตั้งค่า Page Access Token'];
        }

        try {
            $response = $this->sendGraphRequest('/me', [
                'fields' => 'id,name',
            ], $setting);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'ok' => true,
                    'message' => 'เชื่อมต่อ Facebook สำเร็จ: '.($data['name'] ?? $data['id'] ?? 'Facebook Page'),
                    'data' => $data,
                ];
            }

            return [
                'ok' => false,
                'message' => 'เชื่อมต่อ Facebook ไม่สำเร็จ: '.$response->body(),
            ];
        } catch (Throwable $exception) {
            Log::warning('Facebook test connection failed.', ['error' => $exception->getMessage()]);

            return ['ok' => false, 'message' => 'เชื่อมต่อ Facebook ไม่สำเร็จ: '.$exception->getMessage()];
        }
    }

    public function getLeadData(string $leadgenId, ?FacebookSetting $setting = null): ?array
    {
        $setting ??= FacebookSetting::current();

        if (! $setting->page_access_token) {
            Log::warning('Facebook lead fetch skipped: missing page access token.', ['leadgen_id' => $leadgenId]);

            return null;
        }

        try {
            $response = $this->sendGraphRequest('/'.$leadgenId, [
                'fields' => 'id,created_time,field_data,form_id,ad_id,ad_name,campaign_id,campaign_name',
            ], $setting);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Facebook lead fetch failed.', [
                'leadgen_id' => $leadgenId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Facebook lead fetch exception.', ['leadgen_id' => $leadgenId, 'error' => $exception->getMessage()]);
        }

        return null;
    }

    public function sendGraphRequest(string $endpoint, array $query = [], ?FacebookSetting $setting = null): Response
    {
        $setting ??= FacebookSetting::current();

        return Http::timeout(10)
            ->acceptJson()
            ->get('https://graph.facebook.com/v19.0'.'/'.ltrim($endpoint, '/'), $query + [
                'access_token' => $setting->page_access_token,
            ]);
    }
}
