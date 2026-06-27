<?php

namespace App\Http\Controllers;

use App\Models\FacebookConnection;
use App\Models\FacebookIntegrationSetting;
use App\Models\FacebookLog;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Facebook\FacebookConnectorService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FacebookIntegrationController extends Controller
{
    public function index(Request $request): View
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('view', $workspace);

        $connections = FacebookConnection::query()
            ->where('workspace_id', $workspace->getKey())
            ->latest()
            ->paginate(10);

        return view('integrations.facebook.index', [
            'workspace' => $workspace,
            'settings' => FacebookIntegrationSetting::query()->where('workspace_id', $workspace->getKey())->first(),
            'connections' => $connections,
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('update', $workspace);

        $existing = FacebookIntegrationSetting::query()
            ->where('workspace_id', $workspace->getKey())
            ->first();

        $validated = $request->validate([
            'app_id' => ['required', 'string', 'max:255'],
            'app_secret' => [$existing ? 'nullable' : 'required', 'string', 'max:2000'],
            'redirect_uri' => ['required', 'url', 'max:255'],
        ]);

        $attributes = [
            'app_id' => $validated['app_id'],
            'redirect_uri' => $validated['redirect_uri'],
        ];

        if (($validated['app_secret'] ?? null) !== null && $validated['app_secret'] !== '') {
            $attributes['app_secret'] = $validated['app_secret'];
        }

        FacebookIntegrationSetting::query()->updateOrCreate(
            ['workspace_id' => $workspace->getKey()],
            $attributes,
        );

        return back()->with('status', 'Facebook App settings saved.');
    }

    public function connect(Request $request, FacebookConnectorService $facebook): RedirectResponse
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('view', $workspace);

        $state = Str::random(40);
        $request->session()->put('facebook_oauth_state', $state);
        $request->session()->put('facebook_workspace_id', $workspace->getKey());

        try {
            return redirect()->away($facebook->authorizationUrl($workspace, $state));
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }
    }

    public function callback(Request $request, FacebookConnectorService $facebook): RedirectResponse
    {
        if ($request->filled('error')) {
            if ($request->user()?->currentWorkspace) {
                FacebookLog::query()->create([
                    'workspace_id' => $request->user()->currentWorkspace->getKey(),
                    'action' => 'connect_failed',
                    'status' => FacebookLog::STATUS_FAILED,
                    'message' => $request->string('error_description', 'Facebook authorization was cancelled.')->toString(),
                ]);
            }

            return redirect()
                ->route('channels.facebook.index')
                ->withErrors(['facebook' => $request->string('error_description', 'Facebook authorization was cancelled.')]);
        }

        abort_unless(hash_equals((string) $request->session()->pull('facebook_oauth_state'), (string) $request->query('state')), 403);

        $workspaceId = $request->session()->pull('facebook_workspace_id');
        $workspace = $request->user()->workspaces()->whereKey($workspaceId)->firstOrFail();
        $this->authorize('view', $workspace);

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        try {
            $connections = $facebook->storePagesFromCallback($workspace, $validated['code']);
        } catch (RequestException $exception) {
            report($exception);

            return redirect()
                ->route('channels.facebook.index')
                ->withErrors(['facebook' => 'Facebook connection failed. Please try again.']);
        }

        return redirect()
            ->route('channels.facebook.index')
            ->with('status', $connections->count().' Facebook Page connection(s) saved.');
    }

    public function publishTest(Request $request, FacebookConnection $connection, FacebookConnectorService $facebook): RedirectResponse
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('view', $workspace);
        abort_unless($connection->workspace_id === $workspace->getKey(), 404);

        $message = 'Test post from JARVIS AI Marketing Studio';

        try {
            $facebook->publishTextPost($connection, $message);
        } catch (RequestException $exception) {
            report($exception);

            return back()->withErrors(['facebook' => 'Facebook test post failed. Please verify Page permissions and try again.']);
        }

        return back()->with('status', 'Facebook test post published to '.$connection->page_name.'.');
    }

    public function testConnection(Request $request, FacebookConnection $connection, FacebookConnectorService $facebook): RedirectResponse
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('view', $workspace);
        abort_unless($connection->workspace_id === $workspace->getKey(), 404);

        try {
            $facebook->testConnection($connection);
        } catch (RequestException $exception) {
            report($exception);

            return back()->withErrors(['facebook' => 'Facebook connection test failed.']);
        }

        return back()->with('status', 'Facebook connection tested successfully.');
    }

    public function sync(Request $request, FacebookConnection $connection, FacebookConnectorService $facebook): RedirectResponse
    {
        $workspace = $this->currentWorkspace($request->user());
        $this->authorize('view', $workspace);
        abort_unless($connection->workspace_id === $workspace->getKey(), 404);

        try {
            $facebook->syncPageInfo($connection);
        } catch (RequestException $exception) {
            report($exception);

            return back()->withErrors(['facebook' => 'Facebook Page sync failed.']);
        }

        return back()->with('status', 'Facebook Page info synced.');
    }

    private function currentWorkspace(User $user): Workspace
    {
        $workspace = $user->currentWorkspace;

        if ($workspace && $user->workspaces()->whereKey($workspace->getKey())->exists()) {
            return $workspace;
        }

        return $user->workspaces()->orderBy('name')->firstOrFail();
    }
}
