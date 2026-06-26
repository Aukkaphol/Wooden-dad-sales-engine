<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Services\Director\AiDirectorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiDirectorController extends Controller
{
    public function __invoke(Request $request, Workspace $workspace, AiDirectorService $director): View
    {
        $this->authorize('view', $workspace);

        return view('director.show', [
            'workspace' => $workspace->load('brands'),
            'filters' => $request->only('brand_id'),
            'decisions' => $director->decisions($workspace, $request->only('brand_id')),
        ]);
    }
}
