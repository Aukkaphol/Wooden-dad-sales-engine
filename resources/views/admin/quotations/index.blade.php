@extends('layouts.admin', ['title' => 'ใบเสนอราคา | '.company()->display_name])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Quotation Module</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ใบเสนอราคา</h1>
                    <p class="mt-2 text-sm text-pine-700">จัดการใบเสนอราคา สถานะ การอนุมัติ PDF และการสร้างใบสั่งผลิต</p>
                </div>
                <a href="{{ route('admin.quotations.create') }}" class="inline-flex w-fit rounded-xl bg-pine-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">สร้างใบเสนอราคา</a>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            <form method="get" class="mb-6 grid gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-pine-200 md:grid-cols-[1fr_220px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="ค้นหาเลขที่ ชื่อลูกค้า หรือเบอร์โทร" class="rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                <select name="status" class="rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    <option value="">ทุกสถานะ</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">ค้นหา</button>
            </form>

            <div class="grid gap-4 lg:hidden">
                @forelse ($quotations as $quotation)
                    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-ink">{{ $quotation->display_number }}</p>
                                <p class="mt-1 text-sm text-pine-700">{{ $quotation->customer_name ?: $quotation->lead?->name }}</p>
                                <p class="mt-1 text-xs text-pine-600">{{ $quotation->province ?: $quotation->lead?->province }}</p>
                            </div>
                            <span class="rounded-full bg-pine-100 px-3 py-1 text-xs font-semibold text-pine-700">{{ $quotation->status_label }}</span>
                        </div>
                        <p class="mt-4 text-2xl font-semibold text-ink">฿{{ number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2) }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-sm font-semibold">
                            <a href="{{ route('admin.quotations.show', $quotation) }}" class="rounded-lg bg-pine-50 px-3 py-2 text-pine-700">View</a>
                            <a href="{{ route('admin.quotations.edit', $quotation) }}" class="rounded-lg bg-pine-50 px-3 py-2 text-pine-700">Edit</a>
                            <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="rounded-lg bg-pine-50 px-3 py-2 text-pine-700">PDF</a>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl border border-dashed border-pine-300 p-8 text-center text-pine-700">ยังไม่มีใบเสนอราคา</p>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-pine-200 lg:block">
                <table class="min-w-full divide-y divide-pine-200 text-sm">
                    <thead class="bg-pine-100 text-pine-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Quotation No</th>
                            <th class="px-4 py-3 text-left font-semibold">Customer</th>
                            <th class="px-4 py-3 text-left font-semibold">Province</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-right font-semibold">Total Amount</th>
                            <th class="px-4 py-3 text-left font-semibold">Created Date</th>
                            <th class="px-4 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100">
                        @forelse ($quotations as $quotation)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-ink">{{ $quotation->display_number }}</td>
                                <td class="px-4 py-3 text-pine-700">{{ $quotation->customer_name ?: $quotation->lead?->name }}</td>
                                <td class="px-4 py-3 text-pine-700">{{ $quotation->province ?: $quotation->lead?->province }}</td>
                                <td class="px-4 py-3"><span class="rounded-full bg-pine-100 px-3 py-1 text-xs font-semibold text-pine-700">{{ $quotation->status_label }}</span></td>
                                <td class="px-4 py-3 text-right font-semibold text-ink">฿{{ number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2) }}</td>
                                <td class="px-4 py-3 text-pine-700">{{ $quotation->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.quotations.show', $quotation) }}" class="rounded-lg bg-pine-50 px-3 py-2 font-semibold text-pine-700">View</a>
                                        <a href="{{ route('admin.quotations.edit', $quotation) }}" class="rounded-lg bg-pine-50 px-3 py-2 font-semibold text-pine-700">Edit</a>
                                        <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="rounded-lg bg-pine-50 px-3 py-2 font-semibold text-pine-700">PDF</a>
                                        @if ($quotation->status !== 'approved')
                                            <form method="post" action="{{ route('admin.quotations.approve', $quotation) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="rounded-lg bg-emerald-600 px-3 py-2 font-semibold text-white">Approve</button>
                                            </form>
                                        @endif
                                        <form method="post" action="{{ route('admin.quotations.destroy', $quotation) }}" onsubmit="return confirm('ยืนยันการลบใบเสนอราคานี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-rose-50 px-3 py-2 font-semibold text-rose-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-10 text-center text-pine-700">ยังไม่มีใบเสนอราคา</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $quotations->links() }}</div>
        </div>
    </section>
@endsection
