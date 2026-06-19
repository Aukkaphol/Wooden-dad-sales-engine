<?php

namespace App\Http\Controllers;

use App\Models\PortfolioImage;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('portfolio', [
            'categories' => PortfolioImage::CATEGORIES,
            'portfolioImages' => PortfolioImage::query()
                ->where('active', true)
                ->orderBy('sort_order')
                ->latest()
                ->get(),
        ]);
    }
}
