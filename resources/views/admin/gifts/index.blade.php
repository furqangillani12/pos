@extends('layouts.admin')

@section('title', 'Gifts')

@section('content')
<div class="space-y-5">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Gifts</h2>
        <p class="text-sm text-gray-500">Products customers can pick as a gift. On the website the gift list shows before checkout; in the POS use "Add gift". Gifts take stock like a normal sale.</p>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm"><i class="fas fa-circle-check mr-1"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm"><i class="fas fa-circle-exclamation mr-1"></i> {{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Rules --}}
        <div class="rounded-xl bg-white border border-gray-100 shadow-sm p-5 lg:order-2"
             x-data="{ rows: @js($tiers ?: [['min' => '', 'gifts' => '']]) }">
            <div class="font-semibold text-gray-800">How many gifts</div>
            <p class="mt-1 text-xs text-gray-500">Order amount (before delivery) decides how many gifts the customer can pick on the website. With no rules, every order can pick 1 gift.</p>
            <form method="POST" action="{{ route('admin.gifts.tiers') }}" class="mt-3 space-y-2">
                @csrf
                <template x-for="(r, i) in rows" :key="i">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-gray-500 whitespace-nowrap">Rs</span>
                        <input type="number" :name="'tiers[' + i + '][min]'" x-model="r.min" min="0" step="1" placeholder="5000" class="w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                        <span class="text-gray-500 whitespace-nowrap">or more →</span>
                        <input type="number" :name="'tiers[' + i + '][gifts]'" x-model="r.gifts" min="0" max="50" placeholder="3" class="w-16 rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                        <span class="text-gray-500">gifts</span>
                        <button type="button" @click="rows.splice(i, 1)" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                    </div>
                </template>
                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="rows.push({ min: '', gifts: '' })" class="text-xs text-blue-600 hover:underline"><i class="fas fa-plus"></i> Add rule</button>
                    <button class="rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-blue-700">Save rules</button>
                </div>
            </form>
        </div>

        {{-- Gift products --}}
        <div class="lg:col-span-2 lg:order-1 rounded-xl bg-white border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.gifts.store') }}" class="flex flex-col sm:flex-row gap-2 border-b p-4"
                  x-data="{ all: @js($products), q: '', open: false, pick: null,
                            get list() { const q = this.q.toLowerCase(); return this.all.filter(p => !q || p.name.toLowerCase().includes(q) || (p.code || '').toLowerCase().includes(q)).slice(0, 30); } }">
                @csrf
                <input type="hidden" name="product_id" :value="pick ? pick.id : ''">
                <div class="relative flex-1" @click.outside="open = false">
                    <input type="text" x-model="q" @focus="open = true" @input="open = true; pick = null" placeholder="Search a product to add as gift..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <div x-show="open && list.length" class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border bg-white shadow-lg">
                        <template x-for="p in list" :key="p.id">
                            <div @click="pick = p; q = p.name; open = false" class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-50">
                                <span x-text="p.name"></span> <span class="text-[11px] font-mono text-gray-400" x-text="p.code || ''"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rs</span>
                    <input type="number" name="price" min="0" step="1" value="0" class="w-full sm:w-28 rounded-lg border border-gray-300 pl-8 pr-2 py-2 text-sm" title="Gift price (0 = free)">
                </div>
                <button :disabled="!pick" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50"><i class="fas fa-gift"></i> Add gift</button>
            </form>

            <div class="divide-y divide-gray-100">
                @forelse ($gifts as $g)
                    @php $p = $g->product; $stock = $p ? $p->getStockForBranch($p->branch_id) : 0; @endphp
                    <form method="POST" action="{{ route('admin.gifts.update', $g) }}" class="flex flex-wrap items-center gap-3 px-4 py-3 {{ $g->is_active ? '' : 'opacity-60' }}">
                        @csrf @method('PUT')
                        @if ($p?->image)
                            <img src="{{ asset('storage/' . $p->image) }}" class="h-12 w-12 rounded-lg object-cover bg-gray-100" alt="">
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-300"><i class="fas fa-gift"></i></div>
                        @endif
                        <div class="flex-1 min-w-[10rem]">
                            <div class="font-medium text-gray-800">{{ $p->name ?? 'Deleted product' }}</div>
                            <div class="text-xs {{ $p && $p->track_inventory && $stock < 1 ? 'text-red-600' : 'text-gray-500' }}">
                                Stock: {{ $p && $p->track_inventory ? rtrim(rtrim(number_format($stock, 2), '0'), '.') : 'not tracked' }}
                                @if ($p && $p->track_inventory && $stock < 1) · hidden (out of stock) @endif
                            </div>
                        </div>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rs</span>
                            <input type="number" name="price" value="{{ (float) $g->price }}" min="0" step="1" class="w-24 rounded-lg border border-gray-300 pl-7 pr-2 py-1.5 text-sm">
                        </div>
                        <label class="inline-flex items-center gap-1.5 text-sm text-gray-600">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked($g->is_active) class="rounded"> Active
                        </label>
                        <button class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">Save</button>
                        <button form="del-gift-{{ $g->id }}" class="text-xs text-red-400 hover:text-red-600" title="Remove"><i class="fas fa-trash-alt"></i></button>
                    </form>
                    <form id="del-gift-{{ $g->id }}" method="POST" action="{{ route('admin.gifts.destroy', $g) }}" onsubmit="return confirm('Remove this gift?')" class="hidden">@csrf @method('DELETE')</form>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-gray-400"><i class="fas fa-gift text-3xl mb-2 block opacity-30"></i>No gifts yet. Search a product above to add one.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
