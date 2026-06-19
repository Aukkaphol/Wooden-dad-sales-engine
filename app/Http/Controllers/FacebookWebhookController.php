<?php

namespace App\Http\Controllers;

use App\Models\FacebookSetting;
use App\Models\FacebookWebhookLog;
use App\Services\FacebookLeadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class FacebookWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $setting = FacebookSetting::current();

        if (
            $request->query('hub_mode') === 'subscribe'
            || $request->query('hub.mode') === 'subscribe'
        ) {
            $token = $request->query('hub_verify_token') ?: $request->query('hub.verify_token');
            $challenge = $request->query('hub_challenge') ?: $request->query('hub.challenge');

            if ($setting->webhook_enabled && hash_equals((string) $setting->webhook_verify_token, (string) $token)) {
                return response((string) $challenge, 200);
            }
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request, FacebookLeadService $facebookLeadService): Response
    {
        $payload = $request->json()->all() ?: $request->all();

        if ($payload === []) {
            $rawPayload = ltrim($request->getContent(), "\xEF\xBB\xBF");
            $payload = json_decode($rawPayload, true) ?: [];
        }
        $setting = FacebookSetting::current();
        $pageId = data_get($payload, 'entry.0.id');

        $log = FacebookWebhookLog::create([
            'event_type' => $this->detectEventType($payload),
            'page_id' => $pageId,
            'payload' => $payload,
            'status' => 'received',
        ]);

        try {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if (($change['field'] ?? null) === 'leadgen') {
                        $facebookLeadService->handleLeadgen($change['value'] ?? [], $log, $setting);
                    }
                }

                foreach ($entry['messaging'] ?? [] as $messaging) {
                    $facebookLeadService->handleMessenger($messaging, $log, $setting);
                }
            }

            if ($log->status === 'received') {
                $log->update(['status' => 'logged']);
            }
        } catch (Throwable $exception) {
            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            Log::warning('Facebook webhook failed.', ['error' => $exception->getMessage()]);
        }

        return response('EVENT_RECEIVED', 200);
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
}
