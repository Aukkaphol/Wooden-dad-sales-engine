<?php

namespace App\Http\Controllers;

use App\Models\FurnitureSetCategory;
use App\Models\CustomerReview;
use App\Models\PortfolioImage;
use App\Models\WebsiteSection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'sections' => WebsiteSection::query()
                ->orderBy('sort_order')
                ->get()
                ->keyBy('section_key'),
            'categories' => FurnitureSetCategory::query()
                ->where('active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'latestPortfolioImages' => PortfolioImage::query()
                ->where('active', true)
                ->latest()
                ->limit(8)
                ->get(),
            'customerReviews' => CustomerReview::query()
                ->where('active', true)
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
