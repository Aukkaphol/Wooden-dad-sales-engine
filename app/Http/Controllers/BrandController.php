<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brands\StoreBrandRequest;
use App\Http\Requests\Brands\UpdateBrandRequest;
use App\Models\Brand;
use App\Models\Workspace;
use App\Services\Brands\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request, Workspace $workspace, BrandService $brandService): View
    {
        $this->authorize('view', $workspace);

        return view('brands.index', [
            'workspace' => $workspace,
            'brands' => $brandService->listForWorkspace($workspace),
        ]);
    }

    public function create(Workspace $workspace): View
    {
        $this->authorize('create', [Brand::class, $workspace]);

        return view('brands.create', [
            'workspace' => $workspace,
        ]);
    }

    public function store(StoreBrandRequest $request, Workspace $workspace, BrandService $brandService): RedirectResponse
    {
        $brand = $brandService->create($request->user(), $workspace, $request->validated(), $request);

        return redirect()->route('workspaces.brands.show', [$workspace, $brand])->with('status', 'Brand created.');
    }

    public function show(Workspace $workspace, Brand $brand): View
    {
        $this->ensureBrandBelongsToWorkspace($workspace, $brand);
        $this->authorize('view', $brand);

        return view('brands.show', [
            'workspace' => $workspace,
            'brand' => $brand,
        ]);
    }

    public function edit(Workspace $workspace, Brand $brand): View
    {
        $this->ensureBrandBelongsToWorkspace($workspace, $brand);
        $this->authorize('update', $brand);

        return view('brands.edit', [
            'workspace' => $workspace,
            'brand' => $brand,
        ]);
    }

    public function update(UpdateBrandRequest $request, Workspace $workspace, Brand $brand, BrandService $brandService): RedirectResponse
    {
        $this->ensureBrandBelongsToWorkspace($workspace, $brand);
        $brandService->update($request->user(), $brand, $request->validated(), $request);

        return redirect()->route('workspaces.brands.show', [$workspace, $brand])->with('status', 'Brand updated.');
    }

    public function destroy(Request $request, Workspace $workspace, Brand $brand, BrandService $brandService): RedirectResponse
    {
        $this->ensureBrandBelongsToWorkspace($workspace, $brand);
        $this->authorize('delete', $brand);

        $brandService->delete($request->user(), $brand, $request);

        return redirect()->route('workspaces.brands.index', $workspace)->with('status', 'Brand deleted.');
    }

    private function ensureBrandBelongsToWorkspace(Workspace $workspace, Brand $brand): void
    {
        abort_unless($brand->workspace_id === $workspace->getKey(), 404);
    }
}
