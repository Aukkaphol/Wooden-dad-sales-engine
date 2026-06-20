@extends('layouts.admin', ['title' => 'สร้าง PO | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('admin.purchase.index') }}" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
        <h1 class="mt-2 text-3xl font-semibold text-ink">สร้างใบสั่งซื้อวัตถุดิบ</h1>
        <p class="mt-2 text-sm text-pine-700">เลขที่เอกสารตัวอย่าง: {{ $poNumber }}</p>

        @if ($errors->any())
            <div class="mt-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('admin.purchase.po.store') }}" class="mt-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label><span class="text-sm font-semibold text-ink">ผู้จำหน่าย</span><select name="supplier_id" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>@endforeach</select></label>
                <label><span class="text-sm font-semibold text-ink">อ้างอิง PR</span><select name="purchase_requisition_id" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><option value="">ไม่อ้างอิง PR</option>@foreach ($approvedPrs as $pr)<option value="{{ $pr->id }}" @selected(request('pr') == $pr->id)>{{ $pr->pr_number }}</option>@endforeach</select></label>
                <label><span class="text-sm font-semibold text-ink">วันที่สั่งซื้อ</span><input type="date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">กำหนดส่ง</span><input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">วัตถุดิบ</span><select name="material_id" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select></label>
                <label><span class="text-sm font-semibold text-ink">จำนวน</span><input type="number" step="0.001" min="0.001" name="quantity" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ต้นทุนต่อหน่วย</span><input type="number" step="0.01" min="0" name="unit_cost" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">สถานะ</span><select name="status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><option value="draft">ฉบับร่าง</option><option value="sent">ส่งให้ผู้จำหน่ายแล้ว</option></select></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">หมายเหตุ</span><textarea name="notes" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">{{ old('notes') }}</textarea></label>
            </div>
            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white">บันทึก PO</button>
        </form>
    </div>
</section>
@endsection
