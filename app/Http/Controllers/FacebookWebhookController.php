<?php

namespace App\Http\Controllers;

use App\Models\FacebookSetting;
use App\Models\FacebookWebhookEvent;
use App\Models\FacebookWebhookLog;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $setting = FacebookSetting::current();
        $mode = $request->query('hub_mode') ?: $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?: $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?: $request->query('hub.challenge');

        if ($mode === 'subscribe' && $setting->facebook_enabled && hash_equals((string) $setting->effective_verify_token, (string) $token)) {
            return response((string) $challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request): Response
    {
        $payload = $request->json()->all() ?: $request->all();

        if ($payload === []) {
            $rawPayload = ltrim($request->getContent(), "\xEF\xBB\xBF");
            $payload = json_decode($rawPayload, true) ?: [];
        }

        $setting = FacebookSetting::current();
        $eventType = $this->detectEventType($payload);
        $leadgenId = $this->findLeadgenId($payload);
        $pageId = data_get($payload, 'entry.0.id');

        $event = FacebookWebhookEvent::create([
            'event_type' => $eventType,
            'payload_json' => $payload,
            'leadgen_id' => $leadgenId,
        ]);

        $log = FacebookWebhookLog::create([
            'event_type' => $eventType,
            'page_id' => $pageId,
            'payload' => $payload,
            'status' => 'received',
        ]);

        try {
            if ($leadgenId) {
                $this->createOrUpdateLeadFromLeadgen($leadgenId, $payload);
                $event->update(['processed_at' => now()]);
                $log->update(['status' => 'processed']);
            } else {
                $log->update(['status' => $setting->facebook_enabled ? 'logged' : 'disabled']);
            }
        } catch (Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            Log::warning('Facebook webhook failed.', ['error' => $exception->getMessage()]);
        }

        return response('EVENT_RECEIVED', 200);
    }

    private function createOrUpdateLeadFromLeadgen(string $leadgenId, array $payload): Lead
    {
        $formId = $this->firstPayloadValue($payload, 'form_id');
        $adName = $this->firstPayloadValue($payload, 'ad_name');
        $campaignName = $this->firstPayloadValue($payload, 'campaign_name');

        return Lead::updateOrCreate(
            ['external_lead_id' => $leadgenId],
            [
                'name' => 'Facebook Lead '.$leadgenId,
                'phone' => null,
                'email' => null,
                'source' => 'facebook_lead_ads',
                'source_platform' => 'facebook',
                'source_channel' => 'facebook_lead_ads',
                'province' => null,
                'budget' => null,
                'message' => "Facebook Lead Ads\nLeadgen ID: {$leadgenId}\nForm ID: ".($formId ?: '-'),
                'status' => 'new_lead',
                'lead_status' => 'new_lead',
                'quotation_status' => 'not_started',
                'campaign_name' => $campaignName,
                'ad_name' => $adName,
                'raw_payload_json' => $payload,
                'channel_payload' => $payload,
            ]
        );
    }

    private function detectEventType(array $payload): ?string
    {
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) === 'leadgen') {
                    return 'leadgen';
                }
            }

            foreach ($entry['messaging'] ?? [] as $messaging) {
                if (isset($messaging['postback'])) {
                    return 'messaging_postbacks';
                }

                if (isset($messaging['message'])) {
                    return 'messages';
                }
            }
        }

        return data_get($payload, 'object');
    }

    private function findLeadgenId(array $payload): ?string
    {
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $leadgenId = data_get($change, 'value.leadgen_id');

                if ($leadgenId) {
                    return (string) $leadgenId;
                }
            }
        }

        return $this->firstPayloadValue($payload, 'leadgen_id');
    }

    private function firstPayloadValue(array $payload, string $key): ?string
    {
        $stack = [$payload];

        while ($stack !== []) {
            $current = array_pop($stack);

            foreach ($current as $currentKey => $value) {
                if ($currentKey === $key && filled($value)) {
                    return (string) $value;
                }

                if (is_array($value)) {
                    $stack[] = $value;
                }
            }
        }

        return null;
    }
}
