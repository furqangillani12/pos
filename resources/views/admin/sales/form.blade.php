@extends('layouts.admin')

@section('title', $sale->exists ? 'Edit sale' : 'New sale')

@php
    $selected = old('product_ids', $sale->exists ? $sale->products->pluck('id')->all() : []);
    $types    = old('applies_to', $sale->applies_to ?? ['customer']);
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="saleForm({ products: @js($products), selected: @js(array_map('intval', $selected)), pct: @js((float) old('discount_percent', $sale->discount_percent ?? 10)) })">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">{{ $sale->exists ? 'Edit sale' : 'New sale' }}</h2>
        <a href="{{ route('admin.sales.index') }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left text-xs"></i> Back to Sales</a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ $sale->exists ? route('admin.sales.update', $sale) : route('admin.sales.store') }}" class="space-y-4">
        @csrf
        @if ($sale->exists) @method('PUT') @endif

        <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sale name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $sale->name) }}" required placeholder="e.g. Muharram sale"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount % <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="discount_percent" x-model.number="pct" min="0.01" max="100" step="0.01" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-8 text-sm font-bold">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Applies to <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Models\Sale::TYPES as $key => $label)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm cursor-pointer has-[:checked]:border-blue-400 has-[:checked]:bg-blue-50">
                            <input type="checkbox" name="applies_to[]" value="{{ $key }}" x-model="types" @checked(in_array($key, $types)) class="rounded">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-400">Only the ticked customer types get the discount; the others pay their normal price.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Starts</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $sale->starts_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="mt-1 text-xs text-gray-400">Blank = starts now</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ends</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $sale->ends_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="mt-1 text-xs text-gray-400">Blank = runs until switched off</p>
                </div>
                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $sale->is_active)) class="rounded">
                        Active
                    </label>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="rounded-xl bg-white border border-gray-100 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b px-5 py-3">
                <div class="font-semibold text-gray-800">Products <span class="text-sm font-normal text-gray-500">(<span x-text="selected.length"></span> selected)</span></div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="selectShown()" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">Select all shown</button>
                    <button type="button" @click="selected = []" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">Clear</button>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 px-5 py-3 border-b">
                <input type="text" x-model="q" placeholder="Search name or code..." class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <select x-model="cat" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
                <label class="inline-flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" x-model="onlySelected" class="rounded"> Selected only</label>
            </div>
            <template x-for="id in selected" :key="'h' + id"><input type="hidden" name="product_ids[]" :value="id"></template>
            <div class="max-h-[28rem] overflow-y-auto divide-y divide-gray-100">
                <template x-for="p in shown" :key="p.id">
                    <label class="flex items-center gap-3 px-5 py-2 text-sm hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" :checked="selected.includes(p.id)" @change="toggle(p.id)" class="rounded">
                        <div class="flex-1 min-w-0">
                            <div class="truncate text-gray-800" x-text="p.name"></div>
                            <div class="text-[11px] text-gray-400 font-mono" x-text="p.code || ''"></div>
                        </div>
                        <div class="text-right text-xs whitespace-nowrap">
                            <template x-if="types.includes('customer')"><div>Walk-in <s class="text-gray-400" x-text="rs(p.w)"></s> <b class="text-rose-600" x-text="rs(off(p.w))"></b></div></template>
                            <template x-if="types.includes('reseller')"><div>Reseller <s class="text-gray-400" x-text="rs(p.r)"></s> <b class="text-rose-600" x-text="rs(off(p.r))"></b></div></template>
                            <template x-if="types.includes('wholesale')"><div>Wholesale <s class="text-gray-400" x-text="rs(p.h)"></s> <b class="text-rose-600" x-text="rs(off(p.h))"></b></div></template>
                        </div>
                    </label>
                </template>
                <div x-show="!shown.length" class="px-5 py-8 text-center text-sm text-gray-400">No products match.</div>
            </div>
            <div x-show="more > 0" class="border-t px-5 py-2 text-xs text-gray-500">Showing first 200; <span x-text="more"></span> more. Narrow the search to see them.</div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.sales.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
            <button class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save sale</button>
        </div>
    </form>
</div>

<script>
    window.saleForm = function (cfg) {
        return {
            products: cfg.products, selected: cfg.selected, pct: cfg.pct,
            types: @js(array_values($types)),
            q: '', cat: '', onlySelected: false,
            get matches() {
                const q = this.q.trim().toLowerCase();
                return this.products.filter(p =>
                    (!this.cat || String(p.cat) === String(this.cat)) &&
                    (!this.onlySelected || this.selected.includes(p.id)) &&
                    (!q || p.name.toLowerCase().includes(q) || (p.code || '').toLowerCase().includes(q)));
            },
            get shown() { return this.matches.slice(0, 200); },
            get more() { return Math.max(0, this.matches.length - 200); },
            toggle(id) { const i = this.selected.indexOf(id); i >= 0 ? this.selected.splice(i, 1) : this.selected.push(id); },
            selectShown() { this.matches.forEach(p => { if (!this.selected.includes(p.id)) this.selected.push(p.id); }); },
            off(v) { return Math.round(v * (1 - (parseFloat(this.pct) || 0) / 100)); },
            rs(v) { return 'Rs ' + Math.round(v).toLocaleString(); },
        };
    };
</script>
@endsection
