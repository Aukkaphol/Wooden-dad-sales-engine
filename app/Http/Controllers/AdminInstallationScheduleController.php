<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminInstallationScheduleController extends Controller
{
    public function index(): View
    {
        $orders = ProductionOrder::query()
            ->with(['lead', 'quotation', 'craftsmen'])
            ->where(fn ($query) => $query
                ->whereNotNull('delivery_date')
                ->orWhereNotNull('installation_date'))
            ->orderByRaw('COALESCE(installation_date, delivery_date) ASC')
            ->get();

        return view('admin.installation-schedule.index', [
            'orders' => $orders,
            'ordersByMonth' => $orders->groupBy(function (ProductionOrder $order): string {
                $date = $order->installation_date ?? $order->delivery_date ?? $order->created_at;

                return $date->format('Y-m');
            })->map(fn (Collection $rows): Collection => $rows->values()),
            'statusLabels' => [
                'pending' => 'รอกำหนดวันติดตั้ง',
                'scheduled' => 'นัดติดตั้งแล้ว',
                'installed' => 'ติดตั้งเสร็จแล้ว',
                'delayed' => 'เลื่อนนัด',
            ],
        ]);
    }
}
