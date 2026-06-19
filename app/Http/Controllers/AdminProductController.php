<?php

namespace App\Http\Controllers;

use App\Services\CostCalculationService;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(CostCalculationService $costCalculationService): View
    {
        return view('admin.products.index', [
            'productCosts' => $costCalculationService->refreshProducts(),
        ]);
    }
}
