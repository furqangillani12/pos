@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
<div class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Sales</h2>
            <p class="text-sm text-gray-500">Take a % off chosen products for chosen customer types, between two dates. The POS and website use the sale price automatically.</p>
        </div>
        <a href="{{ route('admin.sales.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-plus"></i> New sale</a>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm"><i class="fas fa-circle-check mr-1"></i> {{ session('success') }}</div>
    @endif

    @if ($sales->isEmpty())
        <div class="rounded-xl bg-white border p-10 text-center text-gray-400">
            <i class="fas fa-percent text-4xl mb-3 block opacity-30"></i>
            No sales yet. <a href="{{ route('admin.sales.create') }}" class="text-blue-600 hover:underline">Create one</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($sales as $sale)
                @php
                    $st = $sale->status;
                    $badge = ['running' => 'bg-green-100 text-green-800', 'scheduled' => 'bg-blue-100 text-blue-800', 'ended' => 'bg-gray-100 text-gray-500', 'off' => 'bg-gray-100 text-gray-500'][$st];
                    $label = ['running' => 'Running', 'scheduled' => 'Starts later', 'ended' => 'Ended', 'off' => 'Off'][$st];
                @endphp
                <div class="rounded-xl bg-white border {{ $st === 'running' ? 'border-green-300' : 'border-gray-200' }} p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $sale->name }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->products_count }} {{ Str::plural('product', $sale->products_count) }} · {{ $sale->applies_to_label }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-extrabold text-rose-600">{{ rtrim(rtrim(number_format($sale->discount_percent, 2), '0'), '.') }}%</div>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $badge }}">{{ $label }}</span>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-600">
                        <i class="far fa-calendar mr-1 text-gray-400"></i>
                        {{ $sale->starts_at ? $sale->starts_at->format('d M Y, g:i a') : 'From now' }}
                        to {{ $sale->ends_at ? $sale->ends_at->format('d M Y, g:i a') : 'no end date' }}
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t pt-3">
                        <a href="{{ route('admin.sales.edit', $sale) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200"><i class="fas fa-pen"></i> Edit</a>
                        <form method="POST" action="{{ route('admin.sales.toggle', $sale) }}">
                            @csrf @method('PATCH')
                            <button class="rounded-lg px-3 py-1.5 text-xs font-medium {{ $sale->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                <i class="fas fa-power-off"></i> {{ $sale->is_active ? 'Switch off' : 'Switch on' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}" class="ml-auto" onsubmit="return confirm('Delete this sale?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
