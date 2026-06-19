<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $approvedQuotations = DB::table('quotations')
            ->where('status', 'approved')
            ->whereNotIn('id', DB::table('production_orders')->select('quotation_id'))
            ->orderBy('id')
            ->get();

        foreach ($approvedQuotations as $quotation) {
            DB::table('production_orders')->insert([
                'lead_id' => $quotation->lead_id,
                'quotation_id' => $quotation->id,
                'production_order_number' => $this->nextProductionOrderNumber(),
                'status' => 'waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        //
    }

    private function nextProductionOrderNumber(): string
    {
        $prefix = 'PO-'.now()->format('Ym').'-';
        $latest = DB::table('production_orders')
            ->where('production_order_number', 'like', $prefix.'%')
            ->orderByDesc('production_order_number')
            ->value('production_order_number');

        $next = $latest
            ? ((int) str_replace($prefix, '', $latest)) + 1
            : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
};
