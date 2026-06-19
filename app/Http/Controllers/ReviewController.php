<?php

namespace App\Http\Controllers;

use App\Models\CustomerReview;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('reviews', [
            'reviews' => CustomerReview::query()
                ->where('active', true)
                ->latest()
                ->get(),
        ]);
    }
}
