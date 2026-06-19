@extends('layouts.app', ['title' => $title.' | Wooden Dad Design'])

@section('content')
<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 print:hidden sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.purchase.index') }}" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $title }}</h1>
            </div>
            <button onclick="window.print()" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white">พิมพ์ / บันทึก PDF</button>
        </div>

        <section class="overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200 print:shadow-none print:ring-0">
            <h2 class="text-xl font-semibold text-ink">Wooden Dad Design</h2>
            <p class="mt-1 text-sm text-pine-700">{{ $title }} · {{ now()->format('d/m/Y H:i') }}</p>
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-pine-200 text-sm">
                    @foreach ($rows as $index => $row)
                        @if ($index === 0)
                            <thead class="bg-pine-100 text-pine-700"><tr>@foreach ($row as $cell)<th class="px-3 py-2 text-left font-semibold">{{ $cell }}</th>@endforeach</tr></thead><tbody class="divide-y divide-pine-100">
                        @else
                            <tr>@foreach ($row as $cell)<td class="px-3 py-3 text-pine-700">{{ $cell }}</td>@endforeach</tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>
@endsection
