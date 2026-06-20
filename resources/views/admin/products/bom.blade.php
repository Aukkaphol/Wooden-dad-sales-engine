@extends('layouts.admin', ['title' => 'BOM | '.company()->display_name])
@section('content')
<section class="bg-pine-50"><div class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-pine-700">กลับสินค้า</a>
    <h1 class="mt-2 text-3xl font-semibold text-ink">BOM: {{ $product->name }}</h1>
    @if(session('success'))<div class="my-5 rounded-xl bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200">{{ session('success') }}</div>@endif
    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <h2 class="text-lg font-semibold text-ink">รายการวัสดุ</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-pine-200 text-sm">
                    <thead class="bg-pine-100 text-pine-700"><tr><th class="px-3 py-2 text-left">Material</th><th class="px-3 py-2 text-right">Qty</th><th class="px-3 py-2 text-left">Unit</th><th class="px-3 py-2 text-right">Waste %</th><th class="px-3 py-2 text-right">Cost</th><th></th></tr></thead>
                    <tbody class="divide-y divide-pine-100">
                    @foreach($product->bomItems as $item)
                        @php
                            $cost = $item->required_quantity * (1 + ((float) $item->waste_percent / 100)) * $item->material->cost_price_value;
                        @endphp
                        <tr><td class="px-3 py-3 font-semibold text-ink">{{ $item->material->name }}</td><td class="px-3 py-3 text-right">{{ number_format($item->required_quantity, 3) }}</td><td class="px-3 py-3">{{ $item->material->unit }}</td><td class="px-3 py-3 text-right">{{ number_format((float)$item->waste_percent, 2) }}</td><td class="px-3 py-3 text-right">฿{{ number_format($cost, 2) }}</td><td class="px-3 py-3 text-right"><form method="post" action="{{ route('admin.products.bom.destroy', [$product, $item]) }}">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">Remove</button></form></td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <aside class="space-y-6">
            <form method="post" action="{{ route('admin.products.bom.store', $product) }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">@csrf
                <h2 class="text-lg font-semibold text-ink">Add Material</h2>
                <label class="mt-4 block"><span class="text-sm font-semibold">Material</span><select name="material_id" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200">@foreach($materials as $material)<option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select></label>
                <label class="mt-4 block"><span class="text-sm font-semibold">Quantity</span><input name="qty_required" type="number" step="0.001" min="0.001" required class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
                <label class="mt-4 block"><span class="text-sm font-semibold">Waste Percent</span><input name="waste_percent" type="number" step="0.01" min="0" value="0" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
                <button class="mt-5 w-full rounded-xl bg-pine-700 px-5 py-3 text-sm font-semibold text-white">บันทึก BOM</button>
            </form>
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200"><p class="text-sm text-pine-700">Estimated Material Cost</p><p class="mt-2 text-3xl font-semibold text-ink">฿{{ number_format($productCost['material_cost'], 2) }}</p><p class="mt-4 text-sm text-pine-700">Estimated Product Cost</p><p class="mt-2 text-3xl font-semibold text-ink">฿{{ number_format($productCost['production_cost'], 2) }}</p></div>
        </aside>
    </div>
</div></section>
@endsection
