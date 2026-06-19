@extends('layouts.app', ['title' => 'ระบบ CRM | Wooden Dad Design'])

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
    $maxMonthly = max(1, $monthlyLeads->max('total') ?? 1);
    $maxProvince = max(1, $provinceStats->max('total') ?? 1);
@endphp

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">ระบบติดตามงานขาย</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ระบบติดตามงานขาย Wooden Dad Design</h1>
                    <p class="mt-2 text-sm text-pine-700">ดูสถานะลูกค้าชุดห้องนอนตั้งแต่ลีดใหม่จนถึงติดตั้งเสร็จ</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.leads.export', request()->query()) }}" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 shadow-sm ring-1 ring-inset ring-pine-200 hover:bg-pine-100">ส่งออก Excel</a>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="inline-flex items-center justify-center rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">ออกจากระบบ</button>
                    </form>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดวันนี้</dt>
                    <dd class="mt-2 text-3xl font-semibold text-blue-700">{{ number_format($widgets['leads_today']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดเดือนนี้</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700">{{ number_format($widgets['leads_this_month']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">รอติดตามลูกค้า</dt>
                    <dd class="mt-2 text-3xl font-semibold text-rose-700">{{ number_format($widgets['pending_follow_up']) }}</dd>
                </div>
            </dl>

            <dl class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดทั้งหมด</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($kpis['total_leads']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ยอดขายคาดการณ์</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700">฿{{ number_format($kpis['estimated_revenue']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ยอดมัดจำคาดการณ์</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700">฿{{ number_format($kpis['deposit_revenue']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">อัตราปิดการขาย</dt>
                    <dd class="mt-2 text-3xl font-semibold text-green-700">{{ $kpis['conversion_rate'] }}%</dd>
                </div>
            </dl>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.15fr_.85fr_.7fr]">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">ลีดรายเดือน</h2>
                            <p class="mt-1 text-sm text-pine-700">จำนวนลีดที่เข้ามาในแต่ละเดือน</p>
                        </div>
                    </div>
                    <div class="mt-6 flex h-56 items-end gap-3 overflow-x-auto pb-2">
                        @forelse ($monthlyLeads as $month)
                            <div class="flex min-w-16 flex-1 flex-col items-center gap-2">
                                <div class="flex h-40 w-full items-end rounded-md bg-pine-50 px-2">
                                    <div class="w-full rounded-t-md bg-pine-500" style="height: {{ max(8, ($month->total / $maxMonthly) * 100) }}%"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-ink">{{ $month->total }}</p>
                                    <p class="text-xs text-pine-600">{{ $month->month }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-full w-full items-center justify-center rounded-md border border-dashed border-pine-300 text-pine-700">ยังไม่มีข้อมูลสำหรับทำกราฟ</div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">อัตราปิดการขาย</h2>
                    <p class="mt-1 text-sm text-pine-700">งานที่ปิดสำเร็จเทียบกับลีดทั้งหมด</p>
                    <div class="mt-6 flex items-center justify-center">
                        <div class="flex h-36 w-36 items-center justify-center rounded-full bg-pine-100 ring-8 ring-white">
                            <div class="text-center">
                                <p class="text-4xl font-semibold text-pine-700">{{ $conversionRate }}%</p>
                                <p class="mt-1 text-xs text-pine-600">ปิดงานแล้ว</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 h-3 overflow-hidden rounded-full bg-pine-100">
                        <div class="h-full rounded-full bg-pine-500" style="width: {{ min(100, $conversionRate) }}%"></div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">สถิติตามจังหวัด</h2>
                    <p class="mt-1 text-sm text-pine-700">จังหวัดที่มีลีดมากที่สุด</p>
                    <div class="mt-6 space-y-4">
                        @forelse ($provinceStats as $province)
                            <div>
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="truncate font-medium text-ink">{{ $province->province }}</span>
                                    <span class="text-pine-700">{{ $province->total }}</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-pine-100">
                                    <div class="h-full rounded-full bg-pine-500" style="width: {{ max(8, ($province->total / $maxProvince) * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีข้อมูลจังหวัด</div>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200" data-kanban-board>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">บอร์ดงานขาย CRM</h2>
                        <p class="mt-1 text-sm text-pine-700">ลากการ์ดเพื่อย้ายขั้นตอน หรือใช้ปุ่มย้ายบนการ์ดลูกค้า</p>
                    </div>
                    <p class="hidden rounded-md bg-pine-100 px-3 py-2 text-sm font-semibold text-pine-700" data-kanban-message></p>
                </div>
                <div class="mt-5 grid gap-4 xl:grid-cols-5">
                    @foreach ($kanbanStages as $stage => $label)
                        <div class="rounded-lg bg-pine-50 p-3 ring-1 ring-pine-200" data-kanban-column data-stage="{{ $stage }}">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="font-semibold text-ink">{{ $label }}</h3>
                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200" data-stage-count>{{ ($kanbanLeads[$stage] ?? collect())->count() }}</span>
                            </div>
                            <div class="mt-4 min-h-32 space-y-3 rounded-md border border-dashed border-transparent p-1 transition" data-kanban-dropzone>
                                @forelse (($kanbanLeads[$stage] ?? collect()) as $lead)
                                    <article
                                        class="cursor-grab rounded-md bg-white p-3 shadow-sm ring-1 ring-pine-200 transition active:cursor-grabbing"
                                        draggable="true"
                                        data-lead-card
                                        data-lead-id="{{ $lead->id }}"
                                        data-update-url="{{ route('admin.leads.status', $lead) }}"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.leads.show', ['lead' => $lead] + request()->query()) }}" class="block truncate text-sm font-semibold text-ink hover:text-pine-700">{{ $lead->name }}</a>
                                                <p class="mt-1 text-xs text-pine-700">{{ $lead->phone }}</p>
                                            </div>
                                            @if ($lead->room_image)
                                                <img src="{{ asset('storage/'.$lead->room_image) }}" alt="รูปห้องของ {{ $lead->name }}" class="h-10 w-12 shrink-0 rounded object-cover ring-1 ring-pine-200">
                                            @endif
                                        </div>
                                        <p class="mt-3 truncate text-xs text-pine-700">{{ $lead->province }} · {{ $lead->budget }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-pine-100 px-2 py-1 font-semibold text-pine-700">{{ $lead->quotation_status_label }}</span>
                                            @if ($lead->follow_up_date)
                                                <span class="rounded-full bg-white px-2 py-1 font-semibold text-pine-700 ring-1 ring-pine-200">{{ $lead->follow_up_date->format('d/m/Y') }}</span>
                                            @endif
                                        </div>
                                        <form action="{{ route('admin.leads.status', $lead) }}" method="post" class="mt-3 flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="lead_status" data-card-status-select class="min-w-0 flex-1 rounded-md border-0 bg-pine-50 px-2 py-1.5 text-xs ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                                @foreach ($kanbanStages as $value => $stageLabel)
                                                    <option value="{{ $value }}" @selected($lead->lead_status === $value)>{{ $stageLabel }}</option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-md bg-pine-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pine-500">ย้าย</button>
                                        </form>
                                    </article>
                                @empty
                                    <div class="rounded-md border border-dashed border-pine-300 p-4 text-center text-sm text-pine-700">ไม่มีลีด</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <form method="get" action="{{ route('admin.leads.index') }}" class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <label class="xl:col-span-2">
                        <span class="text-sm font-semibold text-ink">ค้นหาชื่อหรือเบอร์โทร</span>
                        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="เช่น สมชาย หรือ 081..." class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 placeholder:text-pine-400 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">จังหวัด</span>
                        <select name="province" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกจังหวัด</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province }}" @selected(($filters['province'] ?? '') === $province)>{{ $province }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">สถานะ CRM</span>
                        <select name="lead_status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกสถานะ</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['lead_status'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ตั้งแต่วันที่</span>
                        <input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ถึงวันที่</span>
                        <input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">ค้นหา</button>
                    <a href="{{ route('admin.leads.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-inset ring-pine-200 hover:bg-pine-100">ล้างตัวกรอง</a>
                </div>
            </form>

            <div class="mt-8 hidden overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-pine-200 lg:block">
                <table class="min-w-full divide-y divide-pine-200">
                    <thead class="bg-pine-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">ลูกค้า</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">จังหวัด</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">งบประมาณ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">ขนาดห้อง</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">สถานะ CRM</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">รูป</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">วันที่</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-pine-700">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100 bg-white">
                        @forelse ($leads as $lead)
                            <tr class="hover:bg-pine-50">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-ink">{{ $lead->name }}</div>
                                    <div class="mt-1 text-sm text-pine-700">{{ $lead->phone }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-pine-700">{{ $lead->province }}</td>
                                <td class="px-4 py-4 text-sm text-pine-700">{{ $lead->budget }}</td>
                                <td class="px-4 py-4 text-sm text-pine-700">{{ $lead->room_width }} x {{ $lead->room_length }} ม.</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses[$lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20' }}">{{ $lead->lead_status_label }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($lead->room_image)
                                        <img src="{{ asset('storage/'.$lead->room_image) }}" alt="รูปห้องของ {{ $lead->name }}" class="h-12 w-16 rounded-md object-cover ring-1 ring-pine-200">
                                    @else
                                        <span class="text-sm text-pine-400">ไม่มีรูป</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-pine-700">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('admin.leads.show', ['lead' => $lead] + request()->query()) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">ดูรายละเอียด</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-pine-700">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 space-y-4 lg:hidden">
                @forelse ($leads as $lead)
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-ink">{{ $lead->name }}</h2>
                                <p class="mt-1 text-sm text-pine-700">{{ $lead->phone }} · {{ $lead->province }}</p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses[$lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20' }}">{{ $lead->lead_status_label }}</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-pine-500">งบประมาณ</p>
                                <p class="font-medium text-ink">{{ $lead->budget }}</p>
                            </div>
                            <div>
                                <p class="text-pine-500">ขนาดห้อง</p>
                                <p class="font-medium text-ink">{{ $lead->room_width }} x {{ $lead->room_length }} ม.</p>
                            </div>
                        </div>
                        @if ($lead->room_image)
                            <img src="{{ asset('storage/'.$lead->room_image) }}" alt="รูปห้องของ {{ $lead->name }}" class="mt-4 aspect-video w-full rounded-md object-cover ring-1 ring-pine-200">
                        @endif
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-pine-700">{{ $lead->created_at->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('admin.leads.show', ['lead' => $lead] + request()->query()) }}" class="text-sm font-semibold text-pine-700">ดูรายละเอียด</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-pine-700 shadow-sm ring-1 ring-pine-200">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $leads->links() }}
            </div>
        </div>
    </section>

    <script>
        (() => {
            const board = document.querySelector('[data-kanban-board]');
            if (!board) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const message = board.querySelector('[data-kanban-message]');
            let draggedCard = null;

            const setMessage = (text, isError = false) => {
                if (!message) {
                    return;
                }

                message.textContent = text;
                message.classList.remove('hidden', 'bg-pine-100', 'text-pine-700', 'bg-rose-50', 'text-rose-700');
                message.classList.add(isError ? 'bg-rose-50' : 'bg-pine-100', isError ? 'text-rose-700' : 'text-pine-700');
            };

            const refreshCounts = () => {
                board.querySelectorAll('[data-kanban-column]').forEach((column) => {
                    const count = column.querySelectorAll('[data-lead-card]').length;
                    const counter = column.querySelector('[data-stage-count]');
                    if (counter) {
                        counter.textContent = count;
                    }
                });
            };

            const moveLead = async (card, column) => {
                const stage = column.dataset.stage;
                const previousDropzone = card.closest('[data-kanban-dropzone]');
                const dropzone = column.querySelector('[data-kanban-dropzone]');
                const updateUrl = card.dataset.updateUrl;

                if (!stage || !dropzone || !updateUrl || !token) {
                    setMessage('ไม่สามารถย้ายการ์ดได้ เนื่องจากตั้งค่าบอร์ดไม่ครบ', true);
                    return;
                }

                dropzone.appendChild(card);
                const select = card.querySelector('[data-card-status-select]');
                if (select) {
                    select.value = stage;
                }
                refreshCounts();

                try {
                    const response = await fetch(updateUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ lead_status: stage }),
                    });

                    if (!response.ok) {
                        throw new Error('อัปเดตสถานะไม่สำเร็จ');
                    }

                    setMessage('อัปเดตขั้นตอนงานขายเรียบร้อยแล้ว');
                } catch (error) {
                    if (previousDropzone) {
                        previousDropzone.appendChild(card);
                        refreshCounts();
                    }
                    setMessage('อัปเดตขั้นตอนงานขายไม่สำเร็จ กรุณาลองอีกครั้ง', true);
                }
            };

            board.querySelectorAll('[data-lead-card]').forEach((card) => {
                card.addEventListener('dragstart', (event) => {
                    draggedCard = card;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', card.dataset.leadId);
                    card.classList.add('opacity-60');
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('opacity-60');
                    draggedCard = null;
                    board.querySelectorAll('[data-kanban-dropzone]').forEach((zone) => {
                        zone.classList.remove('border-pine-400', 'bg-white');
                    });
                });
            });

            board.querySelectorAll('[data-kanban-column]').forEach((column) => {
                const dropzone = column.querySelector('[data-kanban-dropzone]');
                if (!dropzone) {
                    return;
                }

                dropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    dropzone.classList.add('border-pine-400', 'bg-white');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-pine-400', 'bg-white');
                });

                dropzone.addEventListener('drop', async (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('border-pine-400', 'bg-white');
                    if (draggedCard) {
                        await moveLead(draggedCard, column);
                    }
                });
            });
        })();
    </script>
@endsection
