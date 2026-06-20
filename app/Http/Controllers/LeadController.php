<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\LineNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:120'],
            'budget' => ['required', 'string', 'max:120'],
            'room_width' => ['required', 'numeric', 'min:0.1', 'max:99'],
            'room_length' => ['required', 'numeric', 'min:0.1', 'max:99'],
            'message' => ['nullable', 'string', 'max:2000'],
            'room_image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('room_image')) {
            $validated['room_image'] = $request->file('room_image')->store('room-images', 'public');
        }

        $lead = Lead::create([
            ...$validated,
            'source' => 'website',
            'source_platform' => 'website',
            'source_channel' => 'website_form',
            'utm_source' => $request->string('utm_source')->toString() ?: null,
            'utm_medium' => $request->string('utm_medium')->toString() ?: null,
            'utm_campaign' => $request->string('utm_campaign')->toString() ?: null,
            'referrer_url' => $request->headers->get('referer'),
            'status' => 'new_lead',
            'lead_status' => 'new_lead',
            'quotation_status' => 'not_started',
        ]);

        app(LineNotificationService::class)->notifyNewLead($lead);

        return redirect()->route('thank-you');
    }
}
