<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assets\StoreAssetRequest;
use App\Http\Requests\Assets\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Workspace;
use App\Services\Assets\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request, Workspace $workspace, AssetService $assetService): View
    {
        $this->authorize('view', $workspace);

        return view('assets.index', [
            'workspace' => $workspace->load('brands'),
            'assets' => $assetService->search($workspace, $request->only(['search', 'brand_id', 'type', 'status', 'category', 'tag'])),
            'filters' => $request->only(['search', 'brand_id', 'type', 'status', 'category', 'tag']),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [Asset::class, $workspace]);

        return view('assets.create', [
            'workspace' => $workspace->load('brands'),
        ]);
    }

    public function store(StoreAssetRequest $request, Workspace $workspace, AssetService $assetService): RedirectResponse
    {
        $asset = $assetService->create($request->user(), $workspace, $request->brand(), $request->validated(), $request);

        return redirect()->route('workspaces.assets.show', [$workspace, $asset])->with('status', 'Asset uploaded.');
    }

    public function show(Workspace $workspace, Asset $asset): View
    {
        $this->ensureAssetBelongsToWorkspace($workspace, $asset);
        $this->authorize('view', $asset);

        return view('assets.show', [
            'workspace' => $workspace,
            'asset' => $asset->load(['brand', 'uploader']),
        ]);
    }

    public function edit(Workspace $workspace, Asset $asset): View
    {
        $this->ensureAssetBelongsToWorkspace($workspace, $asset);
        $this->authorize('update', $asset);

        return view('assets.edit', [
            'workspace' => $workspace->load('brands'),
            'asset' => $asset,
        ]);
    }

    public function update(UpdateAssetRequest $request, Workspace $workspace, Asset $asset, AssetService $assetService): RedirectResponse
    {
        $this->ensureAssetBelongsToWorkspace($workspace, $asset);
        $assetService->update($request->user(), $asset, $request->brand(), $request->validated(), $request);

        return redirect()->route('workspaces.assets.show', [$workspace, $asset])->with('status', 'Asset updated.');
    }

    public function destroy(Request $request, Workspace $workspace, Asset $asset, AssetService $assetService): RedirectResponse
    {
        $this->ensureAssetBelongsToWorkspace($workspace, $asset);
        $this->authorize('delete', $asset);

        $assetService->delete($request->user(), $asset, $request);

        return redirect()->route('workspaces.assets.index', $workspace)->with('status', 'Asset deleted.');
    }

    private function ensureAssetBelongsToWorkspace(Workspace $workspace, Asset $asset): void
    {
        abort_unless($asset->workspace_id === $workspace->getKey(), 404);
    }
}
