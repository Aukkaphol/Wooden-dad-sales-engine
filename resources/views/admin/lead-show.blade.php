@extends('layouts.admin', ['title' => 'รายละเอียด Lead | '.company()->display_name])

@php
    $currentStatus = $lead->status ?: $lead->lead_status ?: 'new_lead';
    $timeline = [
        'new_lead' => 'สร้าง Lead',
        'contacted' => 'โทรแล้ว',
        'site_survey' => 'นัดวัดพื้นที่',
        'quotation_sent' => 'ส่งใบเสนอราคา',
        'won' => 'ปิดการขาย',
    ];
    $timelineKeys = array_keys($timeline);
    $currentIndex = array_search($currentStatus, $timelineKeys, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
    $statusClasses = [
        'new_lead' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'site_survey' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
        'designing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'quotation_sent' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'negotiation' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'won' => 'bg-green-50 text-green-700 ring-green-600/20',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
@endphp

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.leads.index', request()->query()) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไป CRM Pipeline</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $lead->name }}</h1>
                    <p class="mt-2 text-sm text-pine-700">Lead จาก {{ $lead->source_label }} · {{ $lead->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $statusClasses[$currentStatus] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20' }}">{{ $lead->lead_status_label }}</span>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
                <div class="space-y-6">
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                                <p class="mt-1 text-sm text-pine-700">รายละเอียด Lead สำหรับทีมขาย</p>
                            </div>
                            @if ($lead->phone)
                                <a href="tel:{{ $lead->phone }}" class="inline-flex w-fit rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">โทรหาลูกค้า</a>
                            @endif
                        </div>

                        <dl class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div><dt class="text-sm font-medium text-pine-500">ชื่อ</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->name }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">เบอร์โทร</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->phone ?: '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">จังหวัด</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->province ?: '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">งบประมาณ</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->budget ?: '-' }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">แหล่งที่มา</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->source_label }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">สถานะใบเสนอราคา</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->quotation_status_label }}</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">ขนาดห้อง</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->room_width ?: '-' }} x {{ $lead->room_length ?: '-' }} ม.</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">วันติดตาม</dt><dd class="mt-1 font-semibold text-ink">{{ $lead->follow_up_date?->format('d/m/Y') ?? '-' }}</dd></div>
                        </dl>

                        <div class="mt-6 rounded-xl bg-pine-50 p-4">
                            <p class="text-sm font-medium text-pine-500">ข้อความจากลูกค้า</p>
                            <p class="mt-2 whitespace-pre-line leading-7 text-pine-700">{{ $lead->message ?: 'ลูกค้าไม่ได้ฝากข้อความเพิ่มเติม' }}</p>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">Timeline งานขาย</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-5">
                            @foreach ($timeline as $status => $label)
                                @php
                                    $index = array_search($status, $timelineKeys, true);
                                    $done = $currentStatus === 'won' || $index <= $currentIndex;
                                @endphp
                                <div class="rounded-xl p-4 ring-1 {{ $done ? 'bg-pine-700 text-white ring-pine-700' : 'bg-pine-50 text-pine-700 ring-pine-200' }}">
                                    <p class="text-xs font-semibold">{{ $loop->iteration }}</p>
                                    <p class="mt-2 text-sm font-semibold">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">รูปพื้นที่หน้างาน</h2>
                                <p class="mt-1 text-sm text-pine-700">รูปที่ลูกค้าอัปโหลดจากฟอร์ม</p>
                            </div>
                            @if ($lead->room_image)
                                <a href="{{ asset('storage/'.$lead->room_image) }}" target="_blank" class="text-sm font-semibold text-pine-700 hover:text-ink">เปิดรูปเต็ม</a>
                            @endif
                        </div>
                        @if ($lead->room_image)
                            <a href="{{ asset('storage/'.$lead->room_image) }}" target="_blank" class="mt-5 block overflow-hidden rounded-2xl ring-1 ring-pine-200">
                                <img src="{{ asset('storage/'.$lead->room_image) }}" alt="รูปพื้นที่ของ {{ $lead->name }}" class="aspect-video w-full object-cover">
                            </a>
                        @else
                            <div class="mt-5 rounded-xl border border-dashed border-pine-300 p-8 text-center text-pine-700">ลูกค้ายังไม่ได้อัปโหลดรูปห้อง</div>
                        @endif
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ประวัติใบเสนอราคา</h2>
                                <p class="mt-1 text-sm text-pine-700">ใบเสนอราคาที่เชื่อมกับ Lead นี้</p>
                            </div>
                            <a href="{{ route('admin.leads.quotations.create', $lead) }}" class="inline-flex w-fit rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้างใบเสนอราคา</a>
                        </div>
                        <div class="mt-5 grid gap-3">
                            @forelse ($lead->quotations as $quotation)
                                <a href="{{ route('admin.quotations.show', $quotation) }}" class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200 hover:bg-pine-100">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink">{{ $quotation->quotation_number }}</p>
                                            <p class="text-sm text-pine-700">{{ $quotation->status_label }}</p>
                                        </div>
                                        <p class="font-semibold text-ink">฿{{ number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2) }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีใบเสนอราคา</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">ใบสั่งผลิต</h2>
                        <div class="mt-5 grid gap-3">
                            @forelse ($lead->productionOrders as $order)
                                <a href="{{ route('admin.production.show', $order) }}" class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200 hover:bg-pine-100">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink">{{ $order->production_order_number }}</p>
                                            <p class="text-sm text-pine-700">{{ $order->quotation?->quotation_number ?: '-' }}</p>
                                        </div>
                                        <p class="font-semibold text-pine-700">{{ $order->status_label }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีใบสั่งผลิต</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">Internal Notes</h2>
                        <form action="{{ route('admin.leads.notes.store', $lead) }}" method="post" class="mt-5">
                            @csrf
                            <textarea name="note" rows="4" class="w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500" placeholder="เพิ่มบันทึกการโทร นัดหมาย หรือข้อกำหนดลูกค้า">{{ old('note') }}</textarea>
                            @error('note') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                            <button class="mt-3 rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกโน้ต</button>
                        </form>

                        <div class="mt-8 space-y-3">
                            @forelse ($lead->notes as $note)
                                <div class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200">
                                    <p class="whitespace-pre-line text-sm leading-6 text-ink">{{ $note->note }}</p>
                                    <p class="mt-2 text-xs text-pine-600">{{ $note->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีโน้ตใน Timeline</div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">เปลี่ยนสถานะ</h2>
                        <div class="mt-5 grid gap-2">
                            @foreach ($statuses as $value => $label)
                                <form action="{{ route('admin.leads.status', $lead) }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="lead_status" value="{{ $value }}">
                                    <button class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-semibold ring-1 ring-inset {{ $currentStatus === $value ? ($statusClasses[$value] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20') : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50' }}">
                                        <span>{{ $label }}</span>
                                        @if ($currentStatus === $value)
                                            <span class="text-xs">ปัจจุบัน</span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </section>

                    <form action="{{ route('admin.leads.update', $lead) }}" method="post" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-lg font-semibold text-ink">รายละเอียด CRM</h2>
                        <input type="hidden" name="lead_status" value="{{ $currentStatus }}">
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">สถานะใบเสนอราคา</span>
                            <select name="quotation_status" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                @foreach ($quotationStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('quotation_status', $lead->quotation_status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">วันติดตามลูกค้า</span>
                            <input name="follow_up_date" type="date" value="{{ old('follow_up_date', $lead->follow_up_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">บันทึกภายใน</span>
                            <textarea name="admin_notes" rows="8" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('admin_notes', $lead->admin_notes) }}</textarea>
                        </label>
                        <button class="mt-5 w-full rounded-xl bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกข้อมูล CRM</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
