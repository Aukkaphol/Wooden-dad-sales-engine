<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\View\View;

class AdminPurchaseRequestController extends Controller
{
    public function index(): View
    {
        return view('admin.purchase-requests.index', [
            'purchaseRequests' => PurchaseRequest::with(['material', 'productionOrder.lead'])->latest()->paginate(20),
            'statuses' => PurchaseRequest::STATUSES,
        ]);
    }

    public function show(PurchaseRequest $purchaseRequest): View
    {
        $purchaseRequest->load(['material', 'productionOrder.lead']);

        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }
}
