@extends('layouts.app', ['title' => 'รายละเอียดลูกค้า | Wooden Dad Design'])

@php
    $statusClasses = [
        'new' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'designing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'quoted' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'deposit_paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'production' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'installation' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
@endphp

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.leads.index', request()->query()) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้าระบบ CRM</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $lead->name }}</h1>
                    <p class="mt-2 text-sm text-pine-700">ลูกค้าส่งข้อมูลเมื่อ {{ $lead->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $statusClasses[$lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20' }}">{{ $lead->lead_status_label }}</span>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
                <div class="space-y-6">
                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                                <p class="mt-1 text-sm text-pine-700">ข้อมูลทั้งหมดจากฟอร์มขอราคาและแบบฟรี</p>
                            </div>
                            <a href="tel:{{ $lead->phone }}" class="inline-flex w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">โทรหาลูกค้า</a>
                        </div>

                        <dl class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-pine-500">ชื่อ</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">เบอร์โทร</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">จังหวัด</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->province }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">งบประมาณ</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->budget }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">ความกว้างห้อง</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->room_width }} ม.</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">ความยาวห้อง</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->room_length }} ม.</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">สถานะใบเสนอราคา</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->quotation_status_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">วันติดตามลูกค้า</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $lead->follow_up_date?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 rounded-md bg-pine-50 p-4">
                            <p class="text-sm font-medium text-pine-500">ข้อความจากลูกค้า</p>
                            <p class="mt-2 whitespace-pre-line leading-7 text-pine-700">{{ $lead->message ?: 'ลูกค้าไม่ได้ฝากข้อความเพิ่มเติม' }}</p>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">รูปพื้นที่หน้างาน</h2>
                                <p class="mt-1 text-sm text-pine-700">รูปห้องที่ลูกค้าอัปโหลดมาในฟอร์ม</p>
                            </div>
                            @if ($lead->room_image)
                                <a href="{{ asset('storage/'.$lead->room_image) }}" target="_blank" class="text-sm font-semibold text-pine-700 hover:text-ink">เปิดรูปเต็ม</a>
                            @endif
                        </div>
                        @if ($lead->room_image)
                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <a href="{{ asset('storage/'.$lead->room_image) }}" target="_blank" class="group overflow-hidden rounded-lg ring-1 ring-pine-200">
                                    <img src="{{ asset('storage/'.$lead->room_image) }}" alt="รูปห้องของ {{ $lead->name }}" class="aspect-video w-full object-cover transition group-hover:scale-105">
                                </a>
                            </div>
                        @else
                            <div class="mt-5 rounded-md border border-dashed border-pine-300 p-8 text-center text-pine-700">ลูกค้ายังไม่ได้อัปโหลดรูปห้อง</div>
                        @endif
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">ไทม์ไลน์ลูกค้า</h2>
                        <div class="mt-5 flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-pine-200"></span>
                                        <div class="relative flex space-x-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-pine-500 text-xs font-semibold text-white">1</div>
                                            <div class="min-w-0 flex-1 rounded-md bg-pine-50 p-4">
                                                <p class="text-sm font-semibold text-ink">ลูกค้าส่งข้อมูลเข้าระบบ</p>
                                                <p class="mt-1 text-xs text-pine-600">{{ $lead->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-pine-200"></span>
                                        <div class="relative flex space-x-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-pine-500 text-xs font-semibold text-white">2</div>
                                            <div class="min-w-0 flex-1 rounded-md bg-pine-50 p-4">
                                                <p class="text-sm font-semibold text-ink">สถานะงานขายปัจจุบัน: {{ $lead->lead_status_label }}</p>
                                                <p class="mt-1 text-xs text-pine-600">ใบเสนอราคา: {{ $lead->quotation_status_label }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-pine-500 text-xs font-semibold text-white">3</div>
                                            <div class="min-w-0 flex-1 rounded-md bg-pine-50 p-4">
                                                <p class="text-sm font-semibold text-ink">การติดตามครั้งถัดไป</p>
                                                <p class="mt-1 text-xs text-pine-600">{{ $lead->follow_up_date?->format('d/m/Y') ?? 'ยังไม่กำหนดวันติดตาม' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ประวัติใบเสนอราคา</h2>
                                <p class="mt-1 text-sm text-pine-700">ใบเสนอราคาที่ผูกกับลูกค้ารายนี้</p>
                            </div>
                            <a href="{{ route('admin.leads.quotations.create', $lead) }}" class="inline-flex w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้างใบเสนอราคา</a>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="min-w-full divide-y divide-pine-200 text-sm">
                                <thead class="bg-pine-100 text-pine-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold">ใบเสนอราคา</th>
                                        <th class="px-3 py-2 text-left font-semibold">สถานะ</th>
                                        <th class="px-3 py-2 text-right font-semibold">ยอดรวม</th>
                                        <th class="px-3 py-2 text-right font-semibold">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-pine-100 bg-white">
                                    @forelse ($lead->quotations as $quotation)
                                        <tr>
                                            <td class="px-3 py-3">
                                                <a href="{{ route('admin.quotations.show', $quotation) }}" class="font-semibold text-ink hover:text-pine-700">{{ $quotation->quotation_number }}</a>
                                                <p class="mt-1 text-xs text-pine-600">{{ $quotation->created_at->format('d/m/Y H:i') }}</p>
                                            </td>
                                            <td class="px-3 py-3 text-pine-700">{{ $quotation->status_label }}</td>
                                            <td class="px-3 py-3 text-right font-semibold text-ink">฿{{ number_format((float) $quotation->subtotal, 2) }}</td>
                                            <td class="px-3 py-3 text-right">
                                                <div class="flex justify-end gap-3">
                                                    <a href="{{ route('admin.quotations.show', $quotation) }}" class="font-semibold text-pine-700 hover:text-ink">ดูรายละเอียด</a>
                                                    <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="font-semibold text-pine-700 hover:text-ink">ไฟล์ PDF</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-8 text-center text-pine-700">ยังไม่มีใบเสนอราคา</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ใบสั่งผลิต</h2>
                                <p class="mt-1 text-sm text-pine-700">สร้างอัตโนมัติเมื่อใบเสนอราคาได้รับอนุมัติ</p>
                            </div>
                            <a href="{{ route('admin.production.index') }}" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">เปิดคิวงานผลิต</a>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="min-w-full divide-y divide-pine-200 text-sm">
                                <thead class="bg-pine-100 text-pine-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold">ใบสั่งผลิต</th>
                                        <th class="px-3 py-2 text-left font-semibold">ใบเสนอราคา</th>
                                        <th class="px-3 py-2 text-left font-semibold">ขั้นตอน</th>
                                        <th class="px-3 py-2 text-right font-semibold">ช่าง</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-pine-100 bg-white">
                                    @forelse ($lead->productionOrders as $order)
                                        <tr>
                                            <td class="px-3 py-3"><a href="{{ route('admin.production.show', $order) }}" class="font-semibold text-ink hover:text-pine-700">{{ $order->production_order_number }}</a></td>
                                            <td class="px-3 py-3 text-pine-700">{{ $order->quotation->quotation_number }}</td>
                                            <td class="px-3 py-3 text-pine-700">{{ $order->status_label }}</td>
                                            <td class="px-3 py-3 text-right text-pine-700">{{ $order->craftsmen->pluck('name')->implode(', ') ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-8 text-center text-pine-700">ยังไม่มีใบสั่งผลิต</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">บันทึกภายใน</h2>
                        <form action="{{ route('admin.leads.notes.store', $lead) }}" method="post" class="mt-5">
                            @csrf
                            <label class="block">
                                <span class="text-sm font-semibold text-ink">เพิ่มโน้ตใหม่</span>
                                <textarea name="note" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500" placeholder="เช่น โทรแล้ว ลูกค้าขอแบบเตียงพร้อมตู้เก็บของ...">{{ old('note') }}</textarea>
                                @error('note') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>
                            <button class="mt-3 rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกโน้ต</button>
                        </form>

                        <div class="mt-8 flow-root">
                            <ul class="-mb-8">
                                @forelse ($lead->notes as $note)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (! $loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-pine-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-pine-500 text-sm font-semibold text-white">N</div>
                                                <div class="min-w-0 flex-1 rounded-md bg-pine-50 p-4">
                                                    <p class="whitespace-pre-line text-sm leading-6 text-ink">{{ $note->note }}</p>
                                                    <p class="mt-2 text-xs text-pine-600">{{ $note->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีโน้ตในไทม์ไลน์</li>
                                @endforelse
                            </ul>
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">อัปเดตสถานะ</h2>
                        <p class="mt-1 text-sm text-pine-700">กดปุ่มเพื่อย้ายลีดไปยังขั้นตอนถัดไปของ CRM</p>
                        <div class="mt-5 grid gap-2">
                            @foreach ($statuses as $value => $label)
                                <form action="{{ route('admin.leads.status', $lead) }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="lead_status" value="{{ $value }}">
                                    <button class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm font-semibold ring-1 ring-inset {{ $lead->lead_status === $value ? ($statusClasses[$value] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20') : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50' }}">
                                        <span>{{ $label }}</span>
                                        @if ($lead->lead_status === $value)
                                            <span class="text-xs">ปัจจุบัน</span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </section>

                    <form action="{{ route('admin.leads.update', $lead) }}" method="post" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-lg font-semibold text-ink">รายละเอียด CRM</h2>
                        <input type="hidden" name="lead_status" value="{{ $lead->lead_status }}">
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">สถานะใบเสนอราคา</span>
                            <select name="quotation_status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                @foreach ($quotationStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('quotation_status', $lead->quotation_status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('quotation_status') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">วันติดตามลูกค้า</span>
                            <input name="follow_up_date" type="date" value="{{ old('follow_up_date', $lead->follow_up_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            @error('follow_up_date') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">บันทึกภายใน</span>
                            <textarea name="admin_notes" rows="8" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
                            @error('admin_notes') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกข้อมูล CRM</button>
                    </form>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">สรุปพื้นที่</h2>
                        <div class="mt-5 rounded-md bg-pine-50 p-4">
                            <p class="text-sm text-pine-700">พื้นที่ห้องโดยประมาณ</p>
                            <p class="mt-1 text-2xl font-semibold text-ink">{{ number_format($lead->room_width * $lead->room_length, 2) }} ตร.ม.</p>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </section>
@endsection
